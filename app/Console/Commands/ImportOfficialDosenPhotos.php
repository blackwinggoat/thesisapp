<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ImportOfficialDosenPhotos extends Command
{
    protected $signature = 'thesis:import-official-dosen-photos
        {--dry-run : Validate the official sources without changing files or data}
        {--refresh : Replace a previously imported lecturer photo}';

    protected $description = 'Import verified lecturer photos from the official FIKOM website';

    public function handle()
    {
        $sources = config('official_dosen_photo_sources', []);
        $dryRun = (bool) $this->option('dry-run');
        $refresh = (bool) $this->option('refresh');
        $imported = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($sources as $kodeDosen => $sourceUrl) {
            try {
                $kodeDosen = $this->validateKodeDosen($kodeDosen);
                $sourceUrl = $this->validateSourceUrl($sourceUrl);
                $currentPhoto = $this->currentPhoto($kodeDosen);

                if ($currentPhoto === null) {
                    throw new RuntimeException('Lecturer record was not found.');
                }

                if (!$refresh && $currentPhoto !== '' && Storage::disk('public')->exists($currentPhoto)) {
                    if (!$dryRun) {
                        $this->syncPhotoReference($kodeDosen, $currentPhoto);
                    }
                    $skipped++;
                    $this->line($kodeDosen . ': existing photo retained.');
                    continue;
                }

                $contents = $this->download($sourceUrl);
                $this->validateImage($contents);
                $path = 'dosen/' . $kodeDosen . '.' . $this->extension($sourceUrl);

                if (!$dryRun) {
                    Storage::disk('public')->put($path, $contents);
                    $this->syncPhotoReference($kodeDosen, $path);
                }

                $imported++;
                $this->info($kodeDosen . ': ' . ($dryRun ? 'ready to import.' : 'imported.'));
            } catch (RuntimeException $exception) {
                $failed++;
                $this->error($kodeDosen . ': ' . $exception->getMessage());
            }
        }

        $this->line('Sources: ' . count($sources));
        $this->line('Imported: ' . $imported);
        $this->line('Retained: ' . $skipped);
        $this->line('Failed: ' . $failed);

        if ($dryRun) {
            $this->warn('Dry run only. No files or database rows were changed.');
        }

        return $failed === 0 ? 0 : 1;
    }

    private function validateKodeDosen($kodeDosen)
    {
        $kodeDosen = trim((string) $kodeDosen);
        if (!preg_match('/\A[0-9]{6,20}\z/', $kodeDosen)) {
            throw new RuntimeException('Invalid lecturer code in the source manifest.');
        }

        return $kodeDosen;
    }

    private function validateSourceUrl($sourceUrl)
    {
        $sourceUrl = trim((string) $sourceUrl);
        $parts = parse_url($sourceUrl);
        $host = isset($parts['host']) ? strtolower($parts['host']) : '';

        if (!isset($parts['scheme'], $parts['path'])
            || strtolower($parts['scheme']) !== 'https'
            || $host !== 'fikom.umi.ac.id'
            || strpos($parts['path'], '/wp-content/uploads/') !== 0) {
            throw new RuntimeException('Source must be an HTTPS image from the official FIKOM website.');
        }

        $this->extension($sourceUrl);

        return $sourceUrl;
    }

    private function extension($sourceUrl)
    {
        $path = parse_url($sourceUrl, PHP_URL_PATH);
        $extension = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));
        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }

        if (!in_array($extension, ['jpg', 'png', 'webp'], true)) {
            throw new RuntimeException('Unsupported official image type.');
        }

        return $extension;
    }

    private function currentPhoto($kodeDosen)
    {
        foreach ($this->photoTables() as $table) {
            $row = DB::table($table)
                ->where('C_KODE_DOSEN', $kodeDosen)
                ->first(['D_FOTO_DOSEN']);
            if ($row) {
                return trim((string) $row->D_FOTO_DOSEN);
            }
        }

        return null;
    }

    private function syncPhotoReference($kodeDosen, $path)
    {
        DB::transaction(function () use ($kodeDosen, $path) {
            foreach ($this->photoTables() as $table) {
                $payload = ['D_FOTO_DOSEN' => $path];
                if (Schema::hasColumn($table, 'updated_at')) {
                    $payload['updated_at'] = Carbon::now();
                }

                DB::table($table)
                    ->where('C_KODE_DOSEN', $kodeDosen)
                    ->update($payload);
            }
        });
    }

    private function photoTables()
    {
        $tables = [];
        foreach (['t_mst_dosen', 'mig_t_mst_dosen'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'D_FOTO_DOSEN')) {
                $tables[] = $table;
            }
        }

        if (empty($tables)) {
            throw new RuntimeException('Lecturer photo columns are not available.');
        }

        return $tables;
    }

    private function download($sourceUrl)
    {
        if (!function_exists('curl_init')) {
            throw new RuntimeException('The cURL PHP extension is required.');
        }

        $curl = curl_init($sourceUrl);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_USERAGENT => 'ThesisApps Official Photo Importer/1.0',
        ]);
        $contents = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($contents === false || $status !== 200) {
            throw new RuntimeException('Official image download failed' . ($error !== '' ? ': ' . $error : '.'));
        }

        return $contents;
    }

    private function validateImage($contents)
    {
        if (!is_string($contents) || $contents === '' || strlen($contents) > 4 * 1024 * 1024) {
            throw new RuntimeException('Downloaded image is empty or exceeds 4 MB.');
        }

        if (!function_exists('getimagesizefromstring') || @getimagesizefromstring($contents) === false) {
            throw new RuntimeException('Downloaded file is not a valid image.');
        }
    }
}
