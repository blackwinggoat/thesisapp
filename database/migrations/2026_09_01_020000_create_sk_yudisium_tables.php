<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkYudisiumTables extends Migration
{
    public function up()
    {
        Schema::create('trt_sk_yudisium', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('tanggal_ujian');
            $table->unsignedTinyInteger('tipe_ujian')->default(2);
            $table->string('kode_prodi', 3);
            $table->string('nomor_surat', 150)->nullable();
            $table->string('verification_token', 64)->nullable()->unique();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique(
                ['tanggal_ujian', 'tipe_ujian', 'kode_prodi'],
                'sk_yudisium_tanggal_tipe_prodi_unique'
            );
        });

        Schema::create('trt_yudisium_mahasiswa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('tanggal_ujian');
            $table->unsignedTinyInteger('tipe_ujian')->default(2);
            $table->string('C_NPM', 15);
            $table->string('nomor_alumni', 30)->nullable();
            $table->decimal('ipk', 3, 2)->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique(
                ['tanggal_ujian', 'tipe_ujian', 'C_NPM'],
                'yudisium_mahasiswa_tanggal_tipe_nim_unique'
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists('trt_yudisium_mahasiswa');
        Schema::dropIfExists('trt_sk_yudisium');
    }
}
