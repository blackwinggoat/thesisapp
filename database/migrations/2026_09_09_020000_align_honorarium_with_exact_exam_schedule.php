<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlignHonorariumWithExactExamSchedule extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('trt_honorium')) {
            return;
        }

        if (!Schema::hasColumn('trt_honorium', 'jadwal_ujian_id')) {
            Schema::table('trt_honorium', function (Blueprint $table) {
                $table->unsignedInteger('jadwal_ujian_id')->nullable()->index()->after('exam_type');
            });
        }

        if (!Schema::hasTable('trt_honorium_schedule_alignment_audit')) {
            Schema::create('trt_honorium_schedule_alignment_audit', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('honorarium_id')->unique();
                $table->unsignedInteger('jadwal_ujian_id');
                $table->date('tanggal_sebelum')->nullable();
                $table->date('tanggal_jadwal');
                $table->string('dasar_pemetaan', 32);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('trt_reg')
            || !Schema::hasTable('trt_jadwal_ujian')
            || !Schema::hasTable('trt_jadwal_ujian_per_mhs')
            || !Schema::hasTable('mst_pendaftaran')) {
            return;
        }

        $candidates = DB::table('trt_honorium as honorarium')
            ->join('trt_reg as registrasi', function ($join) {
                $join->on('registrasi.C_NPM', '=', 'honorarium.C_NPM')
                    ->on('registrasi.status', '=', 'honorarium.exam_type');
            })
            ->join('trt_jadwal_ujian as jadwal', 'jadwal.pendaftaran_id', '=', 'registrasi.pendaftaran_id')
            ->join('mst_pendaftaran as periode', 'periode.pendaftaran_id', '=', 'jadwal.pendaftaran_id')
            ->join('trt_jadwal_ujian_per_mhs as peserta', function ($join) {
                $join->on('peserta.C_NPM', '=', 'honorarium.C_NPM')
                    ->on('peserta.jadwal_ujian', '=', 'jadwal.id');
            })
            ->whereNull('honorarium.jadwal_ujian_id')
            ->whereNotNull('honorarium.exam_type')
            ->whereRaw('periode.tipe_ujian = honorarium.exam_type')
            ->whereNotNull('jadwal.tgl_ujian')
            ->whereRaw("CAST(jadwal.tgl_ujian AS CHAR) <> '0000-00-00'")
            ->select(
                'honorarium.id as honorarium_id',
                'honorarium.date as tanggal_honorarium',
                'honorarium.source_key',
                'registrasi.pendaftaran_id',
                'jadwal.id as jadwal_ujian_id',
                'jadwal.tgl_ujian'
            )
            ->orderBy('honorarium.id')
            ->orderBy('jadwal.tgl_ujian')
            ->orderBy('jadwal.id')
            ->get()
            ->groupBy('honorarium_id');

        foreach ($candidates as $honorariumId => $rows) {
            $resolved = $this->resolveSchedule($rows);
            if ($resolved === null) {
                continue;
            }

            $honorariumId = (int) $honorariumId;
            $jadwalUjianId = (int) $resolved['row']->jadwal_ujian_id;
            $tanggalSebelum = $this->normalizeDate($resolved['row']->tanggal_honorarium);
            $tanggalJadwal = $this->normalizeDate($resolved['row']->tgl_ujian);
            $now = date('Y-m-d H:i:s');

            DB::table('trt_honorium_schedule_alignment_audit')->updateOrInsert(
                ['honorarium_id' => $honorariumId],
                [
                    'jadwal_ujian_id' => $jadwalUjianId,
                    'tanggal_sebelum' => $tanggalSebelum,
                    'tanggal_jadwal' => $tanggalJadwal,
                    'dasar_pemetaan' => $resolved['basis'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            DB::table('trt_honorium')
                ->where('id', $honorariumId)
                ->whereNull('jadwal_ujian_id')
                ->update([
                    'jadwal_ujian_id' => $jadwalUjianId,
                    'date' => $tanggalJadwal,
                ]);
        }
    }

    public function down()
    {
        if (Schema::hasTable('trt_honorium_schedule_alignment_audit')
            && Schema::hasTable('trt_honorium')
            && Schema::hasColumn('trt_honorium', 'jadwal_ujian_id')) {
            foreach (DB::table('trt_honorium_schedule_alignment_audit')->orderBy('id')->get() as $audit) {
                DB::table('trt_honorium')
                    ->where('id', $audit->honorarium_id)
                    ->where('jadwal_ujian_id', $audit->jadwal_ujian_id)
                    ->update([
                        'date' => $audit->tanggal_sebelum,
                        'jadwal_ujian_id' => null,
                    ]);
            }
        }

        if (Schema::hasColumn('trt_honorium', 'jadwal_ujian_id')) {
            Schema::table('trt_honorium', function (Blueprint $table) {
                $table->dropIndex(['jadwal_ujian_id']);
                $table->dropColumn('jadwal_ujian_id');
            });
        }

        Schema::dropIfExists('trt_honorium_schedule_alignment_audit');
    }

    private function resolveSchedule($rows)
    {
        $rows = collect($rows)->unique('jadwal_ujian_id')->values();
        if ($rows->isEmpty()) {
            return null;
        }

        $sourcePeriod = $this->sourcePeriod($rows->first()->source_key);
        if ($sourcePeriod !== null) {
            $sourceRows = $rows->filter(function ($row) use ($sourcePeriod) {
                return (int) $row->pendaftaran_id === $sourcePeriod;
            })->values();
            if ($sourceRows->count() === 1) {
                return ['row' => $sourceRows->first(), 'basis' => 'source_key'];
            }

            $storedDate = $this->normalizeDate($rows->first()->tanggal_honorarium);
            $sourceDateMatches = $sourceRows->filter(function ($row) use ($storedDate) {
                return $this->normalizeDate($row->tgl_ujian) === $storedDate;
            })->values();

            return $sourceDateMatches->count() === 1
                ? ['row' => $sourceDateMatches->first(), 'basis' => 'source_key_date']
                : null;
        }

        if ($rows->count() === 1) {
            return ['row' => $rows->first(), 'basis' => 'single_candidate'];
        }

        $storedDate = $this->normalizeDate($rows->first()->tanggal_honorarium);
        $dateMatches = $rows->filter(function ($row) use ($storedDate) {
            return $this->normalizeDate($row->tgl_ujian) === $storedDate;
        })->values();

        return $dateMatches->count() === 1
            ? ['row' => $dateMatches->first(), 'basis' => 'stored_date_match']
            : null;
    }

    private function sourcePeriod($sourceKey)
    {
        return preg_match('/(?:^|:)periode:(\d+)(?:$|:)/', (string) $sourceKey, $matches)
            ? (int) $matches[1]
            : null;
    }

    private function normalizeDate($date)
    {
        $date = trim((string) $date);

        return $date === '' ? null : substr($date, 0, 10);
    }
}
