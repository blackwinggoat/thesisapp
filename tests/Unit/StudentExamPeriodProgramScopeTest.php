<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class StudentExamPeriodProgramScopeTest extends TestCase
{
    public function testStudentExamPeriodsAreScopedByMasterProgramCode()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/mhs.php');

        $this->assertStringContainsString("'55201' => ['status_prodi' => 1, 'label' => 'Teknik Informatika']", $controller);
        $this->assertStringContainsString("'57201' => ['status_prodi' => 2, 'label' => 'Sistem Informasi']", $controller);
        $this->assertStringContainsString("->value('C_KODE_PRODI')", $controller);
        $this->assertGreaterThanOrEqual(3, substr_count($controller, "->where('status_prodi', \$studentProgram['status_prodi'])"));
        $this->assertGreaterThanOrEqual(2, substr_count($controller, "\$query->whereRaw('1 = 0')"));
        $this->assertStringContainsString("->with('registration_status', 'invalid_period')", $controller);
    }

    public function testBothStudentViewsShowTheResolvedProgram()
    {
        $views = [
            file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/mhs/signup_proposal.blade.php'),
            file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/mhs/signup_ujianmeja.blade.php'),
        ];

        foreach ($views as $view) {
            $this->assertStringContainsString('{{$studentProgramLabel}}', $view);
            $this->assertStringContainsString('Belum ada periode pendaftaran yang tersedia.', $view);
        }
    }

    public function testProposalAndFinalExamShareSafePeriodCancellationFlow()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/mhs.php');
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $statusPartial = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/mhs/partials/exam_registration_status.blade.php');

        $this->assertStringContainsString('private function getStudentRegistrationContext', $controller);
        $this->assertStringContainsString("->where('period.status_prodi', \$studentProgram['status_prodi'])", $controller);
        $this->assertStringContainsString("->where('trt_jadwal_ujian.pendaftaran_id', \$pendaftaranId)", $controller);
        $this->assertStringContainsString("Route::post('/mhs/registrasi/batalkan'", $routes);
        $this->assertStringNotContainsString("Route::get('/mhs/signup_ujianmeja/batalkan/", $routes);
        $this->assertStringContainsString("action=\"{{url('mhs/registrasi/batalkan')}}\"", $statusPartial);
        $this->assertStringContainsString('Keluar dari Periode', $statusPartial);
        $this->assertStringContainsString('Jadwal sudah ditetapkan', $statusPartial);
    }
}
