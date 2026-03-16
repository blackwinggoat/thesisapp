<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Exception;
use DB;
use Illuminate\Support\Facades\Redirect;

class Dekan extends Controller
{
    // Menampilkan Status Bimbingan Mahasiswa
    public function detail_status_bimbingan_mahasiswa($status)
    {
        $data = DB::table('trt_bimbingan')
            ->select("*")
            ->where('trt_bimbingan.status_bimbingan', $status)
            ->get();


        return view('tugasakhir.prodi.detail_status_bimbingan_mahasiswa', compact('data'));
    }
    // Akhir Menampilkan Status Bimbingan Mahasiswa


    // Menampilkan SK Pembimbing
    public function sk_pembimbing()
    {
        $data_sk = DB::table('mst_sk_pembimbing')
            ->join('trt_bimbingan', 'mst_sk_pembimbing.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->select('*')
            ->where("mst_sk_pembimbing.status", 1)
            ->orderBy('mst_sk_pembimbing.sk_pembimbing_id', 'DESC')
            ->get();
        return view('tugasakhir.dekan.sk_pembimbing', compact('data_sk'));
    }

    // Approve SK Pembimbing
    public function approve_sk_pembimbing($id)
    {
        try {
            DB::update('update mst_sk_pembimbing set status = 2 where sk_pembimbing_id = ?', [$id]);
            return redirect::to('dekan/sk_pembimbing')->with('status', 'success');
        } catch (Exception $exception) {
            return redirect::to('dekan/sk_pembimbing')->with('status', 'error');
        }
    }

    // Menampilkan SK Pembimbing
    public function sk_ujian_ta()
    {
        $data_sk = DB::table('mst_sk_penugasan')
            ->join('trt_bimbingan', 'mst_sk_penugasan.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->select('*')
            ->where("mst_sk_penugasan.status", 1)
            ->orderBy('mst_sk_penugasan.sk_penugasan_id', 'DESC')
            ->get();
        return view('tugasakhir.dekan.sk_ujian_ta', compact('data_sk'));
    }

    // Approve SK Pembimbing
    public function approve_sk_ujian_ta($id)
    {
        try {
            DB::update('update mst_sk_penugasan set status = 2 where sk_penugasan_id = ?', [$id]);
            return redirect::to('dekan/sk_ujian_ta')->with('status', 'success');
        } catch (Exception $exception) {
            return redirect::to('dekan/sk_ujian_ta')->with('status', 'error');
        }
    }
}
