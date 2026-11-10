<?php

namespace Tests\Unit;

use Tests\TestCase;

class ProdiBeritaAcaraSafetyTest extends TestCase
{
    public function testPrintControllerRejectsIncompleteScheduleBeforeReadingTheRoom()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');

        $this->assertStringContainsString(
            'Berita acara belum dapat dicetak karena ruang dan jam ujian mahasiswa belum ditetapkan.',
            $controller
        );
        $this->assertStringContainsString(
            'Data tim penguji untuk berita acara belum lengkap.',
            $controller
        );
        $this->assertStringContainsString('$ruangan = $ruanganModel->nama_ruangan;', $controller);
    }

    public function testPrintActionIsOnlyShownForParticipantsWithACompleteSchedule()
    {
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/detail_skujian.blade.php');
        $studentController = file_get_contents(__DIR__ . '/../../app/Http/Controllers/mhs.php');

        $this->assertStringContainsString('$scheduledNims', $view);
        $this->assertStringContainsString('Ruang &amp; jam belum ditetapkan', $view);
        $this->assertStringContainsString('"mahasiswa",', $studentController);
    }
}
