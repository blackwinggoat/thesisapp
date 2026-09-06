<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class StudentExamDocumentLinkLayoutTest extends TestCase
{
    public function testFinalExamDocumentLinkUsesTheFullCellWithoutExpandingTheTable()
    {
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/mhs/signup_ujianmeja.blade.php');

        $this->assertStringContainsString('class="table table-striped table-hover exam-requirements-table"', $view);
        $this->assertStringContainsString('table-layout: fixed;', $view);
        $this->assertStringContainsString('width: 34%;', $view);
        $this->assertStringContainsString('class="document-link-column">Link Dokumen</th>', $view);
        $this->assertStringContainsString('document-link-input', $view);
        $this->assertStringContainsString('width: 100%;', $view);
        $this->assertStringNotContainsString('<div class="col-lg-5">', $view);
    }
}
