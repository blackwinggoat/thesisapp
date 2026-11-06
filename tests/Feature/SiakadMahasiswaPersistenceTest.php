<?php

namespace Tests\Feature;

use App\Services\SiakadMahasiswaService;
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

    private function store(array $students, $programCode)
    {
        $service = new SiakadMahasiswaService;
        $method = new ReflectionMethod($service, 'storeStudents');
        $method->setAccessible(true);

        return $method->invoke($service, $students, $programCode);
    }
}
