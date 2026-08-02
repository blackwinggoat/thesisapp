@php
    $score = (float) str_replace(',', '.', $value ?? 0);
    $hasScore = $score > 0;
    $formattedScore = number_format($score, 1, '.', '');
    $sliderValue = $hasScore ? min($maximum, max($minimum, $score)) : $minimum;
@endphp

<div class="assessment-score-slider{{ $hasScore ? '' : ' is-empty' }}" data-score-slider>
    <input type="hidden" class="assessment-score-value" name="{{ $name }}" value="{{ $hasScore ? $formattedScore : '0' }}">
    <div class="assessment-score-slider__track">
        <span class="assessment-score-slider__rail"><span class="assessment-score-slider__fill"></span></span>
        <output class="assessment-score-slider__value" aria-live="polite">{{ $hasScore ? $formattedScore : '--' }}</output>
        <input
            id="{{ $name }}"
            type="range"
            class="assessment-score-slider__control"
            min="{{ $minimum }}"
            max="{{ $maximum }}"
            step="0.5"
            value="{{ $sliderValue }}"
            aria-label="Nilai {{ $label }}"
            aria-valuetext="{{ $hasScore ? $formattedScore : 'Belum diisi' }}">
    </div>
    <div class="assessment-score-slider__scale" aria-hidden="true">
        <span>{{ number_format($minimum, 1, '.', '') }}</span>
        <span>{{ number_format($maximum, 1, '.', '') }}</span>
    </div>
</div>
