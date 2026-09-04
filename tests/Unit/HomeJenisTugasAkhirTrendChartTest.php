<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HomeJenisTugasAkhirTrendChartTest extends TestCase
{
    public function testHomeLoadsScopedJenisTugasAkhirTrendData()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/HomeController.php');

        $this->assertStringContainsString('ProdiJenisTugasAkhirReportService', $controller);
        $this->assertStringContainsString("\$nimPrefix = '130'", $controller);
        $this->assertStringContainsString("\$nimPrefix = '131'", $controller);
        $this->assertStringContainsString('->buildTrendCharts($nimPrefix)', $controller);
    }

    public function testHomeShowsTwoTrendChartsInOneResponsiveRow()
    {
        $view = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/layouts/content.blade.php'
        );

        $this->assertStringContainsString('home-prodi-jenis-ta-angkatan', $view);
        $this->assertStringContainsString('home-prodi-jenis-ta-tahun-ajaran', $view);
        $this->assertStringContainsString(
            '@json($jenisTugasAkhirTrendCharts)',
            $view
        );
        $this->assertSame(2, substr_count($view, "renderHomeJenisTugasAkhirLine("));
        $this->assertRegExp(
            '/<div class="row">\s*<div class="col-sm-6">.*home-prodi-jenis-ta-angkatan.*<div class="col-sm-6">.*home-prodi-jenis-ta-tahun-ajaran/s',
            $view
        );
    }
}
