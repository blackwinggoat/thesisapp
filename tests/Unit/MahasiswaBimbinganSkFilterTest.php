<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class MahasiswaBimbinganSkFilterTest extends TestCase
{
    public function testDosenBimbinganListRequiresPublishedPembimbingSk()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/dosen.php');

        $this->assertStringContainsString("whereNotNull('mst_sk_pembimbing.nomor_sk')", $controller);
        $this->assertStringContainsString("TRIM(mst_sk_pembimbing.nomor_sk) <> ''", $controller);
    }
}
