<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AkademikFakultasRekapUjianSelesaiTest extends TestCase
{
    public function testRoutesAreProtectedByAkademikFakultasMiddleware()
    {
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $groupStart = strpos($routes, "Route::group(['middleware' => 'akademik_fakultas']");
        $groupEnd = strpos($routes, "Route::group(['middleware' => 'dosen']", $groupStart);
        $routeStart = strpos($routes, "Route::get('/fakultas/rekap-ujian-selesai'", $groupStart);

        $this->assertNotFalse($groupStart);
        $this->assertNotFalse($groupEnd);
        $this->assertNotFalse($routeStart);
        $this->assertGreaterThan($groupStart, $routeStart);
        $this->assertLessThan($groupEnd, $routeStart);
        $this->assertStringContainsString("Route::post('/fakultas/rekap-ujian-selesai/nomor-surat', 'fakultas@rekap_ujian_selesai_nomor_surat')", $routes);
        $this->assertStringContainsString("Route::get('/fakultas/sk-yudisium/{date}/{kode_prodi}', 'fakultas@sk_yudisium_data')", $routes);
        $this->assertStringContainsString("Route::post('/fakultas/sk-yudisium/data', 'fakultas@simpan_data_sk_yudisium')", $routes);
        $this->assertStringContainsString("Route::post('/fakultas/sk-yudisium/ipk', 'fakultas@sinkronkan_ipk_sk_yudisium')", $routes);
        $this->assertStringContainsString("Route::post('/fakultas/sk-yudisium/reset', 'fakultas@reset_data_sk_yudisium')", $routes);
        $this->assertStringContainsString("Route::get('/fakultas/sk-yudisium/{date}/{kode_prodi}/pdf', 'fakultas@cetak_sk_yudisium')", $routes);
        $this->assertStringContainsString("Route::get('/verifikasi/sk-yudisium/{token}', 'fakultas@verifikasi_sk_yudisium')", $routes);
    }

    public function testCompletedExamDataUsesPastFinalExamSchedulesAndStoresSeparateYudisiumDocuments()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/fakultas.php');
        $migration = file_get_contents(__DIR__ . '/../../database/migrations/2026_09_01_020000_create_sk_yudisium_tables.php');
        $ipkMigration = file_get_contents(__DIR__ . '/../../database/migrations/2026_09_01_030000_add_siakad_ipk_metadata_to_yudisium_mahasiswa.php');

        $this->assertStringContainsString("->where('pendaftaran.tipe_ujian', 2)", $controller);
        $this->assertStringContainsString("->whereDate('jadwal.tgl_ujian', '<', Carbon::today()->toDateString())", $controller);
        $this->assertStringContainsString("->where('yudisium_ti.kode_prodi', '=', '130')", $controller);
        $this->assertStringContainsString("->where('yudisium_si.kode_prodi', '=', '131')", $controller);
        $this->assertStringContainsString("protected function pesertaYudisium", $controller);
        $this->assertStringContainsString("protected function kekuranganDokumenYudisium", $controller);
        $this->assertStringContainsString("->where('tanggal_ujian', \$date)", $controller);
        $this->assertStringContainsString("Schema::create('trt_sk_yudisium'", $migration);
        $this->assertStringContainsString("Schema::create('trt_yudisium_mahasiswa'", $migration);
        $this->assertStringContainsString("'sk_yudisium_tanggal_tipe_prodi_unique'", $migration);
        $this->assertStringContainsString("'yudisium_mahasiswa_tanggal_tipe_nim_unique'", $migration);
        $this->assertStringContainsString("ipk_sumber", $ipkMigration);
        $this->assertStringContainsString("ipk_disinkronkan_pada", $ipkMigration);
        $this->assertStringContainsString('SiakadIpkService', $controller);
        $this->assertStringContainsString('sinkronkan_ipk_sk_yudisium', $controller);
        $this->assertStringContainsString('protected function nomorAlumniTerakhir()', $controller);
        $this->assertStringContainsString('protected function nomorSuratYudisiumTerakhir()', $controller);
        $this->assertStringContainsString('protected function bulanRomawiYudisium($bulan)', $controller);
        $this->assertStringContainsString(". '/A.10/SI-FIK/UMI/'", $controller);
        $this->assertStringContainsString("'mahasiswa.*.nomor_alumni' => ['nullable', 'regex:/^[1-9][0-9]{0,8}$/']", $controller);
        $this->assertStringContainsString('Nomor alumni tidak boleh digunakan oleh lebih dari satu mahasiswa.', $controller);
        $this->assertStringContainsString('public function reset_data_sk_yudisium(Request $request)', $controller);
        $this->assertStringContainsString("->whereIn('C_NPM', \$peserta->pluck('nim')->all())", $controller);
        $this->assertStringContainsString("->where('kode_prodi', \$kodeProdi)", $controller);
        $this->assertStringContainsString('Nilai ujian dan jadwal tetap tersimpan.', $controller);
    }

    public function testViewsProvideYudisiumDataEntryPdfAndQrVerification()
    {
        $sidebar = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/sidebarakademikfakultas.blade.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/fakultas/rekap_ujian_selesai.blade.php');
        $dataView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/fakultas/sk_yudisium_data.blade.php');
        $pdfView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/fakultas/sk_yudisium_pdf.blade.php');
        $verificationView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/fakultas/verifikasi_sk_yudisium.blade.php');

        $this->assertStringContainsString("route('fakultas.rekap_ujian_selesai')", $sidebar);
        $this->assertStringContainsString('SK Yudisium', $sidebar);
        $this->assertStringContainsString('Jumlah Mahasiswa', $view);
        $this->assertStringContainsString('Type Mahasiswa', $view);
        $this->assertStringContainsString('SK per Program Studi', $view);
        $this->assertStringContainsString('Nilai Ujian TA', $view);
        $this->assertStringContainsString('IPK', $view);
        $this->assertStringContainsString('fa-info-circle', $view);
        $this->assertStringContainsString('fa-file-pdf-o', $view);
        $this->assertStringContainsString("route('fakultas.sk_yudisium_data'", $view);
        $this->assertStringNotContainsString("route('fakultas.cetak_sk_yudisium'", $view);
        $this->assertStringNotContainsString("route('fakultas.reset_data_sk_yudisium')", $view);
        $this->assertStringContainsString('yudisium-action-buttons', $view);
        $this->assertStringContainsString('style="margin-left: 6px;"', $view);
        $this->assertStringContainsString('Nomor Alumni', $dataView);
        $this->assertStringContainsString('Masukkan nomor SK Yudisium', $dataView);
        $this->assertStringContainsString('$contohNomorSuratYudisium', $dataView);
        $this->assertStringContainsString('Nomor terakhir tersimpan:', $dataView);
        $this->assertStringContainsString('Nomor alumni terakhir terpakai:', $dataView);
        $this->assertStringContainsString('Lanjutkan Nomor Berikutnya', $dataView);
        $this->assertStringContainsString('yudisium-alumni-number', $dataView);
        $this->assertStringContainsString('Tarik IPK SIAKAD', $dataView);
        $this->assertStringContainsString("route('fakultas.sinkronkan_ipk_sk_yudisium')", $dataView);
        $this->assertStringContainsString("route('fakultas.cetak_sk_yudisium'", $dataView);
        $this->assertStringContainsString("route('fakultas.reset_data_sk_yudisium')", $dataView);
        $this->assertStringContainsString('reset-yudisium-modal', $dataView);
        $this->assertStringContainsString('Nilai ujian dan jadwal tetap tersimpan.', $dataView);
        $this->assertStringContainsString('Simpan Data Yudisium', $dataView);
        $this->assertStringContainsString('SURAT KEPUTUSAN', $pdfView);
        $this->assertStringContainsString('DAFTAR ALUMNI FAKULTAS ILMU KOMPUTER', $pdfView);
        $this->assertStringContainsString('qrCodeDataUri($verificationUrl', $pdfView);
        $this->assertStringContainsString('SK Yudisium Terverifikasi', $verificationView);
    }
}
