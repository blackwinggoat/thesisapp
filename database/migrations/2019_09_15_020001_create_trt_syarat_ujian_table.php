<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrtSyaratUjianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trt_syarat_ujian', function (Blueprint $table) {
            $table->increments('id');
            $table->string("C_NPM",15);
            $table->integer("syarat_ujian_id");
            $table->text("link");
            $table->char("status",1);
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
        Schema::dropIfExists('trt_syarat_ujian');
    }
}
