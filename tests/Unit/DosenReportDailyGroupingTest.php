<?php

namespace Tests\Unit;

use App\Http\Controllers\KeuanganFakultas;
use PHPUnit\Framework\TestCase;

class DosenReportDailyGroupingTest extends TestCase
{
    public function testLecturerReportGroupsAssignmentsIntoOneReportPerExamDate()
    {
        $controller = new KeuanganFakultas;
        $method = new \ReflectionMethod($controller, 'groupDosenReportByDate');
        $method->setAccessible(true);

        $reports = $method->invoke($controller, collect([
            $this->assignment('2026-09-17', '13020220001', 'Mahasiswa Satu', 'Ketua Sidang', 'Ujian Meja', 1, 150000),
            $this->assignment('2026-09-17', '13020220002', 'Mahasiswa Dua', 'Penguji I', 'Ujian Meja', 0, 100000),
            $this->assignment('2026-09-16', '13020220001', 'Mahasiswa Satu', 'Pembimbing Utama', '0', 0, 90000),
        ]));

        $this->assertCount(2, $reports);

        $latest = $reports->first();
        $this->assertSame('2026-09-17', $latest->date);
        $this->assertSame(2, $latest->student_count);
        $this->assertSame(2, $latest->assignment_count);
        $this->assertSame(1, $latest->available_count);
        $this->assertSame(1, $latest->unavailable_count);
        $this->assertSame(0, $latest->unset_count);
        $this->assertSame(250000.0, $latest->total_amount);

        $older = $reports->last();
        $this->assertSame('2026-09-16', $older->date);
        $this->assertSame(1, $older->student_count);
        $this->assertSame(1, $older->unset_count);
        $this->assertSame(0.0, $older->total_amount);
    }

    public function testLecturerReportViewKeepsAssignmentsInsideDailyDetail()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/KeuanganFakultas.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/detail_dosen.blade.php');

        $this->assertStringContainsString('groupDosenReportByDate', $controller);
        $this->assertStringContainsString("compact('reportHarian', 'nidn')", $controller);
        $this->assertStringContainsString('Setiap baris mewakili satu tanggal ujian', $view);
        $this->assertStringContainsString('@foreach ($reportHarian as $report)', $view);
        $this->assertStringContainsString('@foreach ($report->items as $honorarium)', $view);
        $this->assertStringContainsString('Rincian Honorarium', $view);
    }

    private function assignment($date, $nim, $name, $role, $type, $status, $amount)
    {
        return (object) [
            'date' => $date,
            'C_NPM' => $nim,
            'nama_mahasiswa' => $name,
            'role' => $role,
            'tipe_ujian' => $type,
            'status' => $status,
            'amount' => $amount,
        ];
    }
}
