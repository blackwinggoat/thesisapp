<?php

namespace Tests\Unit;

use App\Services\IndonesianHijriDateService;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../app/Services/IndonesianHijriDateService.php';

class IndonesianHijriDateServiceTest extends TestCase
{
    public function testItMatchesOfficial2026YudisiumReferenceDates()
    {
        $service = new IndonesianHijriDateService();
        $dates = [
            '2026-04-27' => '09 Dzulqaidah 1447 H',
            '2026-07-31' => '16 Safar 1448 H',
            '2026-08-12' => '28 Safar 1448 H',
            '2026-08-13' => '29 Safar 1448 H',
            '2026-08-18' => '05 Rabiul Awal 1448 H',
        ];

        foreach ($dates as $date => $expected) {
            $this->assertSame($expected, $service->format($date), $date);
        }
    }

    public function testItCoversTheFull2026GregorianYear()
    {
        $service = new IndonesianHijriDateService();

        $this->assertSame('12 Rajab 1447 H', $service->format('2026-01-01'));
        $this->assertSame('22 Rajab 1448 H', $service->format('2026-12-31'));
    }
}
