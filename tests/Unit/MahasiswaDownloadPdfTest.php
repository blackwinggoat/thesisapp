<?php

namespace Tests\Unit;

use Tests\TestCase;

class MahasiswaDownloadPdfTest extends TestCase
{
    public function testStudentExamTableSkRouteStreamsPdfAndDownloadLinkTargetsIt()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/mhs.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/fakultas/cetakskpenugasan.blade.php');

        $this->assertStringContainsString("PDF::loadView('tugasakhir.fakultas.cetakskpenugasan'", $controller);
        $this->assertStringContainsString("->setPaper('a4', 'portrait')", $controller);
        $this->assertStringContainsString("->stream('SK-Ujian-Meja-'", $controller);
        $this->assertStringContainsString("preg_replace('/[^A-Za-z0-9._-]+/', '-',", $controller);
        $downloadView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/mhs/download.blade.php');
        $this->assertStringContainsString("url('mhs/surat_sk_ujian_meja')", $downloadView);
        $this->assertStringContainsString('publicImageDataUri', $view);
        $this->assertStringContainsString('$namaMahasiswa = optional($mahasiswa)->NAMA_MAHASISWA ?: \'-\';', $view);
        $this->assertStringContainsString("substr(\$" . "imageData, -12, 4) === 'IEND'", file_get_contents(__DIR__ . '/../../app/Helper.php'));
    }
}
