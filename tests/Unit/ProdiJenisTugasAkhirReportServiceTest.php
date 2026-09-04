<?php

namespace Tests\Unit;

use App\Services\ProdiJenisTugasAkhirReportService;
use PHPUnit\Framework\TestCase;

class ProdiJenisTugasAkhirReportServiceTest extends TestCase
{
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
        $this->assertSame(1, $report['summary']['fallback_date_count']);
        $this->assertSame(1, $report['summary']['default_type_count']);
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
        $this->assertCount(2, $report['distribution']);
        $this->assertEquals(100.0, array_sum(array_column($report['distribution'], 'percentage')));
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
