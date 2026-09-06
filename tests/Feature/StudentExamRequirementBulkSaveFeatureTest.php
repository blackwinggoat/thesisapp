<?php

namespace Tests\Feature;

use App\Http\Controllers\mhs;
use Illuminate\Auth\GenericUser;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StudentExamRequirementBulkSaveFeatureTest extends TestCase
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

        Schema::create('mst_syarat_ujian', function (Blueprint $table) {
            $table->increments('syarat_ujian_id');
            $table->string('nama_syarat');
            $table->integer('tipe_ujian');
        });
        Schema::create('trt_syarat_ujian', function (Blueprint $table) {
            $table->increments('id');
            $table->string('C_NPM');
            $table->integer('syarat_ujian_id');
            $table->text('link');
            $table->integer('status');
            $table->timestamps();
        });
        Schema::create('t_mst_mahasiswa', function (Blueprint $table) {
            $table->string('C_NPM')->primary();
            $table->string('C_KODE_PRODI')->nullable();
        });
        Schema::create('mst_pendaftaran', function (Blueprint $table) {
            $table->increments('pendaftaran_id');
            $table->integer('status_prodi');
            $table->integer('tipe_ujian');
            $table->integer('status_ujian');
        });

        DB::table('mst_syarat_ujian')->insert([
            ['syarat_ujian_id' => 1, 'nama_syarat' => 'Dokumen A', 'tipe_ujian' => 0],
            ['syarat_ujian_id' => 2, 'nama_syarat' => 'Dokumen B', 'tipe_ujian' => 0],
            ['syarat_ujian_id' => 3, 'nama_syarat' => 'Dokumen TA', 'tipe_ujian' => 2],
        ]);
        DB::table('trt_syarat_ujian')->insert([
            'C_NPM' => '130TEST',
            'syarat_ujian_id' => 1,
            'link' => 'https://drive.google.com/file/d/existing/view',
            'status' => 1,
        ]);
        DB::table('t_mst_mahasiswa')->insert([
            'C_NPM' => '130TEST',
            'C_KODE_PRODI' => '55201',
        ]);
        DB::table('mst_pendaftaran')->insert([
            'pendaftaran_id' => 99,
            'status_prodi' => 2,
            'tipe_ujian' => 2,
            'status_ujian' => 0,
        ]);

        Auth::guard()->setUser(new GenericUser(['id' => 1, 'name' => '130TEST']));
    }

    public function testBulkSaveCreatesNewRequirementAndPreservesUnchangedApproval()
    {
        $response = (new mhs())->syarat_ujianpost_all($this->makeRequest([
            'tipe_ujian' => 0,
            'syarat_ujian_id' => [1, 2],
            'link' => [
                'https://drive.google.com/file/d/existing/view',
                'https://drive.google.com/file/d/new/view',
            ],
        ]));

        $this->assertSame('success', $response->getSession()->get('document_status'));
        $this->assertSame(1, (int) DB::table('trt_syarat_ujian')->where('syarat_ujian_id', 1)->value('status'));
        $this->assertSame(2, (int) DB::table('trt_syarat_ujian')->where('syarat_ujian_id', 2)->value('status'));
        $this->assertSame(2, DB::table('trt_syarat_ujian')->count());
    }

    public function testBulkSaveRejectsRequirementFromAnotherExamType()
    {
        $response = (new mhs())->syarat_ujianpost_all($this->makeRequest([
            'tipe_ujian' => 0,
            'syarat_ujian_id' => [1, 3],
            'link' => [
                'https://drive.google.com/file/d/existing/view',
                'https://drive.google.com/file/d/not-allowed/view',
            ],
        ]));

        $this->assertTrue($response->getSession()->get('errors')->has('document_links'));
        $this->assertSame(1, DB::table('trt_syarat_ujian')->count());
    }

    public function testRegistrationRejectsPeriodFromAnotherProgram()
    {
        $response = (new mhs())->registrasi($this->makeRequest([
            'tipe_ujian' => 2,
            'pendaftaran_id' => 99,
        ]));

        $this->assertSame('invalid_period', $response->getSession()->get('registration_status'));
        $this->assertStringContainsString('mhs/signup_ujianmeja', $response->getTargetUrl());
    }

    private function makeRequest(array $data)
    {
        $request = Request::create('/mhs/syarat_ujianpost_all', 'POST', $data);
        $request->setLaravelSession($this->app['session.store']);

        return $request;
    }
}
