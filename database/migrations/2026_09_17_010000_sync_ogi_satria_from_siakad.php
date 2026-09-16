<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncOgiSatriaFromSiakad extends Migration
{
    const NIM = '13020250228';

    public function up()
    {
        if (!Schema::hasTable('t_mst_mahasiswa')) {
            return;
        }

        if (DB::table('t_mst_mahasiswa')->where('C_NPM', self::NIM)->exists()) {
            return;
        }

        $now = Carbon::now();

        DB::table('t_mst_mahasiswa')->insert([
            'C_KODE_PT' => '091002',
            'C_KODE_FAKULTAS' => '91000',
            'C_KODE_PRODI' => '55201',
            'C_NPM' => self::NIM,
            'NAMA_MAHASISWA' => 'OGI SATRIA',
            'TEMPAT_LAHIR' => 'MAKASSAR',
            'TGL_LAHIR' => '1999-07-09',
            'JENIS_KELAMIN' => 'L',
            'C_KODE_KONSENTRASI' => '55201-K-GEN',
            'TAHUN_MASUK' => '2025',
            'THN_SEMESTER_AWAL_MASUK' => '20251',
            'C_KODE_STATUS_AWAL_MHS' => '2',
            'C_KODE_STATUS_AKTIF_MHS' => 'A',
            'F_AKTIF' => 1,
            'F_IS_C' => 0,
            'F_IS_U' => 0,
            'F_IS_D' => 0,
            'F_CHANGE_LOG' => 'Sinkronisasi terverifikasi dari SIAKAD list-mahasiswa',
            'C_USER' => 'codex-siakad',
            'C_DATE' => $now,
            'U_USER' => 'codex-siakad',
            'U_DATE' => $now,
        ]);
    }

    public function down()
    {
        // Data mahasiswa operasional tidak dihapus otomatis saat rollback.
    }
}
