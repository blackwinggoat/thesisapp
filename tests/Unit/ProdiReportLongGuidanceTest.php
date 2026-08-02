<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ProdiReportLongGuidanceTest extends TestCase
{
    public function testReportListsOnlyActiveUngradatedStudentsBeyondTwoYearsAfterFirstSk()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/report.blade.php');

        $this->assertStringContainsString("MIN(DATE(created_at)) AS tanggal_sk", $controller);
        $this->assertStringContainsString("m.C_KODE_STATUS_AKTIF_MHS = 'A'", $controller);
        $this->assertStringContainsString('tb.status_bimbingan IN (0, 1, 2)', $controller);
        $this->assertStringContainsString('sk.tanggal_sk < ?', $controller);
        $this->assertStringContainsString("COALESCE(jta.kode_jenis_tugas_akhir, 'TA-SM')", $controller);
        $this->assertStringContainsString('formatDurationYearsMonths', $controller);
        $this->assertStringContainsString('Mahasiswa Aktif Lebih dari Dua Tahun sejak SK Pembimbing', $view);
        $this->assertStringContainsString('$mahasiswaMelewatiDuaTahunSk', $view);
        $this->assertStringContainsString('Pembimbing Pendamping', $view);
        $this->assertStringContainsString('Status Saat Ini', $view);
    }
}
