<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Dosen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//      Schema::create('dosen', function (Blueprint $table) {
//          $table->increments('id');
//          $table->string('nidn', 20)->nullable();
//          $table->string('nip', 50)->nullable();
//          $table->string('nama_dosen', 100);
//          $table->unsignedInteger('prodi_id')->nullable();
//          $table->unsignedInteger('jabatan_id')->nullable();
//          $table->enum('jk',['Pria','Wanita'])->nullable();
//          $table->string('tempat_lahir',50)->nullable();
//          $table->date('tgl_lahir')->nullable();
//          $table->string('kota',100)->nullable();
//          $table->text('alamat')->nullable();
//          $table->string('nohp',15)->nullable();
//          $table->string('website',100)->nullable();
//          $table->string('pendidikan_terakhir',100)->nullable();
//          $table->date('waktu_masuk')->nullable();
//          $table->text('foto')->nullable();
//          $table->enum('jabatan_fungsional',['Asisten Ahli', 'Lektor', 'Kepala Lektor', 'Guru Besar'])->nullable();
//          $table->enum('ruang',['A', 'B', 'C', 'D'])->nullable();
//          $table->unsignedInteger('user_id')->nullable();
//          $table->timestamps();
//
//          $table->unique(['nohp']);
//          $table->index(['prodi_id','jabatan_id','user_id']);
//          $table->foreign('prodi_id')->references('id')->on('prodi');
//          $table->foreign('jabatan_id')->references('id')->on('jabatan');
//          $table->foreign('user_id')->references('id')->on('users');
//      });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
//      Schema::dropIfExists('dosen');
  }
}
