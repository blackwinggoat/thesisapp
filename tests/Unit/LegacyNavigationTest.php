<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class LegacyNavigationTest extends TestCase
{
    public function testActiveRoleViewsDoNotLinkToMissingIndexPage()
    {
        $directory = new \RecursiveDirectoryIterator(
            __DIR__ . '/../../resources/views/tugasakhir'
        );
        $views = new \RecursiveIteratorIterator($directory);
        $checked = 0;

        foreach ($views as $view) {
            if (!$view->isFile() || $view->getExtension() !== 'php') {
                continue;
            }

            $path = $view->getPathname();
            if (strpos($path, DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR) !== false) {
                continue;
            }

            $contents = file_get_contents($path);
            $this->assertStringNotContainsString('href="index.html"', $contents, $path);
            $this->assertStringNotContainsString('<a href="#fakelink">Home</a>', $contents, $path);
            $checked++;
        }

        $misplacedView = __DIR__ . '/../../app/Http/Controllers/detail_note.blade.php';
        $misplacedContents = file_get_contents($misplacedView);
        $this->assertStringNotContainsString('href="index.html"', $misplacedContents, $misplacedView);
        $this->assertStringNotContainsString(
            '<a href="#fakelink">Home</a>',
            $misplacedContents,
            $misplacedView
        );
        $this->assertGreaterThan(0, $checked);
    }
}
