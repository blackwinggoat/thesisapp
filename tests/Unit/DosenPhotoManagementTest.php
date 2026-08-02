<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class DosenPhotoManagementTest extends TestCase
{
    public function testLecturerPhotosCanBeManagedAndImportedFromOfficialSources()
    {
        $helper = file_get_contents(__DIR__ . '/../../app/Helper.php');
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/master_dosen.blade.php');
        $command = file_get_contents(__DIR__ . '/../../app/Console/Commands/ImportOfficialDosenPhotos.php');
        $sources = require __DIR__ . '/../../config/official_dosen_photo_sources.php';

        $this->assertStringContainsString('function dosenPhotoUrl', $helper);
        $this->assertStringContainsString("'foto_dosen' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048'", $controller);
        $this->assertStringContainsString("->store('dosen', 'public')", $controller);
        $this->assertStringContainsString("'D_FOTO_DOSEN'", $controller);
        $this->assertStringContainsString('name="foto_dosen"', $view);
        $this->assertStringContainsString('$value->foto_url', $view);
        $this->assertStringContainsString('fikom.umi.ac.id', $command);
        $this->assertStringContainsString('getimagesizefromstring', $command);
        $this->assertCount(33, $sources);

        foreach ($sources as $source) {
            $this->assertStringStartsWith('https://fikom.umi.ac.id/wp-content/uploads/', $source);
        }
    }
}
