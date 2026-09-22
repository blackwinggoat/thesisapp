<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDosenSignatureNormalizationBackups extends Migration
{
    public function up()
    {
        if (Schema::hasTable('mst_tanda_tangan_normalization_backups')) {
            return;
        }

        Schema::create('mst_tanda_tangan_normalization_backups', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_tanda_tangan')->unique();
            $table->string('C_KODE_DOSEN', 20)->nullable();
            $table->longText('original_tanda_tangan_base64');
            $table->string('original_sha256', 64);
            $table->string('normalized_sha256', 64)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mst_tanda_tangan_normalization_backups');
    }
}
