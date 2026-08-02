<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AkademikFakultasNavigationTest extends TestCase
{
    public function testSidebarUsesTheRegisteredPasswordRoute()
    {
        $sidebar = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/layouts/sidebarakademikfakultas.blade.php'
        );

        $this->assertStringContainsString("url('fakultas/ubah_password')", $sidebar);
        $this->assertStringNotContainsString("url('akademikfakultas/ubah_password')", $sidebar);
    }

    public function testFakultasBreadcrumbsDoNotTargetMissingIndexPage()
    {
        $views = glob(__DIR__ . '/../../resources/views/tugasakhir/fakultas/*.blade.php');

        $this->assertNotEmpty($views);
        foreach ($views as $view) {
            $this->assertStringNotContainsString('href="index.html"', file_get_contents($view), $view);
        }

        $sharedSkView = __DIR__ . '/../../resources/views/tugasakhir/prodi/sk_ujian.blade.php';
        $this->assertStringNotContainsString(
            'href="index.html"',
            file_get_contents($sharedSkView),
            $sharedSkView
        );
    }
}
