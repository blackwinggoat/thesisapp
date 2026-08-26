<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AdminProdiCombinedScopeTest extends TestCase
{
    public function testAdminCanUseProdiMenusWithCombinedProgramScope()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $middleware = file_get_contents(__DIR__ . '/../../app/Http/Middleware/kaprodi.php');
        $adminSidebar = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/sidebar.blade.php');
        $jadwalView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/jadwal.blade.php');

        $this->assertStringContainsString('in_array((int) $request->user()->level, [1, 5], true)', $middleware);
        $this->assertStringContainsString('protected function getProdiScope', $controller);
        $this->assertStringContainsString("'label' => 'Semua Program Studi'", $controller);
        $this->assertStringContainsString('protected function currentStatusProdiForWrite(Request $request)', $controller);
        $this->assertStringContainsString('when(!is_null($statusProdi)', $controller);
        $this->assertStringContainsString('MENU PROGRAM STUDI (ADMIN)', $adminSidebar);
        $this->assertStringContainsString("url('prodi/report/laporan')", $adminSidebar);
        $this->assertStringContainsString("url('prodi/master/dosen')", $adminSidebar);
        $this->assertStringContainsString("url('prodi/jadwalpermhs/proposal')", $adminSidebar);
        $this->assertStringContainsString("url('prodi/approve_hasilujian_proposal')", $adminSidebar);
        $this->assertStringContainsString('name="status_prodi"', $jadwalView);
        $this->assertStringContainsString('<option value="1">Teknik Informatika</option>', $jadwalView);
        $this->assertStringContainsString('<option value="2">Sistem Informasi</option>', $jadwalView);
        $this->assertStringNotContainsString("Auth::user()->name == 'proditi' ? '130%' : '131%'", $controller);
        $this->assertStringNotContainsString('Auth::user()->name == "proditi" ? 1 : 2', $controller);
    }

    public function testProdiResultSheetsUseStudentNimForProgramLabel()
    {
        $proposal = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/lembaran_hasilujian_proposal.blade.php');
        $ta = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/lembaran_hasilujian_ta.blade.php');

        foreach ([$proposal, $ta] as $view) {
            $this->assertStringContainsString("substr((string) \$nim, 0, 3) === '130'", $view);
            $this->assertStringContainsString('{{ $programStudiSurat }}', $view);
            $this->assertStringNotContainsString("Auth::user()->name == 'proditi'", $view);
        }
    }
}
