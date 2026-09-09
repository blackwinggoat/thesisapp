<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class JenisTugasAkhirMaximumScoreMigrationTest extends TestCase
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
            $table->decimal('nilai_maksimal', 5, 2)->default(100);
        });

        DB::table('mst_jenis_tugas_akhir')->insert([
            ['kode_jenis_tugas_akhir' => 'NS-KT', 'nilai_maksimal' => 100],
            ['kode_jenis_tugas_akhir' => 'TA-SM', 'nilai_maksimal' => 100],
        ]);
    }

    public function testMigrationSetsOnlyNsKtMaximumScoreToBPlusUpperLimit()
    {
        $migration = $this->migration();
        $migration->up();

        $scores = DB::table('mst_jenis_tugas_akhir')
            ->pluck('nilai_maksimal', 'kode_jenis_tugas_akhir')
            ->map(function ($score) {
                return (float) $score;
            })
            ->all();

        $this->assertSame(80.0, $scores['NS-KT']);
        $this->assertSame(100.0, $scores['TA-SM']);

        $migration->down();
        $this->assertSame(
            100.0,
            (float) DB::table('mst_jenis_tugas_akhir')
                ->where('kode_jenis_tugas_akhir', 'NS-KT')
                ->value('nilai_maksimal')
        );
    }

    private function migration()
    {
        require_once database_path('migrations/2026_09_09_060000_set_ns_kt_maximum_score_to_b_plus.php');

        return new \SetNsKtMaximumScoreToBPlus;
    }
}
