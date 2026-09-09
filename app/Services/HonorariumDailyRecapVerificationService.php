<?php

namespace App\Services;

use Carbon\Carbon;

class HonorariumDailyRecapVerificationService
{
    public function buildReportHash($reports)
    {
        $evidence = collect($reports)->map(function ($report) {
            return [
                'tanggal' => (string) $report->tanggal,
                'mahasiswa' => collect(array_keys($report->student_nims))->sort()->values()->all(),
                'jenis_ujian' => collect($report->exam_types)->map(function ($type) {
                    return [
                        'nama' => (string) $type->name,
                        'mahasiswa' => (int) $type->student_count,
                        'penugasan' => (int) $type->assignment_count,
                        'honor' => round((float) $type->total_honor, 2),
                    ];
                })->sortBy('nama')->values()->all(),
                'dosen' => collect($report->lecturers)->map(function ($lecturer) {
                    return [
                        'kode' => (string) $lecturer->code,
                        'mahasiswa' => (int) $lecturer->student_count,
                        'penugasan' => (int) $lecturer->assignment_count,
                        'honor' => round((float) $lecturer->total_honor, 2),
                    ];
                })->sortBy('kode')->values()->all(),
                'total_honor' => round((float) $report->total_honor, 2),
            ];
        })->sortBy('tanggal')->values()->all();

        return hash('sha256', json_encode($evidence));
    }

    public function buildVerificationToken($reports, $official, Carbon $generatedAt, $key = null)
    {
        $reports = collect($reports);
        $payload = [
            'version' => 1,
            'dates' => $reports->pluck('tanggal')->map(function ($date) {
                return (string) $date;
            })->values()->all(),
            'student_count' => (int) $reports->sum('student_count'),
            'lecturer_count' => (int) $reports->flatMap(function ($report) {
                return collect($report->lecturers)->pluck('code');
            })->unique()->count(),
            'assignment_count' => (int) $reports->sum('assignment_count'),
            'signer_name' => trim((string) ($official->nama ?? '')),
            'signer_id' => trim((string) ($official->nip_nidn ?? '')),
            'report_hash' => $this->buildReportHash($reports),
            'generated_at' => $generatedAt->format('Y-m-d H:i:s'),
        ];
        $encoded = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');
        $signature = hash_hmac('sha256', $encoded, $this->resolveSigningKey($key));

        return $encoded . '.' . $signature;
    }

    public function decodeVerificationToken($token, $key = null)
    {
        $parts = explode('.', (string) $token, 2);
        if (count($parts) !== 2) {
            return null;
        }

        list($encoded, $signature) = $parts;
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $encoded) || !preg_match('/^[a-f0-9]{64}$/', $signature)) {
            return null;
        }

        $expected = hash_hmac('sha256', $encoded, $this->resolveSigningKey($key));
        if (!hash_equals($expected, $signature)) {
            return null;
        }

        $base64Payload = strtr($encoded, '-_', '+/');
        $base64Payload .= str_repeat('=', (4 - strlen($base64Payload) % 4) % 4);
        $decoded = base64_decode($base64Payload, true);
        if ($decoded === false) {
            return null;
        }

        $payload = json_decode($decoded, true);
        $dates = is_array($payload['dates'] ?? null) ? $payload['dates'] : [];
        $validDates = !empty($dates) && count($dates) <= 100;
        foreach ($dates as $date) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date)) {
                $validDates = false;
                break;
            }
        }

        if (!is_array($payload)
            || (int) ($payload['version'] ?? 0) !== 1
            || !$validDates
            || trim((string) ($payload['signer_name'] ?? '')) === ''
            || !preg_match('/^[a-f0-9]{64}$/', (string) ($payload['report_hash'] ?? ''))
            || (int) ($payload['student_count'] ?? -1) < 0
            || (int) ($payload['lecturer_count'] ?? -1) < 0
            || (int) ($payload['assignment_count'] ?? -1) < 0) {
            return null;
        }

        return $payload;
    }

    protected function resolveSigningKey($key)
    {
        $key = trim((string) $key);

        return $key !== '' ? $key : (string) config('app.key');
    }
}
