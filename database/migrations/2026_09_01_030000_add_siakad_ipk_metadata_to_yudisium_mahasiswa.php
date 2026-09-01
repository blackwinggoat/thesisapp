<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSiakadIpkMetadataToYudisiumMahasiswa extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('trt_yudisium_mahasiswa', 'ipk_sumber')) {
            Schema::table('trt_yudisium_mahasiswa', function (Blueprint $table) {
                $table->string('ipk_sumber', 60)->nullable()->after('ipk');
            });
        }

        if (!Schema::hasColumn('trt_yudisium_mahasiswa', 'ipk_disinkronkan_pada')) {
            Schema::table('trt_yudisium_mahasiswa', function (Blueprint $table) {
                $table->timestamp('ipk_disinkronkan_pada')->nullable()->after('ipk_sumber');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('trt_yudisium_mahasiswa', 'ipk_disinkronkan_pada')) {
            Schema::table('trt_yudisium_mahasiswa', function (Blueprint $table) {
                $table->dropColumn('ipk_disinkronkan_pada');
            });
        }

        if (Schema::hasColumn('trt_yudisium_mahasiswa', 'ipk_sumber')) {
            Schema::table('trt_yudisium_mahasiswa', function (Blueprint $table) {
                $table->dropColumn('ipk_sumber');
            });
        }
    }
}
