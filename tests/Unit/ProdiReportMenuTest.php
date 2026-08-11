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
        $this->assertStringContainsString('public function report_laporan(', $controller);
        $this->assertStringContainsString('Distribusi Jumlah Bimbingan Utama', $view);
    }
}
