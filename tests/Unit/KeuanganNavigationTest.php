<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class KeuanganNavigationTest extends TestCase
{
    public function testSidebarUsesTheKeuanganPasswordRouteAndLabel()
    {
        $sidebar = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/layouts/sidebarkeuanganfakultas.blade.php'
        );

        $this->assertStringContainsString("url('keuanganfakultas/ubah_password')", $sidebar);
        $this->assertStringContainsString('MENU KEUANGAN FAKULTAS', $sidebar);
        $this->assertStringNotContainsString("url('dsn/ubah_password')", $sidebar);
    }

    public function testSharedNavigationUsesApplicationRoutes()
    {
        $navigation = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/layouts/navigation.blade.php'
        );

        $this->assertStringContainsString('<a href="{{ url(\'/\') }}">', $navigation);
        $this->assertStringContainsString("url('keuanganfakultas/ubah_password')", $navigation);
        $this->assertStringNotContainsString('<a href="index.html"><img', $navigation);
    }

    public function testKeuanganBreadcrumbsDoNotTargetMissingIndexPage()
    {
        $views = glob(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/*.blade.php');

        $this->assertNotEmpty($views);
        foreach ($views as $view) {
            $this->assertStringNotContainsString('href="index.html"', file_get_contents($view), $view);
        }
    }
}
