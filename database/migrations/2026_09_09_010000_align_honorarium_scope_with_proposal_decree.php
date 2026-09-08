<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlignHonorariumScopeWithProposalDecree extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('mst_pembayaran_honorarium')
            || !Schema::hasColumn('mst_pembayaran_honorarium', 'cakupan_ujian')
            || !Schema::hasTable('mst_pembayaran_honorarium_jenis_tugas_akhir')
            || !Schema::hasTable('mst_jenis_tugas_akhir')) {
            return;
        }

        $standardMappings = [
            [
                'scope' => 'proposal',
                'names' => ['Proposal', 'Proposal Eksekutif'],
                'additional_type_prefix' => 'NS-',
            ],
            [
                'scope' => 'ujian_meja',
                'names' => ['Ujian Meja', 'Ujian Meja Eksekutif'],
                'additional_type_prefix' => 'NS-',
            ],
            [
                'scope' => 'gabungan',
                'names' => [
                    'Non Skripsi [proposal + Ujian Meja]',
                    'Non Skripsi [proposal + Ujian Meja] Eksekutif',
                    'Proposal + Ujian Meja',
                    'Proposal + Ujian Meja Eksekutif',
                ],
                'additional_type_prefix' => 'TA-',
            ],
        ];
        $now = date('Y-m-d H:i:s');

        foreach ($standardMappings as $mapping) {
            $paymentIds = DB::table('mst_pembayaran_honorarium')
                ->where('cakupan_ujian', $mapping['scope'])
                ->whereIn('name', $mapping['names'])
                ->pluck('id_honorarium');
            $finalProjectTypeIds = DB::table('mst_jenis_tugas_akhir')
                ->where('kode_jenis_tugas_akhir', 'like', $mapping['additional_type_prefix'] . '%')
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

        $renames = [
            'Non Skripsi [proposal + Ujian Meja]' => 'Proposal + Ujian Meja',
            'Non Skripsi [proposal + Ujian Meja] Eksekutif' => 'Proposal + Ujian Meja Eksekutif',
        ];
        foreach ($renames as $oldName => $newName) {
            DB::table('mst_pembayaran_honorarium')
                ->where('cakupan_ujian', 'gabungan')
                ->where('name', $oldName)
                ->update(['name' => $newName]);

            if (Schema::hasTable('trt_honorium')) {
                DB::table('trt_honorium')
                    ->where('tipe_ujian', $oldName)
                    ->update(['tipe_ujian' => $newName]);
            }
        }
    }

    public function down()
    {
        if (!Schema::hasTable('mst_pembayaran_honorarium')) {
            return;
        }

        $renames = [
            'Proposal + Ujian Meja' => 'Non Skripsi [proposal + Ujian Meja]',
            'Proposal + Ujian Meja Eksekutif' => 'Non Skripsi [proposal + Ujian Meja] Eksekutif',
        ];
        foreach ($renames as $newName => $oldName) {
            DB::table('mst_pembayaran_honorarium')
                ->where('cakupan_ujian', 'gabungan')
                ->where('name', $newName)
                ->update(['name' => $oldName]);

            if (Schema::hasTable('trt_honorium')) {
                DB::table('trt_honorium')
                    ->where('tipe_ujian', $newName)
                    ->update(['tipe_ujian' => $oldName]);
            }
        }

        // Relasi tambahan tidak dihapus karena mungkin telah dipakai oleh honorarium yang ditetapkan setelah migrasi.
    }
}
