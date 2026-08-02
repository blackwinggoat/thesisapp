<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ProdiMahasiswaLoginAsTest extends TestCase
{
    public function testProdiCanSafelyLoginAsAndReturnFromMahasiswaAccount()
    {
        $prodiController = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $mahasiswaController = file_get_contents(__DIR__ . '/../../app/Http/Controllers/mhs.php');
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $mahasiswaView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/mahasiswa.blade.php');
        $sidebar = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/sidebarmhs.blade.php');

        $this->assertStringContainsString('public function login_as_mahasiswa(Request $request, $nim)', $prodiController);
        $this->assertStringContainsString("->where('level', 8)", $prodiController);
        $this->assertStringContainsString("->where('C_NPM', 'LIKE', \$nimPrefix . '%')", $prodiController);
        $this->assertStringContainsString("Route::get('/prodi/login_as_mahasiswa/{nim}', 'Prodi@login_as_mahasiswa');", $routes);
        $this->assertStringContainsString("url('prodi/login_as_mahasiswa/'.\$value->C_NPM)", $mahasiswaView);
        $this->assertStringContainsString('public function back_to_prodi(Request $request)', $mahasiswaController);
        $this->assertStringContainsString("Route::get('/mhs/back_to_prodi', 'mhs@back_to_prodi');", $routes);
        $this->assertStringContainsString("url('mhs/back_to_prodi')", $sidebar);
    }

    public function testLoginAsProdiSkipsMandatoryMahasiswaProfilePrompt()
    {
        $helper = file_get_contents(__DIR__ . '/../../app/Helper.php');
        $middleware = file_get_contents(__DIR__ . '/../../app/Http/Middleware/mhs.php');

        $this->assertStringContainsString("session('login_as_source_user_level') === 5", $helper);
        $this->assertStringContainsString('return false;', $helper);
        $this->assertStringContainsString('shouldShowCurrentMahasiswaContactPopup($request->user())', $middleware);
    }
}
