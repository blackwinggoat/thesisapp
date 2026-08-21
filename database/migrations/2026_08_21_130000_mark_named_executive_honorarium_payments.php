<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MarkNamedExecutiveHonorariumPayments extends Migration
{
    public function up()
    {
        DB::table('mst_pembayaran_honorarium')
            ->whereIn('name', [
                'Proposal Eksekutif',
                'Ujian Meja Eksekutif',
                'Non Skripsi [proposal + Ujian Meja] Eksekutif',
            ])
            ->update(['untuk_mahasiswa_eksekutif' => 1]);
    }

    public function down()
    {
        // Existing master-payment classifications are not reverted automatically.
    }
}
