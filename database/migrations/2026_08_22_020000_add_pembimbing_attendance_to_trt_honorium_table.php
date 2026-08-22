<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPembimbingAttendanceToTrtHonoriumTable extends Migration
{
    public function up()
    {
        Schema::table('trt_honorium', function (Blueprint $table) {
            if (!Schema::hasColumn('trt_honorium', 'pembimbing_utama_hadir')) {
                $table->unsignedTinyInteger('pembimbing_utama_hadir')->default(1)->after('PP_Stat');
            }

            if (!Schema::hasColumn('trt_honorium', 'pembimbing_pendamping_hadir')) {
                $table->unsignedTinyInteger('pembimbing_pendamping_hadir')->default(1)->after('pembimbing_utama_hadir');
            }
        });
    }

    public function down()
    {
        Schema::table('trt_honorium', function (Blueprint $table) {
            if (Schema::hasColumn('trt_honorium', 'pembimbing_pendamping_hadir')) {
                $table->dropColumn('pembimbing_pendamping_hadir');
            }

            if (Schema::hasColumn('trt_honorium', 'pembimbing_utama_hadir')) {
                $table->dropColumn('pembimbing_utama_hadir');
            }
        });
    }
}
