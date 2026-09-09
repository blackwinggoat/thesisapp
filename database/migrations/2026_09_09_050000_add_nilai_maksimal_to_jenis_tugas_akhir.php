<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNilaiMaksimalToJenisTugasAkhir extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('mst_jenis_tugas_akhir')
            || Schema::hasColumn('mst_jenis_tugas_akhir', 'nilai_maksimal')) {
            return;
        }

        Schema::table('mst_jenis_tugas_akhir', function (Blueprint $table) {
            $table->decimal('nilai_maksimal', 5, 2)->default(100)->after('deskripsi');
        });

        DB::table('mst_jenis_tugas_akhir')->update(['nilai_maksimal' => 100]);
    }

    public function down()
    {
        if (Schema::hasTable('mst_jenis_tugas_akhir')
            && Schema::hasColumn('mst_jenis_tugas_akhir', 'nilai_maksimal')) {
            Schema::table('mst_jenis_tugas_akhir', function (Blueprint $table) {
                $table->dropColumn('nilai_maksimal');
            });
        }
    }
}
