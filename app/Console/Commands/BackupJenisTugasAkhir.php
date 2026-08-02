<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BackupJenisTugasAkhir extends Command
{
    protected $signature = 'thesis:backup-jenis-tugas-akhir {path : Absolute path for the JSON backup file}';

    protected $description = 'Back up final titles and final-project type master data before normalization';

    public function handle()
    {
        $path = (string) $this->argument('path');
        if (strpos($path, '/') !== 0) {
            throw new RuntimeException('Backup path must be absolute.');
        }

        $directory = dirname($path);
        if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
            throw new RuntimeException('Could not create backup directory.');
        }

        $payload = [
            'created_at_utc' => gmdate('c'),
            'trt_bimbingan' => DB::table('trt_bimbingan')
                ->orderBy('bimbingan_id')
                ->get(['bimbingan_id', 'C_NPM', 'judul', 'last_update', 'updated_at']),
            'mst_jenis_tugas_akhir' => DB::table('mst_jenis_tugas_akhir')
                ->orderBy('jenis_tugas_akhir_id')
                ->get(['jenis_tugas_akhir_id', 'kode_jenis_tugas_akhir', 'deskripsi', 'created_at', 'updated_at']),
            'trt_topik' => DB::table('trt_topik')
                ->orderBy('topik_id')
                ->get(['topik_id', 'C_NPM', 'topik', 'status', 'updated_at']),
            'trt_usulan_judul' => DB::table('trt_usulan_judul')
                ->orderBy('usulan_judul_id')
                ->get(['usulan_judul_id', 'C_NPM', 'KODE_DOSEN', 'judul', 'created_at', 'updated_at']),
        ];
        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false || file_put_contents($path, $json, LOCK_EX) === false) {
            throw new RuntimeException('Could not write database backup.');
        }
        chmod($path, 0600);

        $this->info('Backup created: ' . $path);
        $this->line('Bimbingan rows: ' . count($payload['trt_bimbingan']));
        $this->line('Master rows: ' . count($payload['mst_jenis_tugas_akhir']));
        $this->line('Student proposal rows: ' . count($payload['trt_topik']));
        $this->line('Lecturer proposal rows: ' . count($payload['trt_usulan_judul']));
        $this->line('SHA-256: ' . hash_file('sha256', $path));

        return 0;
    }
}
