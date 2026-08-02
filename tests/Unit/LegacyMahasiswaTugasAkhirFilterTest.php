<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class LegacyMahasiswaTugasAkhirFilterTest extends TestCase
{
    public function testDosenActiveWorkflowsRequireAnActiveStudentMasterStatus()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/dosen.php');

        $this->assertStringContainsString("where('t_mst_mahasiswa.C_KODE_STATUS_AKTIF_MHS', 'A')", $controller);
        $this->assertStringContainsString("where('mhs.C_KODE_STATUS_AKTIF_MHS', 'A')", $controller);
        $this->assertStringContainsString("where('trt_bimbingan.status_bimbingan', '<>', 4)", $controller);
    }

    public function testDashboardCalculationsExcludeInactiveStudentsFromActiveCounts()
    {
        $helper = file_get_contents(__DIR__ . '/../../app/Helper.php');

        $this->assertStringContainsString("mhs.C_KODE_STATUS_AKTIF_MHS = 'A'", $helper);
        $this->assertStringContainsString("tb.status_bimbingan <> 4", $helper);
    }
}
