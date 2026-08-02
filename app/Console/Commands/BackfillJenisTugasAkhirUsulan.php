<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class BackfillJenisTugasAkhirUsulan extends Command
{
    protected $signature = 'thesis:backfill-jenis-tugas-akhir-usulan {--apply : Apply the planned proposal title and type updates}';

    protected $description = 'Extract final-project type codes from proposed titles and remove their title prefixes';

    private $targets = [
        ['table' => 'trt_topik', 'id' => 'topik_id', 'title' => 'topik'],
        ['table' => 'trt_usulan_judul', 'id' => 'usulan_judul_id', 'title' => 'judul'],
    ];

    public function handle()
    {
        foreach ($this->targets as $target) {
            if (!Schema::hasColumn($target['table'], 'jenis_tugas_akhir_id')) {
                throw new RuntimeException('Column jenis_tugas_akhir_id is missing from ' . $target['table'] . '. Run the database migration first.');
            }
        }

        $knownCodes = DB::table('mst_jenis_tugas_akhir')->pluck('kode_jenis_tugas_akhir')->all();
        $currentTypeCodes = DB::table('mst_jenis_tugas_akhir')
            ->pluck('kode_jenis_tugas_akhir', 'jenis_tugas_akhir_id');
        $plans = [];
        $problems = [];
        $statistics = [];

        foreach ($this->targets as $target) {
            $tablePlans = [];
            foreach (DB::table($target['table'])->orderBy($target['id'])->get([$target['id'], $target['title'], 'jenis_tugas_akhir_id']) as $row) {
                $parsed = $this->extractTypeAndTitle((string) $row->{$target['title']}, $knownCodes);
                if ($parsed['problem'] !== null) {
                    $problems[] = $target['table'] . ' ID ' . $row->{$target['id']} . ': ' . $parsed['problem'];
                    continue;
                }

                $currentCode = $row->jenis_tugas_akhir_id === null
                    ? null
                    : $currentTypeCodes->get($row->jenis_tugas_akhir_id);
                $currentCode = $this->canonicalCode($currentCode);
                if ($parsed['code'] !== null && $currentCode !== null && $parsed['code'] !== $currentCode) {
                    $problems[] = $target['table'] . ' ID ' . $row->{$target['id']} . ': title ' . $parsed['code'] . ', stored type ' . $currentCode;
                    continue;
                }

                $code = $parsed['code'] ?: ($currentCode ?: 'TA-SM');
                $statistics[$target['table']][$code] = ($statistics[$target['table']][$code] ?? 0) + 1;
                $tablePlans[] = [
                    'id' => $row->{$target['id']},
                    'title_column' => $target['title'],
                    'original_title' => (string) $row->{$target['title']},
                    'title' => $parsed['title'],
                    'code' => $code,
                    'current_type_id' => $row->jenis_tugas_akhir_id,
                ];
            }
            $plans[$target['table']] = $tablePlans;
        }

        if (!empty($problems)) {
            $this->error('Backfill blocked: proposed title data needs review.');
            foreach (array_slice($problems, 0, 20) as $problem) {
                $this->line($problem);
            }

            return 1;
        }

        foreach ($plans as $table => $tablePlans) {
            ksort($statistics[$table]);
            $this->line($table . ' rows: ' . count($tablePlans));
            foreach ($statistics[$table] as $code => $count) {
                $this->line($table . ' ' . $code . ': ' . $count);
            }
            $this->line($table . ' title prefixes to remove: ' . count(array_filter($tablePlans, function ($plan) {
                return $plan['title'] !== $plan['original_title'];
            })));
            $this->line($table . ' rows without a stored type: ' . count(array_filter($tablePlans, function ($plan) {
                return $plan['current_type_id'] === null;
            })));
        }

        if (!$this->option('apply')) {
            $this->warn('Dry run only. Re-run with --apply after a verified database backup.');

            return 0;
        }

        $updated = 0;
        DB::transaction(function () use ($plans, &$updated) {
            $masterIds = DB::table('mst_jenis_tugas_akhir')->pluck('jenis_tugas_akhir_id', 'kode_jenis_tugas_akhir');
            foreach ($plans as $table => $tablePlans) {
                $idColumn = $table === 'trt_topik' ? 'topik_id' : 'usulan_judul_id';
                foreach ($tablePlans as $plan) {
                    if (!$masterIds->has($plan['code'])) {
                        throw new RuntimeException('Master type not found for ' . $plan['code']);
                    }

                    $jenisTugasAkhirId = $masterIds->get($plan['code']);
                    if ($plan['title'] === $plan['original_title']
                        && (int) $plan['current_type_id'] === (int) $jenisTugasAkhirId) {
                        continue;
                    }

                    DB::table($table)->where($idColumn, $plan['id'])->update([
                        $plan['title_column'] => $plan['title'],
                        'jenis_tugas_akhir_id' => $jenisTugasAkhirId,
                    ]);
                    $updated++;
                }
            }
        });

        $this->info('Backfill complete: ' . $updated . ' rows updated.');

        return 0;
    }

    private function extractTypeAndTitle($title, array $knownCodes)
    {
        $original = $title;
        $cleanTitle = trim($title);
        $code = null;

        while (preg_match('/^\(\s*([A-Za-z]{2})\s*(?:-|_|\s|\/)\s*([A-Za-z0-9]{2,})\s*\)\s*/u', $cleanTitle, $matches)) {
            $candidate = $this->canonicalCode(strtoupper($matches[1] . '-' . $matches[2]));
            if (!in_array($candidate, $knownCodes, true)) {
                return ['code' => null, 'title' => $original, 'problem' => $matches[0]];
            }
            if ($code !== null && $code !== $candidate) {
                return ['code' => null, 'title' => $original, 'problem' => $matches[0]];
            }

            $code = $candidate;
            $cleanTitle = trim(substr($cleanTitle, strlen($matches[0])));
        }

        if (strpos($cleanTitle, '(') === 0 || $cleanTitle === '') {
            return ['code' => null, 'title' => $original, 'problem' => $cleanTitle ?: $original];
        }

        return ['code' => $code, 'title' => $code === null ? $original : $cleanTitle, 'problem' => null];
    }

    private function canonicalCode($code)
    {
        return [
            'NS-PK' => 'NS-KP',
            'SN-KT' => 'NS-KT',
        ][$code] ?? $code;
    }
}
