<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddLecturerExamScheduleIndexes extends Migration
{
    public function up()
    {
        $this->addIndex('trt_reg', 'trt_reg_bimbingan_status_index', ['bimbingan_id', 'status']);
        $this->addIndex('trt_penguji', 'trt_penguji_npm_tipe_index', ['C_NPM', 'tipe_ujian']);
        $this->addIndex('trt_jadwal_ujian_per_mhs', 'trt_jadwal_per_mhs_npm_jadwal_index', ['C_NPM', 'jadwal_ujian']);
    }

    public function down()
    {
        $this->dropIndex('trt_reg', 'trt_reg_bimbingan_status_index');
        $this->dropIndex('trt_penguji', 'trt_penguji_npm_tipe_index');
        $this->dropIndex('trt_jadwal_ujian_per_mhs', 'trt_jadwal_per_mhs_npm_jadwal_index');
    }

    private function addIndex($tableName, $indexName, array $columns)
    {
        if (!Schema::hasTable($tableName) || $this->indexExists($tableName, $indexName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($columns, $indexName) {
            $table->index($columns, $indexName);
        });
    }

    private function dropIndex($tableName, $indexName)
    {
        if (!Schema::hasTable($tableName) || !$this->indexExists($tableName, $indexName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($indexName) {
            $table->dropIndex($indexName);
        });
    }

    private function indexExists($tableName, $indexName)
    {
        $indexes = DB::select('SHOW INDEX FROM ' . $tableName);

        foreach ($indexes as $index) {
            if ($index->Key_name === $indexName) {
                return true;
            }
        }

        return false;
    }
}
