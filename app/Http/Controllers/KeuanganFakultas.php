<?php

namespace App\Http\Controllers;

use App\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class KeuanganFakultas extends Controller
{
    public function ubah_password()
    {
        return view('tugasakhir.fakultas.ubah_password', [
            'passwordAction' => url('keuanganfakultas/ubah_password'),
        ]);
    }

    public function ubah_password_post(Request $request)
    {
        if (!Hash::check($request->password_lama, auth()->user()->password)) {
            return redirect()->back()->with('error', 'Password lama tidak sesuai');
        }

        if ($request->password_baru == $request->ulangi_password) {
            DB::update('update users set password = ? where id = ?', [Hash::make($request->password_baru), auth()->id()]);
            return redirect()->back()->with('success', 'Password Berhasil Diubah');
        }

        return redirect()->back()->with('error', 'Password Tidak Sama');
    }

    public function master_pembayaran_home()
    {
        $data = $this->masterPembayaranDenganJenisTugasAkhir();
        $jenisTugasAkhir = DB::table('mst_jenis_tugas_akhir')
            ->orderBy('kode_jenis_tugas_akhir')
            ->get();

        return view('tugasakhir.keuanganfakultas.master_pembayaran', compact(
            'data',
            'jenisTugasAkhir'
        ));
    }

    public function master_pembayaran_store(Request $request)
    {
        try {
            $jenisTugasAkhirIds = $this->validasiJenisTugasAkhirPembayaran($request);

            DB::transaction(function () use ($request, $jenisTugasAkhirIds) {
                $dataPembayaran = [
                    'name' => $request->input('name'),
                    'ketua_sidang' => $request->input('ketua_sidang'),
                    'pembimbing_utama' => $request->input('pembimbing_utama'),
                    'pembimbing_pendamping' => $request->input('pembimbing_pendamping'),
                    'penguji_1' => $request->input('penguji_1'),
                    'penguji_2' => $request->input('penguji_2'),
                    'penguji_3' => $request->input('penguji_3'),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                if ($this->kolomKelasPembayaranTersedia()) {
                    $dataPembayaran['untuk_mahasiswa_eksekutif'] = $request->input('untuk_mahasiswa_eksekutif') ? 1 : 0;
                }

                $idHonorarium = DB::table('mst_pembayaran_honorarium')->insertGetId($dataPembayaran);

                $this->sinkronkanJenisTugasAkhirPembayaran($idHonorarium, $jenisTugasAkhirIds);
            });

            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Tipe pembayaran honorarium berhasil ditambahkan!'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Terjadi kesalahan saat menambahkan tipe pembayaran honorarium: ',
            ]);
        }
    }

    public function master_pembayaran_delete($id)
    {
        try {
            DB::transaction(function () use ($id) {
                if ($this->tabelJenisTugasAkhirPembayaranTersedia()) {
                    DB::table('mst_pembayaran_honorarium_jenis_tugas_akhir')
                        ->where('id_honorarium', $id)
                        ->delete();
                }

                DB::table('mst_pembayaran_honorarium')
                    ->where('id_honorarium', $id)
                    ->delete();
            });

            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Tipe pembayaran honorarium berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Terjadi kesalahan saat menghapus tipe pembayaran honorarium: ',
            ]);
        }
    }

    public function master_pembayaran_update(Request $request)
    {
        try {
            $idHonorarium = (int) $request->input('id_honorarium');
            if ($idHonorarium < 1 || !DB::table('mst_pembayaran_honorarium')->where('id_honorarium', $idHonorarium)->exists()) {
                throw new \RuntimeException('Tipe pembayaran honorarium tidak ditemukan.');
            }

            $jenisTugasAkhirIds = $this->validasiJenisTugasAkhirPembayaran($request);

            DB::transaction(function () use ($request, $idHonorarium, $jenisTugasAkhirIds) {
                $dataPembayaran = [
                    'name' => $request->input('name'),
                    'ketua_sidang' => $request->input('ketua_sidang'),
                    'pembimbing_utama' => $request->input('pembimbing_utama'),
                    'pembimbing_pendamping' => $request->input('pembimbing_pendamping'),
                    'penguji_1' => $request->input('penguji_1'),
                    'penguji_2' => $request->input('penguji_2'),
                    'penguji_3' => $request->input('penguji_3'),
                    'updated_at' => now()
                ];
                if ($this->kolomKelasPembayaranTersedia()) {
                    $dataPembayaran['untuk_mahasiswa_eksekutif'] = $request->input('untuk_mahasiswa_eksekutif') ? 1 : 0;
                }

                DB::table('mst_pembayaran_honorarium')->where('id_honorarium', $idHonorarium)->update($dataPembayaran);

                $this->sinkronkanJenisTugasAkhirPembayaran($idHonorarium, $jenisTugasAkhirIds);
            });

            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Tipe pembayaran honorarium berhasil diubah!'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Terjadi kesalahan saat mengubah tipe pembayaran honorarium: ',
            ]);
        }
    }

    protected function tabelJenisTugasAkhirPembayaranTersedia()
    {
        return Schema::hasTable('mst_pembayaran_honorarium_jenis_tugas_akhir');
    }

    protected function kolomKelasPembayaranTersedia()
    {
        return Schema::hasColumn('mst_pembayaran_honorarium', 'untuk_mahasiswa_eksekutif');
    }

    protected function tabelMahasiswaEksekutifTersedia()
    {
        return Schema::hasTable('trt_mahasiswa_eksekutif');
    }

    protected function validasiJenisTugasAkhirPembayaran(Request $request)
    {
        if (!$this->tabelJenisTugasAkhirPembayaranTersedia()) {
            throw new \RuntimeException('Pengaturan jenis tugas akhir belum tersedia. Jalankan pembaruan database terlebih dahulu.');
        }

        $this->validate($request, [
            'jenis_tugas_akhir_ids' => 'required|array|min:1',
            'jenis_tugas_akhir_ids.*' => 'integer|exists:mst_jenis_tugas_akhir,jenis_tugas_akhir_id',
        ], [
            'jenis_tugas_akhir_ids.required' => 'Pilih minimal satu jenis tugas akhir.',
            'jenis_tugas_akhir_ids.min' => 'Pilih minimal satu jenis tugas akhir.',
        ]);

        return collect($request->input('jenis_tugas_akhir_ids'))
            ->map(function ($id) {
                return (int) $id;
            })
            ->unique()
            ->values()
            ->all();
    }

    protected function sinkronkanJenisTugasAkhirPembayaran($idHonorarium, array $jenisTugasAkhirIds)
    {
        DB::table('mst_pembayaran_honorarium_jenis_tugas_akhir')
            ->where('id_honorarium', $idHonorarium)
            ->delete();

        $now = now();
        $rows = [];
        foreach ($jenisTugasAkhirIds as $jenisTugasAkhirId) {
            $rows[] = [
                'id_honorarium' => $idHonorarium,
                'jenis_tugas_akhir_id' => $jenisTugasAkhirId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('mst_pembayaran_honorarium_jenis_tugas_akhir')->insert($rows);
    }

    protected function masterPembayaranDenganJenisTugasAkhir()
    {
        $data = DB::table('mst_pembayaran_honorarium')
            ->orderBy('name')
            ->get();

        $jenisTugasAkhirByPembayaran = collect();
        if ($this->tabelJenisTugasAkhirPembayaranTersedia() && $data->isNotEmpty()) {
            $jenisTugasAkhirByPembayaran = DB::table('mst_pembayaran_honorarium_jenis_tugas_akhir as relasi')
                ->join('mst_jenis_tugas_akhir as jenis', 'jenis.jenis_tugas_akhir_id', '=', 'relasi.jenis_tugas_akhir_id')
                ->whereIn('relasi.id_honorarium', $data->pluck('id_honorarium')->all())
                ->orderBy('jenis.kode_jenis_tugas_akhir')
                ->select('relasi.id_honorarium', 'jenis.jenis_tugas_akhir_id', 'jenis.kode_jenis_tugas_akhir', 'jenis.deskripsi')
                ->get()
                ->groupBy('id_honorarium');
        }

        return $data->map(function ($pembayaran) use ($jenisTugasAkhirByPembayaran) {
            if (!$this->kolomKelasPembayaranTersedia()) {
                $pembayaran->untuk_mahasiswa_eksekutif = 0;
            }
            $pembayaran->jenis_tugas_akhir = $jenisTugasAkhirByPembayaran->get($pembayaran->id_honorarium, collect());
            $pembayaran->jenis_tugas_akhir_ids = $pembayaran->jenis_tugas_akhir
                ->pluck('jenis_tugas_akhir_id')
                ->map(function ($id) {
                    return (int) $id;
                })
                ->all();

            return $pembayaran;
        });
    }

    protected function jenisTugasAkhirHonorarium($nim)
    {
        return DB::table('trt_bimbingan')
            ->where('C_NPM', $nim)
            ->orderBy('bimbingan_id', 'desc')
            ->value('jenis_tugas_akhir_id');
    }

    protected function pembayaranBerlakuUntukJenisTugasAkhir($idHonorarium, $jenisTugasAkhirId)
    {
        if (!$this->tabelJenisTugasAkhirPembayaranTersedia() || !$jenisTugasAkhirId) {
            return true;
        }

        $relasi = DB::table('mst_pembayaran_honorarium_jenis_tugas_akhir')
            ->where('id_honorarium', $idHonorarium);

        return !$relasi->exists()
            || $relasi->where('jenis_tugas_akhir_id', $jenisTugasAkhirId)->exists();
    }

    protected function mahasiswaEksekutifByNim(array $nims)
    {
        if (!$this->tabelMahasiswaEksekutifTersedia() || empty($nims)) {
            return collect();
        }

        return DB::table('trt_mahasiswa_eksekutif')
            ->whereIn('C_NPM', array_unique($nims))
            ->pluck('C_NPM')
            ->flip();
    }

    protected function pembayaranBerlakuUntukKelasMahasiswa($masterPayment, $mahasiswaEksekutif)
    {
        if (!$this->kolomKelasPembayaranTersedia()) {
            return true;
        }

        return (int) $masterPayment->untuk_mahasiswa_eksekutif === ($mahasiswaEksekutif ? 1 : 0);
    }

    public function honorarium_home()
    {
        $belumTersediaSql = $this->honorariumHasRoleStatusSql(0);
        $data = $this->honorariumDenganJadwalQuery()
            ->whereNotNull('jadwal.tgl_ujian')
            ->where('jadwal.tgl_ujian', '<>', '0000-00-00')
            ->select(
                'jadwal.tgl_ujian as date',
                DB::raw('COUNT(DISTINCT honorarium.C_NPM) as total_mahasiswa'),
                DB::raw("COUNT(DISTINCT CASE WHEN honorarium.C_NPM LIKE '130%' THEN honorarium.C_NPM END) as total_teknik_informatika"),
                DB::raw("COUNT(DISTINCT CASE WHEN honorarium.C_NPM LIKE '131%' THEN honorarium.C_NPM END) as total_sistem_informasi"),
                DB::raw("SUM(CASE WHEN {$belumTersediaSql} THEN 1 ELSE 0 END) as belum_tersedia"),
                DB::raw("SUM(CASE WHEN honorarium.tipe_ujian IS NULL OR honorarium.tipe_ujian = '' OR honorarium.tipe_ujian IN ('0', '2') THEN 1 ELSE 0 END) as perlu_penetapan")
            )
            ->groupBy('jadwal.tgl_ujian')
            ->orderBy('jadwal.tgl_ujian', 'desc')
            ->get();

        $belumTerhubungJadwal = $this->honorariumBelumTerhubungJadwalQuery()->count();

        return view('tugasakhir.keuanganfakultas.honorarium', [
            'data' => $data,
            'belumTerhubungJadwal' => $belumTerhubungJadwal,
        ]);
    }

    public function honorarium_detail_tanggal($date)
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date)) {
            abort(404);
        }

        $data = $this->honorariumDenganJadwalQuery()
            ->whereDate('jadwal.tgl_ujian', $date)
            ->select('honorarium.*', 'jadwal.tgl_ujian as tanggal_ujian')
            ->orderBy('honorarium.C_NPM')
            ->get();

        if ($data->isEmpty()) {
            return redirect()->route('honorarium_home')->with([
                'status' => 'info',
                'message' => 'Tidak ada honorarium aktif dengan jadwal ujian pada tanggal ' . $date . '.',
            ]);
        }

        $dataMasterHonorarium = $this->masterPembayaranDenganJenisTugasAkhir();
        $mahasiswaEksekutif = $this->mahasiswaEksekutifByNim($data->pluck('C_NPM')->all());
        foreach ($data as $honorarium) {
            $honorarium->jenis_tugas_akhir_id = $this->jenisTugasAkhirHonorarium($honorarium->C_NPM);
            $honorarium->mahasiswa_eksekutif = $mahasiswaEksekutif->has($honorarium->C_NPM);
        }

        return view('tugasakhir.keuanganfakultas.honorarium_detail', compact(
            'data',
            'dataMasterHonorarium',
            'date'
        ));
    }

    protected function honorariumBelumLunasQuery()
    {
        return DB::table('trt_honorium')->whereRaw($this->honorariumOutstandingSql());
    }

    protected function honorariumDenganJadwalQuery()
    {
        return DB::table('trt_honorium as honorarium')
            ->whereRaw($this->honorariumOutstandingSql())
            ->leftJoin('trt_reg as registrasi', function ($join) {
                $join->on('registrasi.C_NPM', '=', 'honorarium.C_NPM')
                    ->on('registrasi.status', '=', 'honorarium.exam_type');
            })
            ->leftJoin('trt_jadwal_ujian_per_mhs as peserta', 'peserta.C_NPM', '=', 'honorarium.C_NPM')
            ->leftJoin('trt_jadwal_ujian as jadwal', function ($join) {
                $join->on('jadwal.id', '=', 'peserta.jadwal_ujian')
                    ->on('jadwal.pendaftaran_id', '=', 'registrasi.pendaftaran_id');
            });
    }

    protected function honorariumBelumTerhubungJadwalQuery()
    {
        return DB::table('trt_honorium as honorarium')
            ->whereRaw($this->honorariumOutstandingSql())
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('trt_reg as registrasi')
                    ->join('trt_jadwal_ujian_per_mhs as peserta', 'peserta.C_NPM', '=', 'registrasi.C_NPM')
                    ->join('trt_jadwal_ujian as jadwal', function ($join) {
                        $join->on('jadwal.id', '=', 'peserta.jadwal_ujian')
                            ->on('jadwal.pendaftaran_id', '=', 'registrasi.pendaftaran_id');
                    })
                    ->whereRaw('registrasi.C_NPM = honorarium.C_NPM')
                    ->whereRaw('registrasi.status = honorarium.exam_type')
                    ->whereNotNull('jadwal.tgl_ujian')
                    ->where('jadwal.tgl_ujian', '<>', '0000-00-00');
            });
    }

    protected function honorariumRoles()
    {
        return [
            'KS' => ['amount' => 'KS_H', 'status' => 'KS_Stat', 'master' => 'ketua_sidang'],
            'PU' => ['amount' => 'PU_H', 'status' => 'PU_Stat', 'master' => 'pembimbing_utama'],
            'PP' => ['amount' => 'PP_H', 'status' => 'PP_Stat', 'master' => 'pembimbing_pendamping'],
            'P1' => ['amount' => 'P1_H', 'status' => 'P1_Stat', 'master' => 'penguji_1'],
            'P2' => ['amount' => 'P2_H', 'status' => 'P2_Stat', 'master' => 'penguji_2'],
            'P3' => ['amount' => 'P3_H', 'status' => 'P3_Stat', 'master' => 'penguji_3'],
        ];
    }

    protected function honorariumHasRoleSql($role)
    {
        return "NULLIF(TRIM(COALESCE({$role}, '')), '') IS NOT NULL";
    }

    protected function honorariumHasAnyRoleSql()
    {
        $roles = [];
        foreach (array_keys($this->honorariumRoles()) as $role) {
            $roles[] = $this->honorariumHasRoleSql($role);
        }

        return '(' . implode(' OR ', $roles) . ')';
    }

    protected function honorariumOutstandingSql()
    {
        $conditions = [];
        foreach ($this->honorariumRoles() as $role => $definition) {
            $conditions[] = '(' . $this->honorariumHasRoleSql($role) . ' AND COALESCE(' . $definition['status'] . ', 0) <> 3)';
        }

        return '(' . implode(' OR ', $conditions) . ')';
    }

    protected function honorariumHasRoleStatusSql($status)
    {
        $conditions = [];
        foreach ($this->honorariumRoles() as $role => $definition) {
            $conditions[] = '(' . $this->honorariumHasRoleSql($role) . ' AND COALESCE(' . $definition['status'] . ', 0) = ' . (int) $status . ')';
        }

        return implode(' OR ', $conditions);
    }

    protected function honorariumFullyPaidSql()
    {
        $conditions = [];
        foreach ($this->honorariumRoles() as $role => $definition) {
            $conditions[] = '(NOT (' . $this->honorariumHasRoleSql($role) . ') OR ' . $definition['status'] . ' = 3)';
        }

        return $this->honorariumHasAnyRoleSql() . ' AND ' . implode(' AND ', $conditions);
    }

    protected function honorariumNeedsTypeAssignment($honorarium)
    {
        return empty($honorarium->tipe_ujian) || in_array((string) $honorarium->tipe_ujian, ['0', '2'], true);
    }

    protected function honorariumHasPaidRole($honorarium)
    {
        foreach ($this->honorariumRoles() as $role => $definition) {
            if (trim((string) $honorarium->{$role}) !== '' && (int) $honorarium->{$definition['status']} === 3) {
                return true;
            }
        }

        return false;
    }

    protected function honorariumStatusPayload($honorarium, $status)
    {
        $payload = [];
        foreach ($this->honorariumRoles() as $role => $definition) {
            if (trim((string) $honorarium->{$role}) !== '') {
                $payload[$definition['status']] = (int) $status;
            }
        }

        return $payload;
    }

    protected function honorariumPaymentPayload($honorarium, $masterPayment)
    {
        $payload = ['tipe_ujian' => $masterPayment->name];
        foreach ($this->honorariumRoles() as $role => $definition) {
            $payload[$definition['amount']] = trim((string) $honorarium->{$role}) !== ''
                ? $masterPayment->{$definition['master']}
                : 0;
        }

        return $payload;
    }


    public function honorarium_available_post_yes(Request $request)
    {
        try {
            $id = (int) $request->id_honorarium;
            if ($id < 1) {
                throw new \RuntimeException('Data honorarium tidak valid.');
            }

            DB::transaction(function () use ($id) {
                $honorarium = DB::table('trt_honorium')->where('id', $id)->lockForUpdate()->first();
                if (!$honorarium) {
                    throw new \RuntimeException('Data honorarium tidak ditemukan.');
                }
                if ($this->honorariumNeedsTypeAssignment($honorarium)) {
                    throw new \RuntimeException('Tipe honorarium harus ditetapkan dan disimpan sebelum dana tersedia.');
                }
                if ($this->honorariumHasPaidRole($honorarium)) {
                    throw new \RuntimeException('Honorarium yang sudah memiliki pembayaran tidak dapat diubah menjadi tersedia.');
                }

                $payload = $this->honorariumStatusPayload($honorarium, 1);
                if (empty($payload)) {
                    throw new \RuntimeException('Tidak ada peran dosen yang dapat dibayarkan pada data ini.');
                }

                DB::table('trt_honorium')->where('id', $id)->update($payload);
            });

            return response()->json(['message' => 'Honorarium berhasil di set menjadi Tersedia!'], 200);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengubah status honorarium menjadi tersedia.'], 500);
        }
    }

    public function honorarium_available_post_no(Request $request)
    {
        try {
            $id = (int) $request->id_honorarium;
            if ($id < 1) {
                throw new \RuntimeException('Data honorarium tidak valid.');
            }

            DB::transaction(function () use ($id) {
                $honorarium = DB::table('trt_honorium')->where('id', $id)->lockForUpdate()->first();
                if (!$honorarium) {
                    throw new \RuntimeException('Data honorarium tidak ditemukan.');
                }
                if ($this->honorariumHasPaidRole($honorarium)) {
                    throw new \RuntimeException('Honorarium yang sudah memiliki pembayaran tidak dapat dikembalikan ke tidak tersedia.');
                }

                $payload = $this->honorariumStatusPayload($honorarium, 0);
                if (empty($payload)) {
                    throw new \RuntimeException('Tidak ada peran dosen yang dapat diubah pada data ini.');
                }

                DB::table('trt_honorium')->where('id', $id)->update($payload);
            });

            return response()->json(['message' => 'Honrarium berhasil di set menjadi Tidak Tersedia!'], 200);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengubah status honorarium menjadi tidak tersedia.'], 500);
        }
    }

    public function honorarium_save_all(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $honorariumIds = collect((array) $request->honorariums)
                    ->pluck('id')
                    ->map(function ($id) {
                        return (int) $id;
                    })
                    ->filter()
                    ->all();
                $mahasiswaEksekutif = $this->mahasiswaEksekutifByNim(
                    DB::table('trt_honorium')->whereIn('id', $honorariumIds)->pluck('C_NPM')->all()
                );

                foreach ((array) $request->honorariums as $honorariumData) {
                    if (!isset($honorariumData['id_pembayaran']) || $honorariumData['id_pembayaran'] === 'unset') {
                        continue;
                    }

                    $id = isset($honorariumData['id']) ? (int) $honorariumData['id'] : 0;
                    $existingHonorarium = DB::table('trt_honorium')->where('id', $id)->lockForUpdate()->first();
                    if (!$existingHonorarium) {
                        throw new \RuntimeException('Salah satu data honorarium tidak ditemukan. Tidak ada perubahan yang disimpan.');
                    }
                    if ($this->honorariumHasPaidRole($existingHonorarium)) {
                        throw new \RuntimeException('Tipe atau nominal honorarium yang sudah Lunas tidak dapat diubah.');
                    }

                    $idPembayaran = (int) $honorariumData['id_pembayaran'];
                    if ($idPembayaran < 1) {
                        throw new \RuntimeException('Tipe honorarium yang dipilih tidak valid.');
                    }

                    $masterPayment = DB::table('mst_pembayaran_honorarium')
                        ->where('id_honorarium', $idPembayaran)
                        ->first();
                    if (!$masterPayment) {
                        throw new \RuntimeException('Tipe honorarium yang dipilih tidak ditemukan pada master pembayaran.');
                    }
                    if (!$this->pembayaranBerlakuUntukJenisTugasAkhir(
                        $masterPayment->id_honorarium,
                        $this->jenisTugasAkhirHonorarium($existingHonorarium->C_NPM)
                    )) {
                        throw new \RuntimeException('Tipe honorarium yang dipilih tidak berlaku untuk jenis tugas akhir mahasiswa ini.');
                    }
                    if (!$this->pembayaranBerlakuUntukKelasMahasiswa(
                        $masterPayment,
                        $mahasiswaEksekutif->has($existingHonorarium->C_NPM)
                    )) {
                        throw new \RuntimeException('Tipe honorarium yang dipilih tidak berlaku untuk kelas mahasiswa ini.');
                    }

                    DB::table('trt_honorium')
                        ->where('id', $id)
                        ->update($this->honorariumPaymentPayload($existingHonorarium, $masterPayment));
                }
            });

            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Honorarium data has been saved successfully!'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function honorarium_history()
    {
        $data = DB::table('trt_honorium')
            ->whereRaw($this->honorariumFullyPaidSql())
            ->get();

        $dataMasterHonorarium = $this->masterPembayaranDenganJenisTugasAkhir();

        return view('tugasakhir.keuanganfakultas.history_honorarium', [
            'data' => $data,
            'dataMasterHonorarium' => $dataMasterHonorarium
        ]);
    }

    public function report_periode_ujian_home()
    {
        try {
            $belumTersediaSql = $this->honorariumHasRoleStatusSql(0);
            $tersediaSql = $this->honorariumHasRoleStatusSql(1);
            $data = DB::table('trt_honorium')
                ->select(
                    'date',
                    DB::raw("
                        CASE
                            WHEN SUM(CASE WHEN {$belumTersediaSql} THEN 1 ELSE 0 END) > 0 THEN 0
                            WHEN SUM(CASE WHEN {$tersediaSql} THEN 1 ELSE 0 END) > 0 THEN 1
                            ELSE 2
                        END AS status_complete
                    ")
                )
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();
            return view('tugasakhir.keuanganfakultas.periode_ujian', ['data' => $data]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Terjadi kesalahan saat mengambil data periode ujian: ',
            ]);
        }
    }

    public function report_periode_ujian_by_date($date)
    {
        try {
            $data = DB::table('trt_honorium')
                ->where('date', $date)
                ->get();


            return view('tugasakhir.keuanganfakultas.periode_ujian_detail', [
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Terjadi kesalahan saat mengambil data periode ujian: ',
            ]);
        }
    }

    public function cetak_honorarium_per_mahasiswa($id)
    {
        try {
            $data = DB::table('trt_honorium')
                ->where('id', $id)
                ->first();

            $processedData = [];

            if ($data) {
                foreach (['PU', 'PP', 'KS', 'P1', 'P2', 'P3'] as $posisi) {
                    $nama = Helper::getDeskripsi($data->$posisi);
                    $honor = $data->{$posisi . '_H'};
                    $nidn = $data->{$posisi};
                    if (in_array($posisi, ['PU', 'PP'])) {
                        $jumlah = $honor / 0.95;
                        $ppn = $jumlah * 0.05;
                        $bantuan_internet = $jumlah * 0.30;
                        $transport = $jumlah * 0.40;
                        $pustaka = $jumlah * 0.30;

                        $processedData[] = [
                            'C_NPM' => $data->C_NPM,
                            'TIPE_UJIAN' => $data->tipe_ujian,
                            'NAMA' => $nama,
                            'POSISI' => $posisi,
                            'HONOR' => Helper::formatRupiahWithoutRp($honor),
                            'PPN' => Helper::formatRupiahWithoutRp($ppn),
                            'JUMLAH' => Helper::formatRupiahWithoutRp($jumlah),
                            'BANTUAN INTERNET' => Helper::formatRupiahWithoutRp($bantuan_internet),
                            'TRANSPORT' => Helper::formatRupiahWithoutRp($transport),
                            'PUSTAKA' => Helper::formatRupiahWithoutRp($pustaka),
                            'TOTAL' => Helper::formatRupiahWithoutRp($honor),
                            'TANDA_TANGAN' => helper::getTandaTanganByKodeDosen($nidn),
                            'TANGGAL_UJIAN' => $data->date
                        ];
                    } else {
                        $bantuan_internet = $honor * 0.30;
                        $transport = $honor * 0.40;
                        $pustaka = $honor * 0.30;

                        $processedData[] = [
                            'C_NPM' => $data->C_NPM,
                            'TIPE_UJIAN' => $data->tipe_ujian,
                            'NAMA' => $nama,
                            'POSISI' => $posisi,
                            'HONOR' => Helper::formatRupiahWithoutRp($honor),
                            'PPN' => '-',
                            'JUMLAH' => '-',
                            'BANTUAN INTERNET' => Helper::formatRupiahWithoutRp($bantuan_internet),
                            'TRANSPORT' => Helper::formatRupiahWithoutRp($transport),
                            'PUSTAKA' => Helper::formatRupiahWithoutRp($pustaka),
                            'TOTAL' => Helper::formatRupiahWithoutRp($honor),
                            'TANDA_TANGAN' => helper::getTandaTanganByKodeDosen($nidn),
                            'TANGGAL_UJIAN' => $data->date
                        ];
                    }
                }
            }

            return view('tugasakhir.keuanganfakultas.cetak_honorarium_per_mahasiswa', [
                'data' => $processedData,
            ]);
        } catch (\Exception $th) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Terjadi kesalahan saat mengambil data periode ujian: ' . $th->getMessage(),
            ]);
        }
    }

    public function report_dosen_home()
    {
        try {
            $data = DB::table('t_mst_dosen')->get();
            return view('tugasakhir.keuanganfakultas.dosen', ['data' => $data]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Terjadi kesalahan saat mengambil data dosen: ',
            ]);
        }
    }

    public function report_dosen_detail($nidn)
    {
        try {
            $C_KODE_DOSEN = $nidn;
            $data = DB::table('trt_honorium')
                ->select('trt_honorium.*', DB::raw("
                CASE
                    WHEN KS = '$C_KODE_DOSEN' THEN 'Ketua Sidang'
                    WHEN PU = '$C_KODE_DOSEN' THEN 'Pembimbing Utama'
                    WHEN PP = '$C_KODE_DOSEN' THEN 'Pembimbing Pendamping'
                    WHEN P1 = '$C_KODE_DOSEN' THEN 'Penguji I'
                    WHEN P2 = '$C_KODE_DOSEN' THEN 'Penguji II'
                    WHEN P3 = '$C_KODE_DOSEN' THEN 'Penguji III'
                    ELSE 'Unknown'
                END as role,
                CASE
                    WHEN KS = '$C_KODE_DOSEN' THEN KS_H
                    WHEN PU = '$C_KODE_DOSEN' THEN PU_H
                    WHEN PP = '$C_KODE_DOSEN' THEN PP_H
                    WHEN P1 = '$C_KODE_DOSEN' THEN P1_H
                    WHEN P2 = '$C_KODE_DOSEN' THEN P2_H
                    WHEN P3 = '$C_KODE_DOSEN' THEN P3_H
                    ELSE 0
                END as amount,
                CASE
                    WHEN KS = '$C_KODE_DOSEN' THEN KS_Stat
                    WHEN PU = '$C_KODE_DOSEN' THEN PU_Stat
                    WHEN PP = '$C_KODE_DOSEN' THEN PP_Stat
                    WHEN P1 = '$C_KODE_DOSEN' THEN P1_Stat
                    WHEN P2 = '$C_KODE_DOSEN' THEN P2_Stat
                    WHEN P3 = '$C_KODE_DOSEN' THEN P3_Stat
                    ELSE 0
                END as status
            "))
                ->where(function ($query) use ($C_KODE_DOSEN) {
                    $query->where('trt_honorium.KS', $C_KODE_DOSEN)
                        ->orWhere('trt_honorium.PU', $C_KODE_DOSEN)
                        ->orWhere('trt_honorium.PP', $C_KODE_DOSEN)
                        ->orWhere('trt_honorium.P1', $C_KODE_DOSEN)
                        ->orWhere('trt_honorium.P2', $C_KODE_DOSEN)
                        ->orWhere('trt_honorium.P3', $C_KODE_DOSEN);
                })
                ->having('status', '<>', 3) // Exclude records where status is 3
                ->get();

            return view('tugasakhir.keuanganfakultas.detail_dosen', compact('data', 'nidn'));
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Terjadi kesalahan saat mengambil data dosen: ',
            ]);
        }
    }

    public function report_dosen_history($nidn)
    {
        try {
            $C_KODE_DOSEN = $nidn;
            $data = DB::table('trt_honorium')
                ->select('trt_honorium.*', DB::raw("
        CASE
            WHEN KS = '$C_KODE_DOSEN' THEN 'Ketua Sidang'
            WHEN PU = '$C_KODE_DOSEN' THEN 'Pembimbing Utama'
            WHEN PP = '$C_KODE_DOSEN' THEN 'Pembimbing Pendamping'
            WHEN P1 = '$C_KODE_DOSEN' THEN 'Penguji I'
            WHEN P2 = '$C_KODE_DOSEN' THEN 'Penguji II'
            WHEN P3 = '$C_KODE_DOSEN' THEN 'Penguji III'
            ELSE 'Unknown'
        END as role,
        CASE
            WHEN KS = '$C_KODE_DOSEN' THEN KS_H
            WHEN PU = '$C_KODE_DOSEN' THEN PU_H
            WHEN PP = '$C_KODE_DOSEN' THEN PP_H
            WHEN P1 = '$C_KODE_DOSEN' THEN P1_H
            WHEN P2 = '$C_KODE_DOSEN' THEN P2_H
            WHEN P3 = '$C_KODE_DOSEN' THEN P3_H
            ELSE 0
        END as amount,
        CASE
            WHEN KS = '$C_KODE_DOSEN' THEN KS_Stat
            WHEN PU = '$C_KODE_DOSEN' THEN PU_Stat
            WHEN PP = '$C_KODE_DOSEN' THEN PP_Stat
            WHEN P1 = '$C_KODE_DOSEN' THEN P1_Stat
            WHEN P2 = '$C_KODE_DOSEN' THEN P2_Stat
            WHEN P3 = '$C_KODE_DOSEN' THEN P3_Stat
            ELSE 0
        END as status
    "))
                ->where(function ($query) use ($C_KODE_DOSEN) {
                    $query->where('trt_honorium.KS', $C_KODE_DOSEN)
                        ->orWhere('trt_honorium.PU', $C_KODE_DOSEN)
                        ->orWhere('trt_honorium.PP', $C_KODE_DOSEN)
                        ->orWhere('trt_honorium.P1', $C_KODE_DOSEN)
                        ->orWhere('trt_honorium.P2', $C_KODE_DOSEN)
                        ->orWhere('trt_honorium.P3', $C_KODE_DOSEN);
                })
                ->having('status', '=', 3)
                ->get();

            return view('tugasakhir.keuanganfakultas.history_detail_dosen', compact('data'));
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Terjadi kesalahan saat mengambil data dosen: ',
            ]);
        }
    }

    public function report_dosen_detail_by_date($nidn, $start_date, $end_date)
    {
        try {
            $C_KODE_DOSEN = $nidn;
            $data = DB::table('trt_honorium')
                ->select('trt_honorium.*', DB::raw("
                CASE
                    WHEN KS = '$C_KODE_DOSEN' THEN 'Ketua Sidang'
                    WHEN PU = '$C_KODE_DOSEN' THEN 'Pembimbing Utama'
                    WHEN PP = '$C_KODE_DOSEN' THEN 'Pembimbing Pendamping'
                    WHEN P1 = '$C_KODE_DOSEN' THEN 'Penguji I'
                    WHEN P2 = '$C_KODE_DOSEN' THEN 'Penguji II'
                    WHEN P3 = '$C_KODE_DOSEN' THEN 'Penguji III'
                    ELSE 'Unknown'
                END as role,
                CASE
                    WHEN KS = '$C_KODE_DOSEN' THEN KS_H
                    WHEN PU = '$C_KODE_DOSEN' THEN PU_H
                    WHEN PP = '$C_KODE_DOSEN' THEN PP_H
                    WHEN P1 = '$C_KODE_DOSEN' THEN P1_H
                    WHEN P2 = '$C_KODE_DOSEN' THEN P2_H
                    WHEN P3 = '$C_KODE_DOSEN' THEN P3_H
                    ELSE 0
                END as amount,
                CASE
                    WHEN KS = '$C_KODE_DOSEN' THEN KS_Stat
                    WHEN PU = '$C_KODE_DOSEN' THEN PU_Stat
                    WHEN PP = '$C_KODE_DOSEN' THEN PP_Stat
                    WHEN P1 = '$C_KODE_DOSEN' THEN P1_Stat
                    WHEN P2 = '$C_KODE_DOSEN' THEN P2_Stat
                    WHEN P3 = '$C_KODE_DOSEN' THEN P3_Stat
                    ELSE 0
                END as status
            "))
                ->where(function ($query) use ($C_KODE_DOSEN) {
                    $query->where('trt_honorium.KS', $C_KODE_DOSEN)
                        ->orWhere('trt_honorium.PU', $C_KODE_DOSEN)
                        ->orWhere('trt_honorium.PP', $C_KODE_DOSEN)
                        ->orWhere('trt_honorium.P1', $C_KODE_DOSEN)
                        ->orWhere('trt_honorium.P2', $C_KODE_DOSEN)
                        ->orWhere('trt_honorium.P3', $C_KODE_DOSEN);
                })
                ->where('date', '>=', $start_date)
                ->where('date', '<=', $end_date)
                ->get();
            return view('tugasakhir.keuanganfakultas.filter_detail_dosen', compact('data', 'nidn'));
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Terjadi kesalahan saat mengambil data dosen: ',
            ]);
        }
    }
}
