<?php

namespace Tests\Unit;

use App\Services\SiakadMahasiswaService;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../app/Services/SiakadMahasiswaService.php';

class SiakadMahasiswaServiceTest extends TestCase
{
    public function testItParsesOnlySupportedStudentNumbers()
    {
        $identity = SiakadMahasiswaService::parseNim('13120250054');

        $this->assertSame([
            'nim' => '13120250054',
            'program_code' => '131',
            'angkatan' => '2025',
        ], $identity);
        $this->assertNull(SiakadMahasiswaService::parseNim('13220250054'));
        $this->assertNull(SiakadMahasiswaService::parseNim('1312025054'));
    }

    public function testItNormalizesOnlyMatchingSiakadStudents()
    {
        $student = SiakadMahasiswaService::normalizeSourceStudent([
            'nim' => '13020250206',
            'nama_mahasiswa' => 'NUR HIKMA',
            'angkatan' => '2025',
            'jkel' => 'P',
            'tempatlahir' => 'BABANA',
            'tgllahir' => '2004-04-07',
        ], '130', '2025');

        $this->assertSame([
            'nim' => '13020250206',
            'name' => 'NUR HIKMA',
            'program_code' => '130',
            'angkatan' => '2025',
            'gender' => 'P',
            'birth_place' => 'BABANA',
            'birth_date' => '2004-04-07',
        ], $student);

        $this->assertNull(SiakadMahasiswaService::normalizeSourceStudent([
            'nim' => '13120250054',
            'nama_mahasiswa' => 'HAERUDDIN',
            'angkatan' => '2025',
        ], '130', '2025'));
    }

    public function testItBuildsTheInternalMasterRecordWithoutChangingProgramMapping()
    {
        $record = SiakadMahasiswaService::buildMasterRecord([
            'nim' => '13120250054',
            'name' => 'HAERUDDIN',
            'angkatan' => '2025',
            'gender' => 'L',
            'birth_place' => 'PARIA',
            'birth_date' => '2001-10-02',
        ], '131', '2026-11-06 10:00:00');

        $this->assertSame('091002', $record['C_KODE_PT']);
        $this->assertSame('91000', $record['C_KODE_FAKULTAS']);
        $this->assertSame('57201', $record['C_KODE_PRODI']);
        $this->assertSame('57201-K-GEN', $record['C_KODE_KONSENTRASI']);
        $this->assertSame('20251', $record['THN_SEMESTER_AWAL_MASUK']);
        $this->assertSame('A', $record['C_KODE_STATUS_AKTIF_MHS']);
        $this->assertSame('2026-11-06 10:00:00', $record['C_DATE']);
    }
}
