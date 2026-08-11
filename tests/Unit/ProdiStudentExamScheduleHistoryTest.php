<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ProdiStudentExamScheduleHistoryTest extends TestCase
{
    public function testStudentExamScheduleIsSplitBetweenUpcomingAndHistory()
    {
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/jadwalpermhs.blade.php');

        $this->assertStringContainsString(
            "Route::get('/prodi/jadwalpermhs/{tipe_ujian}/riwayat', 'Prodi@jadwalPerMhsRiwayat')",
            $routes
        );
        $this->assertStringContainsString("return \$this->jadwalPerMhsByMode(\$tipe_ujian, 'aktif');", $controller);
        $this->assertStringContainsString("return \$this->jadwalPerMhsByMode(\$tipe_ujian, 'riwayat');", $controller);
        $this->assertStringContainsString("->whereDate('trt_jadwal_ujian.tgl_ujian', '<', \$today)", $controller);
        $this->assertStringContainsString("->whereDate('trt_jadwal_ujian.tgl_ujian', '>=', \$today)", $controller);
        $this->assertStringContainsString("->where('mst_pendaftaran.status_prodi', \$statusProdi)", $controller);
        $this->assertStringContainsString("->orWhere('mst_pendaftaran.tipe_ujian', 3)", $controller);
        $this->assertStringContainsString("url('prodi/jadwalpermhs/'.\$tipeUjian.'/riwayat')", $view);
        $this->assertStringContainsString('Lihat Riwayat', $view);
        $this->assertStringContainsString('Jadwal Berjalan', $view);
    }

    public function testStudentExamScheduleCanExportSelectedRowsToExcel()
    {
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/jadwalpermhs.blade.php');
        $excelView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/rekap_jadwalpermhs_excel.blade.php');

        $this->assertStringContainsString(
            "Route::post('/prodi/jadwalpermhs/{tipe_ujian}/rekap', 'Prodi@rekapJadwalPerMhsExcel')",
            $routes
        );
        $this->assertStringContainsString('public function rekapJadwalPerMhsExcel', $controller);
        $this->assertStringContainsString("->view('tugasakhir.prodi.rekap_jadwalpermhs_excel'", $controller);
        $this->assertStringContainsString("->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')", $controller);
        $this->assertStringContainsString("->join('trt_jadwal_ujian_per_mhs as jpm'", $controller);
        $this->assertStringContainsString("->leftJoin('mst_ruangan as ruangan'", $controller);
        $this->assertStringContainsString("->leftJoin('mst_jenis_tugas_akhir as jta'", $controller);
        $this->assertStringContainsString("->whereColumn('rg.status', 'mp.tipe_ujian')", $controller);
        $this->assertStringContainsString('name="jadwal_ujian_ids[]"', $view);
        $this->assertStringContainsString('Checklist Semua', $view);
        $this->assertStringContainsString('Rekap Jadwal', $view);
        $this->assertStringContainsString('<th>Ruangan Ujian</th>', $excelView);
        $this->assertStringContainsString('<th>JAM</th>', $excelView);
        $this->assertStringContainsString('<th>Jenis Ujian</th>', $excelView);
        $this->assertStringContainsString('class="highlight"', $excelView);
    }
}
