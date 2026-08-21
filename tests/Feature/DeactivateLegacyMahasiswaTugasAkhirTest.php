<?php

namespace Tests\Feature;

use App\Console\Commands\DeactivateLegacyMahasiswaTugasAkhir;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\TestCase;

class DeactivateLegacyMahasiswaTugasAkhirTest extends TestCase
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
            $table->string('C_NPM')->primary();
            $table->string('TAHUN_MASUK')->nullable();
            $table->string('C_KODE_STATUS_AKTIF_MHS');
        });
        Schema::create('trt_bimbingan', function (Blueprint $table) {
            $table->increments('bimbingan_id');
            $table->string('C_NPM');
            $table->integer('status_bimbingan');
        });
    }

    public function testDryRunDoesNotChangeStudentOrBimbinganStatus()
    {
        $this->seedCandidates();

        $this->runCommand(false);

        $this->assertSame('A', DB::table('t_mst_mahasiswa')->where('C_NPM', 'MHS-AKTIF-LAMA')->value('C_KODE_STATUS_AKTIF_MHS'));
        $this->assertSame(0, (int) DB::table('trt_bimbingan')->where('C_NPM', 'MHS-AKTIF-LAMA')->value('status_bimbingan'));
    }

    public function testApplyCannotDeactivateLegacyStudents()
    {
        $this->seedCandidates();

        $this->runCommand(true);

        $this->assertSame('A', DB::table('t_mst_mahasiswa')->where('C_NPM', 'MHS-AKTIF-LAMA')->value('C_KODE_STATUS_AKTIF_MHS'));
        $this->assertSame(0, (int) DB::table('trt_bimbingan')->where('C_NPM', 'MHS-AKTIF-LAMA')->value('status_bimbingan'));
        $this->assertSame('A', DB::table('t_mst_mahasiswa')->where('C_NPM', 'MHS-LULUS')->value('C_KODE_STATUS_AKTIF_MHS'));
        $this->assertSame(3, (int) DB::table('trt_bimbingan')->where('C_NPM', 'MHS-LULUS')->value('status_bimbingan'));
        $this->assertSame('A', DB::table('t_mst_mahasiswa')->where('C_NPM', 'MHS-BARU')->value('C_KODE_STATUS_AKTIF_MHS'));
        $this->assertSame('K', DB::table('t_mst_mahasiswa')->where('C_NPM', 'MHS-SUDAH-NONAKTIF')->value('C_KODE_STATUS_AKTIF_MHS'));
    }

    private function seedCandidates()
    {
        DB::table('t_mst_mahasiswa')->insert([
            ['C_NPM' => 'MHS-AKTIF-LAMA', 'TAHUN_MASUK' => '2017', 'C_KODE_STATUS_AKTIF_MHS' => 'A'],
            ['C_NPM' => 'MHS-LULUS', 'TAHUN_MASUK' => '2016', 'C_KODE_STATUS_AKTIF_MHS' => 'A'],
            ['C_NPM' => 'MHS-BARU', 'TAHUN_MASUK' => '2018', 'C_KODE_STATUS_AKTIF_MHS' => 'A'],
            ['C_NPM' => 'MHS-SUDAH-NONAKTIF', 'TAHUN_MASUK' => '2016', 'C_KODE_STATUS_AKTIF_MHS' => 'K'],
        ]);
        DB::table('trt_bimbingan')->insert([
            ['C_NPM' => 'MHS-AKTIF-LAMA', 'status_bimbingan' => 0],
            ['C_NPM' => 'MHS-LULUS', 'status_bimbingan' => 3],
            ['C_NPM' => 'MHS-BARU', 'status_bimbingan' => 0],
        ]);
    }

    private function runCommand($apply)
    {
        $command = $this->app->make(DeactivateLegacyMahasiswaTugasAkhir::class);
        $command->setLaravel($this->app);
        $parameters = ['--before' => '2018'];
        if ($apply) {
            $parameters['--apply'] = true;
        }

        return $command->run(new ArrayInput($parameters, $command->getDefinition()), new BufferedOutput());
    }
}
