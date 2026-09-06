<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMstBidangIlmuPeminatanTable extends Migration
{
    public function up()
    {
        Schema::create('mst_bidang_ilmu_peminatan', function (Blueprint $table) {
            $table->increments('bidang_ilmu_peminatan_id');
            $table->string('kode_prodi', 3);
            $table->string('nama_peminatan', 150);
            $table->boolean('status_aktif')->default(1);
            $table->timestamps();

            $table->unique(['kode_prodi', 'nama_peminatan'], 'mst_peminatan_prodi_nama_unique');
            $table->index(['kode_prodi', 'status_aktif'], 'mst_peminatan_prodi_status_index');
        });

        $now = date('Y-m-d H:i:s');
        $rows = [];
        $peminatan = [
            'Rekayasa Perangkat Lunak',
            'Jaringan Komputer',
            'Industri',
            'Lainnya',
        ];

        foreach (['130', '131'] as $kodeProdi) {
            foreach ($peminatan as $namaPeminatan) {
                $rows[] = [
                    'kode_prodi' => $kodeProdi,
                    'nama_peminatan' => $namaPeminatan,
                    'status_aktif' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('mst_bidang_ilmu_peminatan')->insert($rows);

        Schema::table('trt_topik', function (Blueprint $table) {
            $table->unsignedInteger('bidang_ilmu_peminatan_id')->nullable()->after('bidang_ilmu_peminatan');
            $table->index('bidang_ilmu_peminatan_id', 'trt_topik_peminatan_index');
        });

        foreach (DB::table('mst_bidang_ilmu_peminatan')->get() as $master) {
            DB::table('trt_topik')
                ->where('C_NPM', 'LIKE', $master->kode_prodi . '%')
                ->where('bidang_ilmu_peminatan', $master->nama_peminatan)
                ->update(['bidang_ilmu_peminatan_id' => $master->bidang_ilmu_peminatan_id]);
        }
    }

    public function down()
    {
        if (Schema::hasColumn('trt_topik', 'bidang_ilmu_peminatan_id')) {
            Schema::table('trt_topik', function (Blueprint $table) {
                $table->dropIndex('trt_topik_peminatan_index');
                $table->dropColumn('bidang_ilmu_peminatan_id');
            });
        }

        Schema::dropIfExists('mst_bidang_ilmu_peminatan');
    }
}
