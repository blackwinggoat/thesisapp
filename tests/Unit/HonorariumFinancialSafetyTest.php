<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HonorariumFinancialSafetyTest extends TestCase
{
    public function testNewHonorariumUsesStableExamSourceAndScheduledDate()
    {
        $migration = file_get_contents(__DIR__ . '/../../database/migrations/2026_08_21_070000_add_source_key_to_trt_honorium_table.php');
        $examTypeMigration = file_get_contents(__DIR__ . '/../../database/migrations/2026_08_21_083000_add_exam_type_to_trt_honorium_table.php');
        $prodi = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');

        $this->assertStringContainsString('$table->string(\'source_key\', 120)->nullable()->unique()', $migration);
        $this->assertStringContainsString('$table->unsignedTinyInteger(\'exam_type\')->nullable()->index()', $examTypeMigration);
        $this->assertStringContainsString("tipe_ujian IN ('0', '2')", $examTypeMigration);
        $this->assertStringContainsString('createHonorariumForConfirmedExam', $prodi);
        $this->assertStringContainsString('->where(\'jadwal.pendaftaran_id\', $pendaftaranId)', $prodi);
        $this->assertStringContainsString('\'source_key\' => $sourceKey', $prodi);
        $this->assertStringContainsString('\'exam_type\' => (int) $tipeUjian', $prodi);
        $this->assertStringContainsString('\'date\' => Carbon::parse($tanggalUjian)->toDateString()', $prodi);
    }

    public function testFinancialChangesAreLockedAndPaidRecordsAreProtected()
    {
        $keuangan = file_get_contents(__DIR__ . '/../../app/Http/Controllers/KeuanganFakultas.php');
        $dosen = file_get_contents(__DIR__ . '/../../app/Http/Controllers/dosen.php');
        $detailView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/honorarium_detail.blade.php');
        $listView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/honorarium.blade.php');

        $this->assertStringContainsString('honorariumNeedsTypeAssignment', $keuangan);
        $this->assertStringContainsString('honorariumHasPaidRole', $keuangan);
        $this->assertStringContainsString('honorariumHasAnyRoleSql', $keuangan);
        $this->assertStringContainsString('honorariumTotalSql', $keuangan);
        $this->assertStringContainsString("MAX(' . \$this->honorariumTotalSql('honorarium', true)", $keuangan);
        $this->assertStringContainsString('lockForUpdate()', $keuangan);
        $this->assertStringContainsString('COUNT(DISTINCT honorarium.C_NPM) as total_mahasiswa', $keuangan);
        $this->assertStringContainsString("COUNT(DISTINCT CASE WHEN honorarium.C_NPM LIKE '130%'", $keuangan);
        $this->assertStringContainsString("COUNT(DISTINCT CASE WHEN honorarium.C_NPM LIKE '131%'", $keuangan);
        $this->assertStringContainsString('honorariumDenganJadwalQuery', $keuangan);
        $this->assertStringContainsString('jadwal.tgl_ujian as date', $keuangan);
        $this->assertStringContainsString('honorariumBelumTerhubungJadwalQuery', $keuangan);
        $this->assertStringContainsString("Tidak ada honorarium aktif dengan jadwal ujian pada tanggal", $keuangan);
        $this->assertStringContainsString("return '(' . implode(' OR ', \$conditions) . ')';", $keuangan);
        $this->assertStringContainsString('paging: false', $detailView);
        $this->assertStringContainsString('TI: {{ $honorarium->total_teknik_informatika }}', $listView);
        $this->assertStringContainsString('SI: {{ $honorarium->total_sistem_informasi }}', $listView);
        $this->assertStringContainsString('Total Honor Belum Dibayar', $listView);
        $this->assertStringContainsString('Total Honor', $detailView);
        $this->assertStringContainsString('Total Honor Seluruh Mahasiswa', $detailView);
        $this->assertStringContainsString('total-honorarium-tanggal', $detailView);
        $this->assertStringContainsString('data-total-honor', $detailView);
        $this->assertStringContainsString('updateTotalHonorariumTanggal', $detailView);
        $this->assertStringContainsString('modal-ks', $detailView);
        $this->assertStringNotContainsString('modal-ks-h', $detailView);
        $this->assertStringContainsString('Setup Tipe Ujian Otomatis', $detailView);
        $this->assertStringContainsString('getHonorariumAssignmentsForDosen', $dosen);
        $this->assertStringContainsString('clone $record', $dosen);
        $this->assertStringContainsString('Anda tidak memiliki akses untuk mengonfirmasi penugasan honorarium ini.', $dosen);
    }
}
