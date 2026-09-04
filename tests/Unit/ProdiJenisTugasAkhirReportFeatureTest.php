<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ProdiJenisTugasAkhirReportFeatureTest extends TestCase
{
    public function testRoutesControllerAndMenusExposeTheReport()
    {
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $prodiSidebar = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/sidebarkaprodi.blade.php');
        $adminSidebar = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/sidebar.blade.php');

        $this->assertStringContainsString("/prodi/report/jenis-tugas-akhir'", $routes);
        $this->assertStringContainsString("/prodi/report/jenis-tugas-akhir/pdf'", $routes);
        $this->assertStringContainsString("/verifikasi/laporan-jenis-tugas-akhir/{token}'", $routes);
        $this->assertStringContainsString('public function report_jenis_tugas_akhir(', $controller);
        $this->assertStringContainsString('public function report_jenis_tugas_akhir_pdf(', $controller);
        $this->assertStringContainsString('getJenisTugasAkhirReportScope(', $controller);
        $this->assertStringContainsString('Persebaran Jenis TA', $prodiSidebar);
        $this->assertStringContainsString('Persebaran Jenis TA', $adminSidebar);
    }

    public function testViewsContainDetailedReportPdfAndPrivateVerificationPage()
    {
        $web = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/report_jenis_tugas_akhir.blade.php');
        $pdf = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/report_jenis_tugas_akhir_pdf.blade.php');
        $verification = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/verifikasi_report_jenis_tugas_akhir.blade.php');

        $this->assertStringContainsString('Tahun Ajaran', $web);
        $this->assertStringContainsString('Angkatan', $web);
        $this->assertStringContainsString('Perbandingan Seluruh', $web);
        $this->assertStringContainsString('Daftar Mahasiswa Lulus', $web);
        $this->assertStringContainsString('Generate PDF', $web);
        $this->assertStringContainsString("publicImageDataUri('images/branding/umi-pdf.jpg')", $pdf);
        $this->assertStringContainsString("publicImageDataUri('images/branding/fikom-pdf.jpg')", $pdf);
        $this->assertStringContainsString('qrCodeDataUri($verificationUrl', $pdf);
        $this->assertStringContainsString('Ketua Program Studi', $pdf);
        $this->assertStringContainsString('tidak memublikasikan identitas mahasiswa', $verification);
        $this->assertStringNotContainsString("\$payload['nim']", $verification);
        $this->assertStringNotContainsString("\$payload['nama']", $verification);
    }
}
