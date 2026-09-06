<?php

namespace Tests\Unit;

use App\Services\ProdiJenisTugasAkhirReportService;
use PHPUnit\Framework\TestCase;

class ProdiJenisTugasAkhirReportServiceTest extends TestCase
{
    public function testServiceProvidesFacultyWideTrendEntryPoint()
    {
        $this->assertTrue(method_exists(
            ProdiJenisTugasAkhirReportService::class,
            'buildTrendChartsForPrograms'
        ));
    }

    public function testItCalculatesDistributionByAcademicYear()
    {
        $report = (new ProdiJenisTugasAkhirReportService())->aggregate(
            $this->rows(),
            'Teknik Informatika',
            'tahun_ajaran',
            '2025/2026'
        );

        $this->assertSame('2025/2026', $report['selected_period']);
        $this->assertSame(4, $report['summary']['total']);
        $this->assertSame('TA-SM', $report['summary']['dominant_code']);
        $this->assertEquals(75.0, $report['summary']['dominant_percentage']);
        $this->assertSame(3, $report['distribution'][0]['count']);
        $this->assertEquals(75.0, $report['distribution'][0]['percentage']);
        $this->assertSame('13020220004', $report['rows'][0]['nim']);
        $this->assertSame(0, $report['summary']['fallback_date_count']);
        $this->assertSame(1, $report['summary']['default_type_count']);
        $this->assertSame(3, $report['summary']['date_source_counts']['jadwal_ujian']);
        $this->assertSame(1, $report['summary']['date_source_counts']['master_mahasiswa']);
        $this->assertSame(1, $report['summary']['context_count']);
        $this->assertSame('Angkatan mengikuti ujian', $report['summary']['context_label']);
        $this->assertArrayNotHasKey('total_all_periods', $report['summary']);
        $this->assertSame('Angkatan', $report['cross_dimension_label']);
        $this->assertCount(1, $report['cross_distribution']);
        $this->assertSame('2022', $report['cross_distribution'][0]['period']);
        $this->assertSame(4, $report['cross_distribution'][0]['total']);
        $this->assertSame(3, $report['cross_distribution'][0]['counts']['TA-SM']['count']);
        $this->assertEquals(75.0, $report['cross_distribution'][0]['counts']['TA-SM']['percentage']);
        $this->assertArrayNotHasKey('comparison', $report);
    }

    public function testItCalculatesDistributionByCohort()
    {
        $report = (new ProdiJenisTugasAkhirReportService())->aggregate(
            $this->rows(),
            'Teknik Informatika',
            'angkatan',
            '2022'
        );

        $this->assertSame('Angkatan', $report['mode_label']);
        $this->assertSame('2022', $report['selected_period']);
        $this->assertSame(4, $report['summary']['total']);
        $this->assertSame(4, $report['summary']['context_count']);
        $this->assertSame('Total mahasiswa Angkatan 2022', $report['summary']['context_label']);
        $this->assertCount(2, $report['distribution']);
        $this->assertEquals(100.0, array_sum(array_column($report['distribution'], 'percentage')));
        $this->assertSame('Tahun Ajaran', $report['cross_dimension_label']);
        $this->assertCount(1, $report['cross_distribution']);
        $this->assertSame('2025/2026', $report['cross_distribution'][0]['period']);
        $this->assertSame(4, $report['cross_distribution'][0]['total']);
    }

    public function testVerificationTokenContainsOnlyReportMetadataAndRejectsTampering()
    {
        $service = new ProdiJenisTugasAkhirReportService();
        $report = $service->aggregate($this->rows(), 'Teknik Informatika', 'tahun_ajaran', '2025/2026');
        $token = $service->buildVerificationToken($report, 'testing-signing-key');
        $payload = $service->decodeVerificationToken($token, 'testing-signing-key');

        $this->assertSame('Teknik Informatika', $payload['program_studi']);
        $this->assertSame(4, $payload['total']);
        $this->assertArrayNotHasKey('nim', $payload);
        $this->assertArrayNotHasKey('nama', $payload);
        $this->assertNull($service->decodeVerificationToken($token . '0', 'testing-signing-key'));
        $this->assertNull($service->decodeVerificationToken('not-a-token', 'testing-signing-key'));
    }

    public function testItOnlyFlagsRowsWithoutAnyTraceableGraduationDate()
    {
        $rows = $this->rows();
        $rows[] = $this->row(
            '13020220005',
            'TA-SM',
            'Tugas Akhir Skripsi Mandiri',
            '2022',
            '2025/2026',
            '',
            'tidak_diketahui'
        );

        $report = (new ProdiJenisTugasAkhirReportService())->aggregate(
            $rows,
            'Teknik Informatika',
            'tahun_ajaran',
            '2025/2026'
        );

        $this->assertSame(1, $report['summary']['fallback_date_count']);
        $this->assertSame(1, $report['summary']['date_source_counts']['tidak_diketahui']);
    }

    public function testItBuildsLineChartSeriesByCohortAndAcademicYear()
    {
        $charts = (new ProdiJenisTugasAkhirReportService())->aggregateTrendCharts($this->rows());
        $seriesByCode = collect($charts['series'])->keyBy('code');
        $taSmKey = $seriesByCode->get('TA-SM')['key'];
        $nsAiKey = $seriesByCode->get('NS-AI')['key'];

        $this->assertSame(['2021', '2022'], array_column($charts['by_cohort'], 'period'));
        $this->assertSame(3, $charts['by_cohort'][1][$taSmKey]);
        $this->assertSame(1, $charts['by_cohort'][1][$nsAiKey]);
        $this->assertSame(['2024/2025', '2025/2026'], array_column($charts['by_academic_year'], 'period'));
        $this->assertSame(4, $charts['by_academic_year'][1]['total']);
    }

    public function testAggregateIncludesTheSameFullTrendChartsForBothReportModes()
    {
        $service = new ProdiJenisTugasAkhirReportService();
        $academicYearReport = $service->aggregate(
            $this->rows(),
            'Teknik Informatika',
            'tahun_ajaran',
            '2025/2026'
        );
        $cohortReport = $service->aggregate(
            $this->rows(),
            'Teknik Informatika',
            'angkatan',
            '2022'
        );

        $this->assertArrayHasKey('trend_charts', $academicYearReport);
        $this->assertSame($academicYearReport['trend_charts'], $cohortReport['trend_charts']);
        $this->assertSame(
            ['2021', '2022'],
            array_column($academicYearReport['trend_charts']['by_cohort'], 'period')
        );
        $this->assertSame(
            ['2024/2025', '2025/2026'],
            array_column($academicYearReport['trend_charts']['by_academic_year'], 'period')
        );
    }

    private function rows()
    {
        return [
            $this->row('13020220001', 'TA-SM', 'Tugas Akhir Skripsi Mandiri', '2022', '2025/2026', '2026-01-10'),
            $this->row('13020220002', 'TA-SM', 'Tugas Akhir Skripsi Mandiri', '2022', '2025/2026', '2026-02-10'),
            $this->row('13020220003', 'NS-AI', 'Non Skripsi - Artikel Ilmiah', '2022', '2025/2026', '2026-03-10'),
            $this->row('13020220004', 'TA-SM', 'Tugas Akhir Skripsi Mandiri', '2022', '2025/2026', '2026-04-10', 'master_mahasiswa', true),
            $this->row('13020210001', 'TA-SK', 'Tugas Akhir Skripsi Kolaborasi', '2021', '2024/2025', '2025-02-12'),
            $this->row('13020210002', 'NS-KT', 'Non Skripsi - Karya Teknologi', '2021', '2024/2025', '2025-03-12'),
        ];
    }

    private function row($nim, $code, $description, $cohort, $academicYear, $date, $source = 'jadwal_ujian', $default = false)
    {
        return [
            'nim' => $nim,
            'nama' => 'Mahasiswa ' . substr($nim, -2),
            'judul' => 'Judul tugas akhir ' . substr($nim, -2),
            'jenis_code' => $code,
            'jenis_description' => $description,
            'jenis_default' => $default,
            'angkatan' => $cohort,
            'tahun_ajaran' => $academicYear,
            'tanggal_lulus' => $date,
            'tanggal_lulus_label' => $date,
            'tanggal_source' => $source,
        ];
    }
}
