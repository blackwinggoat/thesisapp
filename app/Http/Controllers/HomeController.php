<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade as PDF;
use Auth;
use DB;
use helper;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            return view('/tugasakhir/layouts/content');
    }

    public function surat_sk_pembimbing($nomor)
    {
        $data_sk = DB::table('mst_sk_pembimbing')
            ->join('trt_bimbingan', 'mst_sk_pembimbing.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->select('*')
            ->whereRaw("REPLACE(mst_sk_pembimbing.nomor_sk, '/', '') = ?", [$nomor])
            ->get();

        if ($data_sk->isEmpty()) {
            return response('Data surat SK pembimbing tidak ditemukan.', 404);
        }

        $tgl_ujian = helper::tgl_indo_lengkap(date('Y-m-d'));

        return view('tugasakhir.fakultas.cetakskpembimbing', compact('data_sk', 'tgl_ujian'));
    }

    public function surat_sk_pembimbing_pdf($nomor)
    {
        $data_sk = DB::table('mst_sk_pembimbing as sk')
            ->join('trt_bimbingan as bimbingan', 'sk.bimbingan_id', '=', 'bimbingan.bimbingan_id')
            ->select([
                'sk.sk_pembimbing_id',
                'sk.nomor_sk',
                'sk.status as status_sk',
                'sk.created_at as sk_created_at',
                'bimbingan.bimbingan_id',
                'bimbingan.C_NPM',
                'bimbingan.pembimbing_I_id',
                'bimbingan.pembimbing_II_id',
                'bimbingan.judul',
                'bimbingan.jenis_tugas_akhir_id',
            ])
            ->whereRaw("REPLACE(sk.nomor_sk, '/', '') = ?", [$nomor])
            ->first();

        if (!$data_sk) {
            return response('Data surat SK pembimbing tidak ditemukan.', 404);
        }

        $safeNomorSk = preg_replace('/[^A-Za-z0-9._-]+/', '-', (string) $data_sk->nomor_sk);

        return PDF::loadView('tugasakhir.fakultas.cetakskpembimbing_pdf', compact('data_sk'))
            ->setPaper('a4', 'portrait')
            ->stream('SK-Pembimbing-' . trim($safeNomorSk, '-') . '.pdf');
    }
}
