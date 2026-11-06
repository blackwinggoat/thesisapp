<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ProdiMahasiswaSiakadSyncFeatureTest extends TestCase
{
    public function testTheFeatureKeepsProgramScopeAndDoesNotExposeApiCredentials()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/mahasiswa.blade.php');
        $config = file_get_contents(__DIR__ . '/../../config/services.php');

        $this->assertStringContainsString('use App\\Services\\SiakadMahasiswaService;', $controller);
        $this->assertStringContainsString('public function sync_mahasiswa_siakad_nim', $controller);
        $this->assertStringContainsString('public function sync_mahasiswa_siakad_angkatan', $controller);
        $this->assertStringContainsString('NIM berada di luar program studi yang dapat Anda kelola.', $controller);
        $this->assertStringContainsString('resolveSiakadMahasiswaProgram', $controller);
        $this->assertStringContainsString("Route::post('/prodi/mahasiswa/sinkronisasi/nim', 'Prodi@sync_mahasiswa_siakad_nim')", $routes);
        $this->assertStringContainsString("Route::post('/prodi/mahasiswa/sinkronisasi/angkatan', 'Prodi@sync_mahasiswa_siakad_angkatan')", $routes);
        $this->assertStringContainsString('modalSinkronisasiMahasiswa', $view);
        $this->assertStringContainsString('Tambah dari SIAKAD', $view);
        $this->assertStringContainsString('Tambah Angkatan', $view);
        $this->assertStringContainsString("'siakad_mahasiswa'", $config);
        $this->assertStringContainsString("env('SIAKAD_MAHASISWA_API_KEY', env('SIAKAD_IPK_API_KEY'))", $config);
    }
}
