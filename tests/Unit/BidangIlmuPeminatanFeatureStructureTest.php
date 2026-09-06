<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class BidangIlmuPeminatanFeatureStructureTest extends TestCase
{
    public function testMasterAndStudentFormAreWiredToProgramSpecificData()
    {
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $prodiController = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $studentController = file_get_contents(__DIR__ . '/../../app/Http/Controllers/mhs.php');
        $studentView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/mhs/pengajuan_topik.blade.php');
        $adminSidebar = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/sidebar.blade.php');
        $prodiSidebar = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/sidebarkaprodi.blade.php');

        $this->assertStringContainsString('/prodi/master/bidang_ilmu_peminatan', $routes);
        $this->assertStringContainsString('master_bidang_ilmu_peminatan_store', $prodiController);
        $this->assertStringContainsString("where('kode_prodi', \$scope['nim_prefix'])", $prodiController);
        $this->assertStringContainsString('bidangIlmuPeminatanMahasiswaQuery', $studentController);
        $this->assertStringContainsString("in_array(\$kodeProdi, ['130', '131'], true)", $studentController);
        $this->assertStringContainsString('name="bidang_ilmu_peminatan_id"', $studentView);
        $this->assertStringNotContainsString('<option value="Rekayasa Perangkat Lunak">', $studentView);
        $this->assertStringContainsString('Bidang Ilmu Peminatan', $adminSidebar);
        $this->assertStringContainsString('Bidang Ilmu Peminatan', $prodiSidebar);
    }
}
