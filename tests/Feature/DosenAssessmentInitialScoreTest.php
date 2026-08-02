<?php

namespace Tests\Feature;

use App\Http\Controllers\dosen;
use Illuminate\Auth\GenericUser;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DosenAssessmentInitialScoreTest extends TestCase
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
        Schema::create('t_mst_mahasiswa', function (Blueprint $table) {
            $table->string('C_NPM')->primary();
            $table->string('NAMA_MAHASISWA')->nullable();
        });
        Schema::create('trt_bimbingan', function (Blueprint $table) {
            $table->integer('bimbingan_id')->primary();
            $table->string('C_NPM');
        });
        Schema::create('trt_reg', function (Blueprint $table) {
            $table->integer('reg_id')->primary();
            $table->integer('bimbingan_id');
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
            $table->text('saran')->nullable();
        });

        DB::table('mig_t_mst_dosen')->insert([
            'C_KODE_DOSEN' => 'DOSEN-01',
            'EMAIL' => 'dosen@example.test',
            'NAMA_DOSEN' => 'Dosen Penguji',
        ]);
        DB::table('t_mst_mahasiswa')->insert([
            'C_NPM' => '13020230001',
            'NAMA_MAHASISWA' => 'Mahasiswa Uji',
        ]);
        DB::table('trt_bimbingan')->insert([
            'bimbingan_id' => 10,
            'C_NPM' => '13020230001',
        ]);
        DB::table('trt_reg')->insert([
            'reg_id' => 20,
            'bimbingan_id' => 10,
        ]);
        DB::table('trt_hasil')->insert([
            'reg_id' => 20,
            'nidn' => 'DOSEN-01',
            'nilai_1' => 12,
            'nilai_2' => 22,
            'nilai_3' => 20,
            'nilai_4' => 18,
            'nilai_5' => 18,
            'saran' => 'Perbaiki daftar pustaka.',
        ]);
        DB::table('trt_hasil')->insert([
            'reg_id' => 20,
            'nidn' => 'DOSEN-02',
            'nilai_1' => 10,
            'nilai_2' => 16,
            'nilai_3' => 15,
            'nilai_4' => 15,
            'nilai_5' => 15,
        ]);

        Auth::guard()->setUser(new GenericUser([
            'id' => 1,
            'name' => 'DOSEN-01',
            'email' => 'dosen@example.test',
        ]));
    }

    public function testProposalInitialScoresUseSavedAssessmentForLoggedInLecturer()
    {
        $view = (new dosen())->detailhasil_proposal(20);

        $this->assertSame('DOSEN-01', $view->getData()['kodeDosen']);
        $this->assertSame([
            'nilai_1' => '12',
            'nilai_2' => '22',
            'nilai_3' => '20',
            'nilai_4' => '18',
            'nilai_5' => '18',
            'saran' => 'Perbaiki daftar pustaka.',
        ], $view->getData()['nilai']);
    }

    public function testUjianMejaInitialScoresUseSavedAssessmentForLoggedInLecturer()
    {
        $view = (new dosen())->detailhasil_ujianmeja(20);

        $this->assertSame('DOSEN-01', $view->getData()['kodeDosen']);
        $this->assertSame('12', $view->getData()['nilai']['nilai_1']);
        $this->assertSame('18', $view->getData()['nilai']['nilai_5']);
        $this->assertSame('Perbaiki daftar pustaka.', $view->getData()['nilai']['saran']);
    }
}
