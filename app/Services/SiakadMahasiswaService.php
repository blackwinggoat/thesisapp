<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class SiakadMahasiswaService
{
    const PROGRAMS = [
        '130' => [
            'kode_prodi' => '55201',
            'label' => 'Teknik Informatika',
        ],
        '131' => [
            'kode_prodi' => '57201',
            'label' => 'Sistem Informasi',
        ],
    ];

    public function syncByNim($nim)
    {
        $identity = self::parseNim($nim);
        if (!$identity) {
            return $this->failure('NIM harus terdiri dari 11 angka dan menggunakan prefix 130 atau 131.');
        }

        $source = $this->fetchStudents($identity['program_code'], $identity['angkatan']);
        if (!$source['ok']) {
            return $source;
        }

        $student = null;
        foreach ($source['rows'] as $row) {
            if (!is_array($row) || trim((string) ($row['nim'] ?? '')) !== $identity['nim']) {
                continue;
            }
            $candidate = self::normalizeSourceStudent($row, $identity['program_code'], $identity['angkatan']);
            if (!$candidate) {
                return $this->failure('Data mahasiswa dari SIAKAD tidak valid. Tidak ada data yang diubah.');
            }
            if ($student !== null && $candidate !== $student) {
                return $this->failure('SIAKAD mengembalikan data yang berbeda untuk NIM ini. Tidak ada data yang diubah.');
            }
            $student = $candidate;
        }

        if (!$student) {
            return $this->failure('Mahasiswa tidak ditemukan pada data aktif SIAKAD untuk program studi dan angkatan tersebut.');
        }

        $result = $this->storeStudents([$student], $identity['program_code'], true);
        $result['nim'] = $identity['nim'];

        return $result;
    }

    public function syncByCohort($programCode, $angkatan)
    {
        $programCode = trim((string) $programCode);
        $angkatan = trim((string) $angkatan);

        if (!isset(self::PROGRAMS[$programCode])) {
            return $this->failure('Program studi sinkronisasi tidak valid.');
        }

        if (!preg_match('/^20[0-9]{2}$/', $angkatan)) {
            return $this->failure('Angkatan harus berupa tahun empat digit.');
        }

        $source = $this->fetchStudents($programCode, $angkatan);
        if (!$source['ok']) {
            return $source;
        }

        $students = [];
        $ignored = 0;
        foreach ($source['rows'] as $row) {
            $student = self::normalizeSourceStudent($row, $programCode, $angkatan);
            if (!$student) {
                $ignored++;
                continue;
            }

            $students[$student['nim']] = $student;
        }

        $result = $this->storeStudents(array_values($students), $programCode);
        $result['source_total'] = count($source['rows']);
        $result['ignored'] = $ignored;

        return $result;
    }

    public static function programOptions()
    {
        return self::PROGRAMS;
    }

    public static function parseNim($nim)
    {
        $nim = trim((string) $nim);
        if (!preg_match('/^(130|131)(20[0-9]{2})[0-9]{4}$/', $nim, $matches)) {
            return null;
        }

        return [
            'nim' => $nim,
            'program_code' => $matches[1],
            'angkatan' => $matches[2],
        ];
    }

    public static function normalizeSourceStudent($row, $programCode, $angkatan)
    {
        if (!is_array($row) || !isset(self::PROGRAMS[$programCode])) {
            return null;
        }

        $identity = self::parseNim($row['nim'] ?? '');
        if (!$identity
            || $identity['program_code'] !== (string) $programCode
            || $identity['angkatan'] !== (string) $angkatan) {
            return null;
        }

        $sourceAngkatan = trim((string) ($row['angkatan'] ?? ''));
        if ($sourceAngkatan !== '' && $sourceAngkatan !== $identity['angkatan']) {
            return null;
        }

        $name = trim((string) ($row['nama_mahasiswa'] ?? ''));
        if ($name === '' || mb_strlen($name) > 100) {
            return null;
        }

        $gender = strtoupper(trim((string) ($row['jkel'] ?? '')));
        if (!in_array($gender, ['L', 'P'], true)) {
            $gender = null;
        }

        $birthDate = trim((string) ($row['tgllahir'] ?? ''));
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $birthDate, $dateParts)
            || !checkdate((int) $dateParts[2], (int) $dateParts[3], (int) $dateParts[1])) {
            $birthDate = null;
        }

        $birthPlace = trim((string) ($row['tempatlahir'] ?? ''));
        if ($birthPlace === '' || mb_strlen($birthPlace) > 100) {
            $birthPlace = null;
        }

        return [
            'nim' => $identity['nim'],
            'name' => $name,
            'program_code' => $identity['program_code'],
            'angkatan' => $identity['angkatan'],
            'gender' => $gender,
            'birth_place' => $birthPlace,
            'birth_date' => $birthDate,
        ];
    }

    public static function buildMasterRecord(array $student, $programCode, $timestamp = null)
    {
        if (!isset(self::PROGRAMS[$programCode])) {
            return null;
        }

        $identity = self::parseNim($student['nim'] ?? '');
        if (!$identity || $identity['program_code'] !== (string) $programCode) {
            return null;
        }

        $profile = self::PROGRAMS[$programCode];
        $timestamp = $timestamp ?: Carbon::now();

        return [
            'C_KODE_PT' => '091002',
            'C_KODE_FAKULTAS' => '91000',
            'C_KODE_PRODI' => $profile['kode_prodi'],
            'C_NPM' => $identity['nim'],
            'NAMA_MAHASISWA' => trim((string) ($student['name'] ?? '')),
            'TEMPAT_LAHIR' => $student['birth_place'] ?? null,
            'TGL_LAHIR' => $student['birth_date'] ?? null,
            'JENIS_KELAMIN' => $student['gender'] ?? null,
            'C_KODE_KONSENTRASI' => $profile['kode_prodi'] . '-K-GEN',
            'TAHUN_MASUK' => $identity['angkatan'],
            'THN_SEMESTER_AWAL_MASUK' => $identity['angkatan'] . '1',
            'C_KODE_STATUS_AWAL_MHS' => '2',
            'C_KODE_STATUS_AKTIF_MHS' => 'A',
            'F_AKTIF' => 1,
            'F_IS_C' => 0,
            'F_IS_U' => 0,
            'F_IS_D' => 0,
            'F_CHANGE_LOG' => 'Sinkronisasi dari SIAKAD list-mahasiswa',
            'C_USER' => 'siakad-mahasiswa',
            'C_DATE' => $timestamp,
            'U_USER' => 'siakad-mahasiswa',
            'U_DATE' => $timestamp,
        ];
    }

    protected function storeStudents(array $students, $programCode, $updateExisting = false)
    {
        $students = array_values(array_filter($students, function ($student) use ($programCode) {
            return is_array($student)
                && isset($student['nim'])
                && self::parseNim($student['nim']) !== null
                && substr((string) $student['nim'], 0, 3) === (string) $programCode;
        }));

        $uniqueStudents = [];
        foreach ($students as $student) {
            $uniqueStudents[$student['nim']] = $student;
        }
        $students = array_values($uniqueStudents);

        if (empty($students)) {
            return $this->success([
                'program_code' => $programCode,
                'angkatan' => null,
                'source_total' => 0,
                'added' => 0,
                'already_exists' => 0,
                'ignored' => 0,
            ]);
        }

        $nims = array_column($students, 'nim');
        $timestamp = Carbon::now();

        try {
            $stored = DB::transaction(function () use ($students, $nims, $programCode, $timestamp, $updateExisting) {
                $existing = DB::table('t_mst_mahasiswa')
                    ->whereIn('C_NPM', $nims)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('C_NPM');
                $records = [];
                $updated = 0;
                $updatedColumns = [];
                $semesterNeedsReview = false;

                foreach ($students as $student) {
                    $current = $existing->get($student['nim']);
                    if ($current) {
                        if ($updateExisting) {
                            $semester = trim((string) ($current->THN_SEMESTER_AWAL_MASUK ?? ''));
                            if ($semester !== '' && substr($semester, 0, 4) !== (string) $student['angkatan']) {
                                $semesterNeedsReview = true;
                            }
                            $changes = $this->changesForExistingStudent($current, $student, $programCode);
                            if ($changes) {
                                if (!Schema::hasTable('trt_siakad_mahasiswa_sync_audit')) {
                                    throw new RuntimeException('Audit sinkronisasi belum tersedia.');
                                }

                                $before = [];
                                foreach ($changes as $column => $value) {
                                    $before[$column] = $current->{$column};
                                }
                                $beforeJson = json_encode($before, JSON_UNESCAPED_UNICODE);
                                $afterJson = json_encode($changes, JSON_UNESCAPED_UNICODE);
                                if ($beforeJson === false || $afterJson === false) {
                                    throw new RuntimeException('Audit sinkronisasi tidak dapat disimpan.');
                                }

                                DB::table('trt_siakad_mahasiswa_sync_audit')->insert([
                                    'C_NPM' => $student['nim'],
                                    'user_id' => auth()->id(),
                                    'before_values' => $beforeJson,
                                    'after_values' => $afterJson,
                                    'created_at' => $timestamp,
                                ]);
                                DB::table('t_mst_mahasiswa')->where('C_NPM', $student['nim'])->update($changes);
                                $updated++;
                                $updatedColumns = array_values(array_unique(array_merge($updatedColumns, array_keys($changes))));
                            }
                        }
                        continue;
                    }

                    $record = self::buildMasterRecord($student, $programCode, $timestamp);
                    if ($record && $record['NAMA_MAHASISWA'] !== '') {
                        $records[] = $record;
                    }
                }

                if (!empty($records)) {
                    DB::table('t_mst_mahasiswa')->insert($records);
                }

                return [
                    'added' => count($records),
                    'updated' => $updated,
                    'updated_columns' => $updatedColumns,
                    'already_exists' => $existing->count(),
                    'semester_needs_review' => $semesterNeedsReview,
                ];
            });
        } catch (Throwable $exception) {
            Log::error('Sinkronisasi identitas mahasiswa dari SIAKAD gagal.', [
                'exception_type' => get_class($exception),
            ]);
            return $this->failure('Sinkronisasi gagal. Tidak ada data mahasiswa yang diubah; hubungi administrator.');
        }

        return $this->success([
            'program_code' => $programCode,
            'angkatan' => $students[0]['angkatan'] ?? null,
            'source_total' => count($students),
            'added' => $stored['added'],
            'updated' => $stored['updated'],
            'updated_columns' => $stored['updated_columns'],
            'already_exists' => $stored['already_exists'],
            'semester_needs_review' => $stored['semester_needs_review'],
            'ignored' => 0,
        ]);
    }

    protected function changesForExistingStudent($current, array $student, $programCode)
    {
        $program = self::PROGRAMS[$programCode];
        $values = [
            'NAMA_MAHASISWA' => $student['name'],
            'C_KODE_PRODI' => $program['kode_prodi'],
            'TAHUN_MASUK' => $student['angkatan'],
        ];
        foreach (['gender' => 'JENIS_KELAMIN', 'birth_place' => 'TEMPAT_LAHIR', 'birth_date' => 'TGL_LAHIR'] as $source => $column) {
            if (!empty($student[$source])) {
                $values[$column] = $student[$source];
            }
        }

        $oldProgram = (string) ($current->C_KODE_PRODI ?? '');
        $oldConcentration = (string) ($current->C_KODE_KONSENTRASI ?? '');
        if ($oldProgram !== $program['kode_prodi']
            && in_array($oldProgram, array_column(self::PROGRAMS, 'kode_prodi'), true)
            && $oldConcentration === $oldProgram . '-K-GEN') {
            $values['C_KODE_KONSENTRASI'] = $program['kode_prodi'] . '-K-GEN';
        }

        return array_filter($values, function ($value, $column) use ($current) {
            return (string) ($current->{$column} ?? '') !== (string) $value;
        }, ARRAY_FILTER_USE_BOTH);
    }

    protected function fetchStudents($programCode, $angkatan)
    {
        $baseUrl = trim((string) config('services.siakad_mahasiswa.base_url', ''));
        $apiKey = trim((string) config('services.siakad_mahasiswa.api_key', ''));
        $endpoint = trim((string) config('services.siakad_mahasiswa.endpoint', 'list-mahasiswa'));
        $timeout = max(5, min(60, (int) config('services.siakad_mahasiswa.timeout', 20)));
        $limit = max(1, min(1000, (int) config('services.siakad_mahasiswa.page_limit', 1000)));
        $maxPages = max(1, min(100, (int) config('services.siakad_mahasiswa.max_pages', 30)));

        if ($baseUrl === '' || $apiKey === '') {
            return $this->failure('Konfigurasi API SIAKAD mahasiswa belum lengkap.');
        }

        $rows = [];
        $seen = 0;

        for ($page = 1; $page <= $maxPages; $page++) {
            $separator = strpos($baseUrl, '?') === false ? '?' : '&';
            $url = $baseUrl . $separator . http_build_query([
                'endpoint' => $endpoint,
                'kode_prodi' => $programCode,
                'angkatan' => $angkatan,
                'page' => $page,
                'limit' => $limit,
            ], '', '&', PHP_QUERY_RFC3986);

            $curl = curl_init($url);
            if ($curl === false) {
                return $this->failure('Layanan cURL server tidak tersedia.');
            }

            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => min(10, $timeout),
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                    'X-API-Key: ' . $apiKey,
                    'User-Agent: ThesisAppsFikom/1.0',
                ],
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            ]);

            $body = curl_exec($curl);
            $error = curl_error($curl);
            $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($body === false || $status < 200 || $status >= 300) {
                return $this->failure($error !== ''
                    ? 'Koneksi ke SIAKAD gagal.'
                    : 'SIAKAD mengembalikan HTTP ' . $status . '.');
            }

            $payload = json_decode($body, true);
            if (!is_array($payload)
                || ($payload['status'] ?? null) !== 'success'
                || !is_array($payload['data'] ?? null)) {
                return $this->failure('Respons mahasiswa dari SIAKAD tidak dapat digunakan.');
            }

            $pageRows = array_values($payload['data']);
            $rows = array_merge($rows, $pageRows);
            $seen += count($pageRows);

            if (!$this->hasMore($payload, $seen, count($pageRows))) {
                return $this->success(['rows' => $rows]);
            }
        }

        return $this->failure('Data SIAKAD terlalu banyak untuk diproses sekaligus. Batasi angkatan lalu coba kembali.');
    }

    protected function hasMore(array $payload, $seen, $pageCount)
    {
        $meta = is_array($payload['meta'] ?? null) ? $payload['meta'] : [];
        if (is_bool($meta['has_more'] ?? null)) {
            return $meta['has_more'];
        }

        $total = max(0, (int) ($meta['total'] ?? $payload['total'] ?? $seen));

        return $seen < $total && $pageCount > 0;
    }

    protected function success(array $data = [])
    {
        return array_merge(['ok' => true], $data);
    }

    protected function failure($message)
    {
        return [
            'ok' => false,
            'message' => $message,
        ];
    }
}
