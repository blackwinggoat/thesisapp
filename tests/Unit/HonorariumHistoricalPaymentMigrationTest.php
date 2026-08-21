<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HonorariumHistoricalPaymentMigrationTest extends TestCase
{
    public function testHistoricalPaymentMigrationUsesVerifiedScheduledExamDate()
    {
        $migration = file_get_contents(__DIR__ . '/../../database/migrations/2026_08_21_120000_mark_pre_20260730_honorarium_paid.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/honorarium.blade.php');

        $this->assertStringContainsString("CAST(jadwal.tgl_ujian AS CHAR) < '2026-07-30'", $migration);
        $this->assertStringContainsString('registrasi.status = honorarium.exam_type', $migration);
        $this->assertStringContainsString("THEN 3 ELSE KS_Stat END", $migration);
        $this->assertStringContainsString("THEN 3 ELSE P3_Stat END", $migration);
        $this->assertStringContainsString('paging: false', $view);
    }
}
