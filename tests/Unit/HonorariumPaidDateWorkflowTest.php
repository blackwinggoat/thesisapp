<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HonorariumPaidDateWorkflowTest extends TestCase
{
    public function testSelectedDatesUseAtomicValidatedPaidWorkflow()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/KeuanganFakultas.php');
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $summary = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/honorarium.blade.php');

        $this->assertStringContainsString('honorarium_tandai_terbayar', $controller);
        $this->assertStringContainsString('DB::transaction(function () use ($tanggalTerpilih)', $controller);
        $this->assertStringContainsString('->lockForUpdate()', $controller);
        $this->assertStringContainsString('honorariumNeedsTypeAssignment($honorarium)', $controller);
        $this->assertStringContainsString('!in_array($status, [1, 3], true)', $controller);
        $this->assertStringContainsString('honorariumStatusPayload($honorarium, 3)', $controller);
        $this->assertStringContainsString("Route::post('/tandai-terbayar'", $routes);
        $this->assertStringContainsString('Tandai Terbayar', $summary);
        $this->assertStringContainsString("route('honorarium_tandai_terbayar')", $summary);
        $this->assertStringContainsString('Tandai seluruhnya terbayar?', $summary);
        $this->assertStringContainsString('if (result.value)', $summary);
    }

    public function testHistoryIsGroupedByExamDateAndReadOnly()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/KeuanganFakultas.php');
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $history = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/history_honorarium.blade.php');
        $detail = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/history_honorarium_detail.blade.php');

        $this->assertStringContainsString('honorariumLunasDenganJadwalQuery', $controller);
        $this->assertStringContainsString('honorariumTanggalEfektifSql', $controller);
        $this->assertStringContainsString('COUNT(DISTINCT honorarium.C_NPM) as total_mahasiswa', $controller);
        $this->assertStringContainsString('honorarium_history_detail_tanggal', $controller);
        $this->assertStringContainsString("Route::get('/history/tanggal/{date}'", $routes);
        $this->assertStringContainsString('Riwayat Honorarium per Tanggal Ujian', $history);
        $this->assertStringContainsString('Total Honor Terbayar', $history);
        $this->assertStringContainsString('Lihat Detail', $history);
        $this->assertStringContainsString('Riwayat Honorarium Tanggal {{ $date }}', $detail);
        $this->assertStringNotContainsString('<form', $detail);
    }
}
