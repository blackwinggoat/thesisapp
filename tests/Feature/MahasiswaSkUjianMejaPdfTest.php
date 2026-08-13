<?php

namespace Tests\Feature;

use Tests\TestCase;

class MahasiswaSkUjianMejaPdfTest extends TestCase
{
    public function testPdfEndpointsAreProtectedByStudentMiddleware()
    {
        $this->get('/mhs/surat_sk_ujian_meja_pdf/contoh')->assertStatus(302);
        $this->get('/mhs/surat_sk_pembimbing_pdf/contoh')->assertStatus(302);
        $this->get('/mhs/surat_sk_proposal_pdf/1')->assertStatus(302);
    }
}
