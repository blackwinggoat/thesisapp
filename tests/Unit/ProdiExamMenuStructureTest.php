<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ProdiExamMenuStructureTest extends TestCase
{
    public function testProdiAndAdminUseOneExamScheduleMenuGroup()
    {
        $sidebars = [
            file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/sidebarkaprodi.blade.php'),
            file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/sidebar.blade.php'),
        ];

        foreach ($sidebars as $sidebar) {
            $this->assertStringNotContainsString('Jadwal Ujian Per Mahasiswa', $sidebar);
            $this->assertStringContainsString("<li><a href=\"{{ url('prodi/jadwal')}}\">Periode Ujian</a></li>", $sidebar);

            $menuStart = strpos($sidebar, '<i class="fa fa-calendar icon-sidebar"></i>');
            $menuEnd = strpos($sidebar, '</ul>', $menuStart);
            $menu = substr($sidebar, $menuStart, $menuEnd - $menuStart);

            $this->assertStringContainsString('Jadwal Ujian', $menu);
            $this->assertStringContainsString("url('prodi/jadwal')", $menu);
            $this->assertStringContainsString("url('prodi/jadwalpermhs/proposal')", $menu);
            $this->assertStringContainsString("url('prodi/jadwalpermhs/ujianmeja')", $menu);
        }
    }

    public function testProdiSchedulePagesUseTheNewLabels()
    {
        $periodView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/jadwal.blade.php');
        $scheduleView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/jadwalpermhs.blade.php');

        $this->assertStringContainsString('<li class="active">Periode Ujian</li>', $periodView);
        $this->assertStringContainsString("'Riwayat Jadwal Ujian ' . \$namaTipeUjian", $scheduleView);
        $this->assertStringContainsString("'Daftar Jadwal Ujian ' . \$namaTipeUjian", $scheduleView);
        $this->assertStringNotContainsString("' Per Mahasiswa'", $scheduleView);
    }
}
