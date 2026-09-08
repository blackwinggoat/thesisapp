<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class MasterPembayaranJenisTugasAkhirViewTest extends TestCase
{
    public function testMasterPaymentScreenExposesMultiTypeConfiguration()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/KeuanganFakultas.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/master_pembayaran.blade.php');
        $detailView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/honorarium_detail.blade.php');
        $migration = file_get_contents(__DIR__ . '/../../database/migrations/2026_08_21_100000_create_master_pembayaran_honorarium_jenis_tugas_akhir_table.php');
        $kelasMigration = file_get_contents(__DIR__ . '/../../database/migrations/2026_08_21_110000_add_eksekutif_to_mst_pembayaran_honorarium_table.php');
        $cakupanMigration = file_get_contents(__DIR__ . '/../../database/migrations/2026_09_08_070000_add_exam_scope_to_honorarium_payment_master.php');

        $this->assertStringContainsString('masterPembayaranDenganJenisTugasAkhir', $controller);
        $this->assertStringContainsString('pembayaranBerlakuUntukJenisTugasAkhir', $controller);
        $this->assertStringContainsString('pembayaranBerlakuUntukKelasMahasiswa', $controller);
        $this->assertStringContainsString('id_pembayaran', $controller);
        $this->assertStringContainsString('Jenis Tugas Akhir yang Berlaku', $view);
        $this->assertStringContainsString('jenis_tugas_akhir_ids[]', $view);
        $this->assertStringContainsString('untuk_mahasiswa_eksekutif', $view);
        $this->assertStringContainsString('Cakupan Ujian', $view);
        $this->assertStringContainsString('name="cakupan_ujian"', $view);
        $this->assertStringContainsString('<option value="proposal">Proposal</option>', $view);
        $this->assertStringContainsString('<option value="ujian_meja">Ujian Meja</option>', $view);
        $this->assertStringContainsString('<option value="gabungan">Proposal + Ujian Meja</option>', $view);
        $this->assertStringNotContainsString('Proposal saja', $view);
        $this->assertStringNotContainsString('Ujian Meja saja', $view);
        $this->assertStringContainsString('pembayaranBerlakuUntukTahapUjian', $controller);
        $this->assertStringContainsString("'cakupan_ujian' => 'required|in:proposal,ujian_meja,gabungan'", $controller);
        $this->assertStringContainsString('jenis_tugas_akhir_ids', $detailView);
        $this->assertStringContainsString('id_pembayaran', $detailView);
        $this->assertStringContainsString('mst_pembayaran_honorarium_jenis_tugas_akhir', $migration);
        $this->assertStringContainsString('mst_pembayaran_honorarium_jenis_ta_unique', $migration);
        $this->assertStringContainsString("boolean('untuk_mahasiswa_eksekutif')->default(0)", $kelasMigration);
        $this->assertStringContainsString("string('cakupan_ujian', 20)", $cakupanMigration);
        $this->assertStringContainsString("'cakupan_ujian' => 'gabungan'", $cakupanMigration);
    }
}
