<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class DosenRequestPembimbingPerformanceTest extends TestCase
{
    public function testRequestPembimbingBatchesReferenceDataBeforeRendering()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/dosen.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/dosen/request_pembimbing.blade.php');

        $this->assertStringContainsString('RequestPembimbing::whereIn', $controller);
        $this->assertStringContainsString("->groupBy('topik')", $controller);
        $this->assertStringContainsString("mst_bidangilmu::whereIn('bidangilmu_id'", $controller);
        $this->assertStringContainsString("DB::table('t_mst_dosen')", $controller);
        $this->assertStringContainsString('$requestPembimbingByTopik->get(', $view);
        $this->assertStringContainsString('$bidangIlmuById->get(', $view);
        $this->assertStringContainsString('$dosenByKode->get(', $view);
        $this->assertStringNotContainsString('RequestPembimbing::where(', $view);
        $this->assertStringNotContainsString('mst_bidangilmu::find(', $view);
        $this->assertStringNotContainsString('getNamaDosenByKode', $view);
    }
}
