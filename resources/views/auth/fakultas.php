<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use helper;
use DB;
use Illuminate\Support\Facades\Redirect;
use App\Model\trt_topik;
use App\Model\trt_reg;
use App\Model\mst_sk_pembimbing;
use App\Model\mst_sk_penugasan;
use App\TrtJadwalUjian;
use Illuminate\Support\Facades\Hash;

class fakultas extends Controller
{

    // Halaman Ubah Password
    public function ubah_password()
    {
        return view('tugasakhir.prodi.ubah_password');
    }
    // Akhir Halaman Ubah Passoword

    // Ubah password
    public function ubah_password_post(Request $request)
    {
        if ($request->password_baru == $request->ulangi_password) {
            $status = DB::update('update users set password = ? where name = ?', [Hash::make($request->password_baru), $request->name]);
            return redirect()->back()->with('success', 'Password Berhasil Diubah');
        } else {
            return redirect()->back()->with('error', 'Password Tidak Sama');
        }
    }
    // Akhir Ubah Password


    // Cetak SK TIM Ujian TA Oleh Fakultas

    public function usulan_timujianta($id)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $id)->first();
        $data = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
            ->join("t_mst_mahasiswa", "t_mst_mahasiswa.C_NPM", "=", "trt_reg.C_NPM")
            ->join("trt_penguji", "trt_penguji.C_NPM", "=", "t_mst_mahasiswa.C_NPM")
            ->join("trt_topik", "trt_topik.topik", "=", "trt_bimbingan.judul")
            ->join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "trt_reg.pendaftaran_id")
            ->where([
                "trt_reg.pendaftaran_id" => $id,
                "trt_penguji.tipe_ujian" => $info->tipe_ujian
            ])->get();
        return view('tugasakhir.prodi.surat_usulantimujian', compact("info", "data"));
    }

    // Menampilkan Daftar atau List SK Penugasan TIM Ujian TA

    public function surat_penugasan_ujian_ta()
    {

        $data_sk_penugasan = DB::table('mst_sk_penugasan')
            ->select('*')
            ->join('trt_bimbingan', 'trt_bimbingan.bimbingan_id', '=', 'mst_sk_penugasan.bimbingan_id')
            ->get();

        $daftar_surat_usulan = DB::table('trt_sk_ujian_ta')
            ->select('*')
            ->join('mst_pendaftaran', 'mst_pendaftaran.pendaftaran_id', '=', 'trt_sk_ujian_ta.pendaftaran_id')
            ->get();

        return view('tugasakhir.fakultas.surat_penugasan_ujian_ta', compact('daftar_surat_usulan', 'data_sk_penugasan'));
    }

    //  Membuat SK Per Mahasiswa Melalui TIM Ujian TA

    public function sk_penetapan_tim_ujian_ta(Request $request)
    {
        $datapost = $request->all();
        $data = DB::table('trt_sk_ujian_ta')
            ->join('trt_reg', 'trt_sk_ujian_ta.pendaftaran_id', '=', 'trt_reg.pendaftaran_id')
            ->join('trt_bimbingan', 'trt_bimbingan.bimbingan_id', '=', 'trt_reg.bimbingan_id')
            ->select('*')
            ->where('trt_sk_ujian_ta.pendaftaran_id', $datapost['pendaftaran_id'])
            ->get();
        return view('tugasakhir.fakultas.sk_penetapan_tim_ujian_ta', compact('data'));
    }

    // Menambah SK Penugasan Per Setiap Mahasiswa

    public function add_sk_penugasan_per_mahasiswa(Request $request)
    {
        $datapost = $request->all();
        mst_sk_penugasan::create($datapost);
        return redirect::to('fakultas/surat_penugasan_ujian_ta');
    }

    // Menampilkan Dan Mencetak SK Penugasan Setiap Mahasiswa

    public function cetakskpenugasan(Request $request)
    {
        $datapost = $request->all();
        $data_sk = DB::table('mst_sk_penugasan')
            ->join('trt_bimbingan', 'mst_sk_penugasan.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->join('trt_penguji', 'trt_penguji.C_NPM', '=', 'trt_bimbingan.C_NPM')
            ->join('trt_jadwal_ujian_per_mhs', 'trt_jadwal_ujian_per_mhs.C_NPM', '=', 'trt_bimbingan.C_NPM')
            ->join('trt_jadwal_ujian', 'trt_jadwal_ujian.id', '=', 'trt_jadwal_ujian_per_mhs.jadwal_ujian')
            ->join('mst_ruangan', 'mst_ruangan.id', '=', 'trt_jadwal_ujian_per_mhs.ruangan')
            ->select('*')
            ->where('trt_bimbingan.bimbingan_id', $datapost['bimbingan_id'])
            ->get();


        return view('tugasakhir.fakultas.cetakskpenugasan', compact('data_sk'));
    }


    public function sk_penetapan(Request $request)
    {
        $datapost = $request->all();
        $bimbingan_id = DB::table('trt_sk')
            ->select('bimbingan_id')
            ->where('nomor', $datapost['nomor'])
            ->get();
        $data = DB::table('trt_bimbingan')
            ->join('trt_sk', 'trt_sk.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->select('*')
            ->where('nomor', $datapost['nomor'])
            ->get();


        return view('tugasakhir.fakultas.sk_penetapan', compact('data', 'datapost'));
    }

    public function cetakskpembimbing(Request $request)
    {
        $datapost = $request->all();
        $data_sk = DB::table('mst_sk_pembimbing')
            ->join('trt_bimbingan', 'mst_sk_pembimbing.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->select('*')
            ->where('nomor_sk', $datapost['nomor'])
            ->get();


        return view('tugasakhir.fakultas.cetakskpembimbing', compact('data_sk'));
    }

    public function sk_pembimbing()
    {
        $data = DB::table('t_mst_mahasiswa')
            ->join('trt_bimbingan', 'trt_bimbingan.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->join('t_mst_dosen', 'C_KODE_DOSEN', '=', 'trt_bimbingan.pembimbing_I_id')
            ->select('t_mst_mahasiswa.NAMA_MAHASISWA', 't_mst_dosen.NAMA_DOSEN')
            ->get();

        $penetapan_pengusulan = DB::table('trt_bimbingan')
            ->join('t_mst_mahasiswa', 'trt_bimbingan.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->select('*')
            ->where('status_sk', '<>', 1)
            ->get();

        $riwayat_usulan = DB::table('trt_sk')
            ->select('nomor', 'tgl_surat')
            ->distinct('nomor')
            ->get();

        $data_sk = DB::table('mst_sk_pembimbing')
            ->join('trt_bimbingan', 'mst_sk_pembimbing.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->select('*')
            ->get();





        return view('tugasakhir.fakultas.sk_pembimbing', compact('riwayat_usulan', 'penetapan_pengusulan', 'data', 'data_sk'));
    }



    public function chart_morris()
    {
        return view('tugasakhir.fakultas.chart_morris');
    }

    public function chart_c3()
    {
        return view('tugasakhir.fakultas.chart_c3');
    }

    public function chart_flot()
    {
        return view('tugasakhir.fakultas.chart_flot');
    }

    public function chart_easy_knob()
    {
        return view('tugasakhir.fakultas.chart_easy_knob');
    }

    public function addskpembimbing(Request $request)
    {
        $datapost = $request->all();
        mst_sk_pembimbing::create($datapost);
        return redirect::to('fakultas/sk_pembimbing');
    }
}
