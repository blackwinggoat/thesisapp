<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ProdiReportMenuTest extends TestCase
{
    public function testReportMenuSeparatesDashboardFromReportCenter()
    {
        $sidebar = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/layouts/sidebarkaprodi.blade.php'
        );
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $view = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/prodi/report_laporan.blade.php'
        );

        $this->assertStringContainsString("url('prodi/report')", $sidebar);
        $this->assertStringContainsString('Pusat Laporan', $sidebar);
        $this->assertStringContainsString("Route::get('/prodi/report/laporan', 'Prodi@report_laporan')", $routes);
        $this->assertStringContainsString("Route::get('/prodi/report/laporan/excel', 'Prodi@report_laporan_excel')", $routes);
        $this->assertStringContainsString('public function report_laporan(', $controller);
        $this->assertStringContainsString('public function report_laporan_excel(', $controller);
        $this->assertStringContainsString('Distribusi Jumlah Bimbingan Utama', $view);
        $this->assertStringContainsString('Download Excel', $view);
        $this->assertStringContainsString('Persebaran Jenis TA', $sidebar);
        $this->assertStringContainsString("route('prodi.report_jenis_tugas_akhir')", $sidebar);
        $this->assertStringContainsString('buildTrendCharts(', $controller);

        $dashboard = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/prodi/report.blade.php'
        );
        $this->assertStringContainsString('report-jenis-ta-angkatan', $dashboard);
        $this->assertStringContainsString('report-jenis-ta-tahun-ajaran', $dashboard);
        $this->assertSame(2, substr_count($dashboard, 'renderJenisTugasAkhirLine('));
        $this->assertStringContainsString('Morris.Line({', $dashboard);
    }
}
