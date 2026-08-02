<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class MahasiswaBimbinganSkFilterTest extends TestCase
{
    public function testDosenBimbinganListRequiresPublishedPembimbingSk()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/dosen.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/dosen/detail_pembimbing.blade.php');

        $this->assertStringContainsString("whereNotNull('mst_sk_pembimbing.nomor_sk')", $controller);
        $this->assertStringContainsString("TRIM(mst_sk_pembimbing.nomor_sk) <> ''", $controller);
        $this->assertStringContainsString("'trt_bimbingan.jenis_tugas_akhir_id'", $controller);
        $this->assertStringContainsString('<th>Jenis Tugas Akhir</th>', $view);
        $this->assertStringContainsString('jenisTugasAkhirBadge($value->jenis_tugas_akhir_id ?? null)', $view);
        $this->assertLessThan(
            strpos($view, '<th>NIM</th>'),
            strpos($view, '<th>Jenis Tugas Akhir</th>')
        );
    }
}
