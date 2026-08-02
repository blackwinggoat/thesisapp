<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class JenisTugasAkhirAvailabilityTest extends TestCase
{
    public function testAvailabilitySettingFiltersStudentChoicesWithoutDeletingHistory()
    {
        $migration = file_get_contents(__DIR__ . '/../../database/migrations/2026_08_02_090000_add_mahasiswa_availability_to_jenis_tugas_akhir_table.php');
        $studentController = file_get_contents(__DIR__ . '/../../app/Http/Controllers/mhs.php');
        $prodiController = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $masterView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/master_jenis_tugas_akhir.blade.php');

        $this->assertStringContainsString("boolean('tersedia_untuk_mahasiswa')->default(1)", $migration);
        $this->assertStringContainsString("['NS-AR', 'NS-KP']", $migration);
        $this->assertStringContainsString('Tugas Akhir Skripsi Mandiri', $migration);
        $this->assertStringContainsString('Tugas Akhir Skripsi Kolaborasi', $migration);
        $this->assertStringContainsString('jenisTugasAkhirMahasiswaQuery', $studentController);
        $this->assertStringContainsString("where('tersedia_untuk_mahasiswa', 1)", $studentController);
        $this->assertStringContainsString('jenisTugasAkhirMahasiswaDapatDipilih', $studentController);
        $this->assertStringContainsString('master_jenis_tugas_akhir_availability', $prodiController);
        $this->assertStringContainsString('Tersedia bagi Mahasiswa', $masterView);
        $this->assertStringContainsString('type="checkbox"', $masterView);
    }
}
