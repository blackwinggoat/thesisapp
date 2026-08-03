<?php

namespace Tests\Feature;

use App\Http\Controllers\dosen;
use Illuminate\Auth\GenericUser;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
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

    public function testEmptyProposalAssessmentShowsUnfilledSliderState()
    {
        DB::table('trt_hasil')
            ->where('reg_id', 20)
            ->where('nidn', 'DOSEN-01')
            ->delete();

        $view = (new dosen())->detailhasil_proposal(20);
        $slider = view('tugasakhir.dosen.partials.score_slider', [
            'name' => 'nilai_1',
            'label' => 'Sikap/Presentasi',
            'value' => $view->getData()['nilai']['nilai_1'],
            'minimum' => 10,
            'maximum' => 15,
        ])->render();

        $this->assertSame([
            'nilai_1' => null,
            'nilai_2' => null,
            'nilai_3' => null,
            'nilai_4' => null,
            'nilai_5' => null,
            'saran' => null,
        ], $view->getData()['nilai']);
        $this->assertStringContainsString('assessment-score-slider is-empty', $slider);
        $this->assertStringContainsString('name="nilai_1" value="0"', $slider);
        $this->assertStringContainsString('<output class="assessment-score-slider__value" aria-live="polite">--</output>', $slider);
        $this->assertStringContainsString('value="10"', $slider);
        $this->assertStringContainsString('aria-valuetext="Belum diisi"', $slider);
    }

    public function testPartialAssessmentSubmissionIsRejectedWithoutChangingSavedScores()
    {
        $controller = new dosen();
        $proposalResponse = $controller->detailhasil_proposalpost(new Request([
            'reg_id' => 20,
            'nilai_1' => 0,
            'nilai_2' => 22,
            'nilai_3' => 20,
            'nilai_4' => 18,
            'nilai_5' => 18,
        ]));
        $taResponse = $controller->detailhasil_ujianmejapost(new Request([
            'reg_id' => 20,
            'nilai_1' => 0,
            'nilai_2' => 12,
            'nilai_3' => 15,
            'nilai_4' => 24,
            'nilai_5' => 20,
        ]));

        $savedScores = DB::table('trt_hasil')
            ->where('reg_id', 20)
            ->where('nidn', 'DOSEN-01')
            ->first(['nilai_1', 'nilai_2', 'nilai_3', 'nilai_4', 'nilai_5']);

        $this->assertSame('Lengkapi semua komponen nilai sesuai rentang penilaian sebelum menyimpan.', $proposalResponse->getSession()->get('error'));
        $this->assertSame('Lengkapi semua komponen nilai sesuai rentang penilaian sebelum menyimpan.', $taResponse->getSession()->get('error'));
        $this->assertSame('12', $savedScores->nilai_1);
        $this->assertSame('22', $savedScores->nilai_2);
        $this->assertSame('20', $savedScores->nilai_3);
        $this->assertSame('18', $savedScores->nilai_4);
        $this->assertSame('18', $savedScores->nilai_5);
    }
}
