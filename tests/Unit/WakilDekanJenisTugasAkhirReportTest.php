<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class WakilDekanJenisTugasAkhirReportTest extends TestCase
{
    public function testWakilDekanRoutesAndSidebarExposeTheReport()
    {
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/WakilDekan.php');
        $sidebar = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/sidebarwakildekan.blade.php');

        $this->assertStringContainsString("/wakildekan/report/jenis-tugas-akhir'", $routes);
        $this->assertStringContainsString("/wakildekan/report/jenis-tugas-akhir/pdf'", $routes);
        $this->assertStringContainsString("name('wakildekan.report_jenis_tugas_akhir')", $routes);
        $this->assertStringContainsString('public function report_jenis_tugas_akhir(', $controller);
        $this->assertStringContainsString('public function report_jenis_tugas_akhir_pdf(', $controller);
        $this->assertStringContainsString("'130' => [", $controller);
        $this->assertStringContainsString("'131' => [", $controller);
        $this->assertStringContainsString("isset(\$programStudies[\$programCode])", $controller);
        $this->assertStringContainsString('Report', $sidebar);
        $this->assertStringContainsString('Persebaran Jenis TA', $sidebar);
        $this->assertStringContainsString("route('wakildekan.report_jenis_tugas_akhir')", $sidebar);
    }

    public function testViewKeepsTiAndSiInSeparateReportSections()
    {
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/wakildekan/report_jenis_tugas_akhir.blade.php');

        $this->assertStringContainsString('@foreach ($reports as $programCode => $entry)', $view);
        $this->assertStringContainsString('id="program-{{ $programCode }}"', $view);
        $this->assertStringContainsString('program-{{ $programCode }}', $view);
        $this->assertStringContainsString('wakildekan-jenis-ta-trend-{{ $programCode }}-angkatan', $view);
        $this->assertStringContainsString('wakildekan-jenis-ta-trend-{{ $programCode }}-tahun-ajaran', $view);
        $this->assertStringContainsString('Generate PDF {{ $scope[\'program_studi\'] }}', $view);
        $this->assertStringContainsString("'program_studi' => \$programCode", $view);
        $this->assertStringContainsString('Filter pada satu program studi tidak mengubah data program studi lainnya.', $view);
        $this->assertStringNotContainsString('Total gabungan', $view);
    }

    public function testWakilDekanMenuExposesGuidanceDistributionReportAndExcel()
    {
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $sidebar = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/sidebarwakildekan.blade.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/report_laporan.blade.php');

        $this->assertStringContainsString("/wakildekan/report/distribusi-bimbingan'", $routes);
        $this->assertStringContainsString("/wakildekan/report/distribusi-bimbingan/excel'", $routes);
        $this->assertStringContainsString("name('wakildekan.report_distribusi_bimbingan')", $routes);
        $this->assertStringContainsString("name('wakildekan.report_distribusi_bimbingan_excel')", $routes);
        $this->assertStringContainsString("route('wakildekan.report_distribusi_bimbingan')", $sidebar);
        $this->assertStringContainsString('Distribusi Bimbingan', $sidebar);
        $this->assertStringContainsString("optional(\$request->route())->getName()", $controller);
        $this->assertStringContainsString("\$reportActionUrl", $view);
        $this->assertStringContainsString("\$reportExcelUrl", $view);
        $this->assertStringContainsString('Teknik Informatika dan Sistem Informasi secara terpisah', $view);
    }
}
