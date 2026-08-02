<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class StandardizeJenisTugasAkhir extends Command
{
    protected $signature = 'thesis:standardize-jenis-tugas-akhir {--apply : Apply the academic type standardization}';

    protected $description = 'Standardize final-project type descriptions and historical aliases';

    public function handle()
    {
        $types = DB::table('mst_jenis_tugas_akhir')
            ->pluck('jenis_tugas_akhir_id', 'kode_jenis_tugas_akhir');
        if (!$types->has('NS-AR') || !$types->has('NS-KT')) {
            throw new RuntimeException('NS-AR and NS-KT master types are required.');
        }

        $ntKtId = $types->get('NT-KT');
        $affected = [];
        foreach (['trt_bimbingan', 'trt_topik', 'trt_usulan_judul'] as $table) {
            $affected[$table] = $ntKtId && Schema::hasColumn($table, 'jenis_tugas_akhir_id')
                ? DB::table($table)->where('jenis_tugas_akhir_id', $ntKtId)->count()
                : 0;
        }

        $this->line('NS-AR description: Non Skripsi Artikel');
        foreach ($affected as $table => $count) {
            $this->line($table . ' NT-KT rows to merge into NS-KT: ' . $count);
        }
        $this->line('NT-KT master: ' . ($ntKtId ? 'remove after merge' : 'already absent'));

        if (!$this->option('apply')) {
            $this->warn('Dry run only. Re-run with --apply after a verified database backup.');

            return 0;
        }

        DB::transaction(function () use ($types, $ntKtId, $affected) {
            DB::table('mst_jenis_tugas_akhir')
                ->where('jenis_tugas_akhir_id', $types->get('NS-AR'))
                ->update([
                    'deskripsi' => 'Non Skripsi Artikel',
                    'updated_at' => now(),
                ]);

            if (!$ntKtId) {
                return;
            }

            foreach ($affected as $table => $count) {
                if ($count > 0) {
                    DB::table($table)
                        ->where('jenis_tugas_akhir_id', $ntKtId)
                        ->update(['jenis_tugas_akhir_id' => $types->get('NS-KT')]);
                }
            }

            DB::table('mst_jenis_tugas_akhir')
                ->where('jenis_tugas_akhir_id', $ntKtId)
                ->delete();
        });

        $this->info('Type standardization complete.');

        return 0;
    }
}
