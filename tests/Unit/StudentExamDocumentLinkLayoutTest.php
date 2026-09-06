<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class StudentExamDocumentLinkLayoutTest extends TestCase
{
    public function testProposalAndFinalExamDocumentTablesUseTheSameBalancedColumns()
    {
        $styles = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/mhs/partials/exam_document_table_styles.blade.php');
        $views = [
            file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/mhs/signup_proposal.blade.php'),
            file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/mhs/signup_ujianmeja.blade.php'),
        ];

        $this->assertStringContainsString('table-layout: fixed;', $styles);
        $this->assertStringContainsString('width: 35%;', $styles);
        $this->assertStringContainsString('width: 4%;', $styles);
        $this->assertStringContainsString('width: 31%;', $styles);
        $this->assertStringContainsString('width: 8%;', $styles);
        $this->assertStringContainsString('width: 10%;', $styles);
        $this->assertStringContainsString('width: 6%;', $styles);
        $this->assertStringContainsString('display: block;', $styles);
        $this->assertStringContainsString('max-width: none;', $styles);
        $this->assertStringContainsString('width: 100% !important;', $styles);

        foreach ($views as $view) {
            $this->assertStringContainsString("@include('tugasakhir.mhs.partials.exam_document_table_styles')", $view);
            $this->assertStringContainsString('class="table table-striped table-hover exam-requirements-table"', $view);
            $this->assertStringContainsString('class="document-link-column">Link Dokumen</th>', $view);
            $this->assertStringContainsString('document-number-column document-compact-column', $view);
            $this->assertStringContainsString('document-name-column', $view);
            $this->assertStringContainsString('document-action-column document-compact-column', $view);
            $this->assertStringContainsString('document-link-input', $view);
            $this->assertStringNotContainsString('<div class="col-lg-5">', $view);
        }
    }
}
