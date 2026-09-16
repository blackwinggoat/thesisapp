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
        $this->assertSame(200000.0, $dosenUtama->total_honorarium_dasar_belum_diterima);
        $this->assertSame(-50000.0, $dosenUtama->total_penyesuaian_belum_diterima);
        $this->assertSame(200000.0, $dosenPendamping->total_honorarium_dasar_belum_diterima);
        $this->assertSame(50000.0, $dosenPendamping->total_penyesuaian_belum_diterima);
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
        $this->assertStringContainsString('honor dasar + penyesuaian kehadiran pembimbing', $view);
        $this->assertStringContainsString('penugasan menunggu tipe', $view);
        $this->assertStringContainsString('Rincian Harian', $view);
        $this->assertStringContainsString('paging: false', $view);
        $this->assertStringContainsString('lengthChange: false', $view);
    }

    public function testEveryLecturerReportAssignmentUsesBaseAdjustmentAndFinalAmount()
    {
        $controller = new KeuanganFakultas;
        $method = new \ReflectionMethod($controller, 'buildHonorariumReportAssignments');
        $method->setAccessible(true);

        $assignments = $method->invoke(
            $controller,
            collect([$this->honorarium('Ujian Meja', 0, 1)]),
            collect(['2026-09-17' => 50000]),
            collect(['13020220001' => 'Mahasiswa Uji'])
        );

        $utama = $assignments->firstWhere('kode_dosen', 'D1');
        $pendamping = $assignments->firstWhere('kode_dosen', 'D2');

        $this->assertSame(200000.0, $utama->base_amount);
        $this->assertSame(-50000.0, $utama->adjustment_amount);
        $this->assertSame(150000.0, $utama->amount);
        $this->assertStringContainsString('Pembimbing Utama', $utama->adjustment_note);
        $this->assertSame(200000.0, $pendamping->base_amount);
        $this->assertSame(50000.0, $pendamping->adjustment_amount);
        $this->assertSame(250000.0, $pendamping->amount);
        $this->assertSame('Mahasiswa Uji', $utama->nama_mahasiswa);
    }

    public function testDailyHistoryAndDateRangeViewsExposeAdjustmentBreakdown()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/KeuanganFakultas.php');
        $daily = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/detail_dosen.blade.php');
        $history = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/history_detail_dosen.blade.php');
        $range = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/filter_detail_dosen.blade.php');

        $this->assertStringContainsString('getDosenReportAssignments($nidn)', $controller);
        $this->assertStringContainsString('buildHonorariumReportAssignments', $controller);
        $this->assertStringContainsString('honorariumSemuaDenganJadwalQuery', $controller);
        foreach ([$daily, $history, $range] as $view) {
            $this->assertStringContainsString('Honor Dasar', $view);
            $this->assertStringContainsString('Penyesuaian', $view);
            $this->assertStringContainsString('adjustment_amount', $view);
            $this->assertStringContainsString('adjustment_note', $view);
        }
    }

    private function honorarium($type, $puPresent, $ppPresent)
    {
        return (object) [
            'C_NPM' => '13020220001',
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
