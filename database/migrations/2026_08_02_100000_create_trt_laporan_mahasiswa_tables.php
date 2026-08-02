<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrtLaporanMahasiswaTables extends Migration
{
    public function up()
    {
        Schema::create('trt_laporan_mahasiswa', function (Blueprint $table) {
            $table->increments('laporan_mahasiswa_id');
            $table->string('C_NPM', 15)->index();
            $table->string('C_KODE_DOSEN', 20)->index();
            $table->string('C_KODE_PRODI', 10)->index();
            $table->unsignedInteger('bimbingan_id')->nullable()->index();
            $table->string('kategori', 50);
            $table->string('perihal', 255);
            $table->text('uraian');
            $table->string('status', 20)->default('baru')->index();
            $table->text('tindakan_terakhir')->nullable();
            $table->unsignedInteger('tindakan_oleh_user_id')->nullable();
            $table->timestamp('tindakan_pada')->nullable();
            $table->timestamps();
        });

        Schema::create('trt_laporan_mahasiswa_pesan', function (Blueprint $table) {
            $table->increments('laporan_mahasiswa_pesan_id');
            $table->unsignedInteger('laporan_mahasiswa_id')->index();
            $table->unsignedInteger('pengirim_user_id')->nullable();
            $table->string('pengirim_peran', 20);
            $table->string('nama_pengirim', 255);
            $table->text('isi_pesan');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trt_laporan_mahasiswa_pesan');
        Schema::dropIfExists('trt_laporan_mahasiswa');
    }
}
