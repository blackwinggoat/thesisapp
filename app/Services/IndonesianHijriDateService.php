<?php

namespace App\Services;

use Carbon\Carbon;

class IndonesianHijriDateService
{
    /**
     * Awal bulan Kalender Hijriah Indonesia 2026 berdasarkan kalender resmi Kemenag.
     */
    protected $officialMonthStarts2026 = [
        ['date' => '2025-12-21', 'month' => 'Rajab', 'year' => 1447],
        ['date' => '2026-01-20', 'month' => 'Syaban', 'year' => 1447],
        ['date' => '2026-02-19', 'month' => 'Ramadan', 'year' => 1447],
        ['date' => '2026-03-21', 'month' => 'Syawal', 'year' => 1447],
        ['date' => '2026-04-19', 'month' => 'Dzulqaidah', 'year' => 1447],
        ['date' => '2026-05-18', 'month' => 'Dzulhijjah', 'year' => 1447],
        ['date' => '2026-06-16', 'month' => 'Muharram', 'year' => 1448],
        ['date' => '2026-07-16', 'month' => 'Safar', 'year' => 1448],
        ['date' => '2026-08-14', 'month' => 'Rabiul Awal', 'year' => 1448],
        ['date' => '2026-09-13', 'month' => 'Rabiul Akhir', 'year' => 1448],
        ['date' => '2026-10-12', 'month' => 'Jumadil Awal', 'year' => 1448],
        ['date' => '2026-11-11', 'month' => 'Jumadil Akhir', 'year' => 1448],
        ['date' => '2026-12-10', 'month' => 'Rajab', 'year' => 1448],
    ];

    protected $monthNames = [
        1 => 'Muharram',
        2 => 'Safar',
        3 => 'Rabiul Awal',
        4 => 'Rabiul Akhir',
        5 => 'Jumadil Awal',
        6 => 'Jumadil Akhir',
        7 => 'Rajab',
        8 => 'Syaban',
        9 => 'Ramadan',
        10 => 'Syawal',
        11 => 'Dzulqaidah',
        12 => 'Dzulhijjah',
    ];

    public function format($date)
    {
        $target = $date instanceof Carbon ? $date->copy() : Carbon::parse($date);
        $target->startOfDay();

        if ((int) $target->format('Y') === 2026) {
            return $this->formatOfficial2026($target);
        }

        return $this->formatFallback($target);
    }

    protected function formatOfficial2026(Carbon $target)
    {
        foreach (array_reverse($this->officialMonthStarts2026) as $monthStart) {
            $start = Carbon::createFromFormat('Y-m-d', $monthStart['date'])->startOfDay();
            if ($target->lt($start)) {
                continue;
            }

            return sprintf(
                '%02d %s %d H',
                $start->diffInDays($target) + 1,
                $monthStart['month'],
                $monthStart['year']
            );
        }

        return $this->formatFallback($target);
    }

    protected function formatFallback(Carbon $target)
    {
        if (class_exists('IntlDateFormatter')) {
            $formatter = new \IntlDateFormatter(
                'id_ID@calendar=islamic-umalqura',
                \IntlDateFormatter::NONE,
                \IntlDateFormatter::NONE,
                $target->getTimezone()->getName(),
                \IntlDateFormatter::TRADITIONAL,
                'dd MMMM yyyy'
            );
            $formatted = $formatter->format($target);
            if ($formatted !== false) {
                return $this->normalizeMonthNames($formatted) . ' H';
            }
        }

        return $this->formatCivilHijri($target);
    }

    protected function normalizeMonthNames($formatted)
    {
        return str_ireplace([
            'Muharam',
            'Rabiulawal',
            'Rabiulakhir',
            'Jumadilawal',
            'Jumadilakhir',
            'Syakban',
            'Zulkaidah',
            'Zulhijah',
        ], [
            'Muharram',
            'Rabiul Awal',
            'Rabiul Akhir',
            'Jumadil Awal',
            'Jumadil Akhir',
            'Syaban',
            'Dzulqaidah',
            'Dzulhijjah',
        ], $formatted);
    }

    protected function formatCivilHijri(Carbon $target)
    {
        $year = (int) $target->format('Y');
        $month = (int) $target->format('n');
        $day = (int) $target->format('j');
        $a = (int) floor((14 - $month) / 12);
        $y = $year + 4800 - $a;
        $m = $month + (12 * $a) - 3;
        $julianDay = $day
            + (int) floor(((153 * $m) + 2) / 5)
            + (365 * $y)
            + (int) floor($y / 4)
            - (int) floor($y / 100)
            + (int) floor($y / 400)
            - 32045;

        $l = $julianDay - 1948440 + 10632;
        $n = (int) floor(($l - 1) / 10631);
        $l = $l - (10631 * $n) + 354;
        $j = ((int) floor((10985 - $l) / 5316) * (int) floor((50 * $l) / 17719))
            + ((int) floor($l / 5670) * (int) floor((43 * $l) / 15238));
        $l = $l
            - ((int) floor((30 - $j) / 15) * (int) floor((17719 * $j) / 50))
            - ((int) floor($j / 16) * (int) floor((15238 * $j) / 43))
            + 29;
        $hijriMonth = (int) floor((24 * $l) / 709);
        $hijriDay = $l - (int) floor((709 * $hijriMonth) / 24);
        $hijriYear = (30 * $n) + $j - 30;

        return sprintf(
            '%02d %s %d H',
            $hijriDay,
            $this->monthNames[$hijriMonth],
            $hijriYear
        );
    }
}
