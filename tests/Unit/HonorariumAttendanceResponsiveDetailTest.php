<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HonorariumAttendanceResponsiveDetailTest extends TestCase
{
    public function testAttendanceResponseContainsFreshHonorariumDetail()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/KeuanganFakultas.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/honorarium_detail.blade.php');

        $this->assertStringContainsString('rincianHonorariumSetelahKehadiran', $controller);
        $this->assertStringContainsString("'detail' => \$rincianHonor", $controller);
        $this->assertStringContainsString("request->input('date')", $controller);
        $this->assertStringContainsString("->whereDate('jadwal.tgl_ujian', \$date)", $controller);
        $this->assertStringContainsString('updateRincianHonorarium', $view);
        $this->assertStringContainsString("date: '{{ \$date }}'", $view);
        $this->assertStringContainsString('response.detail', $view);
        $this->assertStringContainsString("button.data(key + '-h'", $view);
    }

    public function testResponsiveDetailUsesTheSameAttendanceCalculationAsThePage()
    {
        $controller = new \App\Http\Controllers\KeuanganFakultas;
        $method = new \ReflectionMethod($controller, 'rincianHonorariumSetelahKehadiran');
        $method->setAccessible(true);

        $honorarium = (object) [
            'KS_H' => 50000,
            'PU_H' => 100000,
            'PP_H' => 100000,
            'P1_H' => 50000,
            'P2_H' => 50000,
            'P3_H' => 0,
            'PU' => 'Pembimbing Utama',
            'PP' => 'Pembimbing Pendamping',
            'pembimbing_utama_hadir' => 0,
            'pembimbing_pendamping_hadir' => 1,
        ];

        $result = $method->invoke($controller, $honorarium, 25000);

        $this->assertSame(100000.0, $result['base_amounts']['PU']);
        $this->assertSame(-25000.0, $result['adjustments']['PU']);
        $this->assertSame(25000.0, $result['adjustments']['PP']);
        $this->assertSame(75000.0, $result['amounts']['PU']);
        $this->assertSame(125000.0, $result['amounts']['PP']);
        $this->assertSame(350000.0, $result['total_honor']);
    }
}
