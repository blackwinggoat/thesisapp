<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ProdiMahasiswaJenisKelasTest extends TestCase
{
    public function testExecutiveStudentStatusUsesDedicatedTableAndConfirmedToggle()
    {
        $migration = file_get_contents(__DIR__ . '/../../database/migrations/2026_08_21_050000_create_trt_mahasiswa_eksekutif_table.php');
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/mahasiswa.blade.php');

        $this->assertStringContainsString("Schema::create('trt_mahasiswa_eksekutif'", $migration);
        $this->assertStringContainsString('$table->string(\'C_NPM\', 15)->unique();', $migration);
        $this->assertStringContainsString('public function update_jenis_kelas_mahasiswa(Request $request, $nim)', $controller);
        $this->assertStringContainsString("Schema::hasTable('trt_mahasiswa_eksekutif')", $controller);
        $this->assertStringContainsString('$studentClassFeatureReady', $controller);
        $this->assertStringContainsString('->where(\'C_NPM\', \'LIKE\', $nimPrefix . \'%\')', $controller);
        $this->assertStringContainsString("DB::table('trt_mahasiswa_eksekutif')->insert", $controller);
        $this->assertStringContainsString("Route::post('/prodi/mahasiswa/{nim}/jenis-kelas', 'Prodi@update_jenis_kelas_mahasiswa')", $routes);
        $this->assertStringContainsString('student-class-toggle', $view);
        $this->assertStringContainsString('studentClassFeatureReady', $view);
        $this->assertStringContainsString("window.confirm('Ubah jenis kelas '", $view);
        $this->assertStringContainsString('Eksekutif', $view);
        $this->assertStringContainsString('Reguler', $view);
    }
}
