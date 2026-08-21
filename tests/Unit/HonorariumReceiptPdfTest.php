<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HonorariumReceiptPdfTest extends TestCase
{
    public function testDateSummaryOffersLecturerReceiptPdf()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/KeuanganFakultas.php');
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $listView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/honorarium.blade.php');
        $pdfView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/tanda_terima_honorarium_pdf.blade.php');

        $this->assertStringContainsString('honorarium_tanda_terima_pdf', $controller);
        $this->assertStringContainsString("->unique('id')", $controller);
        $this->assertStringContainsString('PDF belum dapat dibuat. Tetapkan tipe honorarium', $controller);
        $this->assertStringContainsString("'KS' => ['label' => 'Ketua Sidang', 'amount' => 'KS_H', 'status' => 'KS_Stat']", $controller);
        $this->assertStringContainsString("'P3' => ['label' => 'Penguji III', 'amount' => 'P3_H', 'status' => 'P3_Stat']", $controller);
        $this->assertStringContainsString("(int) \$honorarium->{\$definition['status']} !== 1", $controller);
        $this->assertStringContainsString('Tidak ada honorarium berstatus Available', $controller);
        $this->assertStringContainsString("Route::get('/tanggal/{date}/tanda-terima-pdf'", $routes);
        $this->assertStringContainsString('fa-file-pdf-o', $listView);
        $this->assertStringContainsString('TANDA TERIMA HONORARIUM', $pdfView);
        $this->assertStringContainsString('TOTAL HONORARIUM', $pdfView);
        $this->assertStringContainsString('Penerima,', $pdfView);
        $this->assertStringContainsString('page-break-after: always', $pdfView);
        $this->assertStringContainsString("publicImageDataUri('images/branding/umi-pdf.jpg')", $pdfView);
        $this->assertStringContainsString("publicImageDataUri('images/branding/fikom-pdf.jpg')", $pdfView);
    }
}
