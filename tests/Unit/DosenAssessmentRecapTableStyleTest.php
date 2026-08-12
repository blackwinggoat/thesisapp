<?php

namespace Tests\Unit;

use Tests\TestCase;

class DosenAssessmentRecapTableStyleTest extends TestCase
{
    public function testRecapTableKeepsOnlyTheHeaderColored()
    {
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/dosen/rekap_hasil_penilaian.blade.php');

        $this->assertStringContainsString('.assessment-recap-table th { background: #ffff00;', $view);
        $this->assertStringContainsString('.assessment-recap-table tbody td.marker { background: #ffffff !important; }', $view);
        $this->assertStringContainsString('.assessment-recap-table tbody tr:nth-of-type(odd)', $view);
    }
}
