<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HonorariumAutomaticTypeSetupTest extends TestCase
{
    public function testAutomaticTypeSetupUsesExamClassAndFinalProjectTypeWithoutAi()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/KeuanganFakultas.php');
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/honorarium_detail.blade.php');
        $migration = file_get_contents(__DIR__ . '/../../database/migrations/2026_08_21_130000_mark_named_executive_honorarium_payments.php');
        $attendanceMigration = file_get_contents(__DIR__ . '/../../database/migrations/2026_08_22_020000_add_pembimbing_attendance_to_trt_honorium_table.php');

        $this->assertStringContainsString('honorarium_setup_type_ujian_otomatis', $controller);
        $this->assertStringContainsString('namaPembayaranOtomatis', $controller);
        $this->assertStringContainsString("strpos((string) \$kodeJenisTugasAkhir, 'NS-') === 0", $controller);
        $this->assertStringContainsString("(int) \$examType === 0", $controller);
        $this->assertStringContainsString("(int) \$examType === 2", $controller);
        $this->assertStringContainsString('honorariumHasPaidRole($honorarium)', $controller);
        $this->assertStringContainsString('honorarium_reset_type', $controller);
        $this->assertStringContainsString('honorarium_available_all', $controller);
        $this->assertStringContainsString('honorarium_unavailable_all', $controller);
        $this->assertStringContainsString("Route::get('/', 'KeuanganFakultas@honorarium_penetapan_home')->name('honorarium_penetapan_home')", $routes);
        $this->assertStringContainsString("Route::post('/tanggal/{date}/setup-type-ujian'", $routes);
        $this->assertStringContainsString("->name('honorarium_penetapan_setup_type_ujian_otomatis')", $routes);
        $this->assertStringContainsString("Route::post('/tanggal/{date}/reset-type'", $routes);
        $this->assertStringContainsString("->name('honorarium_penetapan_reset_type')", $routes);
        $this->assertStringContainsString("Route::post('/pembimbing-attendance'", $routes);
        $this->assertStringContainsString("->name('honorarium_penetapan_pembimbing_attendance')", $routes);
        $this->assertStringContainsString("Route::post('/tanggal/{date}/available-all'", $routes);
        $this->assertStringContainsString("Route::post('/tanggal/{date}/unavailable-all'", $routes);
        $this->assertStringContainsString('Setup Tipe Ujian Otomatis', $view);
        $this->assertStringContainsString('Reset Type', $view);
        $this->assertStringContainsString('Available Semua', $view);
        $this->assertStringContainsString('Unavailable Semua', $view);
        $this->assertStringContainsString('kode_jenis_tugas_akhir', $view);
        $this->assertStringContainsString('Proposal Eksekutif', $migration);
        $this->assertStringContainsString('Ujian Meja Eksekutif', $migration);
        $this->assertStringContainsString('pembimbing_utama_hadir', $attendanceMigration);
        $this->assertStringContainsString('pembimbing_pendamping_hadir', $attendanceMigration);
    }

    public function testAcademicFacultyOwnsTypeSetupAndFinanceGetsReadOnlyType()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/KeuanganFakultas.php');
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $summary = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/honorarium.blade.php');
        $detail = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/honorarium_detail.blade.php');
        $sidebar = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/sidebarakademikprodi.blade.php');

        $this->assertStringContainsString('honorarium_penetapan_home', $controller);
        $this->assertStringContainsString('renderHonorariumHome', $controller);
        $this->assertStringContainsString('renderHonorariumDetailTanggal', $controller);
        $this->assertStringContainsString("'honorariumMode' => \$honorariumMode", $controller);
        $this->assertStringContainsString("Route::group(['prefix' => 'fakultas/honorarium']", $routes);
        $this->assertStringContainsString('Penetapan Honorarium', $sidebar);
        $this->assertStringContainsString('$isAkademikHonorarium', $summary);
        $this->assertStringContainsString("{{ \$isAkademikHonorarium ? 'Penetapan Tipe Honorarium per Tanggal Ujian' : 'Manajemen Honorarium per Tanggal Ujian' }}", $summary);
        $this->assertStringContainsString("@if (!\$isAkademikHonorarium)", $summary);
        $this->assertStringContainsString('Type Mahasiswa', $summary);
        $this->assertStringContainsString('total_reguler', $summary);
        $this->assertStringContainsString('total_eksekutif', $summary);
        $this->assertStringContainsString("{{ \$isAkademikHonorarium ? 6 : 9 }}", $summary);
        $this->assertStringContainsString('honorarium_penetapan_save_all', $detail);
        $this->assertStringContainsString('honorarium_penetapan_setup_type_ujian_otomatis', $detail);
        $this->assertStringContainsString('honorarium_penetapan_reset_type', $detail);
        $this->assertStringContainsString('kolomKehadiranPembimbingTersedia', $controller);
        $this->assertStringContainsString('pembimbing_utama_hadir', $controller);
        $this->assertStringContainsString('pembimbing_pendamping_hadir', $controller);
        $this->assertStringContainsString('Kehadiran Pembimbing', $detail);
        $this->assertStringContainsString('honorarium-advisor-attendance', $detail);
        $this->assertStringContainsString('honorarium-advisor-presence', $detail);
        $this->assertStringContainsString('<strong>PU :</strong>', $detail);
        $this->assertStringContainsString('<strong>PP :</strong>', $detail);
        $this->assertStringNotContainsString('<strong>P1 :</strong>', $detail);
        $this->assertStringNotContainsString('<strong>P2 :</strong>', $detail);
        $this->assertStringContainsString('pembimbing-attendance-checkbox', $detail);
        $this->assertStringContainsString('change.pembimbingAttendance', $detail);
        $this->assertStringContainsString('honorarium_penetapan_pembimbing_attendance', $detail);
        $this->assertStringContainsString('honorarium-nim-column', $detail);
        $this->assertStringContainsString('max-width: 105px', $detail);
        $this->assertStringContainsString('honorarium-type-column', $detail);
        $this->assertStringContainsString('max-width: 250px', $detail);
        $this->assertStringContainsString('modal-pu-h', $detail);
        $this->assertStringNotContainsString('modal-pu-status', $detail);
        $this->assertStringContainsString('@if (!$isAkademikHonorarium)', $detail);
        $this->assertStringContainsString('nomorSkUjianHonorariumByNim', $controller);
        $this->assertStringContainsString('nomor_sk_proposal', $controller);
        $this->assertStringContainsString('nomor_sk_ujian_akhir', $controller);
        $this->assertStringContainsString('Nomor SK Ujian', $detail);
        $this->assertStringContainsString('Proposal :', $detail);
        $this->assertStringContainsString('Ujian Akhir :', $detail);
        $this->assertStringContainsString('honorarium-student-class', $detail);
        $this->assertStringContainsString("{{ \$honorarium->mahasiswa_eksekutif ? 'Eksekutif' : 'Reguler' }}", $detail);
        $this->assertStringContainsString('@else', $detail);
        $this->assertStringContainsString('<strong>{{ $honorarium->tipe_ujian }}</strong>', $detail);
        $this->assertStringContainsString('Penetapan tipe honorarium dilakukan oleh Akademik Prodi.', $controller);
    }

    public function testAutomaticPaymentNameFollowsExamFinalProjectAndClassRules()
    {
        $controller = new \App\Http\Controllers\KeuanganFakultas;
        $method = new \ReflectionMethod($controller, 'namaPembayaranOtomatis');
        $method->setAccessible(true);

        $this->assertSame('Proposal', $method->invoke($controller, 0, 'TA-SM', false));
        $this->assertSame('Ujian Meja', $method->invoke($controller, 2, 'TA-SK', false));
        $this->assertSame('Non Skripsi [proposal + Ujian Meja]', $method->invoke($controller, 0, 'NS-KT', false));
        $this->assertSame('Non Skripsi [proposal + Ujian Meja] Eksekutif', $method->invoke($controller, 2, 'NS-AI', true));
    }
}
