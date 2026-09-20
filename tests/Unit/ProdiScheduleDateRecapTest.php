<?php

namespace Tests\Unit;

use App\Http\Controllers\Prodi;
use PHPUnit\Framework\TestCase;

class ProdiScheduleDateRecapTest extends TestCase
{
    public function testSchedulesAreCombinedByDateWithoutDoubleCountingRegistrations()
    {
        $controller = new TestableProdiScheduleDateRecapController();
        $jadwal = collect([
            (object) ['pendaftaran_id' => 10, 'tgl_ujian' => '2026-07-31', 'nama_periode' => 'Proposal SI', 'tipe_ujian' => 0, 'status_prodi' => 2],
            (object) ['pendaftaran_id' => 11, 'tgl_ujian' => '2026-07-31', 'nama_periode' => 'Ujian Meja TI', 'tipe_ujian' => 2, 'status_prodi' => 1],
            (object) ['pendaftaran_id' => 12, 'tgl_ujian' => '2026-08-01', 'nama_periode' => 'Ujian Meja SI', 'tipe_ujian' => 2, 'status_prodi' => 2],
        ]);
        $registrations = collect([
            (object) ['pendaftaran_id' => 10, 'C_NPM' => '13120240001'],
            (object) ['pendaftaran_id' => 11, 'C_NPM' => '13020240001'],
            (object) ['pendaftaran_id' => 11, 'C_NPM' => '13020240002'],
            (object) ['pendaftaran_id' => 11, 'C_NPM' => '13020240002'],
            (object) ['pendaftaran_id' => 12, 'C_NPM' => '13120240002'],
        ]);

        $summary = $controller->buildDateSummary($jadwal, $registrations);

        $this->assertCount(2, $summary);
        $this->assertSame('2026-08-01', $summary[0]->tgl_ujian);
        $this->assertSame('2026-07-31', $summary[1]->tgl_ujian);
        $this->assertSame(3, $summary[1]->jumlah_peserta);
        $this->assertSame(['Proposal', 'Ujian Meja'], $summary[1]->tipe_ujian_list->pluck('label')->all());
        $this->assertSame(['Teknik Informatika', 'Sistem Informasi'], $summary[1]->prodi_list->all());
    }

    public function testCombinedDetailKeepsSetExaminerAndRemovesDeleteAction()
    {
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $scheduleView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/jadwal.blade.php');
        $detailView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/daftar_peserta_tanggal.blade.php');

        $this->assertStringContainsString("Route::get('/prodi/daftar_peserta_tanggal/{tanggal}', 'Prodi@daftar_peserta_tanggal')", $routes);
        $this->assertStringContainsString('Jadwal Ujian per Tanggal', $scheduleView);
        $this->assertStringContainsString("url('prodi/daftar_peserta_tanggal/'.\$rekap->tgl_ujian)", $scheduleView);
        $this->assertStringContainsString("url('prodi/jadwal/tanggal/'.\$rekap->tgl_ujian.'/ruangan')", $scheduleView);
        $this->assertStringContainsString('datatable-jadwal-tanggal', $scheduleView);
        $this->assertStringContainsString("!\$.fn.DataTable.isDataTable('#datatable-jadwal-tanggal')", $scheduleView);
        $this->assertStringContainsString("\$rekap->nama_periode_list->count()}} periode", $scheduleView);
        $this->assertStringContainsString('<th>Set Penguji</th>', $detailView);
        $this->assertStringContainsString("\$d->pendaftaran_id.'/'.\$d->C_NPM.'/'.\$d->tipe_ujian", $detailView);
        $this->assertStringNotContainsString('hapusJadwalUjianPerMahasiswa', $detailView);
        $this->assertStringNotContainsString('<th>Aksi</th>', $detailView);
    }

    public function testRoomScheduleBoardProvidesDragResizeAndMobileEditor()
    {
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $boardView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/jadwal_ruangan_tanggal.blade.php');

        $this->assertStringContainsString("Route::get('/prodi/jadwal/tanggal/{tanggal}/ruangan'", $routes);
        $this->assertStringContainsString("Route::post('/prodi/jadwal/tanggal/{tanggal}/ruangan'", $routes);
        $this->assertStringContainsString('schedule-room-track', $boardView);
        $this->assertStringContainsString('$roomPalette = [', $boardView);
        $this->assertStringContainsString('--room-header:', $boardView);
        $this->assertStringContainsString("card.setAttribute('draggable', 'true')", $boardView);
        $this->assertStringContainsString('schedule-duration-handle', $boardView);
        $this->assertStringContainsString("handle.addEventListener('pointerdown'", $boardView);
        $this->assertStringContainsString("handle.addEventListener('pointermove'", $boardView);
        $this->assertStringContainsString('scheduleEditorModal', $boardView);
        $this->assertStringContainsString('layoutRoom(track)', $boardView);
    }
}

class TestableProdiScheduleDateRecapController extends Prodi
{
    public function buildDateSummary($jadwal, $registrations)
    {
        return $this->buildJadwalTanggalSummary($jadwal, $registrations);
    }
}
