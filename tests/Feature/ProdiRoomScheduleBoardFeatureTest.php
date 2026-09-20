<?php

namespace Tests\Feature;

use App\Http\Controllers\Prodi;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProdiRoomScheduleBoardFeatureTest extends TestCase
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
            $table->string('C_NPM')->nullable();
        });
        Schema::create('trt_bimbingan', function (Blueprint $table) {
            $table->increments('bimbingan_id');
            $table->string('C_NPM');
        });
        Schema::create('t_mst_mahasiswa', function (Blueprint $table) {
            $table->string('C_NPM')->primary();
            $table->string('NAMA_MAHASISWA');
        });
        Schema::create('mst_ruangan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_ruangan');
        });
        Schema::create('trt_jadwal_ujian_per_mhs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('C_NPM');
            $table->integer('jadwal_ujian');
            $table->string('jam_ujian')->nullable();
            $table->integer('ruangan')->nullable();
            $table->timestamps();
        });

        DB::table('mst_pendaftaran')->insert([
            'pendaftaran_id' => 10,
            'nama_periode' => 'Ujian Meja TI',
            'tipe_ujian' => 2,
            'status_prodi' => 1,
        ]);
        DB::table('trt_jadwal_ujian')->insert([
            'id' => 100,
            'pendaftaran_id' => 10,
            'tgl_ujian' => '2026-09-20',
        ]);
        DB::table('t_mst_mahasiswa')->insert([
            ['C_NPM' => '13020240001', 'NAMA_MAHASISWA' => 'Peserta Satu'],
            ['C_NPM' => '13020240002', 'NAMA_MAHASISWA' => 'Peserta Dua'],
        ]);
        DB::table('trt_bimbingan')->insert([
            ['bimbingan_id' => 20, 'C_NPM' => '13020240001'],
            ['bimbingan_id' => 21, 'C_NPM' => '13020240002'],
        ]);
        DB::table('trt_reg')->insert([
            ['reg_id' => 30, 'bimbingan_id' => 20, 'pendaftaran_id' => 10, 'status' => 2, 'C_NPM' => '13020240001'],
            ['reg_id' => 31, 'bimbingan_id' => 21, 'pendaftaran_id' => 10, 'status' => 2, 'C_NPM' => '13020240002'],
        ]);
        DB::table('mst_ruangan')->insert([
            ['id' => 1, 'nama_ruangan' => 'Ruang Sidang I'],
            ['id' => 2, 'nama_ruangan' => 'Ruang Sidang II'],
        ]);
        DB::table('trt_jadwal_ujian_per_mhs')->insert([
            'C_NPM' => '13020240001',
            'jadwal_ujian' => 100,
            'jam_ujian' => '08.30 - 10.10',
            'ruangan' => 1,
        ]);
    }

    public function testBoardReadsExistingRangesAndKeepsUnscheduledParticipants()
    {
        $view = (new TestableProdiRoomScheduleBoardController())
            ->jadwal_ruangan_tanggal('2026-09-20');
        $data = $view->getData();

        $this->assertSame('tugasakhir.prodi.jadwal_ruangan_tanggal', $view->getName());
        $this->assertCount(2, $data['peserta']);
        $this->assertSame(1, $data['scheduledCount']);
        $this->assertCount(2, $data['ruangan']);

        $scheduled = $data['peserta']->firstWhere('C_NPM', '13020240001');
        $unscheduled = $data['peserta']->firstWhere('C_NPM', '13020240002');
        $this->assertTrue($scheduled->is_scheduled);
        $this->assertSame('08:30', $scheduled->jam_mulai);
        $this->assertSame('10:10', $scheduled->jam_selesai);
        $this->assertSame(100, $scheduled->durasi_menit);
        $this->assertFalse($unscheduled->is_scheduled);
    }

    public function testTwoParticipantsCanUseSameRoomAndTime()
    {
        $controller = new TestableProdiRoomScheduleBoardController();
        $response = $controller->jadwal_ruangan_tanggal_update(
            '2026-09-20',
            Request::create('/prodi/jadwal/tanggal/2026-09-20/ruangan', 'POST', [
                'jadwal_ujian_id' => 100,
                'C_NPM' => '13020240002',
                'ruangan' => 1,
                'jam_mulai' => '08:30',
                'durasi_menit' => 100,
            ])
        );

        $payload = $response->getData(true);
        $this->assertTrue($payload['scheduled']);
        $this->assertSame('08:30 - 10:10', $payload['jam_ujian']);
        $this->assertSame(2, DB::table('trt_jadwal_ujian_per_mhs')
            ->where('ruangan', 1)
            ->count());
    }

    public function testParticipantCanBeReturnedToUnscheduledPool()
    {
        $response = (new TestableProdiRoomScheduleBoardController())
            ->jadwal_ruangan_tanggal_update(
                '2026-09-20',
                Request::create('/prodi/jadwal/tanggal/2026-09-20/ruangan', 'POST', [
                    'jadwal_ujian_id' => 100,
                    'C_NPM' => '13020240001',
                    'hapus_jadwal' => 1,
                ])
            );

        $this->assertFalse($response->getData(true)['scheduled']);
        $this->assertSame(0, DB::table('trt_jadwal_ujian_per_mhs')
            ->where('C_NPM', '13020240001')
            ->where('jadwal_ujian', 100)
            ->count());
    }
}

class TestableProdiRoomScheduleBoardController extends Prodi
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
