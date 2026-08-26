<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class LaporanMahasiswaFeatureStructureTest extends TestCase
{
    public function testMigrationCreatesCaseAndDiscussionTables()
    {
        $migration = file_get_contents(__DIR__ . '/../../database/migrations/2026_08_02_100000_create_trt_laporan_mahasiswa_tables.php');

        $this->assertStringContainsString("Schema::create('trt_laporan_mahasiswa'", $migration);
        $this->assertStringContainsString("Schema::create('trt_laporan_mahasiswa_pesan'", $migration);
        $this->assertStringContainsString("'tindakan_terakhir'", $migration);
        $this->assertStringContainsString("'pengirim_peran'", $migration);
    }

    public function testDosenAndProdiControllersKeepReportAccessScoped()
    {
        $dosen = file_get_contents(__DIR__ . '/../../app/Http/Controllers/dosen.php');
        $prodi = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');

        $this->assertStringContainsString("where('trt_laporan_mahasiswa.C_KODE_DOSEN', auth()->user()->name)", $dosen);
        $this->assertStringContainsString("whereIn('status', ['baru', 'ditinjau'])", $dosen);
        $this->assertStringContainsString('protected function getProdiScope', $prodi);
        $this->assertStringContainsString("'kode_prodi' => '55201'", $prodi);
        $this->assertStringContainsString("'kode_prodi' => '57201'", $prodi);
        $this->assertStringContainsString("where('trt_laporan_mahasiswa.C_KODE_PRODI', \$kodeProdi)", $prodi);
    }

    public function testDetailPembimbingProvidesCoordinationAndReportAction()
    {
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/dosen/detail_pembimbing.blade.php');
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');

        $this->assertStringContainsString('Koordinasi Mahasiswa', $view);
        $this->assertStringContainsString('Lapor Prodi', $view);
        $this->assertStringContainsString('modalLaporProdi', $view);
        $this->assertStringContainsString("images/icons/telegram.svg", $view);
        $this->assertFileExists(__DIR__ . '/../../public/images/icons/telegram.svg');
        $this->assertStringContainsString("Route::post('/dsn/laporan_mahasiswa'", $routes);
        $this->assertStringContainsString("Route::post('/prodi/laporan_mahasiswa/{id}/tindakan'", $routes);
    }
}
