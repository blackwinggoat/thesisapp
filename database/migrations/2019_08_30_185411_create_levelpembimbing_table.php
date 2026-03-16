<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLevelpembimbingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trt_level_pembimbing', function (Blueprint $table) {
            $table->increments('trt_level_pembimbing_id');
            $table->string('C_KODE_DOSEN',20);
            $table->char('level',1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trt_level_pembimbing');
    }
}
