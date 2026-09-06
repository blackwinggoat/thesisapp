<?php

namespace Tests\Unit;

use App\Http\Controllers\HomeController;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class HomeJenisTugasAkhirTrendChartTest extends TestCase
{
    public function testHomeLoadsScopedJenisTugasAkhirTrendData()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/HomeController.php');

        $this->assertStringContainsString('ProdiJenisTugasAkhirReportService', $controller);
        $this->assertStringContainsString("\$nimPrefix = '130'", $controller);
        $this->assertStringContainsString("\$nimPrefix = '131'", $controller);
        $this->assertStringContainsString('->buildTrendCharts($nimPrefix)', $controller);
        $this->assertStringContainsString("in_array(\$userLevel, [2, 3, 5], true)", $controller);
        $this->assertStringContainsString("->buildTrendChartsForPrograms(['130', '131'])", $controller);
        $this->assertStringContainsString('summarizeGraduatesByAcademicYear', $controller);
    }

    public function testHomeShowsThesisTypeChartsBeforeGuidanceChartsForAllLeadershipRoles()
    {
        $view = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/layouts/content.blade.php'
        );
        $charts = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/layouts/partials/home-prodi-dashboard-charts.blade.php'
        );

        $this->assertStringContainsString(
            "@include('tugasakhir.layouts.partials.home-prodi-dashboard-charts')",
            $view
        );
        $this->assertStringContainsString(
            'in_array((int) Auth::user()->level, [2, 3, 5], true)',
            $view
        );
        $this->assertStringContainsString(
            '@json($jenisTugasAkhirTrendCharts)',
            $view
        );
        $this->assertSame(2, substr_count($view, "renderHomeJenisTugasAkhirLine("));
        $this->assertRegExp(
            '/<div class="row">\s*<div class="col-sm-6">.*home-prodi-jenis-ta-angkatan.*<div class="col-sm-6">.*home-prodi-jenis-ta-tahun-ajaran/s',
            $charts
        );
        $this->assertLessThan(
            strpos($charts, 'home-prodi-status-bimbingan'),
            strpos($charts, 'home-prodi-jenis-ta-angkatan')
        );
        $this->assertLessThan(
            strpos($charts, 'home-prodi-lulusan-periode'),
            strpos($charts, 'home-prodi-status-bimbingan')
        );
        $this->assertStringContainsString('Semua Program Studi', $controller = file_get_contents(
            __DIR__ . '/../../app/Http/Controllers/HomeController.php'
        ));
        $this->assertStringNotContainsString('getScopeTaLulusanPeriodeChartByAuthUser', $view);
    }

    public function testHomeTotalsGraduatesFromVerifiedAcademicYearTrendRows()
    {
        $controller = (new \ReflectionClass(HomeController::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod(HomeController::class, 'summarizeGraduatesByAcademicYear');
        $method->setAccessible(true);

        $result = $method->invoke($controller, [
            'series' => [
                ['key' => 'jenis_0'],
                ['key' => 'jenis_1'],
            ],
            'by_academic_year' => [
                ['period' => '2024/2025', 'jenis_0' => 2, 'jenis_1' => 3],
                ['period' => '2025/2026', 'jenis_0' => 4, 'jenis_1' => 1],
            ],
        ]);

        $this->assertSame([
            ['y' => '2024/2025', 'total' => 5],
            ['y' => '2025/2026', 'total' => 5],
        ], $result);
    }
}
