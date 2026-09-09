<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CorrectHonorariumScheduleTypeAlignment extends Migration
{
    public function up()
    {
        $requiredTables = [
            'trt_honorium',
            'trt_reg',
            'trt_jadwal_ujian',
            'trt_jadwal_ujian_per_mhs',
            'mst_pendaftaran',
        ];
        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                return;
            }
        }
        if (!Schema::hasColumn('trt_honorium', 'jadwal_ujian_id')) {
            return;
        }

        if (!Schema::hasTable('trt_honorium_schedule_type_correction_audit')) {
            Schema::create('trt_honorium_schedule_type_correction_audit', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('honorarium_id')->unique();
                $table->unsignedInteger('registration_id');
                $table->unsignedInteger('original_schedule_id');
                $table->unsignedInteger('correct_schedule_id');
                $table->unsignedInteger('original_period_id');
                $table->unsignedInteger('correct_period_id');
                $table->date('original_date')->nullable();
                $table->date('correct_date');
                $table->timestamps();
            });
        }

        $mismatches = DB::table('trt_honorium as honorarium')
            ->join('trt_jadwal_ujian as jadwal', 'jadwal.id', '=', 'honorarium.jadwal_ujian_id')
            ->join('mst_pendaftaran as periode', 'periode.pendaftaran_id', '=', 'jadwal.pendaftaran_id')
            ->whereRaw('periode.tipe_ujian <> honorarium.exam_type')
            ->select(
                'honorarium.id as honorarium_id',
                'honorarium.C_NPM',
                'honorarium.exam_type',
                'honorarium.date as original_date',
                'jadwal.id as original_schedule_id',
                'jadwal.pendaftaran_id as original_period_id'
            )
            ->orderBy('honorarium.id')
            ->get();

        $corrections = [];
        $unresolved = [];
        foreach ($mismatches as $mismatch) {
            $schedules = DB::table('trt_jadwal_ujian as jadwal')
                ->join('trt_jadwal_ujian_per_mhs as peserta', function ($join) use ($mismatch) {
                    $join->on('peserta.jadwal_ujian', '=', 'jadwal.id')
                        ->where('peserta.C_NPM', '=', $mismatch->C_NPM);
                })
                ->join('mst_pendaftaran as periode', 'periode.pendaftaran_id', '=', 'jadwal.pendaftaran_id')
                ->where('periode.tipe_ujian', (int) $mismatch->exam_type)
                ->whereNotNull('jadwal.tgl_ujian')
                ->whereRaw("CAST(jadwal.tgl_ujian AS CHAR) <> '0000-00-00'")
                ->select(
                    'jadwal.id as correct_schedule_id',
                    'jadwal.pendaftaran_id as correct_period_id',
                    'jadwal.tgl_ujian as correct_date'
                )
                ->get()
                ->unique('correct_schedule_id')
                ->values();

            $registrations = DB::table('trt_reg')
                ->where('C_NPM', $mismatch->C_NPM)
                ->where('status', (int) $mismatch->exam_type)
                ->get(['reg_id', 'pendaftaran_id']);

            if ($schedules->count() !== 1 || $registrations->count() !== 1) {
                $unresolved[] = (int) $mismatch->honorarium_id;
                continue;
            }

            $corrections[] = [
                'mismatch' => $mismatch,
                'schedule' => $schedules->first(),
                'registration' => $registrations->first(),
            ];
        }

        if (!empty($unresolved)) {
            throw new RuntimeException(
                'Koreksi jadwal honorarium dibatalkan karena bukti jadwal tidak tunggal untuk ID: '
                . implode(', ', $unresolved)
            );
        }

        DB::transaction(function () use ($corrections) {
            $now = date('Y-m-d H:i:s');
            foreach ($corrections as $correction) {
                $mismatch = $correction['mismatch'];
                $schedule = $correction['schedule'];
                $registration = $correction['registration'];
                $correctDate = substr((string) $schedule->correct_date, 0, 10);

                DB::table('trt_honorium_schedule_type_correction_audit')->updateOrInsert(
                    ['honorarium_id' => (int) $mismatch->honorarium_id],
                    [
                        'registration_id' => (int) $registration->reg_id,
                        'original_schedule_id' => (int) $mismatch->original_schedule_id,
                        'correct_schedule_id' => (int) $schedule->correct_schedule_id,
                        'original_period_id' => (int) $registration->pendaftaran_id,
                        'correct_period_id' => (int) $schedule->correct_period_id,
                        'original_date' => $this->normalizeDate($mismatch->original_date),
                        'correct_date' => $correctDate,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );

                $updatedHonorarium = DB::table('trt_honorium')
                    ->where('id', (int) $mismatch->honorarium_id)
                    ->where('jadwal_ujian_id', (int) $mismatch->original_schedule_id)
                    ->update([
                        'jadwal_ujian_id' => (int) $schedule->correct_schedule_id,
                        'date' => $correctDate,
                    ]);
                $updatedRegistration = DB::table('trt_reg')
                    ->where('reg_id', (int) $registration->reg_id)
                    ->where('pendaftaran_id', (int) $registration->pendaftaran_id)
                    ->update(['pendaftaran_id' => (int) $schedule->correct_period_id]);

                if ($updatedHonorarium !== 1 || $updatedRegistration !== 1) {
                    throw new RuntimeException('Koreksi jadwal honorarium berubah saat diproses dan dibatalkan.');
                }
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('trt_honorium_schedule_type_correction_audit')) {
            return;
        }

        DB::transaction(function () {
            foreach (DB::table('trt_honorium_schedule_type_correction_audit')->orderByDesc('id')->get() as $audit) {
                if (Schema::hasTable('trt_honorium')) {
                    DB::table('trt_honorium')
                        ->where('id', $audit->honorarium_id)
                        ->where('jadwal_ujian_id', $audit->correct_schedule_id)
                        ->update([
                            'jadwal_ujian_id' => $audit->original_schedule_id,
                            'date' => $audit->original_date,
                        ]);
                }
                if (Schema::hasTable('trt_reg')) {
                    DB::table('trt_reg')
                        ->where('reg_id', $audit->registration_id)
                        ->where('pendaftaran_id', $audit->correct_period_id)
                        ->update(['pendaftaran_id' => $audit->original_period_id]);
                }
            }
        });

        Schema::dropIfExists('trt_honorium_schedule_type_correction_audit');
    }

    private function normalizeDate($date)
    {
        $date = trim((string) $date);

        return $date === '' ? null : substr($date, 0, 10);
    }
}
