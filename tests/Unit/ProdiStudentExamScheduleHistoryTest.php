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
        $this->assertStringContainsString("->where('mst_pendaftaran.status_prodi', \$statusProdi)", $controller);
        $this->assertStringContainsString("->orWhere('mst_pendaftaran.tipe_ujian', 3)", $controller);
        $this->assertStringContainsString("url('prodi/jadwalpermhs/'.\$tipeUjian.'/riwayat')", $view);
        $this->assertStringContainsString('Lihat Riwayat', $view);
        $this->assertStringContainsString('Jadwal Berjalan', $view);
    }
}
