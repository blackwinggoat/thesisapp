<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ProdiDosenPembimbingPerformanceTest extends TestCase
{
    public function testDosenPembimbingPageBatchesBimbinganAndExamCountsBeforeRendering()
    {
        $prodiController = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $akademikProdiController = file_get_contents(__DIR__ . '/../../app/Http/Controllers/AkademikProdi.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/dosen_pembimbing.blade.php');

        $this->assertStringContainsString('getRingkasanBimbinganPerDosen()', $prodiController);
        $this->assertStringContainsString('getRingkasanBimbinganPerDosen($semesterRange)', $prodiController);
        $this->assertStringContainsString('getRingkasanMengujiPerDosen()', $prodiController);
        $this->assertStringContainsString("orderBy('t_mst_dosen.NAMA_DOSEN')", $prodiController);
        $this->assertStringContainsString('Awal (September-Februari)', $prodiController);
        $this->assertStringContainsString('Akhir (Maret-Agustus)', $prodiController);
        $this->assertStringContainsString('getRingkasanMengujiPerDosen()', $akademikProdiController);
        $this->assertStringContainsString("whereBetween('tb.created_at'", $akademikProdiController);
        $this->assertStringContainsString("data_get(\$value, 'ringkasan_bimbingan.pp', 0)", $view);
        $this->assertStringContainsString("data_get(\$value, 'ringkasan_menguji.aktif', 0)", $view);
        $this->assertStringNotContainsString('App\\TrtPenguji::where(', $view);
        $this->assertStringNotContainsString("DB::table('trt_penguji')", $view);
    }
}
