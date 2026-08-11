<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class MahasiswaTopikSubmissionTest extends TestCase
{
    public function testTopicSubmissionAllowsOptionalFrameworkAttachmentAndShowsFeedback()
    {
        $controller = file_get_contents(
            __DIR__ . '/../../app/Http/Controllers/mhs.php'
        );
        $view = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/mhs/pengajuan_topik.blade.php'
        );

        $this->assertStringContainsString("'kerangka' => 'nullable|file|mimes:", $controller);
        $this->assertStringContainsString("\$datapost['kerangka'] = '';", $controller);
        $this->assertStringContainsString('mkdir($uploadPath, 0775, true)', $controller);
        $this->assertStringContainsString('$errors->any()', $view);
        $this->assertStringContainsString("session('success')", $view);
        $this->assertStringContainsString("session('error')", $view);
        $this->assertStringContainsString('Tidak ada lampiran', $view);
    }
}
