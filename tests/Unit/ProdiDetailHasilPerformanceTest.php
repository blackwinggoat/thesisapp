<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ProdiDetailHasilPerformanceTest extends TestCase
{
    public function testDetailHasilPagesBatchLecturerAndAssessmentLookups()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $partial = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/partials/hasil_ujian_penilai_cells.blade.php');
        $views = [
            'detail_hasilujian_proposal.blade.php',
            'detail_hasilujian_ta.blade.php',
        ];

        $this->assertStringContainsString('preparePenilaiBatchViewData', $controller);
        $this->assertStringContainsString("DB::table('t_mst_dosen')", $controller);
        $this->assertStringContainsString("trt_hasil::whereIn('reg_id', \$regIds)", $controller);
        $this->assertStringContainsString('$dosenByKode->get(', $partial);
        $this->assertStringContainsString('$penilaianLengkap->has(', $partial);

        foreach ($views as $filename) {
            $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/' . $filename);

            $this->assertStringContainsString("@include('tugasakhir.prodi.partials.hasil_ujian_penilai_cells')", $view);
            $this->assertStringNotContainsString('getNamaDosenByKode', $view);
            $this->assertStringNotContainsString('getStatusPenilaianPerDosen', $view);
            $this->assertStringNotContainsString('getStatusBimbinganByNim', $view);
        }
    }
}
