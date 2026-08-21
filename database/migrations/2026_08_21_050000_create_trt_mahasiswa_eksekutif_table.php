<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrtMahasiswaEksekutifTable extends Migration
{
    public function up()
    {
        Schema::create('trt_mahasiswa_eksekutif', function (Blueprint $table) {
            $table->increments('mahasiswa_eksekutif_id');
            $table->string('C_NPM', 15)->unique();
            $table->unsignedInteger('ditetapkan_oleh_user_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trt_mahasiswa_eksekutif');
    }
}
