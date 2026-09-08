<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackfillHonorariumMasterFinalProjectTypes extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('mst_pembayaran_honorarium')
            || !Schema::hasTable('mst_pembayaran_honorarium_jenis_tugas_akhir')
            || !Schema::hasTable('mst_jenis_tugas_akhir')) {
            return;
        }

        $groups = [
            [
                'names' => ['Proposal', 'Proposal Eksekutif', 'Ujian Meja', 'Ujian Meja Eksekutif'],
                'type_prefix' => 'TA-',
            ],
            [
                'names' => [
                    'Non Skripsi [proposal + Ujian Meja]',
                    'Non Skripsi [proposal + Ujian Meja] Eksekutif',
                ],
                'type_prefix' => 'NS-',
            ],
        ];
        $now = date('Y-m-d H:i:s');

        foreach ($groups as $group) {
            $paymentIds = DB::table('mst_pembayaran_honorarium')
                ->whereIn('name', $group['names'])
                ->pluck('id_honorarium');
            $finalProjectTypeIds = DB::table('mst_jenis_tugas_akhir')
                ->where('kode_jenis_tugas_akhir', 'like', $group['type_prefix'] . '%')
                ->pluck('jenis_tugas_akhir_id');

            foreach ($paymentIds as $paymentId) {
                foreach ($finalProjectTypeIds as $finalProjectTypeId) {
                    $exists = DB::table('mst_pembayaran_honorarium_jenis_tugas_akhir')
                        ->where('id_honorarium', $paymentId)
                        ->where('jenis_tugas_akhir_id', $finalProjectTypeId)
                        ->exists();
                    if (!$exists) {
                        DB::table('mst_pembayaran_honorarium_jenis_tugas_akhir')->insert([
                            'id_honorarium' => $paymentId,
                            'jenis_tugas_akhir_id' => $finalProjectTypeId,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }
        }
    }

    public function down()
    {
        // Existing master-payment classifications are not removed automatically.
    }
}
