<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTopikReferenceToTrtBimbinganTable extends Migration
{
    public function up()
    {
        $this->withLegacySqlMode(function () {
            Schema::table('trt_bimbingan', function (Blueprint $table) {
                $table->integer('topik_id')->nullable()->after('bimbingan_id');
                $table->index('topik_id', 'trt_bimbingan_topik_index');
            });
        });
    }

    public function down()
    {
        $this->withLegacySqlMode(function () {
            Schema::table('trt_bimbingan', function (Blueprint $table) {
                $table->dropIndex('trt_bimbingan_topik_index');
                $table->dropColumn('topik_id');
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
