<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class LecturerExamSchedulePerformanceTest extends TestCase
{
    public function testSchedulePagesBatchSupportingDataInsteadOfQueryingInsideEachRow()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/dosen.php');
        $proposalView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/dosen/jadwal_proposal.blade.php');
        $mejaView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/dosen/jadwal_ujianmeja.blade.php');

        $this->assertStringContainsString('private function lecturerExamSchedule($tipeUjian)', $controller);
        $this->assertStringContainsString("->whereIn('tb.C_NPM', \$lecturerNims)", $controller);
        $this->assertStringContainsString("->whereIn('C_KODE_DOSEN', \$kodeDosen)", $controller);
        $this->assertStringContainsString("->whereIn('tb.C_NPM'", $controller);
        $this->assertStringContainsString("->join('trt_jadwal_ujian_per_mhs as jpm'", $controller);
        $this->assertStringContainsString('pembimbing_I_id_nama', $proposalView);
        $this->assertStringContainsString('memiliki_sk', $proposalView);
        $this->assertStringContainsString('pembimbing_I_id_nama', $mejaView);
        $this->assertStringContainsString('memiliki_sk', $mejaView);
        $this->assertStringNotContainsString('getNamaDosenByKode', $proposalView);
        $this->assertStringNotContainsString('getNamaDosenByKode', $mejaView);
        $this->assertStringNotContainsString('getStatusSKUjianProposalForMahasiswa', $proposalView);
        $this->assertStringNotContainsString('getStatusSKUjianMejaForMahasiswa', $mejaView);
    }
}
