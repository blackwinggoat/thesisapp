<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAndSeedFikomFacultyOfficials extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('mst_periode_jabatan_fakultas')) {
            Schema::create('mst_periode_jabatan_fakultas', function (Blueprint $table) {
                $table->increments('id_jabatan_fakultas');
                $table->string('kode_fakultas', 20);
                $table->string('nama_fakultas');
                $table->string('jabatan', 40);
                $table->string('nama');
                $table->string('nip_nidn', 30)->nullable();
                $table->string('email')->nullable();
                $table->string('no_telepon', 20)->nullable();
                $table->string('ttd')->nullable();
                $table->date('tanggal_menjabat');
                $table->date('tanggal_berakhir')->nullable();
                $table->boolean('status_aktif')->default(true);
                $table->timestamps();

                $table->unique(
                    ['kode_fakultas', 'jabatan', 'tanggal_menjabat'],
                    'uniq_fakultas_jabatan_periode'
                );
                $table->index(
                    ['kode_fakultas', 'jabatan', 'tanggal_menjabat', 'tanggal_berakhir'],
                    'idx_fakultas_jabatan_tanggal'
                );
                $table->index('status_aktif', 'idx_status_aktif');
            });
        }

        $officials = [
            [
                'jabatan' => 'Dekan',
                'nama' => 'Dr. Ir. Purnawansyah, M.Kom., MTA',
                'nip_nidn' => '0919027301',
                'email' => 'purnawansyah@umi.ac.id',
                'ttd' => 'ttd_dekan.png',
            ],
            [
                'jabatan' => 'Wakil Dekan I',
                'nama' => 'Ir. Yulita Salim, S.Kom., M.T., MTA.',
                'nip_nidn' => '0922078101',
                'email' => 'yulita.salim@umi.ac.id',
                'ttd' => null,
            ],
            [
                'jabatan' => 'Wakil Dekan II',
                'nama' => 'Dr. Ir. Hj. Harlinda, MM., M.Kom., MTA.',
                'nip_nidn' => '114000775',
                'email' => 'harlinda@umi.ac.id',
                'ttd' => null,
            ],
            [
                'jabatan' => 'Wakil Dekan III',
                'nama' => 'Poetri Lestari Lokapitasari Belluano, S.Kom., M.T., MTA.',
                'nip_nidn' => '0916108403',
                'email' => 'poetrilestari@umi.ac.id',
                'ttd' => null,
            ],
        ];

        foreach ($officials as $official) {
            $exists = DB::table('mst_periode_jabatan_fakultas')
                ->where('kode_fakultas', 'FIKOM')
                ->where('jabatan', $official['jabatan'])
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('mst_periode_jabatan_fakultas')->insert(array_merge($official, [
                'kode_fakultas' => 'FIKOM',
                'nama_fakultas' => 'Fakultas Ilmu Komputer',
                'no_telepon' => null,
                'tanggal_menjabat' => '2022-01-01',
                'tanggal_berakhir' => '2026-12-31',
                'status_aktif' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down()
    {
        // Master pejabat dipertahankan agar rollback kode tidak menghapus data operasional.
    }
}
