<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetNsKtMaximumScoreToBPlus extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('mst_jenis_tugas_akhir')
            || !Schema::hasColumn('mst_jenis_tugas_akhir', 'nilai_maksimal')) {
            return;
        }

        DB::table('mst_jenis_tugas_akhir')
            ->where('kode_jenis_tugas_akhir', 'NS-KT')
            ->update(['nilai_maksimal' => 80]);
    }

    public function down()
    {
        if (!Schema::hasTable('mst_jenis_tugas_akhir')
            || !Schema::hasColumn('mst_jenis_tugas_akhir', 'nilai_maksimal')) {
            return;
        }

        DB::table('mst_jenis_tugas_akhir')
            ->where('kode_jenis_tugas_akhir', 'NS-KT')
            ->update(['nilai_maksimal' => 100]);
    }
}
