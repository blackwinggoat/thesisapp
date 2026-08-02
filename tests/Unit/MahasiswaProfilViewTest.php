<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class MahasiswaProfilViewTest extends TestCase
{
    public function testMahasiswaProfileShowsLockedIdentityAndEditableContactFields()
    {
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/mhs/profil.blade.php');

        $this->assertStringContainsString('name="return_to" value="profil"', $view);
        $this->assertStringContainsString('name="no_wa"', $view);
        $this->assertStringContainsString('name="id_telegram"', $view);
        $this->assertStringContainsString('name="foto"', $view);
        $this->assertStringNotContainsString('name="C_NPM"', $view);
        $this->assertStringNotContainsString('name="NAMA_MAHASISWA"', $view);
        $this->assertStringNotContainsString('name="C_KODE_PRODI"', $view);
    }
}
