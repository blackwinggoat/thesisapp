<?php

namespace Tests\Unit;

use App\Http\Controllers\mhs;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

class MahasiswaTopikSubmissionTest extends TestCase
{
    public function testTopicSubmissionUsesOptionalGoogleDriveFrameworkLinkAndShowsFeedback()
    {
        $controller = file_get_contents(
            __DIR__ . '/../../app/Http/Controllers/mhs.php'
        );
        $view = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/mhs/pengajuan_topik.blade.php'
        );

        $this->assertStringContainsString("'kerangka' => 'nullable|string|max:255'", $controller);
        $this->assertStringContainsString('$this->parseGoogleDriveFileLink($kerangkaUrl)', $controller);
        $this->assertStringContainsString("\$datapost['kerangka'] = \$kerangkaUrl;", $controller);
        $this->assertStringNotContainsString("'kerangka' => 'nullable|file|mimes:", $controller);
        $this->assertStringContainsString('type="url" name="kerangka"', $view);
        $this->assertStringNotContainsString('type="file" name="kerangka"', $view);
        $this->assertStringContainsString('$errors->any()', $view);
        $this->assertStringContainsString("session('success')", $view);
        $this->assertStringContainsString("session('error')", $view);
        $this->assertStringContainsString('tugasakhir.components.kerangka-pikir-link', $view);
    }

    public function testAllTopicReviewViewsUseTheSharedFrameworkLinkComponent()
    {
        $views = [
            'mhs/pengajuan_topik.blade.php',
            'prodi/detail_topikusulan.blade.php',
            'prodi/topik_riwayat.blade.php',
            'dosen/request_pembimbing.blade.php',
            'akademikprodi/dosen/request_pembimbing.blade.php',
        ];

        foreach ($views as $view) {
            $contents = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/' . $view);
            $this->assertStringContainsString(
                "@include('tugasakhir.components.kerangka-pikir-link'",
                $contents,
                $view
            );
        }

        $component = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/components/kerangka-pikir-link.blade.php'
        );
        $this->assertStringContainsString('target="_blank"', $component);
        $this->assertStringContainsString('rel="noopener noreferrer"', $component);
        $this->assertStringContainsString('helper::getKerangkaPikirUrl', $component);
    }

    public function testFrameworkLinkValidationAcceptsDriveFilesAndRejectsFolders()
    {
        $controller = (new ReflectionClass(mhs::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod(mhs::class, 'parseGoogleDriveFileLink');
        $method->setAccessible(true);

        $driveFile = $method->invoke($controller, 'https://drive.google.com/file/d/file-123/view?usp=sharing');
        $docsFile = $method->invoke($controller, 'https://docs.google.com/document/d/doc-456/edit');
        $folder = $method->invoke($controller, 'https://drive.google.com/drive/folders/folder-789');
        $external = $method->invoke($controller, 'https://example.com/file.pdf');
        $insecure = $method->invoke($controller, 'http://drive.google.com/file/d/file-123/view');

        $this->assertTrue($driveFile['valid']);
        $this->assertSame('file-123', $driveFile['file_id']);
        $this->assertTrue($docsFile['valid']);
        $this->assertSame('doc-456', $docsFile['file_id']);
        $this->assertFalse($folder['valid']);
        $this->assertFalse($external['valid']);
        $this->assertFalse($insecure['valid']);
    }
}
