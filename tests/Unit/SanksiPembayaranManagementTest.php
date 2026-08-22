<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SanksiPembayaranManagementTest extends TestCase
{
    public function testFinanceCanManagePaymentPenaltyMasterData()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/KeuanganFakultas.php');
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/sanksi_pembayaran.blade.php');
        $migration = file_get_contents(__DIR__ . '/../../database/migrations/2026_08_22_030000_create_mst_sanksi_pembayaran_table.php');

        $this->assertStringContainsString("Schema::create('mst_sanksi_pembayaran'", $migration);
        $this->assertStringContainsString("decimal('jumlah_sanksi', 15, 2)", $migration);
        $this->assertStringContainsString("date('tanggal_mulai')", $migration);
        $this->assertStringContainsString("date('tanggal_selesai')", $migration);
        $this->assertStringContainsString('sanksi_pembayaran_home', $controller);
        $this->assertStringContainsString('validasiSanksiPembayaran', $controller);
        $this->assertStringContainsString("'jumlah_sanksi' => (float) \$jumlahSanksi", $controller);
        $this->assertStringContainsString("Route::group(['prefix' => 'sanksi_pembayaran']", $routes);
        $this->assertStringContainsString("->name('sanksi_pembayaran_store')", $routes);
        $this->assertStringContainsString('Master Sanksi Pembayaran', $view);
        $this->assertStringContainsString('Jumlah Uang Sanksi', $view);
        $this->assertStringContainsString('Tanggal Mulai Berlaku', $view);
        $this->assertStringContainsString('Tanggal Selesai Berlaku', $view);
    }
}
