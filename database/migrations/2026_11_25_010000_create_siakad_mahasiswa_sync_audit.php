<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiakadMahasiswaSyncAudit extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('trt_siakad_mahasiswa_sync_audit')) {
            Schema::create('trt_siakad_mahasiswa_sync_audit', function (Blueprint $table) {
                $table->increments('id');
                $table->string('C_NPM', 15)->index();
                $table->unsignedInteger('user_id')->nullable();
                $table->longText('before_values');
                $table->longText('after_values');
                $table->dateTime('created_at');
            });
        }
    }

    public function down()
    {
        // Riwayat perubahan identitas tidak dihapus otomatis saat rollback.
    }
}
