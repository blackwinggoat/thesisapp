<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AssessmentFormExperienceTest extends TestCase
{
    public function testUjianMejaUsesSlidersWhileKeepingExistingScoreFields()
    {
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/dosen/detailhasil_ujianmeja.blade.php');
        $proposalView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/dosen/detailhasil_proposal.blade.php');
        $slider = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/dosen/partials/score_slider.blade.php');
        $styles = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/dosen/partials/score_slider_styles.blade.php');

        $this->assertSame(5, substr_count($view, "partials.score_slider',"));
        $this->assertSame(5, substr_count($proposalView, "partials.score_slider',"));
        $this->assertStringContainsString("'minimum' => 6, 'maximum' => 10", $view);
        $this->assertStringContainsString("'minimum' => 20, 'maximum' => 30", $view);
        $this->assertStringContainsString("'minimum' => 16, 'maximum' => 25", $proposalView);
        $this->assertStringContainsString("'minimum' => 15, 'maximum' => 20", $proposalView);
        $this->assertStringContainsString('type="range"', $slider);
        $this->assertStringContainsString('step="0.5"', $slider);
        $this->assertStringContainsString('class="assessment-score-value" name="{{ $name }}"', $slider);
        $this->assertStringContainsString('.assessment-score-slider__value', $styles);
        $this->assertStringContainsString('$sliderControls.on(\'input change\'', $view);
        $this->assertStringContainsString('$sliderControls.on(\'input change\'', $proposalView);
        $this->assertStringContainsString('updateSliderVisual', $view);
        $this->assertStringContainsString('updateSliderVisual', $proposalView);
    }

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
