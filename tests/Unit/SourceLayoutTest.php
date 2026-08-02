<?php

namespace Tests\Unit;

use App\Http\Controllers\Prodi;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class SourceLayoutTest extends TestCase
{
    public function testProdiControllerUsesItsPsr4ClassName()
    {
        $controller = new ReflectionClass(Prodi::class);

        $this->assertSame('Prodi', $controller->getShortName());
        $this->assertSame('Prodi.php', basename($controller->getFileName()));
    }

    public function testRuntimeSourceContainsNoKnownConflictArtifacts()
    {
        $projectRoot = realpath(__DIR__ . '/../..');
        $directories = [
            $projectRoot . '/app',
            $projectRoot . '/resources/views/tugasakhir',
        ];
        $conflictArtifacts = [];

        foreach ($directories as $directory) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

            foreach ($files as $file) {
                if ($file->isFile() && strpos($file->getFilename(), 'conflicted copy') !== false) {
                    $conflictArtifacts[] = $file->getPathname();
                }
            }
        }

        $this->assertSame([], $conflictArtifacts);
        $this->assertFileNotExists($projectRoot . '/app/dosen-0.php');
        $this->assertFileNotExists(
            $projectRoot . '/app/Http/Controllers/detail_note.blade.php'
        );
    }
}
