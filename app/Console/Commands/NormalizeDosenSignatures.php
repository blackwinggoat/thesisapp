<?php

namespace App\Console\Commands;

use App\Services\DosenSignatureImageService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class NormalizeDosenSignatures extends Command
{
    protected $signature = 'thesis:normalize-dosen-signatures
        {--apply : Normalize signatures after storing one original backup for every changed row}';

    protected $description = 'Audit and standardize uploaded lecturer signatures on a transparent canvas';

    public function handle(DosenSignatureImageService $signatureImages)
    {
        if (!Schema::hasTable('mst_tanda_tangan')) {
            $this->error('Tabel tanda tangan dosen belum tersedia.');
            return 1;
        }

        $apply = (bool) $this->option('apply');
        if ($apply && !Schema::hasTable('mst_tanda_tangan_normalization_backups')) {
            $this->error('Tabel backup normalisasi belum tersedia. Jalankan migrasi terlebih dahulu.');
            return 1;
        }

        $summary = [
            'total' => 0,
            'empty' => 0,
            'ready' => 0,
            'normalized' => 0,
            'invalid' => 0,
        ];

        $rows = DB::table('mst_tanda_tangan')
            ->orderBy('id_tanda_tangan')
            ->get(['id_tanda_tangan', 'C_KODE_DOSEN', 'tanda_tangan']);

        foreach ($rows as $row) {
            $summary['total']++;
            $contents = $row->tanda_tangan;

            if (!is_string($contents) || $contents === '') {
                $summary['empty']++;
                continue;
            }

            try {
                $before = $signatureImages->inspect($contents);
                $changed = !$signatureImages->hasStandardCanvas($before);

                if (!$changed) {
                    $summary['ready']++;
                    continue;
                }

                $normalized = $signatureImages->normalize($contents);

                if ($apply) {
                    $this->storeBackupAndNormalize($row, $contents, $normalized);
                }

                $summary['normalized']++;
                $this->line(sprintf(
                    '%s: %dx%d -> %dx%d, area tanda tangan %.2f%%.',
                    trim((string) $row->C_KODE_DOSEN),
                    $before['width'],
                    $before['height'],
                    DosenSignatureImageService::CANVAS_WIDTH,
                    DosenSignatureImageService::CANVAS_HEIGHT,
                    $before['content_ratio'] * 100
                ));
            } catch (RuntimeException $exception) {
                $summary['invalid']++;
                $this->error(trim((string) $row->C_KODE_DOSEN) . ': ' . $exception->getMessage());
            }
        }

        $this->info(sprintf(
            'Total: %d | Kosong: %d | Siap: %d | %s: %d | Tidak valid: %d',
            $summary['total'],
            $summary['empty'],
            $summary['ready'],
            $apply ? 'Dinormalisasi' : 'Perlu dinormalisasi',
            $summary['normalized'],
            $summary['invalid']
        ));

        if (!$apply) {
            $this->warn('Mode audit saja. Tidak ada data tanda tangan yang diubah.');
        }

        // A malformed legacy upload requires a lecturer re-upload, but must not
        // invalidate successfully normalized rows or block a guarded deploy.
        return 0;
    }

    /**
     * @param object $row
     * @param string $contents
     * @param string $normalized
     * @return void
     */
    protected function storeBackupAndNormalize($row, $contents, $normalized)
    {
        DB::transaction(function () use ($row, $contents, $normalized) {
            $backupExists = DB::table('mst_tanda_tangan_normalization_backups')
                ->where('id_tanda_tangan', $row->id_tanda_tangan)
                ->exists();

            if (!$backupExists) {
                DB::table('mst_tanda_tangan_normalization_backups')->insert([
                    'id_tanda_tangan' => $row->id_tanda_tangan,
                    'C_KODE_DOSEN' => $row->C_KODE_DOSEN,
                    'original_tanda_tangan_base64' => base64_encode($contents),
                    'original_sha256' => hash('sha256', $contents),
                    'normalized_sha256' => hash('sha256', $normalized),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            DB::table('mst_tanda_tangan')
                ->where('id_tanda_tangan', $row->id_tanda_tangan)
                ->update([
                    'tanda_tangan' => $normalized,
                    'updated_at' => Carbon::now(),
                ]);
        });
    }
}
