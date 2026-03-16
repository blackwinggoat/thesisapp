<?php

namespace App\Http\Controllers;

use App\Helper;
use App\LampiranPesan;
use App\Model\mst_pendaftaran;
use App\Model\mst_pesan;
use App\Model\mst_tmp_usulan;
use App\Model\trt_bimbingan;
use App\Model\trt_konsultasi;
use App\Model\trt_reg;
use App\Model\trt_topik;
use App\Model\trt_hasil;
use App\Model\mst_pengumuman;
use App\MstRuangan;
use App\RequestPembimbing;
use App\TrtJadwalUjian;
use App\TrtJadwalUjianPerMhs;
use App\TrtPengajuanDokumen;
use App\TrtPenguji;
use App\TrtSyaratUjian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Exception;

class mhs extends Controller
{

    public function chat()
    {
        return view('tugasakhir.mhs.chat');
    }

    // Tampil Catatan Pada Syarat Ujian
    public function signup_ujianmeja_catatan($id)
    {
        $data = DB::table('trt_syarat_ujian')
            ->select("*")
            ->where("id", $id)
            ->where("C_NPM", auth()->user()->name)
            ->get();
        return view('tugasakhir.mhs.catatan_signup_ujianmeja', compact('data'));
    }

    public function signup_ujianmeja_catatan_post(Request $request)
    {
        try {
            TrtSyaratUjian::where("id", $request->id)
                ->where('C_NPM', auth()->user()->name)
                ->update([
                    "catatan" => $request->catatan
                ]);
            return redirect::to('mhs/signup_ujianmeja/')->with('status', 'success');
        } catch (Exception $exception) {
            return redirect::to('mhs/signup_ujianmeja/')->with('status', 'error');
        }
    }

    // Tampil Catatan Pada Syarat Ujian
    public function signup_proposal_catatan($id)
    {
        $data = DB::table('trt_syarat_ujian')
            ->select("*")
            ->where("id", $id)
            ->where("C_NPM", auth()->user()->name)
            ->get();
        return view('tugasakhir.mhs.catatan_signup_proposal', compact('data'));
    }

    public function signup_proposal_catatan_post(Request $request)
    {
        try {
            TrtSyaratUjian::where("id", $request->id)
                ->where('C_NPM', auth()->user()->name)
                ->update([
                    "catatan" => $request->catatan
                ]);
            return redirect::to('mhs/signup_proposal/')->with('status', 'success');
        } catch (Exception $exception) {
            return redirect::to('mhs/signup_proposal/')->with('status', 'error');
        }
    }

    // Balas Pesan
    public function mail_reply(Request $request)
    {
        $data = DB::table('trt_bimbingan')
            ->select('*')
            ->where('C_NPM', auth()->user()->name)
            ->get();

        $data_reply = DB::table('mst_pesan')
            ->join('trt_konsultasi', 'mst_pesan.pesan_id', '=', 'trt_konsultasi.pesan_id')
            ->select('*')
            ->where('mst_pesan.pesan_id', $request->pesan_id)
            ->get();
        return view('tugasakhir.mhs.mail_reply', compact('data', 'data_reply'));
    }

    // Halaman Show Pengumuman
    public function show_pengumuman($id)
    {
        $data = DB::table('mst_pengumuman')
            ->where('pengumuman_id', $id)
            ->first();
        return view('tugasakhir.mhs.single_pengumuman', compact('data'));
    }

    // Halaman Menampilkan Semua Daftar Pengumuman
    public function pengumuman()
    {
        $data = mst_pengumuman::orderBy('last_update', 'desc')->get();
        return view('tugasakhir.mhs.detail_pengumuman', compact('data'));
    }

    // Halaman Tampilan Judul Usulan
    public function usulan_judul_anak_bimbingan()
    {
        $data = DB::table('trt_usulan_judul')
            ->select('*')
            ->join('t_mst_dosen', 'trt_usulan_judul.KODE_DOSEN', '=', 't_mst_dosen.C_KODE_DOSEN')
            ->where('C_NPM', auth()->user()->name)
            ->get();
        return view('tugasakhir.mhs.usulan_judul_anak_bimbingan', compact('data'));
    }
    // Akhir Halaman Tampilan Akhir Usulan

    // Halaman Tampilan Judul Usulan
    public function usulan_judul_calon_pembimbing()
    {
        $data = DB::table('t_mst_dosen')
            ->select('*')
            ->get();
        return view('tugasakhir.mhs.usulan_judul_calon_pembimbing', compact('data'));
    }
    // Akhir Halaman Tampilan Akhir Usulan

    // Halaman Tampilan Judul Usulan
    public function detail_usulan_judul_calon_pembimbing($kode_dosen)
    {
        $data = DB::select("SELECT * FROM `trt_usulan_judul` WHERE trt_usulan_judul.KODE_DOSEN = ? AND trt_usulan_judul.C_NPM NOT IN (?)", [$kode_dosen, auth()->user()->name]);
        return view('tugasakhir.mhs.detail_usulan_judul_calon_pembimbing', compact('data'));
    }
    // Akhir Halaman Tampilan Akhir Usulan

    // Halaman Tampilan Judul Usulan
    public function usulan_judul_semua_mahasiswa()
    {
        $data_riwayat_usulan = DB::table('trt_topik')
            ->join('t_mst_mahasiswa', 'trt_topik.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->select('t_mst_mahasiswa.C_NPM', 't_mst_mahasiswa.NAMA_MAHASISWA', 'trt_topik.topik', 'trt_topik.kerangka', 'trt_topik.status')
            ->get();
        return view('tugasakhir.mhs.usulan_judul_semua_mahasiswa', compact('data_riwayat_usulan'));
    }
    // Akhir Halaman Tampilan Akhir Usulan

    // Cetak Berita Acara Ujian Proposal
    public function cetak_beritaacara_proposal($pendaftaran_id, $nim)
    {
        $trtjadwalujian = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("trt_jadwal_ujian.pendaftaran_id", $pendaftaran_id)->first();
        $trtjadwalujianpermhs = TrtJadwalUjianPerMhs::join("mst_ruangan", "mst_ruangan.id", "trt_jadwal_ujian_per_mhs.ruangan")
            ->where([
                "C_NPM" => $nim,
                "jadwal_ujian" => $trtjadwalujian->id
            ])->first();
        $trt_bimbingan = trt_bimbingan::where("C_NPM", $nim)->first();
        $mst_pendaftaran = mst_pendaftaran::find($pendaftaran_id);
        $trt_penguji = TrtPenguji::where([
            "C_NPM" => $nim,
            "tipe_ujian" => $mst_pendaftaran->tipe_ujian
        ])->first();

        $ruangan = MstRuangan::find($trtjadwalujianpermhs->ruangan)->nama_ruangan;
        $tgl_ujian = Carbon::parse($trtjadwalujian->tgl_ujian)->formatLocalized("%A, %d %B %Y");
        switch ($mst_pendaftaran->tipe_ujian) {
            case "0":
                $tipe_ujian = "Proposal";
                break;
            case "2":
                $tipe_ujian = "Tugas Akhir";
                break;
        }

        $tgl_sekarang = helper::tgl_indo_lengkap(date('Y-m-d'));

        return view("tugasakhir.mhs.cetak_beritaacara_proposal", compact(
            "nim",
            "trt_bimbingan",
            "trt_penguji",
            "tipe_ujian",
            "ruangan",
            "tgl_ujian",
            "tgl_sekarang"
        ));
    }
    // Akhir

    // Halaman Berita Cara Proposal
    public function beritaacara_proposal($nim)
    {

        $data = DB::select("SELECT * FROM trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_penguji.C_NPM = ? AND trt_reg.status = ?", [$nim, 0]);

        return view('tugasakhir.mhs.beritaacara_proposal', compact("data"));
    }
    // Akhir Berita Acara Proposal

    // Halaman Berita Cara Ujian
    public function beritaacara_ujian($nim)
    {
        $data = DB::select("SELECT * FROM trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_penguji.C_NPM = ? AND trt_reg.status = ?", [$nim, 2]);


        return view('tugasakhir.mhs.beritaacara_ujian', compact("data"));
    }
    // Akhir Berita Acara Ujian


    // Halaman Ubah Password
    public function ubah_password()
    {
        return view('tugasakhir.mhs.ubah_password');
    }
    // Akhir Halaman Ubah Passoword

    // Ubah password
    public function ubah_password_post(Request $request)
    {
        if (!Hash::check($request->password_lama, auth()->user()->password)) {
            return redirect()->back()->with('error', 'Password lama tidak sesuai');
        }

        if ($request->password_baru == $request->ulangi_password) {
            $status = DB::update('update users set password = ? where id = ?', [Hash::make($request->password_baru), auth()->id()]);
            return redirect()->back()->with('success', 'Password Berhasil Diubah');
        } else {
            return redirect()->back()->with('error', 'Password Tidak Sama');
        }
    }
    // Akhir Ubah Password

    public function download()
    {
        $data = DB::table('mst_download')->get();
        return view('tugasakhir.mhs.download', compact('data'));
    }

    public function ubah_judul($id)
    {
        $data = DB::table("trt_topik")
            ->select("*")
            ->where("topik_id", $id)
            ->get();
        return view("tugasakhir.mhs.ubah_judul", compact('data'));
    }

    public function judul_update(Request $request, $id)
    {
        trt_topik::where("topik_id", $id)
            ->update([
                'topik' => $request->topik,
            ]);
        return redirect::to('mhs/pengajuan_topik');
    }

    public function detail_note($id)
    {
        $data = DB::table("trt_topik")
            ->select("*")
            ->where("topik_id", $id)
            ->get();
        return view("tugasakhir.mhs.detail_note", compact('data'));
    }

    public function note_update(Request $request, $id)
    {
        trt_topik::where("topik_id", $id)
            ->update([
                'note' => $request->note,
            ]);
        return redirect::to('mhs/pengajuan_topik');
    }
    public function pengajuan_topik()
    {
        $cek = DB::table('mst_tmp_usulan')
            ->select('*')
            ->where('C_NPM', auth()->user()->name)
            ->get();

        $id = auth()->user()->name;
        $data = DB::table('mst_bidangilmu')
            ->select('*')
            ->get();

        $listdosen = DB::table('t_mst_dosen')
            ->leftJoin("trt_level_pembimbing", "trt_level_pembimbing.C_KODE_DOSEN", "=", "t_mst_dosen.C_KODE_DOSEN")
            ->select('t_mst_dosen.*', 'trt_level_pembimbing.level')
            ->get();

        $datatopik = DB::table('trt_topik')
            ->select('*')
            ->where('C_NPM', $id)
            ->get();
        return view('tugasakhir.mhs.pengajuan_topik', compact('data', 'datatopik', 'listdosen', 'cek'));
    }
    public function pengajuan_topikdel($id)
    {
        $data = DB::table("trt_topik")->select("*")->where('topik_id', $id)->get();
        $path = public_path("dokumen/" . $data[0]->kerangka);
        if (strpos($path, ".")) {
            unlink($path);
        }
        trt_topik::where('topik_id', $id)->delete();
        return redirect::to('mhs/pengajuan_topik');
    }


    public function pengajuan_topikpost(Request $request)
    {
        $datapost = $request->except(["bidang_ilmu"]);
        $datapost['status'] = 0;
        $datapost['user_id'] = $datapost['C_NPM'];
        $datapost['bidang_ilmu_peminatan'] = $datapost['bidang_ilmu_peminatan'];
        $file = isset($datapost['kerangka']) ? $datapost['kerangka'] : '';

        $uploadedFileName = helper::uploadFile($file, 'public/dokumen/', '');

        if ($uploadedFileName) {
            $datapost['kerangka'] = $uploadedFileName;
        } else {
            return redirect()->back()->with('error', 'File upload failed. Please try again.');
        }

        $datapost["note"] = $datapost["note"];

        $trt_topik = trt_topik::create($datapost);

        foreach ($request->bidang_ilmu as $key => $bidangilmu) {
            RequestPembimbing::create([
                "C_NPM" => $request->C_NPM,
                "bidang_ilmu" => $bidangilmu,
                "topik" => $trt_topik->topik_id,
            ]);
        }

        return redirect()->back()->with('success', 'Topik berhasil diajukan.');
    }




    public function riwayat_ujian($nim)
    {
        $data = DB::select("SELECT * FROM trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_penguji.C_NPM = ?", [$nim]);

        return view('tugasakhir.mhs.riwayat_ujian', compact('data'));
    }

    public function mail_inbox()
    {
        $data = DB::table('mst_pesan')
            ->join('trt_konsultasi', 'mst_pesan.pesan_id', '=', 'trt_konsultasi.pesan_id')
            ->select('*')
            ->where('penerima_id', auth()->user()->name)
            ->orderBy('trt_konsultasi.created_at', 'DESC')
            ->get();

        $datax = DB::table('mst_pesan')
            ->join('trt_konsultasi', 'mst_pesan.pesan_id', '=', 'trt_konsultasi.pesan_id')
            ->select('*')
            ->where('pengirim_id', auth()->user()->name)
            ->orderBy('trt_konsultasi.created_at', 'DESC')
            ->get();
        return view('tugasakhir.mhs.mail_inbox', compact('data', 'datax'));
    }

    public function mail_new()
    {
        $data = DB::table('trt_bimbingan')
            ->select('*')
            ->where('C_NPM', auth()->user()->name)
            ->get();

        return view('tugasakhir.mhs.mail_new', compact('data'));
    }

    public function pesanpost(Request $request)
    {
        try {
            if ($request->lampiran != null) {
                foreach ($request->lampiran as $lampiran) {
                    $size = round($lampiran->getSize() / 1024);
                    if ($size > 10240) {
                        session()->flash("error", "Setiap file tidak lebih dari 10MB, silahkan sediakan link alternatif.");
                        return redirect()->back();
                    }
                }
            }

            $mstpesan = mst_pesan::create([
                "perihal_pesan" => $request->perihal_pesan,
                "isi_pesan" => $request->isi_pesan,
            ]);
            if ($request->lampiran != null) {
                foreach ($request->lampiran as $lampiran) {
                    LampiranPesan::create([
                        "pesan_id" => $mstpesan->pesan_id,
                        "lampiran" => Helper::uploadFile($lampiran, 'dokumen/', '')
                    ]);
                }
            }
            if ($request->status_kirim == 2) {
                trt_konsultasi::create([
                    "pesan_id" => $mstpesan->pesan_id,
                    "pengirim_id" => auth()->user()->name,
                    "penerima_id" => $request->id_penerima,
                    "status_baca" => 0
                ]);
            } else {
                foreach ($request->penerima_id as $penerima) {
                    trt_konsultasi::create([
                        "pesan_id" => $mstpesan->pesan_id,
                        "pengirim_id" => auth()->user()->name,
                        "penerima_id" => $penerima,
                        "status_baca" => 0
                    ]);
                }
            }
            return redirect::to('mhs/mail_sent');
        } catch (Exception $e) {
            return redirect::to('mhs/mail_sent');
        }
    }

    public function mail_sent()
    {
        $data = DB::table('mst_pesan')
            ->join('trt_konsultasi', 'mst_pesan.pesan_id', '=', 'trt_konsultasi.pesan_id')
            ->select('*')
            ->where('pengirim_id', auth()->user()->name)
            ->orderBy('trt_konsultasi.created_at', 'DESC')
            ->get();
        $datax = DB::table('mst_pesan')
            ->join('trt_konsultasi', 'mst_pesan.pesan_id', '=', 'trt_konsultasi.pesan_id')
            ->select('*')
            ->where('penerima_id', auth()->user()->name)
            ->orderBy('trt_konsultasi.created_at', 'DESC')
            ->get();
        return view('tugasakhir.mhs.mail_sent', compact('data', 'datax'));
    }


    public function mail_read($id, $status)
    {
        $data = DB::table('mst_pesan')
            ->join('trt_konsultasi', 'mst_pesan.pesan_id', '=', 'trt_konsultasi.pesan_id')
            ->select('*')
            ->where('mst_pesan.pesan_id', $id)
            ->first();


        trt_konsultasi::where(["pesan_id" => $id, "penerima_id" => auth()->user()->name])->update([
            "status_baca" => 1
        ]);

        return view('tugasakhir.mhs.mail_read', compact('data', 'status'));
    }

    public function detail_ujian($nim, $tipe_ujian)
    {
        $data = DB::select("SELECT * FROM trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_penguji.C_NPM = ? AND trt_reg.status = ?", [$nim, $tipe_ujian]);
        return view('tugasakhir.mhs.detail_ujian', compact('data'));
    }

    public function signup_proposal()
    {
        if (substr(Auth::user()->name, 0, 3) == '130') {
            $data = DB::table('mst_pendaftaran')
                ->select('*')
                ->Where(['status_ujian' => 0, 'tipe_ujian' => 0, 'status_prodi' => 1])
                ->get();

            $syarat = DB::table('mst_syarat_ujian')
                ->select('*')
                ->where('tipe_ujian', 0)
                ->get();


            $mstsyaratujian = \App\Model\mst_syarat_ujian::where(["tipe_ujian" => 0])->count();
            $trtsyaratujian = \App\TrtSyaratUjian::where(["C_NPM" => auth()->user()->name, "status" =>
            1])->whereIn("syarat_ujian_id", \App\Model\mst_syarat_ujian::where(["tipe_ujian" =>
            0])->select("syarat_ujian_id"))->count();
            $trtreg =
                \App\Model\trt_reg::whereIn("bimbingan_id", \App\Model\trt_bimbingan::where(
                    "C_NPM",
                    auth()->user()->name
                )->select("bimbingan_id"))->whereIn("pendaftaran_id", \App\Model\mst_pendaftaran::where(
                    "tipe_ujian",
                    0
                )->select("pendaftaran_id"))->count();
        } else {
            $data = DB::table('mst_pendaftaran')
                ->select('*')
                ->Where(['status_ujian' => 0, 'tipe_ujian' => 0, 'status_prodi' => 2])
                ->get();


            $syarat = DB::table('mst_syarat_ujian')
                ->select('*')
                ->where('tipe_ujian', 0)
                ->get();


            $mstsyaratujian = \App\Model\mst_syarat_ujian::where(["tipe_ujian" => 0])->count();
            $trtsyaratujian = \App\TrtSyaratUjian::where(["C_NPM" => auth()->user()->name, "status" =>
            1])->whereIn("syarat_ujian_id", \App\Model\mst_syarat_ujian::where(["tipe_ujian" =>
            0])->select("syarat_ujian_id"))->count();
            $trtreg =
                \App\Model\trt_reg::whereIn("bimbingan_id", \App\Model\trt_bimbingan::where(
                    "C_NPM",
                    auth()->user()->name
                )->select("bimbingan_id"))->whereIn("pendaftaran_id", \App\Model\mst_pendaftaran::where(
                    "tipe_ujian",
                    0
                )->select("pendaftaran_id"))->count();
        }

        return view('tugasakhir.mhs.signup_proposal', compact('data', 'syarat', 'mstsyaratujian', 'trtsyaratujian', 'trtreg'));
    }

    public function signup_seminarhasil()
    {
        $data = DB::table('mst_pendaftaran')
            ->select('*')
            ->where('tipe_ujian', 1)
            ->orWhere('tipe_ujian', 3)
            ->get();
        $syarat = DB::table('mst_syarat_ujian')
            ->select('*')
            ->where('tipe_ujian', 1)
            ->get();
        return view('tugasakhir.mhs.signup_seminarhasil', compact('data', 'syarat'));
    }

    public function signup_ujianmeja()
    {
        $data = DB::table('mst_pendaftaran')
            ->select('*')
            ->where('tipe_ujian', 2)
            ->where('status_ujian', 0)
            ->orWhere('tipe_ujian', 3)
            ->get();
        $syarat = DB::table('mst_syarat_ujian')
            ->select('*')
            ->where('tipe_ujian', 2)
            ->get();


        return view('tugasakhir.mhs.signup_ujianmeja', compact('data', 'syarat'));
    }

    public function registrasi(Request $request)
    {
        try {
            $datapost = $request->all();
            $mstsyaratujian = \App\Model\mst_syarat_ujian::where(["tipe_ujian" => $request->tipe_ujian])->count();
            $trtsyaratujian = \App\TrtSyaratUjian::where(["C_NPM" => auth()->user()->name, "status" => 1])->whereIn("syarat_ujian_id", \App\Model\mst_syarat_ujian::where(["tipe_ujian" => $request->tipe_ujian])->select("syarat_ujian_id"))->count();
            $trtreg = \App\Model\trt_reg::whereIn("bimbingan_id", \App\Model\trt_bimbingan::where("C_NPM", auth()->user()->name)->select("bimbingan_id"))->whereIn("pendaftaran_id", \App\Model\mst_pendaftaran::where("tipe_ujian", $request->tipe_ujian)->select("pendaftaran_id"))->count();


            $data_jml = DB::table('mst_pendaftaran')
                ->select('jml_peserta', 'kuota')
                ->where('pendaftaran_id', $datapost['pendaftaran_id'])
                ->first();

            if ($data_jml->jml_peserta < $data_jml->kuota && empty($trtreg) && !empty($mstsyaratujian) && $trtsyaratujian == $mstsyaratujian) :
                $data = DB::table('trt_bimbingan')
                    ->select('*')
                    ->where('C_NPM', auth()->user()->name)
                    ->first();

                if ($request->tipe_ujian == 2) {
                    trt_bimbingan::where('C_NPM', auth()->user()->name)->update([
                        'status_bimbingan' => $request->tipe_ujian,
                        'status_tolak_meja' => 0,
                    ]);
                } else if ($request->tipe_ujian == 0) {
                    trt_bimbingan::where('C_NPM', auth()->user()->name)->update([
                        'status_bimbingan' => $request->tipe_ujian,
                        'status_tolak_proposal' => 0,
                    ]);
                }

                $datapost['bimbingan_id'] = $data->bimbingan_id;
                $datapost['tgl_reg'] = date('Y-m-d');
                $datapost['C_NPM'] = auth()->user()->name;





                $data_penguji_proposal = DB::table('trt_penguji')
                    ->select('*')
                    ->where('C_NPM', auth()->user()->name)
                    ->first();


                if ($request->tipe_ujian == 0) {
                    TrtPenguji::create([
                        'C_NPM' => $datapost["C_NPM"],
                        'tipe_ujian' => $datapost["tipe_ujian"],
                        'ketua_sidang_id' => $data->pembimbing_I_id,
                    ]);
                } else {
                    $data_penguji_lengkap = TrtPenguji::where('C_NPM', auth()->user()->name)->where('tipe_ujian', 0)->first();
                    if ($data_penguji_lengkap == null || $data_penguji_lengkap == '') {
                        TrtPenguji::create([
                            'C_NPM' => $datapost["C_NPM"],
                            'tipe_ujian' => $datapost["tipe_ujian"],
                        ]);
                    } else {
                        TrtPenguji::create([
                            'C_NPM' => $datapost["C_NPM"],
                            'tipe_ujian' => $datapost["tipe_ujian"],
                            'penguji_I_id' => $data_penguji_proposal->penguji_I_id,
                            'penguji_II_id' => $data_penguji_proposal->penguji_II_id,
                            'penguji_III_id' => $data_penguji_proposal->penguji_III_id,
                        ]);
                    }
                }

                trt_reg::create([
                    'bimbingan_id' => $datapost["bimbingan_id"],
                    'pendaftaran_id' => $datapost["pendaftaran_id"],
                    'C_NPM' => $datapost["C_NPM"],
                    'created_at' => $datapost["tgl_reg"],
                    'status' => $datapost["tipe_ujian"],
                ]);


                $data_jml->jml_peserta = $data_jml->jml_peserta + 1;

                DB::table('mst_pendaftaran')
                    ->where('pendaftaran_id', $datapost['pendaftaran_id'])
                    ->update(['jml_peserta' => $data_jml->jml_peserta]);
            endif;
            return redirect('mhs/riwayat_ujian/' . $datapost["C_NPM"]);
        } catch (Exception $e) {
            return redirect('mhs/signup_ujianmeja');
        }
    }

    public function usulan_tmp(Request $request)
    {
        $datapost = $request->all();
        $cek = DB::table('mst_tmp_usulan')
            ->select('*')
            ->where('C_NPM', auth()->user()->name)
            ->get();
        $usulan = DB::table('mst_tmp_usulan')
            ->select('*')
            ->where('C_NPM', auth()->user()->name)
            ->first();
        if ($cek->isEmpty()) {
            $datapost['C_NPM'] = auth()->user()->name;
            mst_tmp_usulan::create($datapost);
        } else {
            if ($usulan->pembimbing_I_id != $request->pembimbing_I_id) :
                mst_tmp_usulan::where('C_NPM', auth()->user()->name)->update([
                    'pembimbing_I_id' => $datapost['pembimbing_I_id'],
                    'pembimbing_II_id' => $datapost['pembimbing_II_id'],
                    'pembimbing_I_status' => "2",
                ]);
            elseif ($usulan->pembimbing_II_id != $request->pembimbing_II_id) :
                mst_tmp_usulan::where('C_NPM', auth()->user()->name)->update([
                    'pembimbing_I_id' => $datapost['pembimbing_I_id'],
                    'pembimbing_II_id' => $datapost['pembimbing_II_id'],
                    'pembimbing_II_status' => "2",
                ]);
            endif;
        }

        //        $id = auth()->user()->name;
        //        $data = DB::table('mst_bidangilmu')
        //            ->select('*')
        //            ->get();
        //
        //        $listdosen = DB::table('t_mst_dosen')
        //            ->select('*')
        //            ->get();
        //
        //        $datatopik = DB::table('trt_topik')
        //            ->select('*')
        //            ->where('C_NPM',$id)
        //            ->get();
        //        return view('tugasakhir.mhs.pengajuan_topik',compact('data','datatopik','listdosen','cek'));
        return redirect()->back();
    }

    public function getPembimbingStatus($index, $id)
    {
        if ($index == "0") {
            $pembimbing = mst_tmp_usulan::where(["pembimbing_I_id" => $id, "C_NPM" => Auth::user()->name])->firstOrFail();
            return response()->json($pembimbing->pembimbing_I_status);
        } elseif ($index == "1") {
            $pembimbing = mst_tmp_usulan::where(["pembimbing_II_id" => $id, "C_NPM" => Auth::user()->name])->firstOrFail();
            return response()->json($pembimbing->pembimbing_II_status);
        }
        return abort(404);
    }

    public function syarat_ujianpost(Request $request)
    {
        if (empty($request->link[$request->sui])) {
            return redirect()->back();
        }

        $trtsyaratujian = TrtSyaratUjian::where(["syarat_ujian_id" => $request->syarat_ujian_id[$request->sui], "C_NPM" => auth()->user()->name])->first();

        if (empty($trtsyaratujian)) {
            TrtSyaratUjian::create([
                "C_NPM" => auth()->user()->name,
                "syarat_ujian_id" => $request->syarat_ujian_id[$request->sui],
                "link" => $request->link[$request->sui],
                "status" => 2
            ]);
        } else {
            TrtSyaratUjian::where(["syarat_ujian_id" => $request->syarat_ujian_id[$request->sui], "C_NPM" => auth()->user()->name])->update([
                "link" => $request->link[$request->sui],
                "status" => 2
            ]);
        }
        return redirect()->back();
    }

    public function syarat_ujianpost_all(Request $request)
    {
        return $request;
        $datanotnull = 0;

        for ($i = 0; $i < count($request->link); $i++) {
            if ($request->link[$i] != null) {
                $datanotnull = $datanotnull + 1;
            }
        }

        for ($i = 0; $i < $datanotnull; $i++) {
            TrtSyaratUjian::updateOrCreate(
                [
                    "C_NPM" => auth()->user()->name,
                    "syarat_ujian_id" => $request->syarat_ujian_id[$i],
                    "status" => 2
                ],
                [
                    "link" => $request->link[$i]
                ]
            );
        }
        return redirect()->back();
    }

    public function syarat_ujiandel($type, $id)
    {
        TrtSyaratUjian::where(["syarat_ujian_id" => $id, "C_NPM" => auth()->user()->name])->delete();
        TrtPengajuanDokumen::where(["C_NPM" => auth()->user()->name, "type" => $type])->delete();
        return redirect()->back();
    }

    public function ajukan_dokumen($type)
    {
        $trtpengajuandokumen = TrtPengajuanDokumen::where(["C_NPM" => auth()->user()->name, "type" => $type])->count();
        if ($trtpengajuandokumen == 0) {
            TrtPengajuanDokumen::create([
                "type" => $type,
                "C_NPM" => auth()->user()->name
            ]);
        } else {
            TrtPengajuanDokumen::where(["C_NPM" => auth()->user()->name, "type" => $type])->delete();
        }
        return redirect()->back();
    }

    public function detail_hasil_proposal($kode_dosen, $reg_id, $pendaftaran_id, $nim, $status)
    {
        $trtjadwalujian = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("trt_jadwal_ujian.pendaftaran_id", $pendaftaran_id)->first();
        $trtjadwalujianpermhs = TrtJadwalUjianPerMhs::join("mst_ruangan", "mst_ruangan.id", "trt_jadwal_ujian_per_mhs.ruangan")
            ->where([
                "C_NPM" => $nim,
                "jadwal_ujian" => $trtjadwalujian->id
            ])->first();
        $trt_bimbingan = trt_bimbingan::where("C_NPM", $nim)->first();
        $mst_pendaftaran = mst_pendaftaran::find($pendaftaran_id);
        $trt_penguji = TrtPenguji::where([
            "C_NPM" => $nim,
            "tipe_ujian" => $mst_pendaftaran->tipe_ujian
        ])->first();

        $ruangan = MstRuangan::find($trtjadwalujianpermhs->ruangan)->nama_ruangan;
        $tgl_ujian = Carbon::parse($trtjadwalujian->tgl_ujian)->formatLocalized("%d %B %Y");
        switch ($mst_pendaftaran->tipe_ujian) {
            case "0":
                $tipe_ujian = "Proposal";
                break;
            case "2":
                $tipe_ujian = "Tugas Akhir";
                break;
        }

        $status_dosen = '';
        if ($status == 1) {
            $status_dosen = "Ketua Sidang";
        } else if ($status == 2) {
            $status_dosen = "Penguji I";
        } else if ($status == 3) {
            $status_dosen = "Penguji II";
        } else if ($status == 4) {
            $status_dosen = "Penguji III";
        } else if ($status == 5) {
            $status_dosen = "Pembimbing Utama";
        } else if ($status == 6) {
            $status_dosen = "Pembimbing Pendamping";
        }


        $data_hasil = trt_hasil::where('reg_id', $reg_id)->where('nidn', $kode_dosen)->get();
        return view("tugasakhir.mhs.lembaran_penilaian_per_dosen", compact(
            "nim",
            "trt_bimbingan",
            "trt_penguji",
            "tipe_ujian",
            "ruangan",
            "tgl_ujian",
            "data_hasil",
            "status_dosen"
        ));
    }

    public function detail_hasil_ujianmeja($kode_dosen, $reg_id, $pendaftaran_id, $nim, $status)
    {
        $trtjadwalujian = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("trt_jadwal_ujian.pendaftaran_id", $pendaftaran_id)->first();
        $trtjadwalujianpermhs = TrtJadwalUjianPerMhs::join("mst_ruangan", "mst_ruangan.id", "trt_jadwal_ujian_per_mhs.ruangan")
            ->where([
                "C_NPM" => $nim,
                "jadwal_ujian" => $trtjadwalujian->id
            ])->first();
        $trt_bimbingan = trt_bimbingan::where("C_NPM", $nim)->first();
        $mst_pendaftaran = mst_pendaftaran::find($pendaftaran_id);
        $trt_penguji = TrtPenguji::where([
            "C_NPM" => $nim,
            "tipe_ujian" => $mst_pendaftaran->tipe_ujian
        ])->first();

        $ruangan = MstRuangan::find($trtjadwalujianpermhs->ruangan)->nama_ruangan;
        $tgl_ujian = Carbon::parse($trtjadwalujian->tgl_ujian)->formatLocalized("%d %B %Y");
        switch ($mst_pendaftaran->tipe_ujian) {
            case "0":
                $tipe_ujian = "Proposal";
                break;
            case "2":
                $tipe_ujian = "Tugas Akhir";
                break;
        }

        $status_dosen = '';
        if ($status == 1) {
            $status_dosen = "Ketua Sidang";
        } else if ($status == 2) {
            $status_dosen = "Penguji I";
        } else if ($status == 3) {
            $status_dosen = "Penguji II";
        } else if ($status == 4) {
            $status_dosen = "Penguji III";
        } else if ($status == 5) {
            $status_dosen = "Pembimbing Utama";
        } else if ($status == 6) {
            $status_dosen = "Pembimbing Pendamping";
        }



        $data_hasil = trt_hasil::where('reg_id', $reg_id)->where('nidn', $kode_dosen)->get();
        return view("tugasakhir.mhs.lembaran_penilaian_per_dosen_ujianmeja", compact(
            "nim",
            "trt_bimbingan",
            "trt_penguji",
            "tipe_ujian",
            "ruangan",
            "tgl_ujian",
            "data_hasil",
            "status_dosen"
        ));
    }

    // Surat Sk Pembimbing
    public function surat_sk_pembimbing($nomor)
    {

        $data = DB::table('mst_sk_pembimbing')
            ->join('trt_bimbingan', 'mst_sk_pembimbing.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->select('*')
            ->get();

        $status = 'tidak';
        $new_nomor = "";

        foreach ($data as $value) {
            if (str_replace("/", "", $value->nomor_sk) == $nomor) {
                $status = "ada";
                $new_nomor = $value->nomor_sk;
            }
        }

        $data_sk = DB::table('mst_sk_pembimbing')
            ->join('trt_bimbingan', 'mst_sk_pembimbing.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->select('*')
            ->where('mst_sk_pembimbing.nomor_sk', $new_nomor)
            ->get();
        $tgl_ujian = helper::tgl_indo_lengkap(date('Y-m-d'));
        return view('tugasakhir.fakultas.cetakskpembimbing', compact('data_sk', 'tgl_ujian'));
    }

    // SK Ujian Meja
    public function surat_sk_ujian_meja($nomor)
    {

        $data = DB::table('mst_sk_penugasan')
            ->join('trt_bimbingan', 'mst_sk_penugasan.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->select('*')
            ->get();

        $status = 'tidak';
        $id_bimbingan = "";

        foreach ($data as $value) {
            if (str_replace("/", "", $value->nomor_sk) == $nomor) {
                $status = "ada";
                $id_bimbingan = $value->bimbingan_id;
            }
        }


        $data_sk = DB::table('mst_sk_penugasan')
            ->join('trt_bimbingan', 'mst_sk_penugasan.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->join('trt_penguji', 'trt_penguji.C_NPM', '=', 'trt_bimbingan.C_NPM')
            ->join('trt_jadwal_ujian_per_mhs', 'trt_jadwal_ujian_per_mhs.C_NPM', '=', 'trt_bimbingan.C_NPM')
            ->join('trt_jadwal_ujian', 'trt_jadwal_ujian.id', '=', 'trt_jadwal_ujian_per_mhs.jadwal_ujian')
            ->join('mst_ruangan', 'mst_ruangan.id', '=', 'trt_jadwal_ujian_per_mhs.ruangan')
            ->select(['mst_sk_penugasan.created_at', 'mst_sk_penugasan.sk_penugasan_id', 'mst_sk_penugasan.nomor_sk', 'trt_bimbingan.pembimbing_I_id', "trt_bimbingan.pembimbing_II_id", "trt_penguji.ketua_sidang_id", "trt_penguji.penguji_I_id", "trt_penguji.penguji_II_id", "trt_penguji.penguji_III_id", "trt_penguji.C_NPM", "trt_jadwal_ujian.tgl_ujian", "trt_jadwal_ujian_per_mhs.jam_ujian", "mst_ruangan.nama_ruangan", "trt_jadwal_ujian.pendaftaran_id"])
            ->where('trt_bimbingan.bimbingan_id', $id_bimbingan)
            ->where('trt_penguji.tipe_ujian', 2)
            ->where('trt_jadwal_ujian.status', 2)
            ->get();

        return view('tugasakhir.fakultas.cetakskpenugasan', compact('data_sk'));
    }

    // Surat Ujian Proposal
    public static function surat_sk_proposal($pendaftaran_id)
    {
        try {
            $trtjadwalujian = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where("trt_jadwal_ujian.pendaftaran_id", $pendaftaran_id)->first();
            $trtjadwalujianpermhs = TrtJadwalUjianPerMhs::join("mst_ruangan", "mst_ruangan.id", "trt_jadwal_ujian_per_mhs.ruangan")
                ->where([
                    "C_NPM" => auth()->user()->name,
                    "jadwal_ujian" => $trtjadwalujian->id
                ])->first();
            $ruangan = $trtjadwalujianpermhs->nama_ruangan;
            $jam_ujian = $trtjadwalujianpermhs->jam_ujian;
            $tgl_ujian = Carbon::parse($trtjadwalujian->tgl_ujian)->formatLocalized("%A, %d %B %Y");
            $tanggal = Carbon::parse($trtjadwalujian->tgl_ujian)->formatLocalized("%d");
            $bulan = Carbon::parse($trtjadwalujian->tgl_ujian)->formatLocalized("%m");
            $tahun = Carbon::parse($trtjadwalujian->tgl_ujian)->formatLocalized("%Y");
            $penguji = TrtPenguji::where([
                "C_NPM" => auth()->user()->name,
                "tipe_ujian" => $trtjadwalujian->tipe_ujian
            ])->first();
            $bimbingan = trt_bimbingan::where("C_NPM", auth()->user()->name)->first();
            switch ($trtjadwalujian->tipe_ujian) {
                case "0":
                    $tipe_ujian = "Proposal";
                    $count_jam_ujian = strlen($jam_ujian);
                    if ($count_jam_ujian == 5) {
                        $waktu = $jam_ujian . "-" . sprintf('%02d', substr($jam_ujian, 0, 2) + 2) . ":30";
                    } else {
                        $waktu = $jam_ujian;
                    }
                    break;
                case "2":
                    $tipe_ujian = "Tugas Akhir";
                    $count_jam_ujian = strlen($jam_ujian);
                    if ($count_jam_ujian == 5) {
                        $waktu = $jam_ujian . "-" . sprintf('%02d', substr($jam_ujian, 0, 2) + 2) . ":30";
                    } else {
                        $waktu = $jam_ujian;
                    }
                    break;
            }
            $nim = auth()->user()->name;
            $tgl_sekarang = helper::tgl_indo_lengkap(date('Y-m-d'));

            return view('tugasakhir.prodi.cetakskpenguji', compact(
                "nim",
                "penguji",
                "bimbingan",
                "tipe_ujian",
                "tgl_ujian",
                "tanggal",
                "bulan",
                "tahun",
                "waktu",
                "ruangan",
                'tgl_sekarang'
            ));
        } catch (Exception $error) {
            return redirect('mhs/download');
        }
    }
}
