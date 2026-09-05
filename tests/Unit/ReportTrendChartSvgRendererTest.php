<?php

namespace Tests\Unit;

use App\Services\ReportTrendChartSvgRenderer;
use PHPUnit\Framework\TestCase;

class ReportTrendChartSvgRendererTest extends TestCase
{
    public function testRendererHighlightsTheSelectedPeriodAndSeriesPoints()
    {
        $renderer = new ReportTrendChartSvgRenderer();
        $uri = $renderer->render(
            [
                ['period' => '2021/2022', 'jenis_0' => 8, 'jenis_1' => 2],
                ['period' => '2022/2023', 'jenis_0' => 12, 'jenis_1' => 4],
            ],
            [
                ['key' => 'jenis_0', 'code' => 'TA-SM', 'color' => '#16794a'],
                ['key' => 'jenis_1', 'code' => 'NS-KT', 'color' => '#b8325a'],
            ],
            '2022/2023'
        );

        $this->assertSame(0, strpos($uri, 'data:image/svg+xml;base64,'));
        $svg = base64_decode(substr($uri, strlen('data:image/svg+xml;base64,')));

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('AKTIF: 2022/2023', $svg);
        $this->assertStringContainsString('fill="#fff7d6"', $svg);
        $this->assertStringContainsString('stroke="#d97706"', $svg);
        $this->assertSame(2, substr_count($svg, 'r="6.3"'));
    }

    public function testRendererReturnsAReadableEmptyState()
    {
        $renderer = new ReportTrendChartSvgRenderer();
        $uri = $renderer->render([], [], null);
        $svg = base64_decode(substr($uri, strlen('data:image/svg+xml;base64,')));

        $this->assertStringContainsString('Belum ada data lulusan.', $svg);
    }
}
