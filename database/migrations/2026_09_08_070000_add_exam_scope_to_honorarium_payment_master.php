<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddExamScopeToHonorariumPaymentMaster extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('mst_pembayaran_honorarium')) {
            return;
        }

        if (!Schema::hasColumn('mst_pembayaran_honorarium', 'cakupan_ujian')) {
            Schema::table('mst_pembayaran_honorarium', function (Blueprint $table) {
                $table->string('cakupan_ujian', 20)
                    ->nullable()
                    ->index()
                    ->after('untuk_mahasiswa_eksekutif');
            });
        }

        DB::table('mst_pembayaran_honorarium')
            ->whereIn('name', ['Proposal', 'Proposal Eksekutif'])
            ->update(['cakupan_ujian' => 'proposal']);
        DB::table('mst_pembayaran_honorarium')
            ->whereIn('name', ['Ujian Meja', 'Ujian Meja Eksekutif'])
            ->update(['cakupan_ujian' => 'ujian_meja']);
        DB::table('mst_pembayaran_honorarium')
            ->whereIn('name', [
                'Non Skripsi [proposal + Ujian Meja]',
                'Non Skripsi [proposal + Ujian Meja] Eksekutif',
            ])
            ->update(['cakupan_ujian' => 'gabungan']);
    }

    public function down()
    {
        if (Schema::hasTable('mst_pembayaran_honorarium')
            && Schema::hasColumn('mst_pembayaran_honorarium', 'cakupan_ujian')) {
            Schema::table('mst_pembayaran_honorarium', function (Blueprint $table) {
                $table->dropIndex(['cakupan_ujian']);
                $table->dropColumn('cakupan_ujian');
            });
        }
    }
}
