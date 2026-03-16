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

class mhs extends Controller
{

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
                $tipe_ujian = "Meja";
                break;
        }
        return view("tugasakhir.mhs.cetak_beritaacara_proposal", compact(
            "nim",
            "trt_bimbingan",
            "trt_penguji",
            "tipe_ujian",
            "ruangan",
            "tgl_ujian"
        ));
    }
    // Akhir

    // Halaman Berita Cara Proposal
    public function beritaacara_proposal($nim)
    {

        $data = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
            ->join("t_mst_mahasiswa", "t_mst_mahasiswa.C_NPM", "=", "trt_reg.C_NPM")
            ->join("trt_penguji", "trt_penguji.C_NPM", "=", "t_mst_mahasiswa.C_NPM")
            ->where([
                "trt_reg.C_NPM" => $nim,
            ])->get();
        return view('tugasakhir.mhs.beritaacara_proposal', compact('data'));
    }
    // Akhir Berita Acara Proposal

    // Halaman Berita Cara Ujian
    public function beritaacara_ujian()
    {
        return view('tugasakhir.mhs.beritaacara_ujian');
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
        if ($request->password_baru == $request->ulangi_password) {
            $status = DB::update('update users set password = ? where name = ?', [Hash::make($request->password_baru), $request->name]);
            return redirect()->back()->with('success', 'Password Berhasil Diubah');
        } else {
            return redirect()->back()->with('error', 'Password Tidak Sama');
        }
    }
    // Akhir Ubah Password

    public function download()
    {
        return view('tugasakhir.mhs.download');
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
        unlink($path);
        trt_topik::where('topik_id', $id)->delete();
        return redirect::to('mhs/pengajuan_topik');
    }


    public function pengajuan_topikpost(Request $request)
    {
        $datapost = $request->except(["bidang_ilmu"]);
        $datapost['status'] = 0;
        $datapost['user_id'] = $datapost['C_NPM'];
        $datapost['bidang_ilmu_peminatan'] = $datapost['bidang_ilmu_peminatan'];
        $datapost['bidang_ilmu_peminatan'] = $datapost['bidang_ilmu_peminatan'];
        $file = isset($datapost['kerangka']) ? $datapost['kerangka'] : '';
        $datapost['kerangka'] = helper::uploadFile($file, 'dokumen/', '');
        $datapost["note"] = $datapost["note"];

        $trt_topik = trt_topik::create($datapost);
        foreach ($request->bidang_ilmu as $key => $bidangilmu) {
            RequestPembimbing::create([
                "C_NPM" => $request->C_NPM,
                "bidang_ilmu" => $bidangilmu,
                "topik" => $trt_topik->topik_id,
            ]);
        }

        return redirect()->back();
    }

    public function riwayat_ujian()
    {

        return view('tugasakhir.mhs.riwayat_ujian');
    }

    public function mail_inbox()
    {
        $data = DB::table('mst_pesan')
            ->join('trt_konsultasi', 'mst_pesan.pesan_id', '=', 'trt_konsultasi.pesan_id')
            ->select('*')
            ->where('penerima_id', auth()->user()->name)
            ->get();

        $datax = DB::table('mst_pesan')
            ->join('trt_konsultasi', 'mst_pesan.pesan_id', '=', 'trt_konsultasi.pesan_id')
            ->select('*')
            ->where('pengirim_id', auth()->user()->name)
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

        foreach ($request->lampiran as $lampiran) {
            $size = round($lampiran->getSize() / 1024);
            if ($size > 10240) {
                session()->flash("error", "Setiap file tidak lebih dari 10MB, silahkan sediakan link alternatif.");
                return redirect()->back();
            }
        }


        $mstpesan = mst_pesan::create([
            "perihal_pesan" => $request->perihal_pesan,
            "isi_pesan" => $request->isi_pesan,
        ]);
        foreach ($request->lampiran as $lampiran) {
            LampiranPesan::create([
                "pesan_id" => $mstpesan->pesan_id,
                "lampiran" => Helper::uploadFile($lampiran, 'dokumen/', '')
            ]);
        }
        foreach ($request->penerima_id as $penerima) {
            trt_konsultasi::create([
                "pesan_id" => $mstpesan->pesan_id,
                "pengirim_id" => auth()->user()->name,
                "penerima_id" => $penerima,
                "status_baca" => 0
            ]);
        }
        return redirect::to('mhs/mail_sent');
    }

    public function mail_sent()
    {
        $data = DB::table('mst_pesan')
            ->join('trt_konsultasi', 'mst_pesan.pesan_id', '=', 'trt_konsultasi.pesan_id')
            ->select('*')
            ->where('pengirim_id', auth()->user()->name)
            ->get();
        $datax = DB::table('mst_pesan')
            ->join('trt_konsultasi', 'mst_pesan.pesan_id', '=', 'trt_konsultasi.pesan_id')
            ->select('*')
            ->where('penerima_id', auth()->user()->name)
            ->get();
        return view('tugasakhir.mhs.mail_sent', compact('data', 'datax'));
    }


    public function mail_read($id)
    {
        $data = DB::table('mst_pesan')
            ->join('trt_konsultasi', 'mst_pesan.pesan_id', '=', 'trt_konsultasi.pesan_id')
            ->select('*')
            ->where('mst_pesan.pesan_id', $id)
            ->first();

        trt_konsultasi::where(["pesan_id" => $id, "penerima_id" => auth()->user()->name])->update([
            "status_baca" => 1
        ]);

        return view('tugasakhir.mhs.mail_read', compact('data'));
    }

    public function detail_ujian()
    {
        return view('tugasakhir.mhs.detail_ujian');
    }

    public function signup_proposal()
    {
        $data = DB::table('mst_pendaftaran')
            ->select('*')
            ->Where(['status_ujian' => 0, 'tipe_ujian' => 0])
            ->get();

        $syarat = DB::table('mst_syarat_ujian')
            ->select('*')
            ->where('tipe_ujian', 0)
            ->get();

        return view('tugasakhir.mhs.signup_proposal', compact('data', 'syarat'));
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
                ->select('bimbingan_id')
                ->where('C_NPM', auth()->user()->name)
                ->first();
            $datapost['bimbingan_id'] = $data->bimbingan_id;
            $datapost['tgl_reg'] = date('Y-m-d');
            $datapost['C_NPM'] = auth()->user()->name;

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
        return view('tugasakhir.mhs.riwayat_ujian');
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
}
