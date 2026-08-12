<?php

namespace Tests\Unit;

use App\Helper;
use Tests\TestCase;

class PrintProgramStudiByStambukTest extends TestCase
{
    public function testPrintProgramUsesTheApprovedNimPrefixes()
    {
        $this->assertSame('Teknik Informatika', Helper::getProgramStudiByStambuk('13020220078'));
        $this->assertSame('Sistem Informasi', Helper::getProgramStudiByStambuk('13120220078'));
    }

    public function testProposalAndUjianPrintHeadersUseTheStudentProgram()
    {
        $proposal = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/mhs/cetak_beritaacara_proposal.blade.php');
        $ujian = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/cetak_berita_acara.blade.php');

        foreach ([$proposal, $ujian] as $view) {
            $this->assertStringContainsString('helper::getProgramStudiByStambuk($nim)', $view);
            $this->assertStringContainsString('{{ $headerProgramStudi }}', $view);
            $this->assertStringContainsString('{{ $headerEmailProdi }}', $view);
            $this->assertStringNotContainsString("Auth::user()->name == 'proditi'", $view);
        }
    }
}
