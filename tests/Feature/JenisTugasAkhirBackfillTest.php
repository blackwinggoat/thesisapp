<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Console\Commands\BackfillJenisTugasAkhir;
use App\Console\Commands\BackupJenisTugasAkhir;
use App\Console\Commands\BackfillJenisTugasAkhirUsulan;
use App\Console\Commands\StandardizeJenisTugasAkhir;
use App\Console\Commands\LinkBimbinganTopik;
use App\Http\Controllers\Prodi;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\TestCase;

class JenisTugasAkhirBackfillTest extends TestCase
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

        Schema::create('mst_jenis_tugas_akhir', function (Blueprint $table) {
            $table->increments('jenis_tugas_akhir_id');
            $table->string('kode_jenis_tugas_akhir')->unique();
            $table->string('deskripsi');
            $table->timestamps();
        });
        Schema::create('trt_bimbingan', function (Blueprint $table) {
            $table->increments('bimbingan_id');
            $table->integer('topik_id')->nullable();
            $table->string('C_NPM')->nullable();
            $table->text('judul');
            $table->integer('jenis_tugas_akhir_id')->nullable();
        });
        Schema::create('trt_topik', function (Blueprint $table) {
            $table->increments('topik_id');
            $table->string('C_NPM');
            $table->text('topik');
            $table->integer('status')->default(0);
            $table->integer('jenis_tugas_akhir_id')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
        Schema::create('trt_usulan_judul', function (Blueprint $table) {
            $table->increments('usulan_judul_id');
            $table->string('C_NPM');
            $table->string('KODE_DOSEN');
            $table->string('judul');
            $table->integer('jenis_tugas_akhir_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        foreach ([
            ['NS-AI', 'Non Skripsi Artikel Ilmiah'],
            ['NS-KT', 'Non Skripsi Kajian Tertulis'],
            ['TA-SM', 'Tugas Akhir Mandiri'],
            ['TA-SK', 'Tugas Akhir Kolaborasi'],
            ['NS-PK', 'Non Skripsi Pembelajaran Khusus'],
        ] as $master) {
            DB::table('mst_jenis_tugas_akhir')->insert([
                'kode_jenis_tugas_akhir' => $master[0],
                'deskripsi' => $master[1],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function testBackfillMovesPrefixToTypeAndCanBeRunAgainSafely()
    {
        DB::table('trt_bimbingan')->insert([
            ['judul' => '(NS - KT) Kajian A'],
            ['judul' => '(NS AR) (NS AR) Artikel B'],
            ['judul' => '(NS-KP) Pembelajaran C'],
            ['judul' => '(TA - SM) Mandiri D'],
            ['judul' => 'Judul tanpa kode'],
        ]);

        $firstRun = $this->runBackfill();
        $this->assertSame(0, $firstRun['exit_code'], $firstRun['output']);

        $rows = DB::table('trt_bimbingan as b')
            ->join('mst_jenis_tugas_akhir as j', 'j.jenis_tugas_akhir_id', '=', 'b.jenis_tugas_akhir_id')
            ->orderBy('b.bimbingan_id')
            ->pluck('j.kode_jenis_tugas_akhir', 'b.judul')
            ->all();

        $this->assertSame([
            'Kajian A' => 'NS-KT',
            'Artikel B' => 'NS-AR',
            'Pembelajaran C' => 'NS-KP',
            'Mandiri D' => 'TA-SM',
            'Judul tanpa kode' => 'TA-SM',
        ], $rows);
        $this->assertSame(0, DB::table('mst_jenis_tugas_akhir')->where('kode_jenis_tugas_akhir', 'NS-PK')->count());

        $secondRun = $this->runBackfill();
        $this->assertSame(0, $secondRun['exit_code']);
        $this->assertStringContainsString('Backfill complete: 0 rows updated.', $secondRun['output']);
        $this->assertSame($rows, DB::table('trt_bimbingan as b')
            ->join('mst_jenis_tugas_akhir as j', 'j.jenis_tugas_akhir_id', '=', 'b.jenis_tugas_akhir_id')
            ->orderBy('b.bimbingan_id')
            ->pluck('j.kode_jenis_tugas_akhir', 'b.judul')
            ->all());

        $normalizer = new \ReflectionMethod(Prodi::class, 'judulTanpaKodeJenisTugasAkhir');
        $normalizer->setAccessible(true);
        $this->assertSame('Judul baru', $normalizer->invoke(new Prodi, '(NS-KT) Judul baru'));
    }

    public function testBackupWritesOnlyTheRelevantDatabaseData()
    {
        DB::table('trt_bimbingan')->insert(['judul' => 'Judul cadangan']);
        DB::table('trt_topik')->insert(['C_NPM' => 'MHS1', 'topik' => 'Topik cadangan']);
        DB::table('trt_usulan_judul')->insert(['C_NPM' => 'MHS2', 'KODE_DOSEN' => 'DSN1', 'judul' => 'Usulan cadangan']);
        $path = sys_get_temp_dir() . '/thesis-jenis-ta-' . bin2hex(random_bytes(8)) . '.json';

        $command = $this->app->make(BackupJenisTugasAkhir::class);
        $command->setLaravel($this->app);
        $exitCode = $command->run(new ArrayInput(['path' => $path]), new BufferedOutput);
        $backup = json_decode(file_get_contents($path), true);

        $this->assertSame(0, $exitCode);
        $this->assertSame(1, count($backup['trt_bimbingan']));
        $this->assertSame(5, count($backup['mst_jenis_tugas_akhir']));
        $this->assertSame(1, count($backup['trt_topik']));
        $this->assertSame(1, count($backup['trt_usulan_judul']));
        $this->assertSame(0600, fileperms($path) & 0777);

        unlink($path);
    }

    public function testProposalBackfillSeparatesTypesForStudentAndLecturerTitles()
    {
        DB::table('trt_topik')->insert([
            ['C_NPM' => 'MHS1', 'topik' => '(NS-KT) Kajian mahasiswa'],
            ['C_NPM' => 'MHS2', 'topik' => '(SN/KT) Kajian salah ketik'],
            ['C_NPM' => 'MHS3', 'topik' => 'Topik tanpa kode'],
        ]);
        DB::table('trt_usulan_judul')->insert([
            ['C_NPM' => 'MHS4', 'KODE_DOSEN' => 'DSN1', 'judul' => 'Usulan tanpa kode'],
        ]);

        $command = $this->app->make(BackfillJenisTugasAkhirUsulan::class);
        $command->setLaravel($this->app);
        $output = new BufferedOutput;
        $this->assertSame(0, $command->run(new ArrayInput(['--apply' => true]), $output));

        $topik = DB::table('trt_topik as t')
            ->join('mst_jenis_tugas_akhir as j', 'j.jenis_tugas_akhir_id', '=', 't.jenis_tugas_akhir_id')
            ->orderBy('t.topik_id')
            ->pluck('j.kode_jenis_tugas_akhir', 't.topik')
            ->all();
        $usulan = DB::table('trt_usulan_judul as u')
            ->join('mst_jenis_tugas_akhir as j', 'j.jenis_tugas_akhir_id', '=', 'u.jenis_tugas_akhir_id')
            ->pluck('j.kode_jenis_tugas_akhir', 'u.judul')
            ->all();

        $this->assertSame([
            'Kajian mahasiswa' => 'NS-KT',
            'Kajian salah ketik' => 'NS-KT',
            'Topik tanpa kode' => 'TA-SM',
        ], $topik);
        $this->assertSame(['Usulan tanpa kode' => 'TA-SM'], $usulan);

        $secondRun = $command->run(new ArrayInput(['--apply' => true]), $output);
        $this->assertSame(0, $secondRun);
        $this->assertStringContainsString('Backfill complete: 0 rows updated.', $output->fetch());
    }

    public function testStandardizationUsesOfficialArticleLabelAndMergesNtKt()
    {
        foreach ([['NS-AR', 'Kode historis NS-AR'], ['NT-KT', 'Kode historis NT-KT']] as $master) {
            DB::table('mst_jenis_tugas_akhir')->insert([
                'kode_jenis_tugas_akhir' => $master[0],
                'deskripsi' => $master[1],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $ntKtId = DB::table('mst_jenis_tugas_akhir')->where('kode_jenis_tugas_akhir', 'NT-KT')->value('jenis_tugas_akhir_id');
        DB::table('trt_bimbingan')->insert(['judul' => 'Judul NT', 'jenis_tugas_akhir_id' => $ntKtId]);
        DB::table('trt_topik')->insert(['C_NPM' => 'MHS1', 'topik' => 'Topik NT', 'jenis_tugas_akhir_id' => $ntKtId]);

        $command = $this->app->make(StandardizeJenisTugasAkhir::class);
        $command->setLaravel($this->app);
        $this->assertSame(0, $command->run(new ArrayInput(['--apply' => true]), new BufferedOutput));

        $nsKtId = DB::table('mst_jenis_tugas_akhir')->where('kode_jenis_tugas_akhir', 'NS-KT')->value('jenis_tugas_akhir_id');
        $this->assertSame('Non Skripsi Artikel', DB::table('mst_jenis_tugas_akhir')->where('kode_jenis_tugas_akhir', 'NS-AR')->value('deskripsi'));
        $this->assertSame(0, DB::table('mst_jenis_tugas_akhir')->where('kode_jenis_tugas_akhir', 'NT-KT')->count());
        $this->assertSame($nsKtId, DB::table('trt_bimbingan')->value('jenis_tugas_akhir_id'));
        $this->assertSame($nsKtId, DB::table('trt_topik')->value('jenis_tugas_akhir_id'));
    }

    public function testLinkingGuidanceToTopicsSkipsAmbiguousHistory()
    {
        DB::table('trt_bimbingan')->insert([
            ['C_NPM' => 'MHS1', 'judul' => 'Judul resmi'],
            ['C_NPM' => 'MHS2', 'judul' => 'Judul ambigu'],
        ]);
        DB::table('trt_topik')->insert([
            ['C_NPM' => 'MHS1', 'topik' => 'Topik tunggal', 'status' => 1],
            ['C_NPM' => 'MHS2', 'topik' => 'Topik pertama', 'status' => 1],
            ['C_NPM' => 'MHS2', 'topik' => 'Topik kedua', 'status' => 1],
        ]);

        $command = $this->app->make(LinkBimbinganTopik::class);
        $command->setLaravel($this->app);
        $this->assertSame(0, $command->run(new ArrayInput(['--apply' => true]), new BufferedOutput));

        $this->assertSame(
            DB::table('trt_topik')->where('C_NPM', 'MHS1')->value('topik_id'),
            DB::table('trt_bimbingan')->where('C_NPM', 'MHS1')->value('topik_id')
        );
        $this->assertNull(DB::table('trt_bimbingan')->where('C_NPM', 'MHS2')->value('topik_id'));
        $this->assertSame('Judul resmi', DB::table('trt_bimbingan')->where('C_NPM', 'MHS1')->value('judul'));
    }

    private function runBackfill()
    {
        $command = $this->app->make(BackfillJenisTugasAkhir::class);
        $command->setLaravel($this->app);
        $output = new BufferedOutput;

        return [
            'exit_code' => $command->run(new ArrayInput(['--apply' => true]), $output),
            'output' => $output->fetch(),
        ];
    }
}
