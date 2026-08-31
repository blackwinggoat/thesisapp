<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AkademikFakultasRekapUjianSelesaiTest extends TestCase
{
    public function testRoutesAreProtectedByAkademikFakultasMiddleware()
    {
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $groupStart = strpos($routes, "Route::group(['middleware' => 'akademik_fakultas']");
        $groupEnd = strpos($routes, "Route::group(['middleware' => 'dosen']", $groupStart);
        $routeStart = strpos($routes, "Route::get('/fakultas/rekap-ujian-selesai'", $groupStart);

        $this->assertNotFalse($groupStart);
        $this->assertNotFalse($groupEnd);
        $this->assertNotFalse($routeStart);
        $this->assertGreaterThan($groupStart, $routeStart);
        $this->assertLessThan($groupEnd, $routeStart);
        $this->assertStringContainsString("Route::post('/fakultas/rekap-ujian-selesai/nomor-surat', 'fakultas@rekap_ujian_selesai_nomor_surat')", $routes);
    }

    public function testCompletedExamDataUsesPastFinalExamSchedulesAndStoresOneLetterNumberPerDate()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/fakultas.php');
        $migration = file_get_contents(__DIR__ . '/../../database/migrations/2026_09_01_010000_create_trt_rekap_ujian_selesai_table.php');

        $this->assertStringContainsString("->where('pendaftaran.tipe_ujian', 2)", $controller);
        $this->assertStringContainsString("->whereDate('jadwal.tgl_ujian', '<', Carbon::today()->toDateString())", $controller);
        $this->assertStringContainsString("->where('rekap_surat.tipe_ujian', '=', 2)", $controller);
        $this->assertStringContainsString("->where('tanggal_ujian', \$date)", $controller);
        $this->assertStringContainsString("\$table->unique(['tanggal_ujian', 'tipe_ujian']", $migration);
    }

    public function testViewProvidesLetterInputInfoPopupAndDeferredPdfAction()
    {
        $sidebar = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/sidebarakademikfakultas.blade.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/fakultas/rekap_ujian_selesai.blade.php');

        $this->assertStringContainsString("route('fakultas.rekap_ujian_selesai')", $sidebar);
        $this->assertStringContainsString('Rekap Ujian TA Selesai', $sidebar);
        $this->assertStringContainsString('Jumlah Mahasiswa', $view);
        $this->assertStringContainsString('Type Mahasiswa', $view);
        $this->assertStringContainsString('Nomor Surat', $view);
        $this->assertStringContainsString('Nilai Ujian TA', $view);
        $this->assertStringContainsString('IPK', $view);
        $this->assertStringContainsString('fa-info-circle', $view);
        $this->assertStringContainsString('fa-file-pdf-o', $view);
        $this->assertStringContainsString('Template PDF belum tersedia', $view);
    }
}
