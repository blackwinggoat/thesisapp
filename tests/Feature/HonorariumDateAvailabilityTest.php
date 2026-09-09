<?php

namespace Tests\Feature;

use App\Http\Controllers\KeuanganFakultas;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HonorariumDateAvailabilityTest extends TestCase
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
            $table->unsignedTinyInteger('exam_type')->nullable();
            $table->unsignedInteger('jadwal_ujian_id')->nullable();
            $table->string('tipe_ujian', 50)->nullable();
            foreach (['KS', 'PU', 'PP', 'P1', 'P2', 'P3'] as $role) {
                $table->string($role)->nullable();
                $table->decimal($role . '_H', 15, 2)->default(0);
                $table->integer($role . '_Stat')->default(0);
            }
        });
        Schema::create('trt_reg', function (Blueprint $table) {
            $table->increments('reg_id');
            $table->integer('pendaftaran_id');
            $table->integer('status');
            $table->string('C_NPM', 20);
        });
        Schema::create('mst_pendaftaran', function (Blueprint $table) {
            $table->increments('pendaftaran_id');
            $table->integer('tipe_ujian');
        });
        Schema::create('trt_jadwal_ujian', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pendaftaran_id');
            $table->date('tgl_ujian');
        });
        Schema::create('trt_jadwal_ujian_per_mhs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('C_NPM', 20);
            $table->integer('jadwal_ujian');
        });
    }

    public function testDateSwitchChangesOnlyTheSelectedExamDate()
    {
        $this->insertHonorarium(1, 'student-a', '2026-09-01', 10, 100, 'KS');
        $this->insertHonorarium(2, 'student-b', '2026-09-01', 10, 100, 'PU');
        $this->insertHonorarium(3, 'student-c', '2026-09-02', 20, 200, 'KS');

        $response = (new KeuanganFakultas)->honorarium_update_date_availability(
            Request::create('/honorarium/tanggal/2026-09-01/availability', 'POST', ['available' => 1]),
            '2026-09-01'
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(1, (int) DB::table('trt_honorium')->where('id', 1)->value('KS_Stat'));
        $this->assertSame(1, (int) DB::table('trt_honorium')->where('id', 2)->value('PU_Stat'));
        $this->assertSame(0, (int) DB::table('trt_honorium')->where('id', 3)->value('KS_Stat'));

        $response = (new KeuanganFakultas)->honorarium_update_date_availability(
            Request::create('/honorarium/tanggal/2026-09-01/availability', 'POST', ['available' => 0]),
            '2026-09-01'
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(0, (int) DB::table('trt_honorium')->where('id', 1)->value('KS_Stat'));
        $this->assertSame(0, (int) DB::table('trt_honorium')->where('id', 2)->value('PU_Stat'));
    }

    public function testDateSwitchIsAtomicWhenTypeIsMissing()
    {
        $this->insertHonorarium(4, 'student-ready', '2026-09-03', 30, 300, 'KS');
        $this->insertHonorarium(5, 'student-unset', '2026-09-03', 30, 300, 'PU', '0');

        $response = (new KeuanganFakultas)->honorarium_update_date_availability(
            Request::create('/honorarium/tanggal/2026-09-03/availability', 'POST', ['available' => 1]),
            '2026-09-03'
        );

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(0, (int) DB::table('trt_honorium')->where('id', 4)->value('KS_Stat'));
        $this->assertSame(0, (int) DB::table('trt_honorium')->where('id', 5)->value('PU_Stat'));
    }

    public function testDateSwitchDoesNotChangeAGroupContainingPaidHonorarium()
    {
        $this->insertHonorarium(6, 'student-unpaid', '2026-09-04', 40, 400, 'KS');
        $this->insertHonorarium(7, 'student-paid', '2026-09-04', 40, 400, 'PU');
        DB::table('trt_honorium')->where('id', 7)->update([
            'PU_Stat' => 3,
            'KS' => 'lecturer-unpaid-role',
            'KS_Stat' => 0,
        ]);

        $response = (new KeuanganFakultas)->honorarium_update_date_availability(
            Request::create('/honorarium/tanggal/2026-09-04/availability', 'POST', ['available' => 1]),
            '2026-09-04'
        );

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(0, (int) DB::table('trt_honorium')->where('id', 6)->value('KS_Stat'));
        $this->assertSame(3, (int) DB::table('trt_honorium')->where('id', 7)->value('PU_Stat'));
    }

    public function testSelectedDateSwitchChangesEverySelectedDateButNotOtherDates()
    {
        $this->insertHonorarium(8, 'student-selected-a', '2026-09-05', 50, 500, 'KS');
        $this->insertHonorarium(9, 'student-selected-b', '2026-09-06', 60, 600, 'PU');
        $this->insertHonorarium(10, 'student-not-selected', '2026-09-07', 70, 700, 'KS');

        $response = (new KeuanganFakultas)->honorarium_update_selected_availability(
            Request::create('/honorarium/availability-selected', 'POST', [
                'available' => 1,
                'tanggal' => ['2026-09-05', '2026-09-06'],
            ])
        );

        $payload = json_decode($response->getContent(), true);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(2, $payload['updated_date_count']);
        $this->assertSame(1, (int) DB::table('trt_honorium')->where('id', 8)->value('KS_Stat'));
        $this->assertSame(1, (int) DB::table('trt_honorium')->where('id', 9)->value('PU_Stat'));
        $this->assertSame(0, (int) DB::table('trt_honorium')->where('id', 10)->value('KS_Stat'));
    }

    public function testSelectedDateSwitchRollsBackAllDatesWhenOneTypeIsMissing()
    {
        $this->insertHonorarium(11, 'student-batch-ready', '2026-09-08', 80, 800, 'KS');
        $this->insertHonorarium(12, 'student-batch-unset', '2026-09-09', 90, 900, 'PU', '2');

        $response = (new KeuanganFakultas)->honorarium_update_selected_availability(
            Request::create('/honorarium/availability-selected', 'POST', [
                'available' => 1,
                'tanggal' => ['2026-09-08', '2026-09-09'],
            ])
        );

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(0, (int) DB::table('trt_honorium')->where('id', 11)->value('KS_Stat'));
        $this->assertSame(0, (int) DB::table('trt_honorium')->where('id', 12)->value('PU_Stat'));
    }

    protected function insertHonorarium($id, $nim, $date, $scheduleId, $periodId, $role, $paymentType = 'Ujian Meja')
    {
        if (!DB::table('mst_pendaftaran')->where('pendaftaran_id', $periodId)->exists()) {
            DB::table('mst_pendaftaran')->insert([
                'pendaftaran_id' => $periodId,
                'tipe_ujian' => 2,
            ]);
        }
        if (!DB::table('trt_jadwal_ujian')->where('id', $scheduleId)->exists()) {
            DB::table('trt_jadwal_ujian')->insert([
                'id' => $scheduleId,
                'pendaftaran_id' => $periodId,
                'tgl_ujian' => $date,
            ]);
        }
        DB::table('trt_reg')->insert([
            'pendaftaran_id' => $periodId,
            'status' => 2,
            'C_NPM' => $nim,
        ]);
        DB::table('trt_jadwal_ujian_per_mhs')->insert([
            'C_NPM' => $nim,
            'jadwal_ujian' => $scheduleId,
        ]);

        $honorarium = [
            'id' => $id,
            'date' => $date,
            'C_NPM' => $nim,
            'exam_type' => 2,
            'jadwal_ujian_id' => $scheduleId,
            'tipe_ujian' => $paymentType,
            $role => 'lecturer-' . $id,
        ];
        DB::table('trt_honorium')->insert($honorarium);
    }
}
