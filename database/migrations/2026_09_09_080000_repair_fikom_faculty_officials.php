<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RepairFikomFacultyOfficials extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('mst_periode_jabatan_fakultas')) {
            return;
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

        $today = Carbon::today();

        foreach ($officials as $official) {
            $rows = DB::table('mst_periode_jabatan_fakultas')
                ->whereRaw('UPPER(TRIM(kode_fakultas)) = ?', ['FIKOM'])
                ->whereRaw('LOWER(TRIM(jabatan)) = ?', [strtolower($official['jabatan'])])
                ->orderBy('tanggal_menjabat', 'desc')
                ->get();

            $current = $rows->first(function ($row) use ($today) {
                $start = $row->tanggal_menjabat ? Carbon::parse($row->tanggal_menjabat)->startOfDay() : null;
                $end = $row->tanggal_berakhir ? Carbon::parse($row->tanggal_berakhir)->endOfDay() : null;

                return (!$start || $today->gte($start)) && (!$end || $today->lte($end));
            });

            if ($current) {
                if (trim((string) $current->nama) === '') {
                    DB::table('mst_periode_jabatan_fakultas')
                        ->where('id_jabatan_fakultas', $current->id_jabatan_fakultas)
                        ->update($this->officialPayload($official, false));
                }

                continue;
            }

            $sameStartDate = $rows->first(function ($row) use ($today) {
                if (!$row->tanggal_menjabat) {
                    return false;
                }

                return Carbon::parse($row->tanggal_menjabat)->isSameDay($today);
            });

            if ($sameStartDate) {
                DB::table('mst_periode_jabatan_fakultas')
                    ->where('id_jabatan_fakultas', $sameStartDate->id_jabatan_fakultas)
                    ->update(array_merge($this->officialPayload($official, false), [
                        'tanggal_berakhir' => '2026-12-31',
                    ]));

                continue;
            }

            DB::table('mst_periode_jabatan_fakultas')->insert(array_merge(
                $this->officialPayload($official, true),
                [
                    'tanggal_menjabat' => $today->format('Y-m-d'),
                    'tanggal_berakhir' => '2026-12-31',
                ]
            ));
        }
    }

    private function officialPayload(array $official, $includeCreatedAt)
    {
        $payload = array_merge($official, [
            'kode_fakultas' => 'FIKOM',
            'nama_fakultas' => 'Fakultas Ilmu Komputer',
            'no_telepon' => null,
            'status_aktif' => 1,
            'updated_at' => now(),
        ]);

        if ($includeCreatedAt) {
            $payload['created_at'] = now();
        }

        return $payload;
    }

    public function down()
    {
        // Data pejabat tidak dihapus saat rollback karena dipakai dokumen operasional.
    }
}
