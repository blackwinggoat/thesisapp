<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackfillConfirmedHonorariumFor20260831 extends Migration
{
    const EXAM_DATE = '2026-08-31';

    public function up()
    {
        $requiredTables = [
            'trt_honorium',
            'trt_reg',
            'trt_bimbingan',
            'trt_penguji',
            'trt_jadwal_ujian',
            'trt_jadwal_ujian_per_mhs',
            'mst_pendaftaran',
        ];
        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                return;
            }
        }
        foreach (['source_key', 'exam_type', 'jadwal_ujian_id'] as $column) {
            if (!Schema::hasColumn('trt_honorium', $column)) {
                return;
            }
        }

        if (!Schema::hasTable('trt_honorium_confirmation_backfill_audit')) {
            Schema::create('trt_honorium_confirmation_backfill_audit', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('honorarium_id')->unique();
                $table->unsignedInteger('registration_id');
                $table->unsignedInteger('guidance_id');
                $table->unsignedInteger('schedule_id');
                $table->unsignedInteger('period_id');
                $table->string('C_NPM', 20);
                $table->unsignedTinyInteger('exam_type');
                $table->date('exam_date');
                $table->string('reason', 80);
                $table->timestamps();
            });
        }

        $candidates = DB::table('trt_jadwal_ujian as jadwal')
            ->join('trt_jadwal_ujian_per_mhs as peserta', 'peserta.jadwal_ujian', '=', 'jadwal.id')
            ->join('mst_pendaftaran as periode', 'periode.pendaftaran_id', '=', 'jadwal.pendaftaran_id')
            ->join('trt_reg as registrasi', function ($join) {
                $join->on('registrasi.C_NPM', '=', 'peserta.C_NPM')
                    ->on('registrasi.pendaftaran_id', '=', 'jadwal.pendaftaran_id')
                    ->on('registrasi.status', '=', 'periode.tipe_ujian');
            })
            ->join('trt_bimbingan as bimbingan', function ($join) {
                $join->on('bimbingan.bimbingan_id', '=', 'registrasi.bimbingan_id')
                    ->on('bimbingan.C_NPM', '=', 'peserta.C_NPM');
            })
            ->join('trt_penguji as penguji', function ($join) {
                $join->on('penguji.C_NPM', '=', 'peserta.C_NPM')
                    ->on('penguji.tipe_ujian', '=', 'periode.tipe_ujian');
            })
            ->whereDate('jadwal.tgl_ujian', self::EXAM_DATE)
            ->where('periode.tipe_ujian', 2)
            ->where('bimbingan.status_bimbingan', 3)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('trt_honorium as honorarium')
                    ->whereRaw('honorarium.C_NPM = peserta.C_NPM')
                    ->whereRaw('honorarium.exam_type = periode.tipe_ujian')
                    ->whereRaw('honorarium.jadwal_ujian_id = jadwal.id');
            })
            ->select(
                'jadwal.id as schedule_id',
                'jadwal.pendaftaran_id as period_id',
                'jadwal.tgl_ujian as exam_date',
                'peserta.C_NPM',
                'periode.tipe_ujian as exam_type',
                'registrasi.reg_id as registration_id',
                'bimbingan.bimbingan_id as guidance_id',
                'bimbingan.pembimbing_I_id',
                'bimbingan.pembimbing_II_id',
                'penguji.ketua_sidang_id',
                'penguji.penguji_I_id',
                'penguji.penguji_II_id',
                'penguji.penguji_III_id'
            )
            ->orderBy('jadwal.id')
            ->orderBy('peserta.C_NPM')
            ->get()
            ->unique(function ($row) {
                return $row->schedule_id . '|' . $row->C_NPM . '|' . $row->exam_type;
            })
            ->values();

        DB::transaction(function () use ($candidates) {
            foreach ($candidates as $candidate) {
                $sourceKey = 'bimbingan:' . (int) $candidate->guidance_id
                    . ':periode:' . (int) $candidate->period_id
                    . ':ujian:' . (int) $candidate->exam_type;

                if (DB::table('trt_honorium')
                    ->where(function ($query) use ($candidate, $sourceKey) {
                        $query->where('source_key', $sourceKey)
                            ->orWhere(function ($exact) use ($candidate) {
                                $exact->where('C_NPM', $candidate->C_NPM)
                                    ->where('exam_type', (int) $candidate->exam_type)
                                    ->where('jadwal_ujian_id', (int) $candidate->schedule_id);
                            });
                    })
                    ->exists()
                ) {
                    continue;
                }

                $payload = [
                    'date' => substr((string) $candidate->exam_date, 0, 10),
                    'C_NPM' => $candidate->C_NPM,
                    'source_key' => $sourceKey,
                    'exam_type' => (int) $candidate->exam_type,
                    'jadwal_ujian_id' => (int) $candidate->schedule_id,
                    'tipe_ujian' => (string) $candidate->exam_type,
                ];
                $roles = [
                    'KS' => $candidate->ketua_sidang_id,
                    'PU' => $candidate->pembimbing_I_id,
                    'PP' => $candidate->pembimbing_II_id,
                    'P1' => $candidate->penguji_I_id,
                    'P2' => $candidate->penguji_II_id,
                    'P3' => $candidate->penguji_III_id,
                ];
                foreach ($roles as $role => $lecturerId) {
                    $hasLecturer = trim((string) $lecturerId) !== '';
                    $payload[$role] = $hasLecturer ? $lecturerId : null;
                    $payload[$role . '_H'] = 0;
                    $payload[$role . '_Stat'] = $hasLecturer ? 0 : 3;
                }

                $honorariumId = DB::table('trt_honorium')->insertGetId($payload);
                DB::table('trt_honorium_confirmation_backfill_audit')->insert([
                    'honorarium_id' => $honorariumId,
                    'registration_id' => (int) $candidate->registration_id,
                    'guidance_id' => (int) $candidate->guidance_id,
                    'schedule_id' => (int) $candidate->schedule_id,
                    'period_id' => (int) $candidate->period_id,
                    'C_NPM' => $candidate->C_NPM,
                    'exam_type' => (int) $candidate->exam_type,
                    'exam_date' => substr((string) $candidate->exam_date, 0, 10),
                    'reason' => 'bulk_confirmation_missing_honorarium',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('trt_honorium_confirmation_backfill_audit')) {
            return;
        }

        DB::transaction(function () {
            foreach (DB::table('trt_honorium_confirmation_backfill_audit')->orderByDesc('id')->get() as $audit) {
                DB::table('trt_honorium')
                    ->where('id', $audit->honorarium_id)
                    ->where('C_NPM', $audit->C_NPM)
                    ->where('jadwal_ujian_id', $audit->schedule_id)
                    ->delete();
            }
        });

        Schema::dropIfExists('trt_honorium_confirmation_backfill_audit');
    }
}
