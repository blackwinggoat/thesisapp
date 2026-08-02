<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddJenisTugasAkhirToTrtBimbinganTable extends Migration
{
    public function up()
    {
        $this->withLegacySqlMode(function () {
            Schema::table('trt_bimbingan', function (Blueprint $table) {
                $table->integer('jenis_tugas_akhir_id')->nullable()->after('judul');
                $table->index('jenis_tugas_akhir_id', 'trt_bimbingan_jenis_ta_index');
            });
        });
    }

    public function down()
    {
        $this->withLegacySqlMode(function () {
            Schema::table('trt_bimbingan', function (Blueprint $table) {
                $table->dropIndex('trt_bimbingan_jenis_ta_index');
                $table->dropColumn('jenis_tugas_akhir_id');
            });
        });
    }

    private function withLegacySqlMode(callable $callback)
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            $callback();

            return;
        }

        $originalMode = DB::selectOne('SELECT @@SESSION.sql_mode AS sql_mode')->sql_mode;
        DB::statement("SET SESSION sql_mode = ''");

        try {
            $callback();
        } finally {
            DB::statement('SET SESSION sql_mode = ' . DB::getPdo()->quote($originalMode));
        }
    }
}
