<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ProdiPengujiDistributionReportMenuTest extends TestCase
{
    public function testReportMenuAndRoutesExposeTheExaminerDistributionReport()
    {
        $prodiSidebar = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/layouts/sidebarkaprodi.blade.php'
        );
        $adminSidebar = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/layouts/sidebar.blade.php'
        );
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $view = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/prodi/report_distribusi_penguji.blade.php'
        );
        $excel = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/prodi/report_distribusi_penguji_excel.blade.php'
        );

        $this->assertStringContainsString('Distribusi Penguji', $prodiSidebar);
        $this->assertStringContainsString('Distribusi Penguji', $adminSidebar);
        $this->assertStringContainsString("Route::get('/prodi/report/distribusi-penguji'", $routes);
        $this->assertStringContainsString('prodi.report_distribusi_penguji_excel', $routes);
        $this->assertStringContainsString('public function report_distribusi_penguji(', $controller);
        $this->assertStringContainsString('getPengujiDistributionReport', $controller);
        $this->assertStringContainsString("'KS' =>", $controller);
        $this->assertStringContainsString("'P3' =>", $controller);
        $this->assertStringContainsString('Ringkasan Tim Ujian', $view);
        $this->assertStringContainsString('Rinci Peran', $view);
        $this->assertStringContainsString('Ketua Sidang', $view);
        $this->assertStringContainsString('Penguji III', $view);
        $this->assertStringContainsString('Data Distribusi Tim Ujian per Peran', $excel);
    }
}
