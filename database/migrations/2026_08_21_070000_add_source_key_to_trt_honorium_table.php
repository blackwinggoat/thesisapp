<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSourceKeyToTrtHonoriumTable extends Migration
{
    public function up()
    {
        Schema::table('trt_honorium', function (Blueprint $table) {
            $table->string('source_key', 120)->nullable()->unique()->after('C_NPM');
        });
    }

    public function down()
    {
        Schema::table('trt_honorium', function (Blueprint $table) {
            $table->dropUnique(['source_key']);
            $table->dropColumn('source_key');
        });
    }
}
