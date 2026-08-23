<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class DosenHonorariumConfirmationWorkflowTest extends TestCase
{
    public function testLecturerConfirmsHonorariumFromDateListInsteadOfDetailModal()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/dosen.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/dosen/honorarium.blade.php');

        $this->assertStringContainsString('Konfirmasi Terima', $view);
        $this->assertStringContainsString('name="honorarium_dates[]"', $view);
        $this->assertStringContainsString('Konfirmasi Telah Terima', $view);
        $this->assertStringContainsString('id="confirmHonorariumButton"', $view);
        $this->assertStringContainsString('honorarium-date-confirmation', $view);
        $this->assertStringNotContainsString('Simpan Konfirmasi Pembayaran', $view);
        $this->assertStringNotContainsString('<th class="text-center">Konfirmasi</th>', $view);
        $this->assertStringNotContainsString('Sudah diterima', $view);
        $this->assertStringContainsString("input('honorarium_dates'", $controller);
        $this->assertStringContainsString('getHonorariumAssignmentsForDosen($kodeDosen)', $controller);
        $this->assertStringContainsString('konfirmasiHonorariumDosen', $controller);
        $this->assertStringContainsString('dipindahkan ke riwayat', $controller);
    }
}
