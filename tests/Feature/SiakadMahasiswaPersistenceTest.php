<?php

namespace Tests\Feature;

use App\Services\SiakadMahasiswaService;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ReflectionMethod;
use Tests\TestCase;

class SiakadMahasiswaPersistenceTest extends TestCase
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
            $table->string('C_NPM')->primary();
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
            $table->string('NO_HP')->nullable();
            $table->string('D_FOTO_MAHASISWA')->nullable();
        });

        require_once database_path('migrations/2026_11_25_010000_create_siakad_mahasiswa_sync_audit.php');
        (new \CreateSiakadMahasiswaSyncAudit())->up();

        Schema::create('trt_bimbingan', function (Blueprint $table) {
            $table->increments('bimbingan_id');
            $table->string('C_NPM');
            $table->string('judul');
        });
    }

    public function testItAddsOnlyMissingStudentsAndKeepsExistingMasterData()
    {
        DB::table('t_mst_mahasiswa')->insert([
            'C_KODE_PT' => '091002',
            'C_KODE_FAKULTAS' => '91000',
            'C_KODE_PRODI' => '55201',
            'C_NPM' => '13020250001',
            'NAMA_MAHASISWA' => 'DATA LAMA TIDAK BOLEH DITIMPA',
        ]);

        $result = $this->store([
            [
                'nim' => '13020250001',
                'name' => 'NAMA DARI SIAKAD',
                'angkatan' => '2025',
                'gender' => 'L',
                'birth_place' => null,
                'birth_date' => null,
            ],
            [
                'nim' => '13020250002',
                'name' => 'MAHASISWA BARU',
                'angkatan' => '2025',
                'gender' => 'P',
                'birth_place' => 'MAKASSAR',
                'birth_date' => '2005-02-01',
            ],
        ], '130');

        $this->assertTrue($result['ok']);
        $this->assertSame(1, $result['added']);
        $this->assertSame(1, $result['already_exists']);
        $this->assertSame(
            'DATA LAMA TIDAK BOLEH DITIMPA',
            DB::table('t_mst_mahasiswa')->where('C_NPM', '13020250001')->value('NAMA_MAHASISWA')
        );
        $this->assertDatabaseHas('t_mst_mahasiswa', [
            'C_NPM' => '13020250002',
            'C_KODE_PRODI' => '55201',
            'NAMA_MAHASISWA' => 'MAHASISWA BARU',
        ]);
    }

    public function testSingleNimUpdatesVerifiedFieldsWithAnAuditAndPreservesOtherData()
    {
        DB::table('t_mst_mahasiswa')->insert([
            'C_KODE_PT' => '091002',
            'C_KODE_FAKULTAS' => '91000',
            'C_KODE_PRODI' => '55201',
            'C_NPM' => '13120250055',
            'NAMA_MAHASISWA' => 'NAMA LAMA',
            'C_KODE_KONSENTRASI' => '55201-K-GEN',
            'TAHUN_MASUK' => '2003',
            'THN_SEMESTER_AWAL_MASUK' => '20031',
            'JENIS_KELAMIN' => 'L',
            'TEMPAT_LAHIR' => 'DATA LAMA',
            'TGL_LAHIR' => '2000-01-01',
            'NO_HP' => '08123456789',
            'D_FOTO_MAHASISWA' => 'existing-photo.jpg',
        ]);
        DB::table('trt_bimbingan')->insert(['C_NPM' => '13120250055', 'judul' => 'Judul tetap']);
        $service = new FakeSiakadMahasiswaService([$this->sourceRow()]);

        $result = $service->syncByNim('13120250055');

        $this->assertTrue($result['ok']);
        $this->assertSame(0, $result['added']);
        $this->assertSame(1, $result['updated']);
        $this->assertTrue($result['semester_needs_review']);
        $this->assertDatabaseHas('t_mst_mahasiswa', [
            'C_NPM' => '13120250055',
            'NAMA_MAHASISWA' => 'NAMA SIAKAD',
            'C_KODE_PRODI' => '57201',
            'C_KODE_KONSENTRASI' => '57201-K-GEN',
            'TAHUN_MASUK' => '2025',
            'THN_SEMESTER_AWAL_MASUK' => '20031',
            'JENIS_KELAMIN' => 'P',
            'TEMPAT_LAHIR' => 'DATA LAMA',
            'TGL_LAHIR' => '2000-01-01',
            'NO_HP' => '08123456789',
            'D_FOTO_MAHASISWA' => 'existing-photo.jpg',
        ]);
        $this->assertDatabaseHas('trt_bimbingan', ['C_NPM' => '13120250055', 'judul' => 'Judul tetap']);
        $audit = DB::table('trt_siakad_mahasiswa_sync_audit')->first();
        $this->assertSame('NAMA LAMA', json_decode($audit->before_values, true)['NAMA_MAHASISWA']);
        $this->assertSame('NAMA SIAKAD', json_decode($audit->after_values, true)['NAMA_MAHASISWA']);
        $this->assertSame(1, DB::table('trt_siakad_mahasiswa_sync_audit')->count());

        $again = $service->syncByNim('13120250055');
        $this->assertTrue($again['ok']);
        $this->assertSame(0, $again['updated']);
        $this->assertTrue($again['semester_needs_review']);
        $this->assertSame(1, DB::table('trt_siakad_mahasiswa_sync_audit')->count());
    }

    public function testExistingDataIsNotChangedOnFailureAmbiguityOrMissingAudit()
    {
        DB::table('t_mst_mahasiswa')->insert([
            'C_KODE_PT' => '091002', 'C_KODE_FAKULTAS' => '91000', 'C_KODE_PRODI' => '55201',
            'C_NPM' => '13120250055', 'NAMA_MAHASISWA' => 'NAMA LAMA',
        ]);
        $missing = new FakeSiakadMahasiswaService([]);
        $ambiguous = new FakeSiakadMahasiswaService([$this->sourceRow(), array_merge($this->sourceRow(), ['nama_mahasiswa' => 'NAMA LAIN'])]);
        $invalid = new FakeSiakadMahasiswaService([$this->sourceRow(), array_merge($this->sourceRow(), ['nama_mahasiswa' => ''])]);

        $this->assertFalse($missing->syncByNim('13120250055')['ok']);
        $this->assertFalse($ambiguous->syncByNim('13120250055')['ok']);
        $this->assertFalse($invalid->syncByNim('13120250055')['ok']);
        Schema::drop('trt_siakad_mahasiswa_sync_audit');
        $this->assertFalse((new FakeSiakadMahasiswaService([$this->sourceRow()]))->syncByNim('13120250055')['ok']);
        $this->assertDatabaseHas('t_mst_mahasiswa', [
            'C_NPM' => '13120250055', 'NAMA_MAHASISWA' => 'NAMA LAMA', 'C_KODE_PRODI' => '55201',
        ]);
    }

    public function testSingleNimAddsNewDataAndCohortStillNeverUpdatesExistingData()
    {
        $service = new FakeSiakadMahasiswaService([$this->sourceRow()]);
        $result = $service->syncByNim('13120250055');
        $this->assertTrue($result['ok']);
        $this->assertSame(1, $result['added']);
        DB::table('t_mst_mahasiswa')->where('C_NPM', '13120250055')->update(['NAMA_MAHASISWA' => 'NAMA DIPERTAHANKAN']);
        $cohort = $service->syncByCohort('131', '2025');
        $this->assertTrue($cohort['ok']);
        $this->assertSame(1, $cohort['already_exists']);
        $this->assertDatabaseHas('t_mst_mahasiswa', ['C_NPM' => '13120250055', 'NAMA_MAHASISWA' => 'NAMA DIPERTAHANKAN']);
        $this->assertSame(0, DB::table('trt_siakad_mahasiswa_sync_audit')->count());
    }

    public function testSyncRouteAllowsTheMatchingProdiAndRecordsTheActor()
    {
        DB::table('t_mst_mahasiswa')->insert([
            'C_KODE_PT' => '091002', 'C_KODE_FAKULTAS' => '91000', 'C_KODE_PRODI' => '55201',
            'C_NPM' => '13120250055', 'NAMA_MAHASISWA' => 'NAMA LAMA',
        ]);
        $this->app->instance(SiakadMahasiswaService::class, new FakeSiakadMahasiswaService([$this->sourceRow()]));
        $user = new User;
        $user->id = 42;
        $user->name = 'prodisi';
        $user->level = 5;

        $this->actingAs($user)->from('/prodi/mahasiswa')
            ->post('/prodi/mahasiswa/sinkronisasi/nim', ['nim' => '13120250055'])
            ->assertRedirect('/prodi/mahasiswa')
            ->assertSessionHas('success', 'NIM 13120250055 berhasil diperbarui sesuai data SIAKAD. Data diperbarui: nama, program studi, angkatan, jenis kelamin.');

        $this->assertDatabaseHas('trt_siakad_mahasiswa_sync_audit', ['C_NPM' => '13120250055', 'user_id' => 42]);
    }

    public function testAnotherProdiCannotUpdateTheStudent()
    {
        DB::table('t_mst_mahasiswa')->insert([
            'C_KODE_PT' => '091002', 'C_KODE_FAKULTAS' => '91000', 'C_KODE_PRODI' => '55201',
            'C_NPM' => '13120250055', 'NAMA_MAHASISWA' => 'NAMA LAMA',
        ]);
        $this->app->instance(SiakadMahasiswaService::class, new FakeSiakadMahasiswaService([$this->sourceRow()]));
        $user = new User;
        $user->id = 43;
        $user->name = 'proditi';
        $user->level = 5;

        $this->actingAs($user)->from('/prodi/mahasiswa')
            ->post('/prodi/mahasiswa/sinkronisasi/nim', ['nim' => '13120250055'])
            ->assertRedirect('/prodi/mahasiswa')
            ->assertSessionHas('error', 'NIM berada di luar program studi yang dapat Anda kelola.');

        $this->assertDatabaseHas('t_mst_mahasiswa', ['C_NPM' => '13120250055', 'NAMA_MAHASISWA' => 'NAMA LAMA']);
        $this->assertSame(0, DB::table('trt_siakad_mahasiswa_sync_audit')->count());
    }

    private function sourceRow()
    {
        return [
            'nim' => '13120250055', 'nama_mahasiswa' => 'NAMA SIAKAD', 'angkatan' => '2025',
            'jkel' => 'P', 'tempatlahir' => '', 'tgllahir' => '2025-02-31',
        ];
    }

    private function store(array $students, $programCode)
    {
        $service = new SiakadMahasiswaService;
        $method = new ReflectionMethod($service, 'storeStudents');
        $method->setAccessible(true);

        return $method->invoke($service, $students, $programCode);
    }
}

class FakeSiakadMahasiswaService extends SiakadMahasiswaService
{
    private $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    protected function fetchStudents($programCode, $angkatan)
    {
        return ['ok' => true, 'rows' => $this->rows];
    }
}
