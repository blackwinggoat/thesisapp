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

    public function testViewsContainAggregateReportPdfAndPrivateVerificationPage()
    {
        $web = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/report_jenis_tugas_akhir.blade.php');
        $pdf = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/report_jenis_tugas_akhir_pdf.blade.php');
        $verification = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/verifikasi_report_jenis_tugas_akhir.blade.php');

        $this->assertStringContainsString('Tahun Ajaran', $web);
        $this->assertStringContainsString('Angkatan', $web);
        $this->assertStringNotContainsString('Perbandingan Seluruh', $web);
        $this->assertStringContainsString("\$report['cross_title']", $web);
        $this->assertStringContainsString("\$report['cross_distribution']", $web);
        $this->assertStringContainsString("\$report['summary']['context_count']", $web);
        $this->assertStringContainsString("\$report['summary']['context_label']", $web);
        $this->assertStringContainsString('jenis-ta-trend-angkatan', $web);
        $this->assertStringContainsString('jenis-ta-trend-tahun-ajaran', $web);
        $this->assertStringContainsString('grid-template-columns: repeat(2, minmax(0, 1fr))', $web);
        $this->assertStringContainsString("@json(\$report['trend_charts'])", $web);
        $this->assertSame(2, substr_count($web, 'renderJenisTugasAkhirTrend('));
        $this->assertLessThan(
            strpos($web, '<div class="jenis-ta-summary">'),
            strpos($web, '<div class="jenis-ta-trends">')
        );
        $this->assertStringNotContainsString('Total lulusan seluruh periode', $web);
        $this->assertStringNotContainsString("\$report['summary']['total_all_periods']", $web);
        $this->assertStringNotContainsString('Daftar Mahasiswa Lulus', $web);
        $this->assertStringNotContainsString("\$row['nim']", $web);
        $this->assertStringNotContainsString("\$row['nama']", $web);
        $this->assertStringContainsString('Generate PDF', $web);
        $this->assertStringContainsString("publicImageDataUri('images/branding/umi-pdf.jpg')", $pdf);
        $this->assertStringContainsString("publicImageDataUri('images/branding/fikom-pdf.jpg')", $pdf);
        $this->assertStringContainsString('qrCodeDataUri($verificationUrl', $pdf);
        $this->assertStringContainsString('Ketua Program Studi', $pdf);
        $this->assertStringNotContainsString('Daftar Mahasiswa Lulus', $pdf);
        $this->assertStringNotContainsString("\$row['nim']", $pdf);
        $this->assertStringNotContainsString("\$row['nama']", $pdf);
        $this->assertStringContainsString('B. {{ $report[\'cross_title\'] }}', $pdf);
        $this->assertStringNotContainsString('Perbandingan Seluruh', $pdf);
        $this->assertStringNotContainsString("\$report['comparison']", $pdf);
        $this->assertStringContainsString("\$report['cross_distribution']", $pdf);
        $this->assertStringContainsString("\$report['summary']['context_count']", $pdf);
        $this->assertStringContainsString("\$report['summary']['context_label']", $pdf);
        $this->assertStringNotContainsString('Total lulusan seluruh periode', $pdf);
        $this->assertStringNotContainsString("\$report['summary']['total_all_periods']", $pdf);
        $this->assertStringNotContainsString('Sidik laporan:', $pdf);
        $this->assertStringNotContainsString('class="footer"', $pdf);
        $this->assertSame(1, substr_count($pdf, '<table class="signature-wrap">'));
        $this->assertStringContainsString('<div class="signature-heading">', $pdf);
        $this->assertStringContainsString('<div class="signature-qr-box">', $pdf);
        $this->assertStringContainsString('<div class="signature-identity">', $pdf);
        $this->assertSame(1, substr_count($pdf, 'qrCodeDataUri($verificationUrl'));
        $this->assertGreaterThan(strpos($pdf, '<div class="signature-heading">'), strpos($pdf, '<div class="signature-qr-box">'));
        $this->assertGreaterThan(strpos($pdf, '<div class="signature-qr-box">'), strpos($pdf, '<div class="signature-identity">'));
        $this->assertGreaterThan(strpos($pdf, 'B. {{ $report[\'cross_title\'] }}'), strpos($pdf, '<table class="signature-wrap">'));
        $this->assertGreaterThan(strpos($pdf, '<div class="quality-note">'), strpos($pdf, '<table class="signature-wrap">'));
        $this->assertStringContainsString('tidak memublikasikan identitas mahasiswa', $verification);
        $this->assertStringNotContainsString("\$payload['nim']", $verification);
        $this->assertStringNotContainsString("\$payload['nama']", $verification);
    }
}
