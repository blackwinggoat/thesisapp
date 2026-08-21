<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddExamTypeToTrtHonoriumTable extends Migration
{
    public function up()
    {
        Schema::table('trt_honorium', function (Blueprint $table) {
            $table->unsignedTinyInteger('exam_type')->nullable()->index()->after('source_key');
        });

        DB::statement("UPDATE trt_honorium SET exam_type = CAST(tipe_ujian AS UNSIGNED) WHERE exam_type IS NULL AND tipe_ujian IN ('0', '2')");
    }

    public function down()
    {
        Schema::table('trt_honorium', function (Blueprint $table) {
            $table->dropIndex(['exam_type']);
            $table->dropColumn('exam_type');
        });
    }
}
