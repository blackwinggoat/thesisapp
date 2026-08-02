<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class FakultasRekapPerformanceTest extends TestCase
{
    public function testRecapPagesBatchLecturerAndAssessmentLookups()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/fakultas.php');
        $proposalView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/fakultas/detail_rekap_nilai_proposal.blade.php');
        $ujianTaView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/fakultas/detail_rekap_nilai_ujian_ta.blade.php');

        $this->assertStringContainsString('prepareRekapNilaiViewData', $controller);
        $this->assertStringContainsString("DB::table('t_mst_dosen')", $controller);
        $this->assertStringContainsString("trt_hasil::whereIn('reg_id', \$regIds)", $controller);

        foreach ([$proposalView, $ujianTaView] as $view) {
            $this->assertStringContainsString('$dosenByKode->get(', $view);
            $this->assertStringContainsString('$penilaianLengkap->has(', $view);
            $this->assertStringNotContainsString('getNamaDosenByKode', $view);
            $this->assertStringNotContainsString('getStatusPenilaianPerDosen', $view);
        }
    }
}
