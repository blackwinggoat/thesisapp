<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class StudentExamRequirementBulkSaveTest extends TestCase
{
    public function testProposalAndFinalExamUseOneBulkSaveAction()
    {
        $views = [
            file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/mhs/signup_proposal.blade.php'),
            file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/mhs/signup_ujianmeja.blade.php'),
        ];

        foreach ($views as $view) {
            $this->assertStringContainsString('action="{{url("mhs/syarat_ujianpost_all")}}"', $view);
            $this->assertStringContainsString('Simpan Semua Persyaratan', $view);
            $this->assertStringContainsString("\$submittedRequirements->get(\$value->syarat_ujian_id)", $view);
            $this->assertStringNotContainsString('data-formaction="{{url("mhs/syarat_ujianpost")}}"', $view);
            $this->assertStringNotContainsString('input[name=sui]', $view);
        }
    }

    public function testBulkSaveValidatesRequirementOwnershipAndUsesATransaction()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/mhs.php');

        $this->assertStringNotContainsString('return $request;', $controller);
        $this->assertStringContainsString("->where('tipe_ujian', \$examType)", $controller);
        $this->assertStringContainsString("in_array(\$requirementId, \$allowedRequirementIds, true)", $controller);
        $this->assertStringContainsString("preg_match('/^https?:\\/\\//i', \$link)", $controller);
        $this->assertStringContainsString('DB::transaction(function () use ($normalizedLinks, $nim)', $controller);
        $this->assertStringContainsString("(int) \$existing->status === 0", $controller);
    }
}
