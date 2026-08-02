<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ProdiSchedulePerformanceTest extends TestCase
{
    public function testSchedulePageBatchesPeriodCountsAndKeepsDatabaseQueriesOutOfTheView()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/jadwal.blade.php');

        $this->assertStringContainsString('jumlah_tipe_ujian', $controller);
        $this->assertStringContainsString("->groupBy('nama_periode')", $controller);
        $this->assertStringContainsString("compact('pendaftaran', 'mstpendaftaran', 'jadwalujian')", $controller);
        $this->assertStringContainsString('$value->jumlah_tipe_ujian', $view);
        $this->assertStringNotContainsString('mst_pendaftaran::where("nama_periode"', $view);
        $this->assertStringNotContainsString('TrtJadwalUjian::select("pendaftaran_id")', $view);
    }
}
