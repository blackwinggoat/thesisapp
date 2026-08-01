<?php

namespace App\Console\Commands;

use App\Helper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class MigrateOfficialAssets extends Command
{
    protected $signature = 'official-assets:migrate {--delete-public : Delete each legacy source only after verification}';

    protected $description = 'Copy official signatures and stamps from public/gambar to private persistent storage';

    public function handle()
    {
        $fileNames = $this->officialFileNames();
        $copied = 0;
        $missing = 0;

        foreach ($fileNames as $fileName) {
            $source = public_path('gambar/' . $fileName);

            if (!is_file($source)) {
                if (Storage::disk('official')->exists($fileName)) {
                    $this->line('already private: ' . $fileName);
                    continue;
                }

                $this->warn('missing: ' . $fileName);
                $missing++;
                continue;
            }

            $contents = file_get_contents($source);

            if ($contents === false || !Storage::disk('official')->put($fileName, $contents)) {
                throw new RuntimeException('Failed to copy ' . $fileName);
            }

            if (hash('sha256', $contents) !== hash('sha256', Storage::disk('official')->get($fileName))) {
                throw new RuntimeException('Checksum verification failed for ' . $fileName);
            }

            $copied++;
            $this->info('verified: ' . $fileName);

            if ($this->option('delete-public')) {
                unlink($source);
                $this->line('removed public source: ' . $fileName);
            }
        }

        $legacyRootSource = base_path('gambar/ttd_kaprodi_si.png');

        if (is_file($legacyRootSource)) {
            $legacyFileName = 'legacy-root-ttd_kaprodi_si.png';
            $contents = file_get_contents($legacyRootSource);

            if ($contents === false || !Storage::disk('official')->put($legacyFileName, $contents)) {
                throw new RuntimeException('Failed to copy ' . $legacyFileName);
            }

            if (hash('sha256', $contents) !== hash('sha256', Storage::disk('official')->get($legacyFileName))) {
                throw new RuntimeException('Checksum verification failed for ' . $legacyFileName);
            }

            $copied++;
            $this->info('verified legacy root copy: ' . $legacyFileName);

            if ($this->option('delete-public')) {
                unlink($legacyRootSource);
                $this->line('removed legacy root source: gambar/ttd_kaprodi_si.png');
            }
        }

        $this->info(sprintf('Complete: %d copied, %d missing.', $copied, $missing));

        return $missing === 0 ? 0 : 1;
    }

    private function officialFileNames()
    {
        $fileNames = [
            'paraf_wd.png',
            'stempelfakultas.png',
            'stempelprodi.png',
            'stempelprodi_si.png',
            'ttd_dekan.png',
            'ttd_kaprodi.png',
            'ttd_kaprodi_si.png',
        ];

        foreach (['mst_periode_jabatan', 'mst_periode_jabatan_fakultas'] as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'ttd')) {
                continue;
            }

            foreach (DB::table($table)->whereNotNull('ttd')->pluck('ttd') as $fileName) {
                if (Helper::isSafeOfficialImageName($fileName)) {
                    $fileNames[] = $fileName;
                }
            }
        }

        return array_values(array_unique($fileNames));
    }
}
