<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class JenisTugasAkhirAvailabilityTest extends TestCase
{
    public function testAvailabilitySettingFiltersStudentChoicesWithoutDeletingHistory()
    {
        $migration = file_get_contents(__DIR__ . '/../../database/migrations/2026_08_02_090000_add_mahasiswa_availability_to_jenis_tugas_akhir_table.php');
        $maximumScoreMigration = file_get_contents(__DIR__ . '/../../database/migrations/2026_09_09_050000_add_nilai_maksimal_to_jenis_tugas_akhir.php');
        $studentController = file_get_contents(__DIR__ . '/../../app/Http/Controllers/mhs.php');
        $prodiController = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $masterView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/master_jenis_tugas_akhir.blade.php');

        $this->assertStringContainsString("boolean('tersedia_untuk_mahasiswa')->default(1)", $migration);
        $this->assertStringContainsString("decimal('nilai_maksimal', 5, 2)->default(100)", $maximumScoreMigration);
        $this->assertStringContainsString("['NS-AR', 'NS-KP']", $migration);
        $this->assertStringContainsString('Tugas Akhir Skripsi Mandiri', $migration);
        $this->assertStringContainsString('Tugas Akhir Skripsi Kolaborasi', $migration);
        $this->assertStringContainsString('jenisTugasAkhirMahasiswaQuery', $studentController);
        $this->assertStringContainsString("where('tersedia_untuk_mahasiswa', 1)", $studentController);
        $this->assertStringContainsString('jenisTugasAkhirMahasiswaDapatDipilih', $studentController);
        $this->assertStringContainsString('master_jenis_tugas_akhir_availability', $prodiController);
        $this->assertStringContainsString('master_jenis_tugas_akhir_update', $prodiController);
        $this->assertStringContainsString('Tersedia bagi Mahasiswa', $masterView);
        $this->assertStringContainsString('type="checkbox"', $masterView);
        $this->assertStringContainsString('Edit Jenis Tugas Akhir', $masterView);
        $this->assertStringContainsString('Nilai Maksimal', $masterView);
        $this->assertStringContainsString('name="nilai_maksimal"', $masterView);
        $this->assertStringContainsString('showEditJenisTugasAkhir', $masterView);
        $this->assertStringContainsString('class="the-box jenis-ta-form-box"', $masterView);
        $this->assertStringContainsString('class="jenis-ta-form-body"', $masterView);
        $this->assertStringContainsString('class="jenis-ta-switch"', $masterView);
        $this->assertStringContainsString('<span class="input-group-addon">poin</span>', $masterView);
        $this->assertStringContainsString("$(modalId).modal('show')", $masterView);
        $this->assertStringNotContainsString('<br><br>', $masterView);
    }
}
