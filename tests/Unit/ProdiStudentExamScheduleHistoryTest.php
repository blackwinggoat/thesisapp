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
        $this->assertStringContainsString("->whereIn('mst_pendaftaran.status_prodi', [1, 2])", $controller);
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
        $this->assertStringContainsString('$lecturerSheets = $this->getRekapJadwalPerMhsLecturerSheets($rows);', $controller);
        $this->assertStringContainsString('private function getRekapJadwalPerMhsLecturerSheets', $controller);
        $this->assertStringContainsString('private function makeExcelWorksheetName', $controller);
        $this->assertStringContainsString("->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')", $controller);
        $this->assertStringContainsString("->join('trt_jadwal_ujian_per_mhs as jpm'", $controller);
        $this->assertStringContainsString("->leftJoin('mst_ruangan as ruangan'", $controller);
        $this->assertStringContainsString("->leftJoin('mst_jenis_tugas_akhir as jta'", $controller);
        $this->assertStringContainsString("->whereIn('mp.status_prodi', [1, 2])", $controller);
        $this->assertStringContainsString("->whereColumn('rg.status', 'mp.tipe_ujian')", $controller);
        $this->assertStringContainsString('name="jadwal_ujian_ids[]"', $view);
        $this->assertStringContainsString('Checklist Semua', $view);
        $this->assertStringContainsString('Rekap Jadwal', $view);
        $this->assertStringContainsString('Notif Dosen', $view);
        $this->assertStringContainsString("'Ruangan Ujian'", $excelView);
        $this->assertStringContainsString("'JAM'", $excelView);
        $this->assertStringContainsString("'Jenis Ujian'", $excelView);
        $this->assertStringContainsString('<Worksheet ss:Name="Rekap Jadwal">', $excelView);
        $this->assertStringContainsString('@foreach($lecturerSheets as $sheet)', $excelView);
        $this->assertStringContainsString('ss:ID="Header"', $excelView);
        $this->assertStringContainsString('ss:ID="Marker"', $excelView);
        $this->assertStringContainsString('$sheetRow[\'highlight_roles\']', $excelView);
    }

    public function testSelectedSchedulesCanBuildWhatsappLecturerNotifications()
    {
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/jadwalpermhs.blade.php');
        $notificationView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/notif_jadwalpermhs_dosen.blade.php');
        $lecturerView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/jadwal_dosen_link.blade.php');

        $this->assertStringContainsString(
            "Route::post('/prodi/jadwalpermhs/{tipe_ujian}/notif-dosen', 'Prodi@notifDosenJadwalPerMhs')",
            $routes
        );
        $this->assertStringContainsString("Route::get('/jadwal-dosen/{token}', 'Prodi@jadwalDosenLink')", $routes);
        $this->assertStringContainsString('public function notifDosenJadwalPerMhs', $controller);
        $this->assertStringContainsString('public function jadwalDosenLink', $controller);
        $this->assertStringContainsString('private function buildNotifDosenJadwalPerMhs', $controller);
        $this->assertStringContainsString('private function buildDosenScheduleToken', $controller);
        $this->assertStringContainsString('private function storeShortDosenScheduleToken', $controller);
        $this->assertStringContainsString('private function resolveShortDosenScheduleToken', $controller);
        $this->assertStringContainsString('private function decodeDosenScheduleToken', $controller);
        $this->assertStringContainsString('private function findDosenRecordForRekap', $controller);
        $this->assertStringContainsString('private function normalizeKodeDosenForRekap', $controller);
        $this->assertStringContainsString("TRIM(LEADING '0' FROM TRIM(C_KODE_DOSEN))", $controller);
        $this->assertStringContainsString('return $this->getDosenContactsForRekapJadwal($kodeDosen)', $controller);
        $this->assertStringContainsString('$this->normalizeKodeDosenForRekap($kode)', $controller);
        $this->assertStringContainsString('private function normalizeWhatsappNumberForRekap', $controller);
        $this->assertStringContainsString('formaction="{{ url(\'prodi/jadwalpermhs/\'.$tipeUjian.\'/notif-dosen\') }}"', $view);
        $this->assertStringContainsString('https://wa.me/', $controller);
        $this->assertStringContainsString("url('jadwal-dosen/' . \$slug)", $controller);
        $this->assertStringContainsString("storage_path('app/schedule-links", $controller);
        $this->assertStringContainsString("->groupBy(function (\$item) {", $controller);
        $this->assertStringContainsString("\$this->getJamMulaiUjianSortKey(\$row->jam_ujian)", $controller);
        $this->assertStringContainsString("\$lines[] = \$this->formatTanggalSingkatRekap(\$tanggal);", $controller);
        $this->assertStringContainsString('Kirim WA', $notificationView);
        $this->assertStringContainsString('Link Rekap', $notificationView);
        $this->assertStringContainsString('Download Excel', $lecturerView);
        $this->assertStringContainsString('Buka PDF / Print', $lecturerView);
        $this->assertStringContainsString('.marker', $lecturerView);
        $this->assertStringContainsString('$highlightRoles', $lecturerView);
    }

    public function testLecturerScheduleIsGroupedByDateAndSortedByStartTime()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $lecturerView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/jadwal_dosen_link.blade.php');

        $this->assertStringContainsString('->sortBy(function ($row)', $controller);
        $this->assertStringContainsString('$this->getJamMulaiUjianSortKey($row->jam_ujian)', $controller);
        $this->assertStringContainsString('$jadwalPerTanggal = $rows->groupBy', $controller);
        $this->assertStringContainsString("'jadwalPerTanggal'", $controller);
        $this->assertStringContainsString('private function getJamMulaiUjianSortKey', $controller);
        $this->assertStringContainsString('@forelse($jadwalPerTanggal as $tanggal => $jadwalHariIni)', $lecturerView);
        $this->assertStringContainsString('Jadwal Ujian: {{ helper::tgl_indo_lengkap($tanggal) }}', $lecturerView);
        $this->assertStringContainsString('@foreach($jadwalHariIni as $row)', $lecturerView);
    }
}
