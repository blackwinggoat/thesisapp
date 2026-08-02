<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Console\Commands\BackfillJenisTugasAkhir;
use App\Console\Commands\BackupJenisTugasAkhir;
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
            $table->text('judul');
            $table->integer('jenis_tugas_akhir_id')->nullable();
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
        $path = sys_get_temp_dir() . '/thesis-jenis-ta-' . bin2hex(random_bytes(8)) . '.json';

        $command = $this->app->make(BackupJenisTugasAkhir::class);
        $command->setLaravel($this->app);
        $exitCode = $command->run(new ArrayInput(['path' => $path]), new BufferedOutput);
        $backup = json_decode(file_get_contents($path), true);

        $this->assertSame(0, $exitCode);
        $this->assertSame(1, count($backup['trt_bimbingan']));
        $this->assertSame(5, count($backup['mst_jenis_tugas_akhir']));
        $this->assertSame(0600, fileperms($path) & 0777);

        unlink($path);
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
