<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrtDraftFinalMahasiswaTable extends Migration
{
    public function up()
    {
        Schema::create('trt_draft_final_mahasiswa', function (Blueprint $table) {
            $table->increments('draft_final_mahasiswa_id');
            $table->string('C_NPM', 15)->unique();
            $table->text('draft_proposal_url')->nullable();
            $table->string('draft_proposal_file_id', 255)->nullable();
            $table->text('draft_tugas_akhir_url')->nullable();
            $table->string('draft_tugas_akhir_file_id', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trt_draft_final_mahasiswa');
    }
}
