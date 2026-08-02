<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class MahasiswaPhotoCompletionTest extends TestCase
{
    public function testPhotoIsRequiredAndStoredWithStudentProfile()
    {
        $helper = file_get_contents(__DIR__ . '/../../app/Helper.php');
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/mhs.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/content.blade.php');
        $dosenController = file_get_contents(__DIR__ . '/../../app/Http/Controllers/dosen.php');

        $this->assertStringContainsString("'D_FOTO_MAHASISWA'", $helper);
        $this->assertStringContainsString("\$missing[] = 'Foto';", $helper);
        $this->assertStringContainsString("asset('gambar/' . \$photo)", $helper);
        $this->assertStringContainsString("asset('images/defaults/student-female.png')", $helper);
        $this->assertStringContainsString("asset('images/defaults/student-male.png')", $helper);
        $this->assertStringContainsString("'foto' => (\$fotoWajib ? 'required' : 'nullable')", $controller);
        $this->assertStringContainsString("->store('mahasiswa', 'public')", $controller);
        $this->assertStringContainsString("'D_FOTO_MAHASISWA' => \$fotoBaru", $controller);
        $this->assertStringContainsString('enctype="multipart/form-data"', $view);
        $this->assertStringContainsString('name="foto"', $view);
        $this->assertStringContainsString("'mhs.JENIS_KELAMIN'", $dosenController);
        $this->assertStringContainsString('Helper::mahasiswaPhotoUrl($item->D_FOTO_MAHASISWA, $item->JENIS_KELAMIN)', $dosenController);
    }
}
