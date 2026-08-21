<?php

namespace Tests\Feature;

use App\Http\Controllers\KeuanganFakultas;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MasterPembayaranJenisTugasAkhirTest extends TestCase
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
            $table->string('kode_jenis_tugas_akhir')->unique();
            $table->string('deskripsi');
            $table->timestamps();
        });
        Schema::create('mst_pembayaran_honorarium', function (Blueprint $table) {
            $table->increments('id_honorarium');
            $table->string('name');
            $table->decimal('ketua_sidang', 12, 2);
            $table->decimal('pembimbing_utama', 12, 2);
            $table->decimal('pembimbing_pendamping', 12, 2);
            $table->decimal('penguji_1', 12, 2);
            $table->decimal('penguji_2', 12, 2);
            $table->decimal('penguji_3', 12, 2);
            $table->timestamps();
        });
        Schema::create('mst_pembayaran_honorarium_jenis_tugas_akhir', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_honorarium');
            $table->unsignedInteger('jenis_tugas_akhir_id');
            $table->timestamps();
            $table->unique(['id_honorarium', 'jenis_tugas_akhir_id']);
        });

        foreach ([
            ['TA-SM', 'Tugas Akhir Skripsi Mandiri'],
            ['TA-SK', 'Tugas Akhir Skripsi Kolaborasi'],
            ['NS-KT', 'Non Skripsi Kajian Tertulis'],
        ] as $jenis) {
            DB::table('mst_jenis_tugas_akhir')->insert([
                'kode_jenis_tugas_akhir' => $jenis[0],
                'deskripsi' => $jenis[1],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function testPaymentTypeCanBeAssignedToMultipleFinalProjectTypes()
    {
        $jenisIds = DB::table('mst_jenis_tugas_akhir')
            ->orderBy('kode_jenis_tugas_akhir')
            ->pluck('jenis_tugas_akhir_id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->all();

        $controller = new KeuanganFakultas;
        $response = $controller->master_pembayaran_store(Request::create('/master_pembayaran/add', 'POST', array_merge(
            $this->paymentPayload('Proposal'),
            ['jenis_tugas_akhir_ids' => [$jenisIds[0], $jenisIds[1], $jenisIds[1]]]
        )));

        $idHonorarium = DB::table('mst_pembayaran_honorarium')->value('id_honorarium');
        $expectedJenisIds = [$jenisIds[0], $jenisIds[1]];
        sort($expectedJenisIds);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(
            $expectedJenisIds,
            DB::table('mst_pembayaran_honorarium_jenis_tugas_akhir')
                ->where('id_honorarium', $idHonorarium)
                ->orderBy('jenis_tugas_akhir_id')
                ->pluck('jenis_tugas_akhir_id')
                ->map(function ($id) {
                    return (int) $id;
                })
                ->all()
        );
    }

    public function testEditingPaymentTypeReplacesOnlyItsFinalProjectTypeMappings()
    {
        $jenisIds = DB::table('mst_jenis_tugas_akhir')
            ->orderBy('kode_jenis_tugas_akhir')
            ->pluck('jenis_tugas_akhir_id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->all();
        $idHonorarium = DB::table('mst_pembayaran_honorarium')->insertGetId(array_merge(
            $this->paymentPayload('Ujian Meja'),
            ['created_at' => now(), 'updated_at' => now()]
        ));
        DB::table('mst_pembayaran_honorarium_jenis_tugas_akhir')->insert([
            [
                'id_honorarium' => $idHonorarium,
                'jenis_tugas_akhir_id' => $jenisIds[0],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_honorarium' => $idHonorarium,
                'jenis_tugas_akhir_id' => $jenisIds[1],
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = (new KeuanganFakultas)->master_pembayaran_update(Request::create('/master_pembayaran/update', 'POST', array_merge(
            $this->paymentPayload('Ujian Meja Revisi'),
            [
                'id_honorarium' => $idHonorarium,
                'jenis_tugas_akhir_ids' => [$jenisIds[2]],
            ]
        )));

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('Ujian Meja Revisi', DB::table('mst_pembayaran_honorarium')->where('id_honorarium', $idHonorarium)->value('name'));
        $this->assertSame(
            [$jenisIds[2]],
            DB::table('mst_pembayaran_honorarium_jenis_tugas_akhir')
                ->where('id_honorarium', $idHonorarium)
                ->pluck('jenis_tugas_akhir_id')
                ->map(function ($id) {
                    return (int) $id;
                })
                ->all()
        );
    }

    private function paymentPayload($name)
    {
        return [
            'name' => $name,
            'ketua_sidang' => 100000,
            'pembimbing_utama' => 100000,
            'pembimbing_pendamping' => 100000,
            'penguji_1' => 100000,
            'penguji_2' => 100000,
            'penguji_3' => 100000,
        ];
    }
}
