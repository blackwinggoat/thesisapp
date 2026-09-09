<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AuditHonorariumDate extends Command
{
    protected $signature = 'thesis:audit-honorarium-date {date : Exam date in YYYY-MM-DD format} {--json}';

    protected $description = 'Reconcile scheduled students and honorarium records for one exam date';

    public function handle()
    {
        $date = trim((string) $this->argument('date'));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->error('Invalid date. Use YYYY-MM-DD.');

            return 1;
        }

        foreach ([
            'trt_jadwal_ujian',
            'trt_jadwal_ujian_per_mhs',
            'mst_pendaftaran',
            'trt_honorium',
            'trt_reg',
            'trt_bimbingan',
            'trt_penguji',
            't_mst_mahasiswa',
        ] as $table) {
            if (!Schema::hasTable($table)) {
                $this->error('Required table is missing: ' . $table);

                return 1;
            }
        }

        $scheduled = DB::table('trt_jadwal_ujian as jadwal')
            ->join('trt_jadwal_ujian_per_mhs as peserta', 'peserta.jadwal_ujian', '=', 'jadwal.id')
            ->join('mst_pendaftaran as periode', 'periode.pendaftaran_id', '=', 'jadwal.pendaftaran_id')
            ->leftJoin('t_mst_mahasiswa as mahasiswa', 'mahasiswa.C_NPM', '=', 'peserta.C_NPM')
            ->whereDate('jadwal.tgl_ujian', $date)
            ->whereIn('periode.tipe_ujian', [0, 2])
            ->select(
                'jadwal.id as schedule_id',
                'jadwal.pendaftaran_id as period_id',
                'periode.tipe_ujian as exam_type',
                'peserta.C_NPM as nim',
                'mahasiswa.NAMA_MAHASISWA as name'
            )
            ->orderBy('jadwal.id')
            ->orderBy('peserta.C_NPM')
            ->get()
            ->unique(function ($row) {
                return $row->schedule_id . '|' . $row->nim . '|' . $row->exam_type;
            })
            ->values();

        $rows = $scheduled->map(function ($scheduledRow) {
            $registration = DB::table('trt_reg')
                ->where('C_NPM', $scheduledRow->nim)
                ->where('status', (int) $scheduledRow->exam_type)
                ->where('pendaftaran_id', (int) $scheduledRow->period_id)
                ->orderByDesc('reg_id')
                ->first();
            $guidance = DB::table('trt_bimbingan')
                ->where('C_NPM', $scheduledRow->nim)
                ->orderByDesc('bimbingan_id')
                ->first();
            $examiner = DB::table('trt_penguji')
                ->where('C_NPM', $scheduledRow->nim)
                ->where('tipe_ujian', (int) $scheduledRow->exam_type)
                ->orderByDesc('id')
                ->first();
            $honorariums = DB::table('trt_honorium')
                ->where('C_NPM', $scheduledRow->nim)
                ->where('exam_type', (int) $scheduledRow->exam_type)
                ->where('jadwal_ujian_id', (int) $scheduledRow->schedule_id)
                ->orderBy('id')
                ->get();

            $activeHonorariumIds = [];
            $paidHonorariumIds = [];
            foreach ($honorariums as $honorarium) {
                if ($this->isOutstanding($honorarium)) {
                    $activeHonorariumIds[] = (int) $honorarium->id;
                } else {
                    $paidHonorariumIds[] = (int) $honorarium->id;
                }
            }

            $reason = 'displayed';
            if (empty($activeHonorariumIds)) {
                if (!empty($paidHonorariumIds)) {
                    $reason = 'paid_history';
                } elseif (!$registration) {
                    $reason = 'registration_missing_or_wrong_period';
                } elseif (!$guidance) {
                    $reason = 'guidance_missing';
                } elseif (!$examiner) {
                    $reason = 'examiner_team_missing';
                } elseif ($this->isAwaitingConfirmation($scheduledRow->exam_type, $guidance->status_bimbingan)) {
                    $reason = 'awaiting_result_confirmation';
                } else {
                    $reason = 'honorarium_missing_after_confirmation';
                }
            }

            return [
                'nim' => (string) $scheduledRow->nim,
                'name' => trim((string) $scheduledRow->name),
                'exam_type' => (int) $scheduledRow->exam_type,
                'schedule_id' => (int) $scheduledRow->schedule_id,
                'period_id' => (int) $scheduledRow->period_id,
                'registration_id' => $registration ? (int) $registration->reg_id : null,
                'guidance_id' => $guidance ? (int) $guidance->bimbingan_id : null,
                'guidance_status' => $guidance ? (int) $guidance->status_bimbingan : null,
                'examiner_team_id' => $examiner ? (int) $examiner->id : null,
                'active_honorarium_ids' => $activeHonorariumIds,
                'paid_honorarium_ids' => $paidHonorariumIds,
                'result' => $reason,
            ];
        })->values();

        $result = [
            'date' => $date,
            'scheduled_exam_instances' => $rows->count(),
            'displayed_active_honorarium' => $rows->where('result', 'displayed')->count(),
            'paid_history' => $rows->where('result', 'paid_history')->count(),
            'not_displayed' => $rows->whereNotIn('result', ['displayed', 'paid_history'])->count(),
            'reason_counts' => $rows->groupBy('result')->map->count(),
            'rows' => $rows,
        ];

        if ($this->option('json')) {
            $this->line(json_encode($result, JSON_UNESCAPED_SLASHES));
        } else {
            $this->table(['Pemeriksaan', 'Jumlah'], [
                ['scheduled_exam_instances', $result['scheduled_exam_instances']],
                ['displayed_active_honorarium', $result['displayed_active_honorarium']],
                ['paid_history', $result['paid_history']],
                ['not_displayed', $result['not_displayed']],
            ]);
        }

        return 0;
    }

    private function isOutstanding($honorarium)
    {
        foreach (['KS', 'PU', 'PP', 'P1', 'P2', 'P3'] as $role) {
            if (trim((string) $honorarium->{$role}) !== ''
                && (int) $honorarium->{$role . '_Stat'} !== 3
            ) {
                return true;
            }
        }

        return false;
    }

    private function isAwaitingConfirmation($examType, $guidanceStatus)
    {
        return ((int) $examType === 0 && (int) $guidanceStatus === 0)
            || ((int) $examType === 2 && (int) $guidanceStatus === 2);
    }
}
