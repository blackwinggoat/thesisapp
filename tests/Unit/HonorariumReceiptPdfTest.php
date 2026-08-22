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
        $this->assertStringContainsString('honorarium_tanda_terima_pdf_dengan_status', $controller);
        $this->assertStringContainsString('honorarium_history_tanda_terima_pdf', $controller);
        $this->assertStringContainsString("request->input('tanggal')", $controller);
        $this->assertStringContainsString('honorariumTanggalEfektifSql()', $controller);
        $this->assertStringContainsString('->whereIn(DB::raw($tanggalSql), $tanggalTerpilih->all())', $controller);
        $this->assertStringContainsString('honorariumLunasDenganJadwalQuery()', $controller);
        $this->assertStringContainsString('jumlahSanksiPembayaranPadaTanggal', $controller);
        $this->assertStringContainsString('penyesuaianHonorPembimbing', $controller);
        $this->assertStringContainsString("'honor_awal' => \$honorAwal", $controller);
        $this->assertStringContainsString("'penyesuaian' => \$penyesuaian", $controller);
        $this->assertStringContainsString("'adjustments' => collect()", $controller);
        $this->assertStringContainsString("'tanggal' => collect()", $controller);
        $this->assertStringContainsString("'tanggal' => \$tanggalUjian", $controller);
        $this->assertStringContainsString('$report->adjustments->push($adjustment)', $controller);
        $this->assertStringContainsString('$report->adjustments = $report->adjustments', $controller);
        $this->assertStringContainsString("'total_penerimaan' => 0", $controller);
        $this->assertStringContainsString("'total_penyesuaian' => 0", $controller);
        $this->assertStringContainsString("'subtotal_penerimaan' => 0", $controller);
        $this->assertStringContainsString("'subtotal_penyesuaian' => 0", $controller);
        $this->assertStringContainsString("'subtotal_honor' => 0", $controller);
        $this->assertStringContainsString('PDF belum dapat dibuat. Tetapkan tipe honorarium', $controller);
        $this->assertStringContainsString('if (!$riwayat && $belumDitetapkan > 0)', $controller);
        $this->assertStringContainsString("'KS' => ['label' => 'Ketua Sidang', 'amount' => 'KS_H', 'status' => 'KS_Stat']", $controller);
        $this->assertStringContainsString("'P3' => ['label' => 'Penguji III', 'amount' => 'P3_H', 'status' => 'P3_Stat']", $controller);
        $this->assertStringContainsString("(int) \$honorarium->{\$definition['status']} !== \$statusDibutuhkan", $controller);
        $this->assertStringContainsString("'Tidak ada honorarium berstatus ' . \$statusLabel", $controller);
        $this->assertStringContainsString("Route::post('/tanda-terima-pdf'", $routes);
        $this->assertStringContainsString("Route::post('/history/tanda-terima-pdf'", $routes);
        $this->assertStringContainsString('fa-file-pdf-o', $listView);
        $this->assertStringContainsString('Download PDF Terpilih', $listView);
        $this->assertStringContainsString('name="tanggal[]"', $listView);
        $this->assertStringContainsString('select-all-honorarium-dates', $listView);
        $this->assertStringContainsString('TANDA TERIMA HONORARIUM', $pdfView);
        $this->assertStringContainsString('@foreach ($report->tanggal as $laporanTanggal)', $pdfView);
        $this->assertStringContainsString("\$report->tanggal->pluck('tanggal')", $pdfView);
        $this->assertStringContainsString('Honor Diterima', $pdfView);
        $this->assertStringContainsString('Tabel Penyesuaian Honorarium', $pdfView);
        $this->assertSame(1, substr_count($pdfView, 'Tabel Penyesuaian Honorarium'));
        $this->assertStringContainsString('@if ($report->adjustments->isNotEmpty())', $pdfView);
        $this->assertStringNotContainsString('@if ($laporanTanggal->adjustments->isNotEmpty())', $pdfView);
        $this->assertStringContainsString("helper::tgl_indo_lengkap(\$adjustment->tanggal)", $pdfView);
        $this->assertStringContainsString('SUBTOTAL PENERIMAAN', $pdfView);
        $this->assertStringNotContainsString('SUBTOTAL PENYESUAIAN', $pdfView);
        $this->assertStringContainsString('TOTAL DITERIMA', $pdfView);
        $this->assertStringContainsString('TOTAL SUBTOTAL PENERIMAAN', $pdfView);
        $this->assertStringContainsString('TOTAL PENYESUAIAN HONORARIUM', $pdfView);
        $this->assertStringNotContainsString('Rincian Penyesuaian Sanksi Kehadiran Pembimbing', $pdfView);
        $this->assertStringNotContainsString('Sanksi', $pdfView);
        $this->assertStringNotContainsString('<th class="adjustment">Perubahan</th>', $pdfView);
        $this->assertStringNotContainsString('<th class="note">Keterangan</th>', $pdfView);
        $this->assertStringNotContainsString('$item->keterangan', $pdfView);
        $this->assertStringContainsString('SUBTOTAL', $pdfView);
        $this->assertStringContainsString('TOTAL HONORARIUM SELURUH JADWAL', $pdfView);
        $this->assertStringContainsString('Penerima,', $pdfView);
        $this->assertStringContainsString('page-break-after: always', $pdfView);
        $this->assertStringContainsString("publicImageDataUri('images/branding/umi-pdf.jpg')", $pdfView);
        $this->assertStringContainsString("publicImageDataUri('images/branding/fikom-pdf.jpg')", $pdfView);
    }

    public function testAdvisorAttendancePenaltyMovesHonorToPresentCounterpart()
    {
        $controller = new \App\Http\Controllers\KeuanganFakultas;
        $method = new \ReflectionMethod($controller, 'penyesuaianHonorPembimbing');
        $method->setAccessible(true);

        $honorarium = (object) [
            'KS_H' => 0,
            'PU_H' => 100000,
            'PP_H' => 100000,
            'P1_H' => 0,
            'P2_H' => 0,
            'P3_H' => 0,
            'PU' => 'Dosen Pembimbing Utama',
            'PP' => 'Dosen Pembimbing Pendamping',
            'pembimbing_utama_hadir' => 0,
            'pembimbing_pendamping_hadir' => 1,
        ];

        $result = $method->invoke($controller, $honorarium, 25000);

        $this->assertSame(75000.0, $result['amounts']['PU']);
        $this->assertSame(125000.0, $result['amounts']['PP']);
        $this->assertSame(100000.0, $result['base_amounts']['PU']);
        $this->assertSame(100000.0, $result['base_amounts']['PP']);
        $this->assertSame('Pengurangan Rp 25.000 berdasarkan status kehadiran Pembimbing Utama.', $result['notes']['PU']);
        $this->assertSame('Penambahan Rp 25.000 berdasarkan status kehadiran Pembimbing Utama.', $result['notes']['PP']);
    }
}
