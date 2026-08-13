<?php

namespace Tests\Unit;

use Tests\TestCase;

class MahasiswaDownloadPdfTest extends TestCase
{
    public function testStudentExamTableSkRouteStreamsPdfAndDownloadLinkTargetsIt()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/mhs.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/mhs/download.blade.php');

        $this->assertStringContainsString("PDF::loadView('tugasakhir.fakultas.cetakskpenugasan'", $controller);
        $this->assertStringContainsString("->setPaper('a4', 'portrait')", $controller);
        $this->assertStringContainsString("->stream('SK-Ujian-Meja-'", $controller);
        $this->assertStringContainsString("url('mhs/surat_sk_ujian_meja')", $view);
    }
}
