<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SyncOgiSatriaFromSiakadTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('t_mst_mahasiswa', function (Blueprint $table) {
            $table->string('C_KODE_PT');
            $table->string('C_KODE_FAKULTAS');
            $table->string('C_KODE_PRODI');
            $table->string('C_NPM');
            $table->string('NAMA_MAHASISWA')->nullable();
            $table->string('TEMPAT_LAHIR')->nullable();
            $table->date('TGL_LAHIR')->nullable();
            $table->char('JENIS_KELAMIN', 1)->nullable();
            $table->string('C_KODE_KONSENTRASI')->nullable();
            $table->string('TAHUN_MASUK')->nullable();
            $table->string('THN_SEMESTER_AWAL_MASUK')->nullable();
            $table->string('C_KODE_STATUS_AWAL_MHS')->nullable();
            $table->string('C_KODE_STATUS_AKTIF_MHS')->nullable();
            $table->integer('F_AKTIF')->nullable();
            $table->integer('F_IS_C')->nullable();
            $table->integer('F_IS_U')->nullable();
            $table->integer('F_IS_D')->nullable();
            $table->string('F_CHANGE_LOG')->nullable();
            $table->string('C_USER')->nullable();
            $table->dateTime('C_DATE')->nullable();
            $table->string('U_USER')->nullable();
            $table->dateTime('U_DATE')->nullable();
        });
    }

    public function testMigrationAddsVerifiedStudentDataOnlyOnce()
    {
        $migration = $this->migration();

        $migration->up();
        $migration->up();

        $this->assertSame(1, DB::table('t_mst_mahasiswa')->where('C_NPM', '13020250228')->count());
        $this->assertDatabaseHas('t_mst_mahasiswa', [
            'C_NPM' => '13020250228',
            'NAMA_MAHASISWA' => 'OGI SATRIA',
            'C_KODE_PRODI' => '55201',
            'TAHUN_MASUK' => '2025',
            'THN_SEMESTER_AWAL_MASUK' => '20251',
            'C_KODE_STATUS_AWAL_MHS' => '2',
            'C_KODE_STATUS_AKTIF_MHS' => 'A',
        ]);
    }

    public function testMigrationDoesNotOverwriteAnExistingStudent()
    {
        DB::table('t_mst_mahasiswa')->insert([
            'C_KODE_PT' => '091002',
            'C_KODE_FAKULTAS' => '91000',
            'C_KODE_PRODI' => '55201',
            'C_NPM' => '13020250228',
            'NAMA_MAHASISWA' => 'DATA SUDAH ADA',
        ]);

        $this->migration()->up();

        $this->assertSame(
            'DATA SUDAH ADA',
            DB::table('t_mst_mahasiswa')->where('C_NPM', '13020250228')->value('NAMA_MAHASISWA')
        );
    }

    private function migration()
    {
        require_once database_path('migrations/2026_09_17_010000_sync_ogi_satria_from_siakad.php');

        return new \SyncOgiSatriaFromSiakad();
    }
}
