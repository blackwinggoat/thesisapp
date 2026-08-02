<style>
    .assessment-score-slider { max-width: 100%; padding: 8px 20px 0; width: 100%; }
    .assessment-score-slider__track { height: 42px; position: relative; width: 100%; }
    .assessment-score-slider__rail { background: #cbd5db; border-radius: 8px; height: 8px; left: 0; overflow: hidden; position: absolute; right: 0; top: 17px; }
    .assessment-score-slider__fill { background: linear-gradient(90deg, #d64545 0%, #f0b429 50%, #249a5a 100%); display: block; height: 100%; width: 0; }
    .assessment-score-slider__control { -webkit-appearance: none; appearance: none; background: transparent; cursor: pointer; height: 42px; left: 0; margin: 0; opacity: 0; outline: none; position: absolute; top: 0; width: 100%; z-index: 3; }
    .assessment-score-slider__control::-webkit-slider-runnable-track { background: transparent; border: 0; height: 42px; }
    .assessment-score-slider__control::-moz-range-track { background: transparent; border: 0; height: 42px; }
    .assessment-score-slider__control::-webkit-slider-thumb { -webkit-appearance: none; appearance: none; background: transparent; border: 0; border-radius: 50%; height: 42px; width: 42px; }
    .assessment-score-slider__control::-moz-range-thumb { background: transparent; border: 0; border-radius: 50%; height: 42px; width: 42px; }
    .assessment-score-slider__value { background: var(--assessment-score-color, #159f78); border: 2px solid #fff; border-radius: 50%; box-shadow: 0 1px 4px rgba(28, 65, 77, .25); color: #fff; font-size: 12px; font-weight: 700; height: 42px; left: 0; line-height: 38px; pointer-events: none; position: absolute; text-align: center; top: 0; transform: translateX(-50%); width: 42px; z-index: 2; }
    .assessment-score-slider.is-empty .assessment-score-slider__value { background: #778792; }
    .assessment-score-slider.is-focused .assessment-score-slider__value { box-shadow: 0 0 0 3px var(--assessment-score-shadow, rgba(21, 159, 120, .25)); }
    .assessment-score-slider__scale { color: #687986; display: flex; font-size: 12px; font-weight: 700; justify-content: space-between; margin-top: 2px; }
    @media (max-width: 767px) {
        .assessment-score-slider { padding-left: 12px; padding-right: 12px; }
    }
</style>
