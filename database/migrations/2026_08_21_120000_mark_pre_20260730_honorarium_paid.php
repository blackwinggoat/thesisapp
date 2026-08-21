<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MarkPre20260730HonorariumPaid extends Migration
{
    public function up()
    {
        DB::statement("
            UPDATE trt_honorium AS honorarium
            SET
                KS_Stat = CASE WHEN NULLIF(TRIM(COALESCE(KS, '')), '') IS NOT NULL THEN 3 ELSE KS_Stat END,
                PU_Stat = CASE WHEN NULLIF(TRIM(COALESCE(PU, '')), '') IS NOT NULL THEN 3 ELSE PU_Stat END,
                PP_Stat = CASE WHEN NULLIF(TRIM(COALESCE(PP, '')), '') IS NOT NULL THEN 3 ELSE PP_Stat END,
                P1_Stat = CASE WHEN NULLIF(TRIM(COALESCE(P1, '')), '') IS NOT NULL THEN 3 ELSE P1_Stat END,
                P2_Stat = CASE WHEN NULLIF(TRIM(COALESCE(P2, '')), '') IS NOT NULL THEN 3 ELSE P2_Stat END,
                P3_Stat = CASE WHEN NULLIF(TRIM(COALESCE(P3, '')), '') IS NOT NULL THEN 3 ELSE P3_Stat END
            WHERE EXISTS (
                SELECT 1
                FROM trt_reg AS registrasi
                INNER JOIN trt_jadwal_ujian_per_mhs AS peserta
                    ON peserta.C_NPM = registrasi.C_NPM
                INNER JOIN trt_jadwal_ujian AS jadwal
                    ON jadwal.id = peserta.jadwal_ujian
                    AND jadwal.pendaftaran_id = registrasi.pendaftaran_id
                WHERE registrasi.C_NPM = honorarium.C_NPM
                    AND registrasi.status = honorarium.exam_type
                    AND CAST(jadwal.tgl_ujian AS CHAR) < '2026-07-30'
                    AND CAST(jadwal.tgl_ujian AS CHAR) <> '0000-00-00'
            )
        ");
    }

    public function down()
    {
        // Financial status updates are intentionally not reversed automatically.
    }
}
