<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AkademikProdiNavigationTest extends TestCase
{
    public function testPasswordControlsUseTheAkademikProdiRoute()
    {
        $sidebar = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/layouts/sidebarakademikprodi.blade.php'
        );
        $navigation = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/layouts/navigation.blade.php'
        );

        $this->assertStringContainsString("url('akademikprodi/ubah_password')", $sidebar);
        $this->assertStringNotContainsString('color: wh ite', $sidebar);
        $this->assertRegExp(
            "/level==6\)\s*<li><a[^>]+url\('akademikprodi\/ubah_password'\)/s",
            $navigation
        );
        $this->assertNotRegExp(
            "/level==6\)\s*<li><a[^>]+url\('mhs\/ubah_password/s",
            $navigation
        );
    }

    public function testAkademikProdiBreadcrumbsDoNotTargetMissingIndexPage()
    {
        $directory = new \RecursiveDirectoryIterator(
            __DIR__ . '/../../resources/views/tugasakhir/akademikprodi'
        );
        $views = new \RecursiveIteratorIterator($directory);

        foreach ($views as $view) {
            if (!$view->isFile() || $view->getExtension() !== 'php') {
                continue;
            }

            $path = $view->getPathname();
            $this->assertStringNotContainsString('href="index.html"', file_get_contents($path), $path);
        }
    }

    public function testSharedAkademikProdiViewsUseApplicationHome()
    {
        $views = [
            'persyaratan_proposal.blade.php',
            'persyaratan_ujianmeja.blade.php',
            'detail_persyaratan_proposal.blade.php',
            'detail_persyaratan_ujianmeja.blade.php',
            'detail_status_bimbingan_mahasiswa.blade.php',
        ];

        foreach ($views as $view) {
            $path = __DIR__ . '/../../resources/views/tugasakhir/prodi/' . $view;
            $this->assertStringNotContainsString('href="index.html"', file_get_contents($path), $path);
        }
    }
}
