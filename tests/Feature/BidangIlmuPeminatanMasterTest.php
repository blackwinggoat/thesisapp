<?php

namespace Tests\Feature;

use App\Http\Controllers\mhs;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ReflectionMethod;
use Tests\TestCase;

class BidangIlmuPeminatanMasterTest extends TestCase
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

        Schema::create('trt_topik', function (Blueprint $table) {
            $table->increments('topik_id');
            $table->string('C_NPM', 15);
            $table->string('bidang_ilmu_peminatan', 150)->nullable();
        });
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('password');
            $table->integer('level');
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('trt_topik')->insert([
            ['C_NPM' => '13020220001', 'bidang_ilmu_peminatan' => 'Rekayasa Perangkat Lunak'],
            ['C_NPM' => '13120220001', 'bidang_ilmu_peminatan' => 'Rekayasa Perangkat Lunak'],
        ]);

        require_once __DIR__ . '/../../database/migrations/2026_09_06_010000_create_mst_bidang_ilmu_peminatan_table.php';
        (new \CreateMstBidangIlmuPeminatanTable)->up();
    }

    public function testMigrationCreatesSeparateMastersAndBackfillsLegacyTopics()
    {
        $tiId = DB::table('mst_bidang_ilmu_peminatan')
            ->where('kode_prodi', '130')
            ->where('nama_peminatan', 'Rekayasa Perangkat Lunak')
            ->value('bidang_ilmu_peminatan_id');
        $siId = DB::table('mst_bidang_ilmu_peminatan')
            ->where('kode_prodi', '131')
            ->where('nama_peminatan', 'Rekayasa Perangkat Lunak')
            ->value('bidang_ilmu_peminatan_id');

        $this->assertSame(4, DB::table('mst_bidang_ilmu_peminatan')->where('kode_prodi', '130')->count());
        $this->assertSame(4, DB::table('mst_bidang_ilmu_peminatan')->where('kode_prodi', '131')->count());
        $this->assertNotSame($tiId, $siId);
        $this->assertSame($tiId, DB::table('trt_topik')->where('C_NPM', '13020220001')->value('bidang_ilmu_peminatan_id'));
        $this->assertSame($siId, DB::table('trt_topik')->where('C_NPM', '13120220001')->value('bidang_ilmu_peminatan_id'));
    }

    public function testStudentChoicesAreLimitedToTheirProgramAndActiveStatus()
    {
        $inactiveTiId = DB::table('mst_bidang_ilmu_peminatan')
            ->where('kode_prodi', '130')
            ->where('nama_peminatan', 'Industri')
            ->value('bidang_ilmu_peminatan_id');
        DB::table('mst_bidang_ilmu_peminatan')
            ->where('bidang_ilmu_peminatan_id', $inactiveTiId)
            ->update(['status_aktif' => 0]);

        $controller = new mhs;
        $queryMethod = new ReflectionMethod(mhs::class, 'bidangIlmuPeminatanMahasiswaQuery');
        $queryMethod->setAccessible(true);

        $tiChoices = $queryMethod->invoke($controller, '13020220001')
            ->pluck('kode_prodi')
            ->all();
        $siChoices = $queryMethod->invoke($controller, '13120220001')
            ->pluck('kode_prodi')
            ->all();
        $tiIncludingHistorical = $queryMethod->invoke($controller, '13020220001', $inactiveTiId)
            ->pluck('bidang_ilmu_peminatan_id')
            ->all();

        $this->assertSame(['130', '130', '130'], $tiChoices);
        $this->assertSame(['131', '131', '131', '131'], $siChoices);
        $this->assertContains($inactiveTiId, $tiIncludingHistorical);
    }

    public function testForgedPeminatanFromAnotherProgramIsRejected()
    {
        $siId = DB::table('mst_bidang_ilmu_peminatan')
            ->where('kode_prodi', '131')
            ->value('bidang_ilmu_peminatan_id');

        $controller = new mhs;
        $method = new ReflectionMethod(mhs::class, 'bidangIlmuPeminatanMahasiswaDapatDipilih');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($controller, $siId, '13020220001'));
        $this->assertNotNull($method->invoke($controller, $siId, '13120220001'));
    }

    public function testAdminSeesBothProgramsWhileProdiOnlySeesTheirOwnProgram()
    {
        $admin = User::create([
            'name' => 'master-admin',
            'email' => 'master-admin@example.test',
            'password' => bcrypt('secret'),
            'level' => 1,
        ]);
        $prodiTi = User::create([
            'name' => 'proditi',
            'email' => 'proditi@example.test',
            'password' => bcrypt('secret'),
            'level' => 5,
        ]);

        $this->actingAs($admin)
            ->get('/prodi/master/bidang_ilmu_peminatan')
            ->assertStatus(200)
            ->assertSee('Teknik Informatika')
            ->assertSee('data-prodi="Sistem Informasi"', false);

        $this->actingAs($prodiTi)
            ->get('/prodi/master/bidang_ilmu_peminatan')
            ->assertStatus(200)
            ->assertSee('Teknik Informatika')
            ->assertDontSee('data-prodi="Sistem Informasi"', false);
    }

    public function testProdiCannotCreateOrChangeAnotherProgramsMaster()
    {
        $prodiTi = User::create([
            'name' => 'proditi',
            'email' => 'proditi@example.test',
            'password' => bcrypt('secret'),
            'level' => 5,
        ]);
        $siId = DB::table('mst_bidang_ilmu_peminatan')
            ->where('kode_prodi', '131')
            ->value('bidang_ilmu_peminatan_id');

        $this->actingAs($prodiTi)->post('/prodi/master/bidang_ilmu_peminatan', [
            'kode_prodi' => '131',
            'nama_peminatan' => 'Kecerdasan Artifisial',
            'status_aktif' => 1,
        ])->assertRedirect('/prodi/master/bidang_ilmu_peminatan');

        $this->assertDatabaseHas('mst_bidang_ilmu_peminatan', [
            'kode_prodi' => '130',
            'nama_peminatan' => 'Kecerdasan Artifisial',
        ]);
        $this->assertDatabaseMissing('mst_bidang_ilmu_peminatan', [
            'kode_prodi' => '131',
            'nama_peminatan' => 'Kecerdasan Artifisial',
        ]);

        $this->actingAs($prodiTi)->post('/prodi/master/bidang_ilmu_peminatan/' . $siId . '/availability', [
            'status_aktif' => 0,
        ])->assertRedirect('/prodi/master/bidang_ilmu_peminatan');

        $this->assertSame(1, (int) DB::table('mst_bidang_ilmu_peminatan')
            ->where('bidang_ilmu_peminatan_id', $siId)
            ->value('status_aktif'));
    }

    public function testRenamingMasterKeepsLegacyTopicTextSynchronized()
    {
        $admin = User::create([
            'name' => 'master-admin',
            'email' => 'master-admin@example.test',
            'password' => bcrypt('secret'),
            'level' => 1,
        ]);
        $tiId = DB::table('mst_bidang_ilmu_peminatan')
            ->where('kode_prodi', '130')
            ->where('nama_peminatan', 'Industri')
            ->value('bidang_ilmu_peminatan_id');
        DB::table('trt_topik')->insert([
            'C_NPM' => '13020220001',
            'bidang_ilmu_peminatan' => 'Industri',
            'bidang_ilmu_peminatan_id' => $tiId,
        ]);

        $this->actingAs($admin)->post('/prodi/master/bidang_ilmu_peminatan/' . $tiId . '/update', [
            'nama_peminatan' => 'Sistem Industri',
            'status_aktif' => 1,
        ])->assertRedirect('/prodi/master/bidang_ilmu_peminatan');

        $this->assertSame('Sistem Industri', DB::table('trt_topik')
            ->where('bidang_ilmu_peminatan_id', $tiId)
            ->value('bidang_ilmu_peminatan'));
    }
}
