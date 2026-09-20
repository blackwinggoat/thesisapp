<?php

namespace Tests\Feature;

use App\Http\Controllers\Prodi;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProdiScheduleDateRecapFeatureTest extends TestCase
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

        Schema::create('mst_pendaftaran', function (Blueprint $table) {
            $table->increments('pendaftaran_id');
            $table->string('nama_periode');
            $table->integer('tipe_ujian');
            $table->integer('status_prodi');
        });
        Schema::create('trt_jadwal_ujian', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pendaftaran_id');
            $table->date('tgl_ujian');
        });
        Schema::create('trt_reg', function (Blueprint $table) {
            $table->increments('reg_id');
            $table->integer('bimbingan_id');
            $table->integer('pendaftaran_id');
            $table->integer('status');
            $table->string('C_NPM');
        });
        Schema::create('trt_bimbingan', function (Blueprint $table) {
            $table->increments('bimbingan_id');
            $table->string('C_NPM');
            $table->string('pembimbing_I_id')->nullable();
            $table->string('pembimbing_II_id')->nullable();
        });
        Schema::create('t_mst_mahasiswa', function (Blueprint $table) {
            $table->string('C_NPM')->primary();
            $table->string('NAMA_MAHASISWA');
        });
        Schema::create('trt_penguji', function (Blueprint $table) {
            $table->increments('id');
            $table->string('C_NPM');
            $table->integer('tipe_ujian');
            $table->string('penguji_I_id')->nullable();
            $table->string('penguji_II_id')->nullable();
            $table->string('penguji_III_id')->nullable();
            $table->string('ketua_sidang_id')->nullable();
        });
        Schema::create('t_mst_dosen', function (Blueprint $table) {
            $table->string('C_KODE_DOSEN')->primary();
            $table->string('NAMA_DOSEN');
        });
        Schema::create('mig_t_mst_dosen', function (Blueprint $table) {
            $table->string('C_KODE_DOSEN')->primary();
            $table->string('NAMA_DOSEN');
        });
    }

    public function testCombinedDetailIncludesParticipantsWithoutExaminerAssignment()
    {
        DB::table('mst_pendaftaran')->insert([
            ['pendaftaran_id' => 10, 'nama_periode' => 'Proposal SI', 'tipe_ujian' => 0, 'status_prodi' => 2],
            ['pendaftaran_id' => 11, 'nama_periode' => 'Ujian Meja TI', 'tipe_ujian' => 2, 'status_prodi' => 1],
        ]);
        DB::table('trt_jadwal_ujian')->insert([
            ['id' => 100, 'pendaftaran_id' => 10, 'tgl_ujian' => '2026-07-31'],
            ['id' => 101, 'pendaftaran_id' => 11, 'tgl_ujian' => '2026-07-31'],
        ]);
        DB::table('t_mst_mahasiswa')->insert([
            ['C_NPM' => '13120240001', 'NAMA_MAHASISWA' => 'Mahasiswa Proposal'],
            ['C_NPM' => '13020240001', 'NAMA_MAHASISWA' => 'Mahasiswa Ujian Meja'],
        ]);
        DB::table('trt_bimbingan')->insert([
            ['bimbingan_id' => 20, 'C_NPM' => '13120240001', 'pembimbing_I_id' => 'DOSEN-1', 'pembimbing_II_id' => 'DOSEN-2'],
            ['bimbingan_id' => 21, 'C_NPM' => '13020240001', 'pembimbing_I_id' => 'DOSEN-1', 'pembimbing_II_id' => 'DOSEN-2'],
        ]);
        DB::table('trt_reg')->insert([
            ['reg_id' => 30, 'bimbingan_id' => 20, 'pendaftaran_id' => 10, 'status' => 0, 'C_NPM' => '13120240001'],
            ['reg_id' => 31, 'bimbingan_id' => 21, 'pendaftaran_id' => 11, 'status' => 2, 'C_NPM' => '13020240001'],
        ]);
        DB::table('trt_penguji')->insert([
            'id' => 40,
            'C_NPM' => '13020240001',
            'tipe_ujian' => 2,
            'penguji_I_id' => 'DOSEN-3',
            'penguji_II_id' => null,
            'penguji_III_id' => null,
            'ketua_sidang_id' => 'DOSEN-1',
        ]);
        DB::table('t_mst_dosen')->insert([
            ['C_KODE_DOSEN' => 'DOSEN-1', 'NAMA_DOSEN' => 'Dosen Satu'],
            ['C_KODE_DOSEN' => 'DOSEN-2', 'NAMA_DOSEN' => 'Dosen Dua'],
            ['C_KODE_DOSEN' => 'DOSEN-3', 'NAMA_DOSEN' => 'Dosen Tiga'],
        ]);

        $view = (new TestableProdiScheduleDateRecapFeatureController())
            ->daftar_peserta_tanggal('2026-07-31');
        $viewData = $view->getData();

        $this->assertSame('tugasakhir.prodi.daftar_peserta_tanggal', $view->getName());
        $this->assertCount(2, $viewData['data']);
        $this->assertSame(2, $viewData['info']->jumlah_peserta);
        $this->assertSame(['Proposal', 'Ujian Meja'], $viewData['info']->tipe_ujian_list->pluck('label')->all());
        $this->assertNull($viewData['data']->firstWhere('C_NPM', '13120240001')->penguji_id);
        $this->assertSame('Dosen Tiga', $viewData['dosenByKode']->get('DOSEN-3'));
    }
}

class TestableProdiScheduleDateRecapFeatureController extends Prodi
{
    protected function getProdiScope($user = null)
    {
        return [
            'nim_prefix' => null,
            'nim_like' => '%',
            'kode_prodi' => null,
            'status_prodi' => null,
            'label' => 'Semua Program Studi',
            'is_mapped' => true,
        ];
    }
}
