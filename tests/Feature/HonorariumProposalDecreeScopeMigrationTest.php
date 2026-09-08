<?php

namespace Tests\Feature;

use App\Http\Controllers\KeuanganFakultas;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HonorariumProposalDecreeScopeMigrationTest extends TestCase
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

        Schema::create('mst_jenis_tugas_akhir', function (Blueprint $table) {
            $table->increments('jenis_tugas_akhir_id');
            $table->string('kode_jenis_tugas_akhir');
        });
        Schema::create('mst_pembayaran_honorarium', function (Blueprint $table) {
            $table->increments('id_honorarium');
            $table->string('name');
            $table->string('cakupan_ujian', 20);
            $table->boolean('untuk_mahasiswa_eksekutif')->default(0);
            $table->unsignedInteger('nominal_penanda')->default(0);
        });
        Schema::create('mst_pembayaran_honorarium_jenis_tugas_akhir', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_honorarium');
            $table->unsignedInteger('jenis_tugas_akhir_id');
            $table->timestamps();
            $table->unique(['id_honorarium', 'jenis_tugas_akhir_id']);
        });
        Schema::create('trt_honorium', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tipe_ujian')->nullable();
            $table->unsignedInteger('nominal_penanda')->default(0);
        });
        Schema::create('trt_penguji', function (Blueprint $table) {
            $table->increments('id');
            $table->string('C_NPM');
            $table->integer('tipe_ujian');
            $table->string('nomor_sk')->nullable();
        });
    }

    public function testMigrationExtendsOnlyStandardMastersAndPreservesNominals()
    {
        $typeIds = [];
        foreach (['TA-SM', 'TA-SK', 'NS-KT', 'NS-AI'] as $code) {
            $typeIds[$code] = DB::table('mst_jenis_tugas_akhir')->insertGetId([
                'kode_jenis_tugas_akhir' => $code,
            ]);
        }

        $standardMasters = [
            ['Proposal', 'proposal', 100],
            ['Ujian Meja', 'ujian_meja', 200],
            ['Non Skripsi [proposal + Ujian Meja]', 'gabungan', 300],
        ];
        $masterIds = [];
        foreach ($standardMasters as $master) {
            $masterIds[$master[1]] = DB::table('mst_pembayaran_honorarium')->insertGetId([
                'name' => $master[0],
                'cakupan_ujian' => $master[1],
                'nominal_penanda' => $master[2],
            ]);
        }
        $customMasterId = DB::table('mst_pembayaran_honorarium')->insertGetId([
            'name' => 'Master Khusus',
            'cakupan_ujian' => 'proposal',
            'nominal_penanda' => 999,
        ]);

        foreach (['proposal', 'ujian_meja'] as $scope) {
            foreach (['TA-SM', 'TA-SK'] as $code) {
                $this->insertMapping($masterIds[$scope], $typeIds[$code]);
            }
        }
        foreach (['NS-KT', 'NS-AI'] as $code) {
            $this->insertMapping($masterIds['gabungan'], $typeIds[$code]);
        }
        DB::table('trt_honorium')->insert([
            'tipe_ujian' => 'Non Skripsi [proposal + Ujian Meja]',
            'nominal_penanda' => 777,
        ]);

        require_once __DIR__ . '/../../database/migrations/2026_09_09_010000_align_honorarium_scope_with_proposal_decree.php';
        (new \AlignHonorariumScopeWithProposalDecree)->up();

        foreach ($masterIds as $masterId) {
            $this->assertSame(
                4,
                DB::table('mst_pembayaran_honorarium_jenis_tugas_akhir')
                    ->where('id_honorarium', $masterId)
                    ->count()
            );
        }
        $this->assertSame(
            0,
            DB::table('mst_pembayaran_honorarium_jenis_tugas_akhir')
                ->where('id_honorarium', $customMasterId)
                ->count()
        );
        $this->assertSame(
            'Proposal + Ujian Meja',
            DB::table('mst_pembayaran_honorarium')
                ->where('id_honorarium', $masterIds['gabungan'])
                ->value('name')
        );
        $this->assertSame(300, (int) DB::table('mst_pembayaran_honorarium')
            ->where('id_honorarium', $masterIds['gabungan'])
            ->value('nominal_penanda'));
        $this->assertSame('Proposal + Ujian Meja', DB::table('trt_honorium')->value('tipe_ujian'));
        $this->assertSame(777, (int) DB::table('trt_honorium')->value('nominal_penanda'));
    }

    public function testProposalDecreeResolverRequiresNonEmptyProposalDecreeNumber()
    {
        DB::table('trt_penguji')->insert([
            ['C_NPM' => 'student-with-decree', 'tipe_ujian' => 0, 'nomor_sk' => 'SK-PROP-001'],
            ['C_NPM' => 'student-blank-decree', 'tipe_ujian' => 0, 'nomor_sk' => ''],
            ['C_NPM' => 'student-final-only', 'tipe_ujian' => 2, 'nomor_sk' => 'SK-FINAL-001'],
        ]);

        $method = new \ReflectionMethod(KeuanganFakultas::class, 'mahasiswaDenganSkProposalByNim');
        $method->setAccessible(true);
        $result = $method->invoke(new KeuanganFakultas, [
            'student-with-decree',
            'student-blank-decree',
            'student-final-only',
        ]);

        $this->assertTrue($result->has('student-with-decree'));
        $this->assertFalse($result->has('student-blank-decree'));
        $this->assertFalse($result->has('student-final-only'));
    }

    private function insertMapping($paymentId, $finalProjectTypeId)
    {
        DB::table('mst_pembayaran_honorarium_jenis_tugas_akhir')->insert([
            'id_honorarium' => $paymentId,
            'jenis_tugas_akhir_id' => $finalProjectTypeId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
