<?php

namespace Tests\Feature;

use App\Http\Controllers\Prodi;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProdiPengujiDistributionReportTest extends TestCase
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

        Schema::create('t_mst_dosen', function (Blueprint $table) {
            $table->string('C_KODE_DOSEN')->primary();
            $table->string('NAMA_DOSEN')->nullable();
        });
        Schema::create('mig_t_mst_dosen', function (Blueprint $table) {
            $table->string('C_KODE_DOSEN')->primary();
            $table->string('NAMA_DOSEN')->nullable();
        });
        Schema::create('t_mst_mahasiswa', function (Blueprint $table) {
            $table->string('C_NPM')->primary();
            $table->string('C_KODE_PRODI');
        });
        Schema::create('trt_reg', function (Blueprint $table) {
            $table->increments('reg_id');
            $table->string('C_NPM');
            $table->unsignedInteger('pendaftaran_id');
            $table->unsignedInteger('status');
        });
        Schema::create('trt_jadwal_ujian', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pendaftaran_id');
            $table->date('tgl_ujian')->nullable();
        });
        Schema::create('trt_jadwal_ujian_per_mhs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('C_NPM');
            $table->unsignedInteger('jadwal_ujian');
        });
        Schema::create('trt_penguji', function (Blueprint $table) {
            $table->increments('id');
            $table->string('C_NPM');
            $table->unsignedInteger('tipe_ujian');
            $table->string('ketua_sidang_id')->nullable();
            $table->string('penguji_I_id')->nullable();
            $table->string('penguji_II_id')->nullable();
            $table->string('penguji_III_id')->nullable();
        });

        DB::table('t_mst_dosen')->insert([
            ['C_KODE_DOSEN' => 'KS-01', 'NAMA_DOSEN' => 'Dosen Ketua'],
            ['C_KODE_DOSEN' => 'P1-01', 'NAMA_DOSEN' => 'Dosen Penguji Satu'],
            ['C_KODE_DOSEN' => 'P2-01', 'NAMA_DOSEN' => 'Dosen Penguji Dua'],
            ['C_KODE_DOSEN' => 'P3-01', 'NAMA_DOSEN' => 'Dosen Penguji Tiga'],
        ]);
        DB::table('t_mst_mahasiswa')->insert([
            ['C_NPM' => '13020250001', 'C_KODE_PRODI' => '55201'],
            ['C_NPM' => '13120250001', 'C_KODE_PRODI' => '57201'],
        ]);
        DB::table('trt_reg')->insert([
            ['C_NPM' => '13020250001', 'pendaftaran_id' => 10, 'status' => 0],
            ['C_NPM' => '13120250001', 'pendaftaran_id' => 11, 'status' => 2],
        ]);
        DB::table('trt_jadwal_ujian')->insert([
            ['id' => 100, 'pendaftaran_id' => 10, 'tgl_ujian' => '2025-10-10'],
            ['id' => 101, 'pendaftaran_id' => 11, 'tgl_ujian' => '2026-04-10'],
        ]);
        DB::table('trt_jadwal_ujian_per_mhs')->insert([
            ['C_NPM' => '13020250001', 'jadwal_ujian' => 100],
            // A duplicate participant row must not count a lecturer assignment twice.
            ['C_NPM' => '13020250001', 'jadwal_ujian' => 100],
            ['C_NPM' => '13120250001', 'jadwal_ujian' => 101],
        ]);
        DB::table('trt_penguji')->insert([
            [
                'C_NPM' => '13020250001',
                'tipe_ujian' => 0,
                'ketua_sidang_id' => 'KS-01',
                'penguji_I_id' => 'P1-01',
                'penguji_II_id' => 'P2-01',
                'penguji_III_id' => 'P3-01',
            ],
            [
                'C_NPM' => '13120250001',
                'tipe_ujian' => 2,
                'ketua_sidang_id' => 'KS-01',
                'penguji_I_id' => 'P1-01',
                'penguji_II_id' => 'P2-01',
                'penguji_III_id' => 'P3-01',
            ],
        ]);
    }

    public function testDetailedDistributionSeparatesExaminerRolesByProgramAndSemester()
    {
        $report = (new TestableProdiPengujiDistributionController())
            ->buildPengujiDistributionReport('%', '2025/2026', 'lengkap');

        $rows = collect($report['rows'])->keyBy('kode_dosen');
        $pengujiSatu = $rows->get('P1-01');

        $this->assertTrue($report['is_detailed']);
        $this->assertCount(4, $report['rows']);
        $this->assertSame(1, $pengujiSatu['role_counts']['ti']['Ganjil']['P1']);
        $this->assertSame(1, $pengujiSatu['role_counts']['si']['Genap']['P1']);
        $this->assertSame(0, $pengujiSatu['role_totals']['KS']);
        $this->assertSame(2, $pengujiSatu['role_totals']['P1']);
        $this->assertSame(4, $report['total_penugasan_by_program']['ti']['total']);
        $this->assertSame(4, $report['total_penugasan_by_program']['si']['total']);
        $this->assertSame(8, $report['total_penugasan']);
    }

    public function testSummaryDistributionKeepsTheSameValidatedScheduleTotals()
    {
        $report = (new TestableProdiPengujiDistributionController())
            ->buildPengujiDistributionReport('%', '2025/2026', 'ringkas');

        $rows = collect($report['rows'])->keyBy('kode_dosen');
        $pengujiSatu = $rows->get('P1-01');

        $this->assertFalse($report['is_detailed']);
        $this->assertSame(1, $pengujiSatu['counts']['ti']['Ganjil']);
        $this->assertSame(1, $pengujiSatu['counts']['si']['Genap']);
        $this->assertSame(2, $pengujiSatu['total']);
    }
}

class TestableProdiPengujiDistributionController extends Prodi
{
    public function buildPengujiDistributionReport($nimLike, $academicYear, $mode)
    {
        return $this->getPengujiDistributionReport($nimLike, $academicYear, $mode);
    }
}
