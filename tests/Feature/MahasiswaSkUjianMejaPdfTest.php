<?php

namespace Tests\Feature;

use Tests\TestCase;

class MahasiswaSkUjianMejaPdfTest extends TestCase
{
    public function testPdfEndpointIsProtectedByStudentMiddleware()
    {
        $response = $this->get('/mhs/surat_sk_ujian_meja_pdf/contoh');

        $response->assertStatus(302);
    }
}
