<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddMahasiswaAvailabilityToJenisTugasAkhirTable extends Migration
{
    public function up()
    {
        $this->withLegacySqlMode(function () {
            Schema::table('mst_jenis_tugas_akhir', function (Blueprint $table) {
                $table->boolean('tersedia_untuk_mahasiswa')->default(1)->after('deskripsi');
            });
        });

        DB::table('mst_jenis_tugas_akhir')
            ->whereIn('kode_jenis_tugas_akhir', ['NS-AR', 'NS-KP'])
            ->update(['tersedia_untuk_mahasiswa' => 0]);

        DB::table('mst_jenis_tugas_akhir')
            ->where('kode_jenis_tugas_akhir', 'TA-SM')
            ->update(['deskripsi' => 'Tugas Akhir Skripsi Mandiri']);

        DB::table('mst_jenis_tugas_akhir')
            ->where('kode_jenis_tugas_akhir', 'TA-SK')
            ->update(['deskripsi' => 'Tugas Akhir Skripsi Kolaborasi']);
    }

    public function down()
    {
        DB::table('mst_jenis_tugas_akhir')
            ->where('kode_jenis_tugas_akhir', 'TA-SM')
            ->update(['deskripsi' => 'Tugas Akhir Mandiri']);

        DB::table('mst_jenis_tugas_akhir')
            ->where('kode_jenis_tugas_akhir', 'TA-SK')
            ->update(['deskripsi' => 'Tugas Akhir Kolaborasi']);

        $this->withLegacySqlMode(function () {
            Schema::table('mst_jenis_tugas_akhir', function (Blueprint $table) {
                $table->dropColumn('tersedia_untuk_mahasiswa');
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
