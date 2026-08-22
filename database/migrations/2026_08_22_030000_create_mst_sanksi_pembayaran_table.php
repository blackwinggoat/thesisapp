<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstSanksiPembayaranTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('mst_sanksi_pembayaran')) {
            Schema::create('mst_sanksi_pembayaran', function (Blueprint $table) {
                $table->increments('id_sanksi_pembayaran');
                $table->decimal('jumlah_sanksi', 15, 2);
                $table->date('tanggal_mulai');
                $table->date('tanggal_selesai');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('mst_sanksi_pembayaran');
    }
}
