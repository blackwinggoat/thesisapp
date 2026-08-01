<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Exception;
use DB;
use Illuminate\Support\Facades\Redirect;

class WakilDekan extends Controller
{
    public function ubah_password()
    {
        return view('tugasakhir.fakultas.ubah_password', [
            'passwordAction' => url('wakildekan/ubah_password'),
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

    // Menampilkan SK Pembimbing
    public function sk_pembimbing()
    {
        $data_sk = DB::table('mst_sk_pembimbing')
            ->join('trt_bimbingan', 'mst_sk_pembimbing.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->select('*')
            ->where("mst_sk_pembimbing.status", 0)
            ->orderBy('mst_sk_pembimbing.sk_pembimbing_id', 'DESC')
            ->get();
        return view('tugasakhir.wakildekan.sk_pembimbing', compact('data_sk'));
    }

    // Menampilkan Status Bimbingan Mahasiswa
    public function detail_status_bimbingan_mahasiswa($status)
    {
        $data = DB::table('trt_bimbingan')
            ->select("*")
            ->where('trt_bimbingan.status_bimbingan', $status)
            ->orderBy('updated_at', 'desc')
            ->get();

        $filterActionRoute = route('wakildekan.tampilDetailStatusBimbinganDenganFilterTanggal');
        $resetUrl = url('wakildekan/detail_status_bimbingan_mahasiswa/' . $status);

        return view('tugasakhir.prodi.detail_status_bimbingan_mahasiswa', compact('data', 'status', 'filterActionRoute', 'resetUrl'));
    }
    // Akhir Menampilkan Status Bimbingan Mahasiswa

    public function tampilDetailStatusBimbinganDenganFilterTanggal(Request $request)
    {
        $tanggalDari = $request->input('tanggal_dari');
        $tanggalSampai = $request->input('tanggal_sampai');
        $status = $request->input('status');

        $query = DB::table('trt_bimbingan')
            ->select('*')
            ->where('trt_bimbingan.status_bimbingan', $status);

        if (!empty($tanggalDari) && !empty($tanggalSampai)) {
            $query->whereBetween('trt_bimbingan.updated_at', [$tanggalDari, $tanggalSampai]);
        }

        $data = $query
            ->orderBy('updated_at', 'desc')
            ->get();

        $filterActionRoute = route('wakildekan.tampilDetailStatusBimbinganDenganFilterTanggal');
        $resetUrl = url('wakildekan/detail_status_bimbingan_mahasiswa/' . $status);

        return view('tugasakhir.prodi.detail_status_bimbingan_mahasiswa', compact('data', 'status', 'filterActionRoute', 'resetUrl'));
    }


    // Approve SK Pembimbing
    public function approve_sk_pembimbing($id)
    {
        try {
            DB::update('update mst_sk_pembimbing set status = 1 where sk_pembimbing_id = ?', [$id]);
            return redirect::to('wakildekan/sk_pembimbing')->with('status', 'success');
        } catch (Exception $exception) {
            return redirect::to('wakildekan/sk_pembimbing')->with('status', 'error');
        }
    }

    // Menampilkan SK Pembimbing
    public function sk_ujian_ta()
    {
        $data_sk = DB::table('mst_sk_penugasan')
            ->join('trt_bimbingan', 'mst_sk_penugasan.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->select('*')
            ->where("mst_sk_penugasan.status", 0)
            ->orderBy('mst_sk_penugasan.sk_penugasan_id', 'DESC')
            ->get();
        return view('tugasakhir.wakildekan.sk_ujian_ta', compact('data_sk'));
    }

    // Approve SK Pembimbing
    public function approve_sk_ujian_ta($id)
    {
        try {
            DB::update('update mst_sk_penugasan set status = 1 where sk_penugasan_id = ?', [$id]);
            return redirect::to('wakildekan/sk_ujian_ta')->with('status', 'success');
        } catch (Exception $exception) {
            return redirect::to('wakildekan/sk_ujian_ta')->with('status', 'error');
        }
    }
}
