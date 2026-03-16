<?php

namespace App\Http\Controllers;

use App\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KeuanganFakultas extends Controller
{
    public function master_pembayaran_home()
    {
        $data = DB::table('mst_pembayaran_honorarium')->get();
        return view('tugasakhir.keuanganfakultas.master_pembayaran', ['data' => $data]);
    }
    public function master_pembayaran_store(Request $request)
    {
        try {
            DB::table('mst_pembayaran_honorarium')->insert([
                'name' => $request->input('name'),
                'ketua_sidang' => $request->input('ketua_sidang'),
                'pembimbing_utama' => $request->input('pembimbing_utama'),
                'pembimbing_pendamping' => $request->input('pembimbing_pendamping'),
                'penguji_1' => $request->input('penguji_1'),
                'penguji_2' => $request->input('penguji_2'),
                'penguji_3' => $request->input('penguji_3'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

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
            DB::table('mst_pembayaran_honorarium')->where('id_honorarium', $id)->delete();
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
            DB::table('mst_pembayaran_honorarium')->where('id_honorarium', $request->input('id_honorarium'))->update([
                'name' => $request->input('name'),
                'ketua_sidang' => $request->input('ketua_sidang'),
                'pembimbing_utama' => $request->input('pembimbing_utama'),
                'pembimbing_pendamping' => $request->input('pembimbing_pendamping'),
                'penguji_1' => $request->input('penguji_1'),
                'penguji_2' => $request->input('penguji_2'),
                'penguji_3' => $request->input('penguji_3'),
                'updated_at' => now()
            ]);

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

    public function honorarium_home()
    {
        $data = DB::table('trt_honorium')
            ->where('KS_Stat', '<>', 3)
            ->orWhere('PU_Stat', '<>', 3)
            ->orWhere('PP_Stat', '<>', 3)
            ->orWhere('P1_Stat', '<>', 3)
            ->orWhere('P2_Stat', '<>', 3)
            ->orWhere('P3_Stat', '<>', 3)
            ->get();

        $dataMasterHonorarium = DB::table('mst_pembayaran_honorarium')->get();

        return view('tugasakhir.keuanganfakultas.honorarium', [
            'data' => $data,
            'dataMasterHonorarium' => $dataMasterHonorarium
        ]);
    }



    public function honorarium_available_post_yes(Request $request)
    {
        try {
            DB::table('trt_honorium')
                ->where('id', $request->id_honorarium)
                ->update([
                    'KS_Stat' => 1,
                    'PU_Stat' => 1,
                    'PP_Stat' => 1,
                    'P1_Stat' => 1,
                    'P2_Stat' => 1,
                    'P3_Stat' => 1,
                ]);

            return response()->json(['message' => 'Honorarium berhasil di set menjadi Tersedia!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengubah status honorarium menjadi Available Post Yes.'], 500);
        }
    }

    public function honorarium_available_post_no(Request $request)
    {
        try {
            DB::table('trt_honorium')
                ->where('id', $request->id_honorarium)
                ->update([
                    'KS_Stat' => 0,
                    'PU_Stat' => 0,
                    'PP_Stat' => 0,
                    'P1_Stat' => 0,
                    'P2_Stat' => 0,
                    'P3_Stat' => 0,
                ]);

            return response()->json(['message' => 'Honrarium berhasil di set menjadi Tidak Tersedia!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengubah status honorarium menjadi Available Post No.'], 500);
        }
    }

    public function honorarium_save_all(Request $request)
    {
        try {
            foreach ($request->honorariums as $honorariumData) {
                if (!isset($honorariumData['tipe_ujian']) || $honorariumData['tipe_ujian'] == 'unset') {
                    continue;
                }

                $masterPayment = DB::table('mst_pembayaran_honorarium')
                    ->where('name', $honorariumData['tipe_ujian'])
                    ->first();

                if ($masterPayment) {
                    $existingHonorarium = DB::table('trt_honorium')
                        ->where('id', $honorariumData['id'])
                        ->first();

                    if ($existingHonorarium) {
                        DB::table('trt_honorium')
                            ->where('id', $honorariumData['id'])
                            ->update([
                                'tipe_ujian' => $honorariumData['tipe_ujian'],
                                'KS_H' => $masterPayment->ketua_sidang,
                                'PU_H' => $masterPayment->pembimbing_utama,
                                'PP_H' => $masterPayment->pembimbing_pendamping,
                                'P1_H' => $masterPayment->penguji_1,
                                'P2_H' => $masterPayment->penguji_2,
                                'P3_H' => $masterPayment->penguji_3,
                            ]);
                    } else {
                        // Insert a new record
                        DB::table('trt_honorium')->insert([
                            'id' => $honorariumData['id'],
                            'tipe_ujian' => $honorariumData['tipe_ujian'],
                            'KS_H' => $masterPayment->ketua_sidang,
                            'PU_H' => $masterPayment->pembimbing_utama,
                            'PP_H' => $masterPayment->pembimbing_pendamping,
                            'P1_H' => $masterPayment->penguji_1,
                            'P2_H' => $masterPayment->penguji_2,
                            'P3_H' => $masterPayment->penguji_3,
                        ]);
                    }
                }
            }

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
            ->where('KS_Stat', '=', 3)
            ->where('PU_Stat', '=', 3)
            ->where('PP_Stat', '=', 3)
            ->where('P1_Stat', '=', 3)
            ->where('P2_Stat', '=', 3)
            ->where('P3_Stat', '=', 3)
            ->get();

        $dataMasterHonorarium = DB::table('mst_pembayaran_honorarium')->get();

        return view('tugasakhir.keuanganfakultas.history_honorarium', [
            'data' => $data,
            'dataMasterHonorarium' => $dataMasterHonorarium
        ]);
    }

    public function report_periode_ujian_home()
    {
        try {
            $data = DB::table('trt_honorium')
                ->select(
                    'date',
                    DB::raw("
                        CASE
                            WHEN SUM(CASE WHEN KS_Stat = 0 OR PU_Stat = 0 OR PP_Stat = 0 OR P1_Stat = 0 OR P2_Stat = 0 OR P3_Stat = 0 THEN 1 ELSE 0 END) > 0 THEN 0
                            WHEN SUM(CASE WHEN KS_Stat = 1 OR PU_Stat = 1 OR PP_Stat = 1 OR P1_Stat = 1 OR P2_Stat = 1 OR P3_Stat = 1 THEN 1 ELSE 0 END) > 0 THEN 1
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
