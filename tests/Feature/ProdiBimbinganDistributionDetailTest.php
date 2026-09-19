<?php

namespace Tests\Feature;

use App\Http\Controllers\Prodi;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProdiBimbinganDistributionDetailTest extends TestCase
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

        Schema::create('trt_bimbingan', function (Blueprint $table) {
            $table->increments('bimbingan_id');
            $table->string('C_NPM');
            $table->string('pembimbing_I_id')->nullable();
            $table->string('pembimbing_II_id')->nullable();
        });
        Schema::create('mst_sk_pembimbing', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('bimbingan_id');
            $table->dateTime('created_at')->nullable();
        });
        Schema::create('t_mst_mahasiswa', function (Blueprint $table) {
            $table->string('C_NPM')->primary();
            $table->string('C_KODE_PRODI');
        });
        Schema::create('t_mst_dosen', function (Blueprint $table) {
            $table->string('C_KODE_DOSEN')->primary();
            $table->string('NAMA_DOSEN')->nullable();
        });
        Schema::create('mig_t_mst_dosen', function (Blueprint $table) {
            $table->string('C_KODE_DOSEN')->primary();
            $table->string('NAMA_DOSEN')->nullable();
        });

        DB::table('t_mst_dosen')->insert([
            ['C_KODE_DOSEN' => 'DOSEN-PU', 'NAMA_DOSEN' => 'Dosen Utama'],
            ['C_KODE_DOSEN' => 'DOSEN-PP', 'NAMA_DOSEN' => 'Dosen Pendamping'],
        ]);
        DB::table('t_mst_mahasiswa')->insert([
            ['C_NPM' => '13020250001', 'C_KODE_PRODI' => '55201'],
            ['C_NPM' => '13120250001', 'C_KODE_PRODI' => '57201'],
        ]);
        DB::table('trt_bimbingan')->insert([
            [
                'bimbingan_id' => 1,
                'C_NPM' => '13020250001',
                'pembimbing_I_id' => 'DOSEN-PU',
                'pembimbing_II_id' => 'DOSEN-PP',
            ],
            [
                'bimbingan_id' => 2,
                'C_NPM' => '13120250001',
                'pembimbing_I_id' => 'DOSEN-PU',
                'pembimbing_II_id' => 'DOSEN-PP',
            ],
        ]);
        DB::table('mst_sk_pembimbing')->insert([
            ['bimbingan_id' => 1, 'created_at' => '2025-10-10 09:00:00'],
            ['bimbingan_id' => 2, 'created_at' => '2026-04-10 09:00:00'],
        ]);
    }

    public function testMainModeKeepsTheExistingPrimarySupervisorReport()
    {
        $report = (new TestableProdiBimbinganDistributionController())
            ->buildBimbinganDistributionReport('%', '2025/2026', 'utama');

        $this->assertFalse($report['is_detailed']);
        $this->assertCount(1, $report['rows']);
        $this->assertSame('DOSEN-PU', $report['rows'][0]['kode_dosen']);
        $this->assertSame(2, $report['rows'][0]['total']);
        $this->assertSame(2, $report['total_mahasiswa']);
    }

    public function testDetailedModeSeparatesPuAndPpByProgramAndSemester()
    {
        $report = (new TestableProdiBimbinganDistributionController())
            ->buildBimbinganDistributionReport('%', '2025/2026', 'lengkap');

        $rows = collect($report['rows'])->keyBy('kode_dosen');
        $utama = $rows->get('DOSEN-PU');
        $pendamping = $rows->get('DOSEN-PP');

        $this->assertTrue($report['is_detailed']);
        $this->assertCount(2, $report['rows']);
        $this->assertSame(1, $utama['role_counts']['ti']['Ganjil']['PU']);
        $this->assertSame(1, $utama['role_counts']['si']['Genap']['PU']);
        $this->assertSame(0, $utama['role_totals']['PP']);
        $this->assertSame(1, $pendamping['role_counts']['ti']['Ganjil']['PP']);
        $this->assertSame(1, $pendamping['role_counts']['si']['Genap']['PP']);
        $this->assertSame(2, $pendamping['role_totals']['PP']);
        $this->assertSame(2, $report['total_penugasan_by_program']['ti']['total']);
        $this->assertSame(2, $report['total_penugasan_by_program']['si']['total']);
        $this->assertSame(4, $report['total_penugasan']);
    }
}

class TestableProdiBimbinganDistributionController extends Prodi
{
    public function buildBimbinganDistributionReport($nimLike, $academicYear, $mode)
    {
        return $this->getBimbinganDistributionReport($nimLike, $academicYear, $mode);
    }
}
