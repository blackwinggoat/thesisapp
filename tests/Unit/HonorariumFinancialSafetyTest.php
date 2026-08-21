<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HonorariumFinancialSafetyTest extends TestCase
{
    public function testNewHonorariumUsesStableExamSourceAndScheduledDate()
    {
        $migration = file_get_contents(__DIR__ . '/../../database/migrations/2026_08_21_070000_add_source_key_to_trt_honorium_table.php');
        $prodi = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');

        $this->assertStringContainsString('$table->string(\'source_key\', 120)->nullable()->unique()', $migration);
        $this->assertStringContainsString('createHonorariumForConfirmedExam', $prodi);
        $this->assertStringContainsString('->where(\'jadwal.pendaftaran_id\', $pendaftaranId)', $prodi);
        $this->assertStringContainsString('\'source_key\' => $sourceKey', $prodi);
        $this->assertStringContainsString('\'date\' => Carbon::parse($tanggalUjian)->toDateString()', $prodi);
    }

    public function testFinancialChangesAreLockedAndPaidRecordsAreProtected()
    {
        $keuangan = file_get_contents(__DIR__ . '/../../app/Http/Controllers/KeuanganFakultas.php');
        $dosen = file_get_contents(__DIR__ . '/../../app/Http/Controllers/dosen.php');

        $this->assertStringContainsString('honorariumNeedsTypeAssignment', $keuangan);
        $this->assertStringContainsString('honorariumHasPaidRole', $keuangan);
        $this->assertStringContainsString('lockForUpdate()', $keuangan);
        $this->assertStringContainsString('COUNT(DISTINCT C_NPM) as total_mahasiswa', $keuangan);
        $this->assertStringContainsString('getHonorariumAssignmentsForDosen', $dosen);
        $this->assertStringContainsString('clone $record', $dosen);
        $this->assertStringContainsString('Anda tidak memiliki akses untuk mengonfirmasi penugasan honorarium ini.', $dosen);
    }
}
