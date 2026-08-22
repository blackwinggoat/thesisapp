<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HonorariumDosenDateAlignmentTest extends TestCase
{
    public function testLecturerHonorariumUsesScheduledExamDateAndSeparatesUnlinkedRows()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/dosen.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/dosen/honorarium.blade.php');

        $this->assertStringContainsString('schedule_date', $controller);
        $this->assertStringContainsString('ju.tgl_ujian', $controller);
        $this->assertStringContainsString('rg.status = honorarium.exam_type', $controller);
        $this->assertStringContainsString('honorarium_date', $controller);
        $this->assertStringContainsString('has_schedule', $controller);
        $this->assertStringContainsString('$orphanAssignments', $controller);
        $this->assertStringContainsString('orphanAssignments', $view);
        $this->assertStringContainsString('belum terhubung ke jadwal ujian', $view);
        $this->assertStringContainsString('tidak dimasukkan ke kelompok tanggal', $view);
    }
}
