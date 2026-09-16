<?php

namespace Tests\Unit;

use App\Http\Controllers\KeuanganFakultas;
use PHPUnit\Framework\TestCase;

class DosenReportOverviewTest extends TestCase
{
    public function testOverviewTotalsOnlyOutstandingAssignedHonorariumWithAttendanceAdjustment()
    {
        $controller = new KeuanganFakultas;
        $method = new \ReflectionMethod($controller, 'buildDosenReportOverview');
        $method->setAccessible(true);

        $signature = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
        $result = $method->invoke(
            $controller,
            collect([
                (object) ['C_KODE_DOSEN' => 'D1', 'NAMA_DOSEN' => 'Dosen Utama'],
                (object) ['C_KODE_DOSEN' => 'D2', 'NAMA_DOSEN' => 'Dosen Pendamping'],
            ]),
            collect([
                $this->honorarium('Ujian Meja', 0, 1),
                $this->honorarium('0', 1, 1),
            ]),
            collect(['2026-09-17' => 50000]),
            collect(['D1' => $signature])
        );

        $dosenUtama = $result->firstWhere('C_KODE_DOSEN', 'D1');
        $dosenPendamping = $result->firstWhere('C_KODE_DOSEN', 'D2');

        $this->assertSame(150000.0, $dosenUtama->total_honorarium_belum_diterima);
        $this->assertSame(250000.0, $dosenPendamping->total_honorarium_belum_diterima);
        $this->assertSame(1, $dosenUtama->jumlah_penugasan_belum_ditetapkan);
        $this->assertSame(1, $dosenPendamping->jumlah_penugasan_belum_ditetapkan);
        $this->assertStringStartsWith('data:image/png;base64,', $dosenUtama->tanda_tangan_data_uri);
        $this->assertSame('', $dosenPendamping->tanda_tangan_data_uri);
    }

    public function testOverviewViewUsesCompactColumnsAndDisablesPaging()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/KeuanganFakultas.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/dosen.blade.php');

        $this->assertStringContainsString('buildDosenReportOverview', $controller);
        $this->assertStringContainsString('total_honorarium_belum_diterima', $controller);
        $this->assertStringContainsString('Tanda Tangan', $view);
        $this->assertStringContainsString('Honorarium Belum Diterima', $view);
        $this->assertStringContainsString('penugasan menunggu tipe', $view);
        $this->assertStringContainsString('Rincian Harian', $view);
        $this->assertStringContainsString('paging: false', $view);
        $this->assertStringContainsString('lengthChange: false', $view);
    }

    private function honorarium($type, $puPresent, $ppPresent)
    {
        return (object) [
            'tanggal_ujian' => '2026-09-17',
            'date' => '2026-09-17',
            'tipe_ujian' => $type,
            'KS' => '',
            'KS_H' => 0,
            'KS_Stat' => 0,
            'PU' => 'D1',
            'PU_H' => 200000,
            'PU_Stat' => 1,
            'PP' => 'D2',
            'PP_H' => 200000,
            'PP_Stat' => 1,
            'P1' => 'D1',
            'P1_H' => 100000,
            'P1_Stat' => 3,
            'P2' => '',
            'P2_H' => 0,
            'P2_Stat' => 0,
            'P3' => '',
            'P3_H' => 0,
            'P3_Stat' => 0,
            'pembimbing_utama_hadir' => $puPresent,
            'pembimbing_pendamping_hadir' => $ppPresent,
        ];
    }
}
