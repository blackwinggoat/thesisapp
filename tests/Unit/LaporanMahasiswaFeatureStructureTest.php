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
        $this->assertStringContainsString("\$this->applyProdiNimScope(\$query, 't_mst_mahasiswa.C_NPM')", $prodi);
        $this->assertStringContainsString("leftJoin('trt_prodi', 'trt_prodi.kode_prodi', '=', 't_mst_mahasiswa.C_KODE_PRODI')", $prodi);
    }

    public function testProdiScopeRecognizesDescriptiveProgramAccountNames()
    {
        $controller = new \App\Http\Controllers\Prodi();
        $scopeMethod = new \ReflectionMethod($controller, 'getProdiScope');
        $scopeMethod->setAccessible(true);

        $teknikInformatika = $scopeMethod->invoke($controller, (object) ['name' => 'Teknik Informatika']);
        $sistemInformasi = $scopeMethod->invoke($controller, (object) ['name' => 'Sistem Informasi']);

        $this->assertSame('130', $teknikInformatika['nim_prefix']);
        $this->assertSame('131', $sistemInformasi['nim_prefix']);
    }

    public function testProdiScopeRecognizesRegisteredAccountsAndFailsClosedForUnknownAccount()
    {
        $controller = new \App\Http\Controllers\Prodi();
        $scopeMethod = new \ReflectionMethod($controller, 'getProdiScope');
        $scopeMethod->setAccessible(true);

        $sistemInformasi = $scopeMethod->invoke($controller, (object) ['name' => 'prodinyalilis', 'level' => 5]);
        $unknownProdi = $scopeMethod->invoke($controller, (object) ['name' => 'uat-prodi-slider', 'level' => 5]);
        $admin = $scopeMethod->invoke($controller, (object) ['name' => 'admin', 'level' => 1]);

        $this->assertSame('131', $sistemInformasi['nim_prefix']);
        $this->assertTrue($sistemInformasi['is_mapped']);
        $this->assertSame('__unmapped_prodi__', $unknownProdi['nim_prefix']);
        $this->assertFalse($unknownProdi['is_mapped']);
        $this->assertNull($admin['nim_prefix']);
        $this->assertTrue($admin['is_mapped']);
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
