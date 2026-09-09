<?php

namespace Tests\Feature;

use App\Http\Controllers\KeuanganFakultas;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HonorariumScheduleAlignmentTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('trt_honorium', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->nullable();
            $table->string('C_NPM', 20);
            $table->string('source_key', 120)->nullable()->unique();
            $table->unsignedTinyInteger('exam_type')->nullable()->index();
            $table->string('tipe_ujian', 50)->nullable();
            foreach (['KS', 'PU', 'PP', 'P1', 'P2', 'P3'] as $role) {
                $table->string($role)->nullable();
                $table->decimal($role . '_H', 15, 2)->default(0);
                $table->integer($role . '_Stat')->default(0);
            }
        });
        Schema::create('trt_reg', function (Blueprint $table) {
            $table->increments('reg_id');
            $table->integer('pendaftaran_id')->index();
            $table->integer('status')->nullable();
            $table->string('C_NPM', 20);
        });
        Schema::create('trt_jadwal_ujian', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pendaftaran_id')->index();
            $table->date('tgl_ujian');
        });
        Schema::create('trt_jadwal_ujian_per_mhs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('C_NPM', 20);
            $table->integer('jadwal_ujian');
        });
    }

    public function testMigrationUsesExactSourcePeriodAndAlignsStoredDate()
    {
        $this->insertExam('student-source', 2, 100, 10, '2026-08-01');
        $this->insertExam('student-source', 2, 200, 20, '2026-08-15');
        DB::table('trt_honorium')->insert([
            'id' => 1,
            'date' => '2026-08-02',
            'C_NPM' => 'student-source',
            'source_key' => 'bimbingan:9:periode:200:ujian:2',
            'exam_type' => 2,
            'KS' => 'lecturer-1',
        ]);

        $this->runMigration();

        $honorarium = DB::table('trt_honorium')->where('id', 1)->first();
        $this->assertSame(20, (int) $honorarium->jadwal_ujian_id);
        $this->assertSame('2026-08-15', $honorarium->date);
        $this->assertDatabaseHas('trt_honorium_schedule_alignment_audit', [
            'honorarium_id' => 1,
            'jadwal_ujian_id' => 20,
            'tanggal_sebelum' => '2026-08-02',
            'tanggal_jadwal' => '2026-08-15',
            'dasar_pemetaan' => 'source_key',
        ]);
    }

    public function testMigrationOnlyBackfillsLegacyRowsWithUnambiguousEvidence()
    {
        $this->insertExam('student-date-match', 2, 300, 30, '2026-08-10');
        $this->insertExam('student-date-match', 2, 301, 31, '2026-08-20');
        $this->insertExam('student-ambiguous', 2, 400, 40, '2026-08-11');
        $this->insertExam('student-ambiguous', 2, 401, 41, '2026-08-21');

        DB::table('trt_honorium')->insert([
            [
                'id' => 2,
                'date' => '2026-08-20',
                'C_NPM' => 'student-date-match',
                'exam_type' => 2,
                'KS' => 'lecturer-1',
            ],
            [
                'id' => 3,
                'date' => '2026-08-22',
                'C_NPM' => 'student-ambiguous',
                'exam_type' => 2,
                'KS' => 'lecturer-1',
            ],
        ]);

        $this->runMigration();

        $this->assertSame(31, (int) DB::table('trt_honorium')->where('id', 2)->value('jadwal_ujian_id'));
        $this->assertNull(DB::table('trt_honorium')->where('id', 3)->value('jadwal_ujian_id'));
        $this->assertSame(1, DB::table('trt_honorium_schedule_alignment_audit')->count());
    }

    public function testFinanceQueryDisplaysOnlyVerifiedExactScheduleWithoutDuplicates()
    {
        $this->insertExam('student-valid', 2, 500, 50, '2026-08-25');
        DB::table('trt_honorium')->insert([
            'id' => 4,
            'date' => '2026-08-25',
            'C_NPM' => 'student-valid',
            'exam_type' => 2,
            'KS' => 'lecturer-1',
        ]);
        $this->runMigration();

        DB::table('trt_reg')->insert([
            'pendaftaran_id' => 500,
            'status' => 2,
            'C_NPM' => 'student-valid',
        ]);
        DB::table('trt_jadwal_ujian_per_mhs')->insert([
            'C_NPM' => 'student-valid',
            'jadwal_ujian' => 50,
        ]);
        DB::table('trt_honorium')->insert([
            'id' => 5,
            'date' => '2026-08-25',
            'C_NPM' => 'student-invalid',
            'exam_type' => 2,
            'jadwal_ujian_id' => 50,
            'KS' => 'lecturer-1',
        ]);

        $method = new \ReflectionMethod(KeuanganFakultas::class, 'honorariumDenganJadwalQuery');
        $method->setAccessible(true);
        $rows = $method->invoke(new KeuanganFakultas)
            ->select('honorarium.id', 'jadwal.tgl_ujian')
            ->get();

        $this->assertCount(1, $rows);
        $this->assertSame(4, (int) $rows->first()->id);
        $this->assertSame('2026-08-25', $rows->first()->tgl_ujian);
    }

    public function testStrictAuditRejectsAStoredDateThatDiffersFromTheLinkedSchedule()
    {
        $this->insertExam('student-audit', 2, 600, 60, '2026-08-26');
        DB::table('trt_honorium')->insert([
            'id' => 6,
            'date' => '2026-08-26',
            'C_NPM' => 'student-audit',
            'exam_type' => 2,
            'KS' => 'lecturer-1',
        ]);
        $this->runMigration();

        $this->assertSame(0, Artisan::call('thesis:audit-honorarium-schedules', [
            '--strict' => true,
            '--json' => true,
        ]));

        DB::table('trt_honorium')->where('id', 6)->update(['date' => '2026-08-27']);

        $this->assertSame(1, Artisan::call('thesis:audit-honorarium-schedules', [
            '--strict' => true,
            '--json' => true,
        ]));
    }

    private function insertExam($nim, $examType, $periodId, $scheduleId, $date)
    {
        DB::table('trt_reg')->insert([
            'pendaftaran_id' => $periodId,
            'status' => $examType,
            'C_NPM' => $nim,
        ]);
        DB::table('trt_jadwal_ujian')->insert([
            'id' => $scheduleId,
            'pendaftaran_id' => $periodId,
            'tgl_ujian' => $date,
        ]);
        DB::table('trt_jadwal_ujian_per_mhs')->insert([
            'C_NPM' => $nim,
            'jadwal_ujian' => $scheduleId,
        ]);
    }

    private function runMigration()
    {
        require_once __DIR__ . '/../../database/migrations/2026_09_09_020000_align_honorarium_with_exact_exam_schedule.php';
        (new \AlignHonorariumWithExactExamSchedule)->up();
    }
}
