<?php

namespace Tests\Feature;

use App\Http\Controllers\Prodi;
use Illuminate\Auth\GenericUser;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ExamResultConfirmationTest extends TestCase
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
            $table->integer('pendaftaran_id')->primary();
            $table->integer('status_prodi');
            $table->integer('tipe_ujian');
        });
        Schema::create('trt_bimbingan', function (Blueprint $table) {
            $table->integer('bimbingan_id')->primary();
            $table->string('C_NPM');
            $table->integer('status_bimbingan');
            $table->string('pembimbing_I_id')->nullable();
            $table->string('pembimbing_II_id')->nullable();
        });
        Schema::create('trt_reg', function (Blueprint $table) {
            $table->integer('reg_id')->primary();
            $table->integer('bimbingan_id');
            $table->integer('pendaftaran_id');
            $table->integer('status');
        });
        Schema::create('trt_jadwal_ujian', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pendaftaran_id');
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
        Schema::create('trt_hasil', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('reg_id');
            $table->string('nidn');
            $table->decimal('nilai_1', 5, 2)->nullable();
            $table->decimal('nilai_2', 5, 2)->nullable();
            $table->decimal('nilai_3', 5, 2)->nullable();
            $table->decimal('nilai_4', 5, 2)->nullable();
            $table->decimal('nilai_5', 5, 2)->nullable();
        });

        Auth::guard()->setUser(new GenericUser(['id' => 1, 'name' => 'proditi']));
    }

    public function testProposalBulkConfirmationOnlyUpdatesCompleteLatestRegistrationsForCurrentProgram()
    {
        $this->addCandidate(1, 101, 'MHS1', 0, 0, 1, ['P1', 'U1', 'K1']);
        $this->addCandidate(2, 102, 'MHS2', 0, 0, 1, ['P2', 'U2']);
        $this->addCandidate(3, 103, 'MHS3', 0, 2, 1, ['P3', 'U3', 'K3']);
        $this->addCandidate(4, 104, 'MHS4', 0, 0, 2, ['P4', 'U4', 'K4']);
        $this->addCandidate(5, 105, 'MHS5', 0, 0, 1, ['P5', 'U5', 'K5']);
        $this->addCandidate(6, 107, 'MHS6', 0, 0, 1, ['P6', 'U6', 'K6'], 2);
        $this->addCandidate(7, 108, 'MHS7', 0, 0, 1, ['P7', 'U7', 'K7'], null, false);

        DB::table('trt_reg')->insert([
            'reg_id' => 106,
            'bimbingan_id' => 5,
            'pendaftaran_id' => 105,
            'status' => 0,
        ]);

        $response = (new Prodi())->approve_hasilujian_proposal_all_post();

        $this->assertSame(2, $this->statusBimbingan(1));
        $this->assertSame(0, $this->statusBimbingan(2));
        $this->assertSame(0, $this->statusBimbingan(3));
        $this->assertSame(0, $this->statusBimbingan(4));
        $this->assertSame(0, $this->statusBimbingan(5));
        $this->assertSame(0, $this->statusBimbingan(6));
        $this->assertSame(0, $this->statusBimbingan(7));
        $this->assertSame('success', $response->getSession()->get('status'));
        $this->assertSame(1, $response->getSession()->get('total'));
        $this->assertSame(2, $response->getSession()->get('total_belum_lengkap'));
    }

    public function testTaBulkConfirmationLeavesCandidateWithOneMissingAssessmentUnchanged()
    {
        $this->addCandidate(11, 201, 'TA1', 2, 2, 1, ['P11', 'U11', 'K11']);
        $this->addCandidate(12, 202, 'TA2', 2, 2, 1, ['P12', 'U12']);
        $this->addCandidate(13, 203, 'TA3', 2, 0, 1, ['P13', 'U13', 'K13']);
        DB::table('trt_hasil')->insert([
            'reg_id' => 202,
            'nidn' => 'K12',
            'nilai_1' => 0,
            'nilai_2' => 0,
            'nilai_3' => 0,
            'nilai_4' => 0,
            'nilai_5' => 0,
        ]);

        $response = (new Prodi())->approve_hasilujian_ta_all_post();

        $this->assertSame(3, $this->statusBimbingan(11));
        $this->assertSame(2, $this->statusBimbingan(12));
        $this->assertSame(2, $this->statusBimbingan(13));
        $this->assertSame(1, $response->getSession()->get('total'));
        $this->assertSame(1, $response->getSession()->get('total_belum_lengkap'));
    }

    public function testIndividualConfirmationCannotBypassIncompleteAssessmentCheck()
    {
        $this->addCandidate(21, 301, 'P-INCOMPLETE', 0, 0, 1, ['P21', 'U21']);
        $this->addCandidate(22, 302, 'TA-INCOMPLETE', 2, 2, 1, ['P22', 'U22']);

        $controller = new Prodi();
        $proposalResponse = $controller->approve_hasilujian_proposal_post(21, 'P-INCOMPLETE', 301);
        $taResponse = $controller->approve_hasilujian_ta_post(22, 'TA-INCOMPLETE', 302);

        $this->assertSame(0, $this->statusBimbingan(21));
        $this->assertSame(2, $this->statusBimbingan(22));
        $this->assertSame('warning', $proposalResponse->getSession()->get('status'));
        $this->assertSame('warning', $taResponse->getSession()->get('status'));
    }

    public function testBulkConfirmationRoutesUsePostFormsWithCsrfProtection()
    {
        $routes = [];
        foreach ($this->app['router']->getRoutes() as $route) {
            foreach ($route->methods() as $method) {
                $routes[$method . ' ' . $route->uri()] = true;
            }
        }

        $this->assertArrayHasKey('POST prodi/approve_hasilujian_proposal_all_post', $routes);
        $this->assertArrayHasKey('POST prodi/approve_hasilujian_ta_all_post', $routes);
        $this->assertArrayNotHasKey('GET prodi/approve_hasilujian_proposal_all_post', $routes);
        $this->assertArrayNotHasKey('GET prodi/approve_hasilujian_ta_all_post', $routes);

        foreach (['proposal', 'ta'] as $exam) {
            $view = file_get_contents(
                __DIR__ . '/../../resources/views/tugasakhir/prodi/approve_hasilujian_' . $exam . '.blade.php'
            );
            $this->assertStringContainsString('method="POST"', $view);
            $this->assertStringContainsString('csrf_field()', $view);
            $this->assertStringContainsString('Konfirmasi Semua Nilai Lengkap', $view);
        }
    }

    private function addCandidate($bimbinganId, $regId, $nim, $statusBimbingan, $tipeUjian, $statusProdi, array $filledAssessors, $tipePendaftaran = null, $scheduled = true)
    {
        DB::table('mst_pendaftaran')->insert([
            'pendaftaran_id' => $regId,
            'status_prodi' => $statusProdi,
            'tipe_ujian' => $tipePendaftaran === null ? $tipeUjian : $tipePendaftaran,
        ]);
        DB::table('trt_bimbingan')->insert([
            'bimbingan_id' => $bimbinganId,
            'C_NPM' => $nim,
            'status_bimbingan' => $statusBimbingan,
            'pembimbing_I_id' => 'P' . $bimbinganId,
            'pembimbing_II_id' => null,
        ]);
        DB::table('trt_reg')->insert([
            'reg_id' => $regId,
            'bimbingan_id' => $bimbinganId,
            'pendaftaran_id' => $regId,
            'status' => $tipeUjian,
        ]);
        if ($scheduled) {
            DB::table('trt_jadwal_ujian')->insert(['pendaftaran_id' => $regId]);
        }
        DB::table('trt_penguji')->insert([
            'C_NPM' => $nim,
            'tipe_ujian' => $tipeUjian,
            'penguji_I_id' => 'U' . $bimbinganId,
            'penguji_II_id' => null,
            'penguji_III_id' => null,
            'ketua_sidang_id' => 'K' . $bimbinganId,
        ]);

        foreach ($filledAssessors as $nidn) {
            DB::table('trt_hasil')->insert([
                'reg_id' => $regId,
                'nidn' => $nidn,
                'nilai_1' => 1,
                'nilai_2' => 1,
                'nilai_3' => 1,
                'nilai_4' => 1,
                'nilai_5' => 1,
            ]);
        }
    }

    private function statusBimbingan($bimbinganId)
    {
        return (int) DB::table('trt_bimbingan')
            ->where('bimbingan_id', $bimbinganId)
            ->value('status_bimbingan');
    }
}
