<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class KeuanganHonorariumDateGroupingTest extends TestCase
{
    public function testHonorariumManagementGroupsUnpaidRecordsByExamDate()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/KeuanganFakultas.php');
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $summary = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/honorarium.blade.php');
        $detail = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/honorarium_detail.blade.php');

        $this->assertStringContainsString('honorariumBelumLunasQuery()', $controller);
        $this->assertStringContainsString("->groupBy('date')", $controller);
        $this->assertStringContainsString('honorarium_detail_tanggal', $controller);
        $this->assertStringContainsString("Route::get('/tanggal/{date}'", $routes);
        $this->assertStringContainsString('Manajemen Honorarium per Tanggal Ujian', $summary);
        $this->assertStringContainsString('Kelola Mahasiswa', $summary);
        $this->assertStringContainsString('Honorarium Tanggal {{ $date }}', $detail);
        $this->assertStringContainsString('@foreach ($data as $honorarium)', $detail);
    }
}
