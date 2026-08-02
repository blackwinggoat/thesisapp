<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AkademikProdiRekapPerformanceTest extends TestCase
{
    public function testRecapPagesBatchLecturerAndAssessmentLookups()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/AkademikProdi.php');
        $proposalView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/akademikprodi/detail_rekap_nilai_proposal.blade.php');
        $ujianTaView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/akademikprodi/detail_rekap_nilai_ujian_ta.blade.php');

        $this->assertStringContainsString('prepareRekapNilaiViewData', $controller);
        $this->assertStringContainsString("Dosen::whereIn('C_KODE_DOSEN', \$dosenIds)", $controller);
        $this->assertStringContainsString("trt_hasil::whereIn('reg_id', \$regIds)", $controller);

        foreach ([$proposalView, $ujianTaView] as $view) {
            $this->assertStringContainsString('$dosenByKode->get(', $view);
            $this->assertStringContainsString('$penilaianLengkap->has(', $view);
            $this->assertStringNotContainsString('\\App\\Dosen::where(', $view);
            $this->assertStringNotContainsString('getStatusPenilaianPerDosen', $view);
        }
    }
}
