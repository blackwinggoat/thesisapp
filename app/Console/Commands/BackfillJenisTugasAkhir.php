<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class BackfillJenisTugasAkhir extends Command
{
    protected $signature = 'thesis:backfill-jenis-tugas-akhir {--apply : Apply the planned title and type updates}';

    protected $description = 'Extract final-project type codes from final titles and remove their title prefixes';

    public function handle()
    {
        if (!Schema::hasColumn('trt_bimbingan', 'jenis_tugas_akhir_id')) {
            throw new RuntimeException('Column jenis_tugas_akhir_id is missing. Run the database migration first.');
        }

        $apply = (bool) $this->option('apply');
        $masterPlan = $this->masterPlan();
        $knownCodes = $this->knownCodes($masterPlan);
        $currentTypeCodes = DB::table('mst_jenis_tugas_akhir')
            ->pluck('kode_jenis_tugas_akhir', 'jenis_tugas_akhir_id');
        $plans = [];
        $unknownPrefixes = [];
        $inconsistentTypes = [];
        $statistics = [];

        foreach (DB::table('trt_bimbingan')->orderBy('bimbingan_id')->get(['bimbingan_id', 'judul', 'jenis_tugas_akhir_id']) as $row) {
            $parsed = $this->extractTypeAndTitle((string) $row->judul, $knownCodes);
            if ($parsed['unknown_prefix'] !== null) {
                $unknownPrefixes[] = 'ID ' . $row->bimbingan_id . ': ' . $parsed['unknown_prefix'];
                continue;
            }

            $currentCode = $row->jenis_tugas_akhir_id === null
                ? null
                : $currentTypeCodes->get($row->jenis_tugas_akhir_id);
            $currentCode = $currentCode === 'NS-PK' ? 'NS-KP' : $currentCode;

            if ($parsed['code'] !== null && $currentCode !== null && $parsed['code'] !== $currentCode) {
                $inconsistentTypes[] = 'ID ' . $row->bimbingan_id . ': title ' . $parsed['code'] . ', stored type ' . $currentCode;
                continue;
            }

            $code = $parsed['code'] ?: ($currentCode ?: 'TA-SM');
            $statistics[$code] = ($statistics[$code] ?? 0) + 1;
            $plans[] = [
                'bimbingan_id' => $row->bimbingan_id,
                'code' => $code,
                'original_title' => (string) $row->judul,
                'judul' => $parsed['title'],
                'title_changed' => $parsed['title'] !== (string) $row->judul,
                'current_type_id' => $row->jenis_tugas_akhir_id,
            ];
        }

        if (!empty($unknownPrefixes) || !empty($inconsistentTypes)) {
            $this->error('Backfill blocked: title or stored type data needs review.');
            foreach (array_slice(array_merge($unknownPrefixes, $inconsistentTypes), 0, 20) as $problem) {
                $this->line($problem);
            }

            return 1;
        }

        ksort($statistics);
        $this->line('Rows: ' . count($plans));
        foreach ($statistics as $code => $count) {
            $this->line($code . ': ' . $count);
        }
        $this->line('Title prefixes to remove: ' . count(array_filter($plans, function ($plan) {
            return $plan['title_changed'];
        })));
        $this->line('Rows without a stored type: ' . count(array_filter($plans, function ($plan) {
            return $plan['current_type_id'] === null;
        })));
        foreach ($masterPlan as $operation) {
            $this->line('Master plan: ' . $operation['action'] . ' ' . $operation['code']);
        }

        if (!$apply) {
            $this->warn('Dry run only. Re-run with --apply after a verified database backup.');

            return 0;
        }

        $updated = 0;
        DB::transaction(function () use ($masterPlan, $plans, &$updated) {
            $this->applyMasterPlan($masterPlan);
            $masterIds = DB::table('mst_jenis_tugas_akhir')->pluck('jenis_tugas_akhir_id', 'kode_jenis_tugas_akhir');

            foreach ($plans as $plan) {
                if (!$masterIds->has($plan['code'])) {
                    throw new RuntimeException('Master type not found for ' . $plan['code']);
                }

                $jenisTugasAkhirId = $masterIds->get($plan['code']);
                if ($plan['judul'] === $plan['original_title']
                    && (int) $plan['current_type_id'] === (int) $jenisTugasAkhirId) {
                    continue;
                }

                DB::table('trt_bimbingan')
                    ->where('bimbingan_id', $plan['bimbingan_id'])
                    ->update([
                        'judul' => $plan['judul'],
                        'jenis_tugas_akhir_id' => $jenisTugasAkhirId,
                    ]);
                $updated++;
            }
        });

        $this->info('Backfill complete: ' . $updated . ' rows updated.');

        return 0;
    }

    private function masterPlan()
    {
        $masters = DB::table('mst_jenis_tugas_akhir')
            ->orderBy('jenis_tugas_akhir_id')
            ->get(['jenis_tugas_akhir_id', 'kode_jenis_tugas_akhir', 'deskripsi']);
        $codes = $masters->pluck('jenis_tugas_akhir_id', 'kode_jenis_tugas_akhir');
        $plan = [];

        if (!$codes->has('NS-KP') && $codes->has('NS-PK')) {
            $plan[] = ['action' => 'rename', 'id' => $codes->get('NS-PK'), 'code' => 'NS-KP', 'deskripsi' => 'Non Skripsi Pembelajaran Khusus'];
        } elseif (!$codes->has('NS-KP')) {
            $plan[] = ['action' => 'insert', 'code' => 'NS-KP', 'deskripsi' => 'Non Skripsi Pembelajaran Khusus'];
        }

        foreach (['NS-AR', 'NT-KT'] as $code) {
            if (!$codes->has($code)) {
                $plan[] = ['action' => 'insert', 'code' => $code, 'deskripsi' => 'Kode historis ' . $code];
            }
        }

        return $plan;
    }

    private function knownCodes(array $masterPlan)
    {
        $codes = DB::table('mst_jenis_tugas_akhir')->pluck('kode_jenis_tugas_akhir')->all();
        foreach ($masterPlan as $operation) {
            $codes[] = $operation['code'];
        }

        return array_values(array_unique($codes));
    }

    private function applyMasterPlan(array $masterPlan)
    {
        foreach ($masterPlan as $operation) {
            if ($operation['action'] === 'rename') {
                DB::table('mst_jenis_tugas_akhir')
                    ->where('jenis_tugas_akhir_id', $operation['id'])
                    ->update([
                        'kode_jenis_tugas_akhir' => $operation['code'],
                        'deskripsi' => $operation['deskripsi'],
                    ]);
                continue;
            }

            DB::table('mst_jenis_tugas_akhir')->insert([
                'kode_jenis_tugas_akhir' => $operation['code'],
                'deskripsi' => $operation['deskripsi'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function extractTypeAndTitle($title, array $knownCodes)
    {
        $original = $title;
        $cleanTitle = trim($title);
        $code = null;

        while (preg_match('/^\(\s*([A-Za-z]{2})\s*(?:-|_|\s)\s*([A-Za-z0-9]{2,})\s*\)\s*/u', $cleanTitle, $matches)) {
            $candidate = strtoupper($matches[1] . '-' . $matches[2]);
            $candidate = $candidate === 'NS-PK' ? 'NS-KP' : $candidate;
            if (!in_array($candidate, $knownCodes, true)) {
                return ['code' => null, 'title' => $original, 'unknown_prefix' => $matches[0]];
            }
            if ($code !== null && $code !== $candidate) {
                return ['code' => null, 'title' => $original, 'unknown_prefix' => $matches[0]];
            }

            $code = $candidate;
            $cleanTitle = trim(substr($cleanTitle, strlen($matches[0])));
        }

        if (strpos($cleanTitle, '(') === 0) {
            return ['code' => null, 'title' => $original, 'unknown_prefix' => $cleanTitle];
        }

        if ($cleanTitle === '') {
            return ['code' => null, 'title' => $original, 'unknown_prefix' => $original];
        }

        return ['code' => $code, 'title' => $code === null ? $original : $cleanTitle, 'unknown_prefix' => null];
    }
}
