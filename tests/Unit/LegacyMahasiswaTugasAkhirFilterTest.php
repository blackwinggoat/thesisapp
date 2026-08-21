<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class LegacyMahasiswaTugasAkhirFilterTest extends TestCase
{
    public function testDosenSupervisionDetailDoesNotHideStudentsByInactiveStatus()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/dosen.php');
        preg_match(
            '/protected function getMahasiswaBimbinganByPeran.*?protected function getStatusBimbinganLabel/s',
            $controller,
            $matches
        );

        $this->assertNotEmpty($matches);
        $this->assertStringNotContainsString('C_KODE_STATUS_AKTIF_MHS', $matches[0]);
        $this->assertStringNotContainsString("status_bimbingan', '<>', 4", $matches[0]);
    }

    public function testLegacyDeactivationCommandCannotChangeStudentStatus()
    {
        $command = file_get_contents(__DIR__ . '/../../app/Console/Commands/DeactivateLegacyMahasiswaTugasAkhir.php');

        $this->assertStringContainsString('Perintah penonaktifan mahasiswa telah dinonaktifkan', $command);
        $this->assertStringNotContainsString("update(['C_KODE_STATUS_AKTIF_MHS' => 'N'])", $command);
        $this->assertStringNotContainsString("update(['status_bimbingan' => 4])", $command);
    }
}
