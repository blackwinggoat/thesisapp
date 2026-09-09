<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AuditHonorariumScheduleAlignment extends Command
{
    protected $signature = 'thesis:audit-honorarium-schedules
        {--strict : Return a failure when a linked row is invalid or its stored date differs from the schedule}
        {--json : Print the result as JSON}';

    protected $description = 'Audit honorarium records against exact student exam schedules';

    public function handle()
    {
        $requiredTables = [
            'trt_honorium',
            'trt_reg',
            'trt_jadwal_ujian',
            'trt_jadwal_ujian_per_mhs',
        ];
        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                $this->error('Required table is missing: ' . $table);

                return 1;
            }
        }
        if (!Schema::hasColumn('trt_honorium', 'jadwal_ujian_id')) {
            $this->error('Column trt_honorium.jadwal_ujian_id is missing.');

            return 1;
        }

        $outstandingSql = $this->outstandingSql('honorarium');
        $validLinkSql = $this->validLinkSql('honorarium');
        $total = DB::table('trt_honorium')->count();
        $outstanding = DB::table('trt_honorium as honorarium')
            ->whereRaw($outstandingSql)
            ->count();
        $valid = DB::table('trt_honorium as honorarium')
            ->whereNotNull('honorarium.jadwal_ujian_id')
            ->whereExists($validLinkSql)
            ->count();
        $validOutstanding = DB::table('trt_honorium as honorarium')
            ->whereRaw($outstandingSql)
            ->whereNotNull('honorarium.jadwal_ujian_id')
            ->whereExists($validLinkSql)
            ->count();
        $invalid = DB::table('trt_honorium as honorarium')
            ->whereNotNull('honorarium.jadwal_ujian_id')
            ->whereNotExists($validLinkSql)
            ->count();
        $dateMismatch = DB::table('trt_honorium as honorarium')
            ->join('trt_jadwal_ujian as jadwal', 'jadwal.id', '=', 'honorarium.jadwal_ujian_id')
            ->whereExists($validLinkSql)
            ->where(function ($query) {
                $query->whereNull('honorarium.date')
                    ->orWhereRaw("CAST(honorarium.date AS CHAR) = '0000-00-00'")
                    ->orWhereRaw('DATE(honorarium.date) <> DATE(jadwal.tgl_ujian)');
            })
            ->count();
        $sourcePeriodMismatch = $this->sourcePeriodMismatchCount();

        $result = [
            'total_honorarium' => $total,
            'valid_schedule_links' => $valid,
            'unlinked_or_unverifiable' => $total - $valid,
            'outstanding_honorarium' => $outstanding,
            'outstanding_displayed' => $validOutstanding,
            'outstanding_excluded' => $outstanding - $validOutstanding,
            'invalid_schedule_links' => $invalid,
            'stored_date_mismatches' => $dateMismatch,
            'source_period_mismatches' => $sourcePeriodMismatch,
        ];

        if ($this->option('json')) {
            $this->line(json_encode($result, JSON_UNESCAPED_SLASHES));
        } else {
            $this->table(['Pemeriksaan', 'Jumlah'], collect($result)->map(function ($value, $key) {
                return [$key, $value];
            })->values()->all());
        }

        $failed = $invalid > 0 || $dateMismatch > 0 || $sourcePeriodMismatch > 0;
        if ($failed) {
            $this->error('Audit failed: a linked honorarium record is not aligned with its exact exam schedule.');

            return $this->option('strict') ? 1 : 0;
        }

        if ($outstanding > $validOutstanding) {
            $this->warn(($outstanding - $validOutstanding) . ' outstanding record(s) remain excluded because no exact exam schedule can be verified.');
        }
        $this->info('Audit passed: every displayed honorarium record uses its verified exam date.');

        return 0;
    }

    private function validLinkSql($alias)
    {
        return function ($query) use ($alias) {
            $query->select(DB::raw(1))
                ->from('trt_jadwal_ujian as jadwal_validasi')
                ->whereRaw('jadwal_validasi.id = ' . $alias . '.jadwal_ujian_id')
                ->whereNotNull('jadwal_validasi.tgl_ujian')
                ->whereRaw("CAST(jadwal_validasi.tgl_ujian AS CHAR) <> '0000-00-00'")
                ->whereExists(function ($participant) use ($alias) {
                    $participant->select(DB::raw(1))
                        ->from('trt_jadwal_ujian_per_mhs as peserta_validasi')
                        ->whereRaw('peserta_validasi.jadwal_ujian = jadwal_validasi.id')
                        ->whereRaw('peserta_validasi.C_NPM = ' . $alias . '.C_NPM');
                })
                ->whereExists(function ($registration) use ($alias) {
                    $registration->select(DB::raw(1))
                        ->from('trt_reg as registrasi_validasi')
                        ->whereRaw('registrasi_validasi.C_NPM = ' . $alias . '.C_NPM')
                        ->whereRaw('registrasi_validasi.status = ' . $alias . '.exam_type')
                        ->whereRaw('registrasi_validasi.pendaftaran_id = jadwal_validasi.pendaftaran_id');
                });
        };
    }

    private function sourcePeriodMismatchCount()
    {
        $records = DB::table('trt_honorium as honorarium')
            ->join('trt_jadwal_ujian as jadwal', 'jadwal.id', '=', 'honorarium.jadwal_ujian_id')
            ->whereNotNull('honorarium.source_key')
            ->where('honorarium.source_key', '<>', '')
            ->get(['honorarium.source_key', 'jadwal.pendaftaran_id']);

        return $records->filter(function ($record) {
            if (!preg_match('/(?:^|:)periode:(\d+)(?:$|:)/', (string) $record->source_key, $matches)) {
                return true;
            }

            return (int) $matches[1] !== (int) $record->pendaftaran_id;
        })->count();
    }

    private function outstandingSql($alias)
    {
        $conditions = [];
        foreach (['KS', 'PU', 'PP', 'P1', 'P2', 'P3'] as $role) {
            $hasRole = "NULLIF(TRIM(COALESCE({$alias}.{$role}, '')), '') IS NOT NULL";
            $conditions[] = "({$hasRole} AND COALESCE({$alias}.{$role}_Stat, 0) <> 3)";
        }

        return '(' . implode(' OR ', $conditions) . ')';
    }
}
