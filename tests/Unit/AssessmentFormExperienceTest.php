<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AssessmentFormExperienceTest extends TestCase
{
    public function testAssessmentFormsRefreshScoresAndProtectUnsavedChanges()
    {
        foreach (['detailhasil_ujianmeja', 'detailhasil_proposal'] as $viewName) {
            $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/dosen/' . $viewName . '.blade.php');

            $this->assertStringContainsString("on('change', updateAssessmentSummary)", $view);
            $this->assertStringContainsString('Lengkapi semua komponen nilai sebelum mengirim penilaian.', $view);
            $this->assertStringContainsString('Nilai atau saran yang diubah belum disimpan. Keluar tanpa menyimpan?', $view);
            $this->assertStringContainsString("window.addEventListener('beforeunload'", $view);
            $this->assertStringContainsString('window.assessmentFormSubmitting = true;', $view);
        }
    }
}
