<?php

namespace Tests\Feature;

use App\Http\Controllers\dosen;
use Illuminate\Auth\GenericUser;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DosenAssessmentCardRoleTest extends TestCase
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

        Schema::create('mig_t_mst_dosen', function (Blueprint $table) {
            $table->string('C_KODE_DOSEN')->primary();
            $table->string('EMAIL')->nullable();
            $table->string('NAMA_DOSEN')->nullable();
        });
        Schema::create('t_mst_dosen', function (Blueprint $table) {
            $table->string('C_KODE_DOSEN')->primary();
            $table->string('NAMA_DOSEN')->nullable();
        });
        Schema::create('t_mst_mahasiswa', function (Blueprint $table) {
            $table->string('C_NPM')->primary();
            $table->string('NAMA_MAHASISWA')->nullable();
            $table->string('D_FOTO_MAHASISWA')->nullable();
            $table->string('JENIS_KELAMIN')->nullable();
            $table->string('C_KODE_STATUS_AKTIF_MHS')->nullable();
        });
        Schema::create('trt_bimbingan', function (Blueprint $table) {
            $table->integer('bimbingan_id')->primary();
            $table->string('C_NPM');
            $table->string('judul')->nullable();
            $table->string('jenis_tugas_akhir_id')->nullable();
            $table->integer('status_bimbingan')->default(0);
            $table->string('pembimbing_I_id')->nullable();
            $table->string('pembimbing_II_id')->nullable();
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
        Schema::create('trt_reg', function (Blueprint $table) {
            $table->integer('reg_id')->primary();
            $table->integer('pendaftaran_id');
            $table->integer('bimbingan_id');
            $table->integer('status');
        });
        Schema::create('trt_hasil', function (Blueprint $table) {
            $table->increments('nilai_id');
            $table->integer('reg_id');
            $table->string('nidn');
            $table->decimal('nilai_1', 5, 2)->nullable();
            $table->decimal('nilai_2', 5, 2)->nullable();
            $table->decimal('nilai_3', 5, 2)->nullable();
            $table->decimal('nilai_4', 5, 2)->nullable();
            $table->decimal('nilai_5', 5, 2)->nullable();
        });
        Schema::create('trt_jadwal_ujian', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pendaftaran_id');
            $table->date('tgl_ujian')->nullable();
        });
        Schema::create('trt_jadwal_ujian_per_mhs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('C_NPM');
            $table->integer('jadwal_ujian');
            $table->integer('ruangan')->nullable();
            $table->time('jam_ujian')->nullable();
        });
        Schema::create('mst_ruangan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_ruangan')->nullable();
        });

        DB::table('mig_t_mst_dosen')->insert([
            'C_KODE_DOSEN' => 'KETUA-01',
            'EMAIL' => 'ketua@example.test',
            'NAMA_DOSEN' => 'Dosen Ketua Sidang',
        ]);
        DB::table('t_mst_dosen')->insert([
            'C_KODE_DOSEN' => 'KETUA-01',
            'NAMA_DOSEN' => 'Dosen Ketua Sidang',
        ]);
        DB::table('t_mst_mahasiswa')->insert([
            'C_NPM' => '13020230001',
            'NAMA_MAHASISWA' => 'Mahasiswa Ketua',
            'C_KODE_STATUS_AKTIF_MHS' => 'A',
        ]);

        Auth::guard()->setUser(new GenericUser([
            'id' => 1,
            'name' => 'KETUA-01',
            'email' => 'ketua@example.test',
        ]));
    }

    public function testProposalAssessmentCardsIncludeLoggedInKetuaSidang()
    {
        $this->seedAssessmentCardData(0, 20, 100);

        $cards = $this->assessmentCards(0, [2, 3, 4]);

        $this->assertCount(1, $cards);
        $this->assertSame(['Ketua Sidang'], $cards->first()->peran_login);
        $this->assertTrue($cards->first()->boleh_menilai);
    }

    public function testUjianMejaAssessmentCardsIncludeLoggedInKetuaSidang()
    {
        $this->seedAssessmentCardData(2, 21, 101);

        $cards = $this->assessmentCards(2, [3, 4]);

        $this->assertCount(1, $cards);
        $this->assertSame(['Ketua Sidang'], $cards->first()->peran_login);
        $this->assertTrue($cards->first()->boleh_menilai);
    }

    public function testAssessmentCardsIncludeStudentRegardlessOfActiveStatus()
    {
        DB::table('t_mst_mahasiswa')
            ->where('C_NPM', '13020230001')
            ->update(['C_KODE_STATUS_AKTIF_MHS' => 'N']);
        $this->seedAssessmentCardData(2, 22, 102);

        $cards = $this->assessmentCards(2, [3, 4]);

        $this->assertCount(1, $cards);
        $this->assertSame('13020230001', $cards->first()->C_NPM);
    }

    public function testAssessmentCardsMarkOnlyCompletedLecturerAssessments()
    {
        $this->seedAssessmentCardData(2, 25, 105);
        DB::table('trt_hasil')->insert([
            'reg_id' => 25,
            'nidn' => 'KETUA-01',
            'nilai_1' => 10,
            'nilai_2' => 20,
            'nilai_3' => 20,
            'nilai_4' => 20,
            'nilai_5' => 20,
        ]);
        DB::table('trt_hasil')->insert([
            'reg_id' => 25,
            'nidn' => 'DOSEN-PENGUJI-1',
            'nilai_1' => 10,
            'nilai_2' => 20,
            'nilai_3' => null,
            'nilai_4' => 20,
            'nilai_5' => 20,
        ]);

        $cards = $this->assessmentCards(2, [3, 4]);

        $this->assertTrue($cards->first()->penilaian_lengkap_by_dosen['KETUA-01']);
        $this->assertArrayNotHasKey('DOSEN-PENGUJI-1', $cards->first()->penilaian_lengkap_by_dosen);
        $this->assertSame('complete', $cards->first()->penilaian_status_by_dosen['KETUA-01']);
        $this->assertSame('incomplete', $cards->first()->penilaian_status_by_dosen['DOSEN-PENGUJI-1']);
        $this->assertArrayNotHasKey('DOSEN-PENGUJI-2', $cards->first()->penilaian_status_by_dosen);
    }

    public function testProposalRecapUsesTheSameAssessmentCardsAndGroupsSchedulesByDate()
    {
        $this->seedAssessmentCardData(0, 23, 103);

        $view = (new dosen())->rekap_hasil_proposal();

        $this->assertSame('tugasakhir.dosen.rekap_hasil_penilaian', $view->getName());
        $this->assertCount(1, $view->getData()['data']);
        $this->assertArrayHasKey('2026-08-12', $view->getData()['rekapPerTanggal']->all());
        $this->assertTrue($view->getData()['data']->first()->highlight_roles['ketua_sidang_id']);
    }

    public function testUjianMejaRecapUsesTheSameAssessmentCardsAndGroupsSchedulesByDate()
    {
        $this->seedAssessmentCardData(2, 24, 104);

        $view = (new dosen())->rekap_hasil_ujianmeja();

        $this->assertSame('tugasakhir.dosen.rekap_hasil_penilaian', $view->getName());
        $this->assertCount(1, $view->getData()['data']);
        $this->assertArrayHasKey('2026-08-12', $view->getData()['rekapPerTanggal']->all());
    }

    public function testAssessmentRecapRoutesAreProtectedByLecturerMiddleware()
    {
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');

        $this->assertStringContainsString('Route::get("/dsn/hasil_proposal/rekap", "dosen@rekap_hasil_proposal")', $routes);
        $this->assertStringContainsString('Route::get("/dsn/hasil_ujianmeja/rekap", "dosen@rekap_hasil_ujianmeja")', $routes);
    }

    private function assessmentCards($tipeUjian, array $excludedBimbinganStatuses)
    {
        $controller = new dosen();
        $method = new \ReflectionMethod($controller, 'assessmentCards');
        $method->setAccessible(true);

        return $method->invoke($controller, $tipeUjian, $excludedBimbinganStatuses);
    }

    private function seedAssessmentCardData($tipeUjian, $regId, $pendaftaranId)
    {
        DB::table('trt_bimbingan')->insert([
            'bimbingan_id' => $regId,
            'C_NPM' => '13020230001',
            'judul' => 'Judul Uji Ketua Sidang',
            'status_bimbingan' => 0,
            'pembimbing_I_id' => 'DOSEN-PEMBIMBING',
            'pembimbing_II_id' => null,
        ]);
        DB::table('trt_penguji')->insert([
            'C_NPM' => '13020230001',
            'tipe_ujian' => $tipeUjian,
            'penguji_I_id' => 'DOSEN-PENGUJI-1',
            'penguji_II_id' => 'DOSEN-PENGUJI-2',
            'penguji_III_id' => null,
            'ketua_sidang_id' => 'KETUA-01',
        ]);
        DB::table('trt_reg')->insert([
            'reg_id' => $regId,
            'pendaftaran_id' => $pendaftaranId,
            'bimbingan_id' => $regId,
            'status' => $tipeUjian,
        ]);
        DB::table('trt_jadwal_ujian')->insert([
            'id' => $regId,
            'pendaftaran_id' => $pendaftaranId,
            'tgl_ujian' => '2026-08-12',
        ]);
        DB::table('trt_jadwal_ujian_per_mhs')->insert([
            'C_NPM' => '13020230001',
            'jadwal_ujian' => $regId,
            'ruangan' => null,
            'jam_ujian' => '09:00:00',
        ]);
    }
}
