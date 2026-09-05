<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ProdiBimbinganDistributionReportTest extends TestCase
{
    public function testBimbinganDistributionReportUsesAcademicPeriodsAndMainSupervisor()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/report_laporan.blade.php');

        $this->assertStringContainsString('getBimbinganDistributionReport', $controller);
        $this->assertStringContainsString("getBimbinganDistributionReport(\n                    '%',", $controller);
        $this->assertStringContainsString("where('tb.pembimbing_I_id', '<>', '')", $controller);
        $this->assertStringContainsString('Helper::getSemesterAkademik($assignment->tanggal_sk)', $controller);
        $this->assertStringContainsString("name=\"tahun_ajaran\"", $view);
        $this->assertStringContainsString('Distribusi Jumlah Bimbingan Utama', $view);
        $this->assertStringContainsString("'label' => 'Teknik Informatika'", $controller);
        $this->assertStringContainsString("'label' => 'Sistem Informasi'", $controller);
        $this->assertStringContainsString('Awal', $view);
        $this->assertStringContainsString('Akhir', $view);
        $this->assertStringContainsString('total_mahasiswa_by_program', $controller);
        $this->assertStringContainsString('Total Penugasan Mahasiswa -', $view);
        $this->assertStringContainsString('$reportExcelUrl', $view);
    }
}
