<?php

namespace App\Services;

class ReportTrendChartSvgRenderer
{
    public function render(array $data, array $series, $selectedPeriod = null)
    {
        $data = array_values(array_map(function ($row) {
            return is_array($row) ? $row : (array) $row;
        }, $data));
        $series = array_values($series);
        $selectedPeriod = trim((string) $selectedPeriod);
        $width = 720;
        $height = 260;
        $left = 50;
        $right = 14;
        $top = 26;
        $bottom = 52;
        $plotWidth = $width - $left - $right;
        $plotHeight = $height - $top - $bottom;
        $maxValue = $this->maxValue($data, $series);
        $scaleMax = $this->niceScaleMax($maxValue);
        $selectedIndex = $this->selectedIndex($data, $selectedPeriod);
        $clipId = 'trend-' . substr(sha1(json_encode([$data, $series, $selectedPeriod])), 0, 10);

        $svg = [];
        $svg[] = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">';
        $svg[] = '<rect width="100%" height="100%" fill="#ffffff"/>';
        $svg[] = '<defs><clipPath id="' . $clipId . '"><rect x="' . $left . '" y="' . $top . '" width="' . $plotWidth . '" height="' . $plotHeight . '"/></clipPath></defs>';

        if (empty($data) || empty($series) || $maxValue <= 0) {
            $svg[] = '<rect x="' . $left . '" y="' . $top . '" width="' . $plotWidth . '" height="' . $plotHeight . '" fill="#f8fafc" stroke="#d5dde5"/>';
            $svg[] = '<text x="' . ($width / 2) . '" y="' . ($height / 2) . '" text-anchor="middle" font-family="DejaVu Sans, sans-serif" font-size="14" fill="#64748b">Belum ada data lulusan.</text>';
            $svg[] = '</svg>';

            return 'data:image/svg+xml;base64,' . base64_encode(implode('', $svg));
        }

        $xPositions = $this->xPositions(count($data), $left, $plotWidth);

        if ($selectedIndex !== null) {
            $activeX = $xPositions[$selectedIndex];
            $spacing = count($xPositions) > 1 ? abs($xPositions[1] - $xPositions[0]) : $plotWidth;
            $bandWidth = min(44, max(18, $spacing * 0.62));
            $svg[] = '<rect x="' . $this->number($activeX - ($bandWidth / 2)) . '" y="' . $top . '" width="' . $this->number($bandWidth) . '" height="' . $plotHeight . '" fill="#fff7d6"/>';
            $svg[] = '<line x1="' . $this->number($activeX) . '" y1="' . $top . '" x2="' . $this->number($activeX) . '" y2="' . ($top + $plotHeight) . '" stroke="#d97706" stroke-width="2" stroke-dasharray="5 4"/>';
            $activeLabelX = min($width - 54, max(54, $activeX));
            $svg[] = '<rect x="' . $this->number($activeLabelX - 48) . '" y="3" width="96" height="18" rx="3" fill="#d97706"/>';
            $svg[] = '<text x="' . $this->number($activeLabelX) . '" y="16" text-anchor="middle" font-family="DejaVu Sans, sans-serif" font-size="10" font-weight="700" fill="#ffffff">AKTIF: ' . $this->escape($selectedPeriod) . '</text>';
        }

        $tickCount = 5;
        for ($tick = 0; $tick <= $tickCount; $tick++) {
            $value = ($scaleMax / $tickCount) * $tick;
            $y = $top + $plotHeight - (($value / $scaleMax) * $plotHeight);
            $svg[] = '<line x1="' . $left . '" y1="' . $this->number($y) . '" x2="' . ($left + $plotWidth) . '" y2="' . $this->number($y) . '" stroke="#d8e0e8" stroke-width="1"/>';
            $svg[] = '<text x="' . ($left - 8) . '" y="' . $this->number($y + 4) . '" text-anchor="end" font-family="DejaVu Sans, sans-serif" font-size="10" fill="#52606d">' . number_format($value, 0, ',', '.') . '</text>';
        }

        $svg[] = '<g clip-path="url(#' . $clipId . ')">';
        foreach ($series as $seriesItem) {
            $key = isset($seriesItem['key']) ? (string) $seriesItem['key'] : '';
            $color = $this->safeColor(isset($seriesItem['color']) ? $seriesItem['color'] : '#4b5563');
            $points = [];
            foreach ($data as $index => $row) {
                $value = max(0, (float) (isset($row[$key]) ? $row[$key] : 0));
                $y = $top + $plotHeight - (($value / $scaleMax) * $plotHeight);
                $points[] = $this->number($xPositions[$index]) . ',' . $this->number($y);
            }
            $svg[] = '<polyline points="' . implode(' ', $points) . '" fill="none" stroke="' . $color . '" stroke-width="2.2" stroke-linejoin="round" stroke-linecap="round"/>';

            foreach ($data as $index => $row) {
                $value = max(0, (float) (isset($row[$key]) ? $row[$key] : 0));
                $y = $top + $plotHeight - (($value / $scaleMax) * $plotHeight);
                $radius = $selectedIndex === $index ? 4.6 : 2.5;
                $svg[] = '<circle cx="' . $this->number($xPositions[$index]) . '" cy="' . $this->number($y) . '" r="' . $radius . '" fill="' . $color . '" stroke="#ffffff" stroke-width="1.2"/>';
                if ($selectedIndex === $index) {
                    $svg[] = '<circle cx="' . $this->number($xPositions[$index]) . '" cy="' . $this->number($y) . '" r="6.3" fill="none" stroke="#d97706" stroke-width="1.4"/>';
                }
            }
        }
        $svg[] = '</g>';

        foreach ($data as $index => $row) {
            $period = isset($row['period']) ? (string) $row['period'] : '';
            $x = $xPositions[$index];
            $active = $selectedIndex === $index;
            $svg[] = '<text x="' . $this->number($x) . '" y="' . ($top + $plotHeight + 15) . '" transform="rotate(-35 ' . $this->number($x) . ' ' . ($top + $plotHeight + 15) . ')" text-anchor="end" font-family="DejaVu Sans, sans-serif" font-size="9" font-weight="' . ($active ? '700' : '400') . '" fill="' . ($active ? '#9a3412' : '#52606d') . '">' . $this->escape($period) . '</text>';
        }

        $svg[] = '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode(implode('', $svg));
    }

    protected function maxValue(array $data, array $series)
    {
        $maximum = 0;
        foreach ($data as $row) {
            foreach ($series as $seriesItem) {
                $key = isset($seriesItem['key']) ? (string) $seriesItem['key'] : '';
                $maximum = max($maximum, (float) (isset($row[$key]) ? $row[$key] : 0));
            }
        }

        return $maximum;
    }

    protected function niceScaleMax($maximum)
    {
        if ($maximum <= 0) {
            return 5;
        }

        $roughStep = $maximum / 5;
        $magnitude = pow(10, floor(log10($roughStep)));
        $normalized = $roughStep / $magnitude;

        if ($normalized <= 1) {
            $niceStep = 1;
        } elseif ($normalized <= 2) {
            $niceStep = 2;
        } elseif ($normalized <= 2.5) {
            $niceStep = 2.5;
        } elseif ($normalized <= 5) {
            $niceStep = 5;
        } else {
            $niceStep = 10;
        }

        return ceil($maximum / ($niceStep * $magnitude)) * ($niceStep * $magnitude);
    }

    protected function selectedIndex(array $data, $selectedPeriod)
    {
        if ($selectedPeriod === '') {
            return null;
        }

        foreach ($data as $index => $row) {
            if ((string) (isset($row['period']) ? $row['period'] : '') === $selectedPeriod) {
                return $index;
            }
        }

        return null;
    }

    protected function xPositions($count, $left, $plotWidth)
    {
        if ($count <= 1) {
            return [$left + ($plotWidth / 2)];
        }

        $positions = [];
        for ($index = 0; $index < $count; $index++) {
            $positions[] = $left + (($plotWidth / ($count - 1)) * $index);
        }

        return $positions;
    }

    protected function safeColor($color)
    {
        return preg_match('/^#[0-9a-f]{6}$/i', (string) $color) ? $color : '#4b5563';
    }

    protected function escape($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    protected function number($value)
    {
        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
    }
}
