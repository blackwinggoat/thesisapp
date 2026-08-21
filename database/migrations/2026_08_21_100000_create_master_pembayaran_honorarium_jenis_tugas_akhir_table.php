<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterPembayaranHonorariumJenisTugasAkhirTable extends Migration
{
    public function up()
    {
        Schema::create('mst_pembayaran_honorarium_jenis_tugas_akhir', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_honorarium');
            $table->unsignedInteger('jenis_tugas_akhir_id');
            $table->timestamps();

            $table->unique(
                ['id_honorarium', 'jenis_tugas_akhir_id'],
                'mst_pembayaran_honorarium_jenis_ta_unique'
            );
            $table->index('jenis_tugas_akhir_id', 'mst_pembayaran_jenis_ta_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mst_pembayaran_honorarium_jenis_tugas_akhir');
    }
}
