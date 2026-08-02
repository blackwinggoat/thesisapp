<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class DosenRekapPerformanceTest extends TestCase
{
    public function testRecapPagesBatchLecturerAndAssessmentLookups()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/dosen.php');
        $partial = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/dosen/partials/rekap_penilai_cells.blade.php');
        $views = [
            'detail_rekap_nilai_proposal.blade.php',
            'detail_rekap_nilai_proposal_history.blade.php',
            'detail_rekap_nilai_ujian_ta.blade.php',
            'detail_rekap_nilai_ujian_ta_history.blade.php',
        ];

        $this->assertStringContainsString('prepareRekapNilaiViewData', $controller);
        $this->assertStringContainsString("DB::table('t_mst_dosen')", $controller);
        $this->assertStringContainsString("trt_hasil::whereIn('reg_id', \$regIds)", $controller);
        $this->assertStringContainsString('$dosenByKode->get(', $partial);
        $this->assertStringContainsString('$penilaianLengkap->has(', $partial);

        foreach ($views as $filename) {
            $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/dosen/' . $filename);

            $this->assertStringContainsString("@include('tugasakhir.dosen.partials.rekap_penilai_cells')", $view);
            $this->assertStringNotContainsString('getNamaDosenByKode', $view);
            $this->assertStringNotContainsString('getStatusPenilaianPerDosen', $view);
        }
    }
}
