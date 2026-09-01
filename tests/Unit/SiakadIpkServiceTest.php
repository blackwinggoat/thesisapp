<?php

namespace Tests\Unit;

use App\Services\SiakadIpkService;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../app/Services/SiakadIpkService.php';

class SiakadIpkServiceTest extends TestCase
{
    public function testItCalculatesIpkFromActiveFinalCourseRows()
    {
        $result = SiakadIpkService::calculateFromRows([
            ['sks' => 3, 'nilai_mutu' => 4],
            ['sks' => 2, 'nilai_mutu' => 3],
            ['sks' => 0, 'nilai_mutu' => 4],
            ['sks' => 2, 'nilai_mutu' => null],
        ]);

        $this->assertTrue($result['ok']);
        $this->assertSame(5, $result['total_sks']);
        $this->assertSame(2, $result['course_count']);
        $this->assertSame(3.6, $result['ipk']);
    }

    public function testItRejectsRowsWithoutUsableFinalGrades()
    {
        $result = SiakadIpkService::calculateFromRows([
            ['sks' => 0, 'nilai_mutu' => 4],
            ['sks' => 3, 'nilai_mutu' => 4.5],
        ]);

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('belum memiliki nilai akhir aktif', $result['message']);
    }
}
