<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrtRekapUjianSelesaiTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('trt_rekap_ujian_selesai')) {
            Schema::create('trt_rekap_ujian_selesai', function (Blueprint $table) {
                $table->increments('id');
                $table->date('tanggal_ujian');
                $table->unsignedTinyInteger('tipe_ujian')->default(2);
                $table->string('nomor_surat', 150)->nullable();
                $table->unsignedInteger('created_by')->nullable();
                $table->unsignedInteger('updated_by')->nullable();
                $table->timestamps();

                $table->unique(['tanggal_ujian', 'tipe_ujian'], 'rekap_ujian_selesai_tanggal_tipe_unique');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('trt_rekap_ujian_selesai');
    }
}
