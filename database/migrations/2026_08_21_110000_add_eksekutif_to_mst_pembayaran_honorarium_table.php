<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEksekutifToMstPembayaranHonorariumTable extends Migration
{
    public function up()
    {
        Schema::table('mst_pembayaran_honorarium', function (Blueprint $table) {
            $table->boolean('untuk_mahasiswa_eksekutif')->default(0)->after('name');
        });
    }

    public function down()
    {
        Schema::table('mst_pembayaran_honorarium', function (Blueprint $table) {
            $table->dropColumn('untuk_mahasiswa_eksekutif');
        });
    }
}
