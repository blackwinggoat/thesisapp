<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class DosenProfilViewTest extends TestCase
{
    public function testLecturerProfileKeepsIdentityLockedAndSupportsPhotoUpdates()
    {
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/dosen/profil.blade.php');
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/dosen.php');
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');

        $this->assertStringContainsString("Route::get(\"/dsn/profil\", \"dosen@profil\")", $routes);
        $this->assertStringContainsString('name="return_to" value="profil"', $view);
        $this->assertStringContainsString('name="foto_dosen"', $view);
        $this->assertStringNotContainsString('name="C_KODE_DOSEN"', $view);
        $this->assertStringNotContainsString('name="NAMA_DOSEN"', $view);
        $this->assertStringContainsString("'foto_dosen' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048'", $controller);
        $this->assertStringContainsString("->store('dosen', 'public')", $controller);
        $this->assertStringContainsString("'D_FOTO_DOSEN'", $controller);

        $sidebar = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/sidebardosen.blade.php');
        $style = file_get_contents(__DIR__ . '/../../public/master/assets/css/style.css');
        $this->assertStringContainsString('dosen-profile-avatar', $sidebar);
        $this->assertStringContainsString('dosen-sidebar-summary', $sidebar);
        $this->assertStringContainsString('object-fit: contain', $style);
        $this->assertStringContainsString('.dosen-sidebar-actions', $style);
    }
}
