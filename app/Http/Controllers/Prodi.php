<?php

namespace App\Http\Controllers;

use App\Dosen;
use App\Helper;
use App\Model\mst_bidangilmu;
use App\Model\mst_jenis_tugas_akhir;
use App\Model\mst_pendaftaran;
use App\Model\mst_pengumuman;
use App\Model\mst_syarat_ujian;
use App\Model\mst_tmp_usulan;
use App\Model\t_mst_mahasiswa;
use App\Model\trt_bimbingan;
use App\Model\trt_reg;
use App\Model\trt_sk;
use App\Model\trt_topik;
use App\Model\trt_hasil;
use App\Model\trt_sk_ujian_ta;
use App\Model\users;
use App\MstRuangan;
use App\TrtJadwalUjian;
use App\TrtJadwalUjianPerMhs;
use App\TrtLevelPembimbing;
use App\TrtPengajuanDokumen;
use App\TrtPenguji;
use App\TrtSyaratUjian;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class prodi extends Controller
{

    // Ubah Pembimbing Per Mahasiswa
    public function ubah_pembimbing_per_mahasiswa($nim)
    {
        $data = DB::table('t_mst_dosen')
            ->leftJoin("trt_level_pembimbing", "trt_level_pembimbing.C_KODE_DOSEN", "=", "t_mst_dosen.C_KODE_DOSEN")
            ->select('t_mst_dosen.*', 'trt_level_pembimbing.level')
            ->get();
        $cek = DB::table('trt_bimbingan')
            ->select('*')
            ->where('C_NPM', $nim)
            ->get();

        $data_mahasiswa = DB::table('t_mst_mahasiswa')
            ->select('*')
            ->where('C_NPM', $nim)
            ->first();

        return view("tugasakhir.prodi.ubah_pembimbing_per_mahasiswa", compact('data', 'cek', 'data_mahasiswa'));
    }
    // Akhir Ubah Pembimbing Per Mahasiswa

    // Ubah Pembimbing Per Mahasiswa
    public function ubah_pembimbing_per_mahasiswa_post(Request $request)
    {
        try {
            DB::table('trt_bimbingan')->where('C_NPM', $request->nim)->update([
                "pembimbing_I_id" => $request->pembimbing_I_id,
                "pembimbing_II_id" => $request->pembimbing_II_id
            ]);
            return redirect::to('prodi/detail_mahasiswa/' . $request->nim);
        } catch (Exception $e) {
            return redirect::to('prodi/detail_mahasiswa/' . $request->nim);
        }
    }
    // Akhir Ubah Pembimbing Per Mahasiswa

    // Edit Judul Mahasiswa
    public function edit_judul_detail_mahasiswa($nim)
    {
        $data = DB::table("trt_bimbingan")
            ->select("*")
            ->where("C_NPM", $nim)
            ->get();
        return view("tugasakhir.prodi.edit_judul_detail_mahasiswa", compact('data'));
    }
    // Akhir Edit Judul mahasiswa

    // Edit Judul Mahasiswa
    public function ubah_judul(Request $request, $nim)
    {
        DB::table("trt_bimbingan")->where('C_NPM', $nim)->update([
            'judul' => $request->topik,
        ]);
        return redirect::to('prodi/detail_mahasiswa/' . $nim);
    }
    // Akhir Edit Judul mahasiswa

    // Batal Surat Pengusulan
    public function batal_set_pembimbing($nim)
    {
        DB::table('trt_bimbingan')->where('trt_bimbingan.C_NPM', $nim)->delete();
        return redirect::to('prodi/sk_pembimbing/');
    }
    // Akhir Batal Surat Pengusulan

    // Tampil Catatan Pada Syarat Ujian
    public function detail_persyaratan_ujianmeja_catatan($id, $nim)
    {
        $data = DB::table('trt_syarat_ujian')
            ->select("*")
            ->where("id", $id)
            ->where("C_NPM", $nim)
            ->get();
        return view('tugasakhir.prodi.detail_persyaratan_ujianmeja_catatan', compact('data', 'nim'));
    }

    public function detail_persyaratan_ujianmeja_catatan_post(Request $request)
    {
        try {
            TrtSyaratUjian::where("id", $request->id)
                ->where('C_NPM', $request->C_NPM)
                ->update([
                    "catatan" => $request->catatan
                ]);
            return redirect::to('prodi/detail_persyaratan_ujianmeja/' . $request->C_NPM)->with('status', 'success');
        } catch (Exception $exception) {
            return redirect::to('prodi/detail_persyaratan_ujianmeja/' . $request->C_NPM)->with('status', 'error');
        }
    }

    // Tampil Catatan Pada Syarat Ujian
    public function detail_persyaratan_proposal_catatan($id, $nim)
    {
        $data = DB::table('trt_syarat_ujian')
            ->select("*")
            ->where("id", $id)
            ->where("C_NPM", $nim)
            ->get();
        return view('tugasakhir.prodi.detail_persyaratan_proposal_catatan', compact('data', 'nim'));
    }

    public function detail_persyaratan_proposal_catatan_post(Request $request)
    {
        try {
            TrtSyaratUjian::where("id", $request->id)
                ->where('C_NPM', $request->C_NPM)
                ->update([
                    "catatan" => $request->catatan
                ]);
            return redirect::to('prodi/detail_persyaratan_proposal/' . $request->C_NPM)->with('status', 'success');
        } catch (Exception $exception) {
            return redirect::to('prodi/detail_persyaratan_proposal/' . $request->C_NPM)->with('status', 'error');
        }
    }

    // Set SK
    public function set_sk($id)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $id)->first();
        $data = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
            ->join("t_mst_mahasiswa", "t_mst_mahasiswa.C_NPM", "=", "trt_reg.C_NPM")
            ->join("trt_penguji", "trt_penguji.C_NPM", "=", "t_mst_mahasiswa.C_NPM")
            ->where([
                "trt_reg.pendaftaran_id" => $id,
                "trt_penguji.tipe_ujian" => $info->tipe_ujian
            ])->get();


        return view('tugasakhir.prodi.set_sk', compact('data'));
    }
    // Akhir Set SK

    // Add Sk Pembimbing By Prodi
    public function add_sk_pembimbing(Request $request)
    {
        $path = "";
        if (Auth::user()->level == 6) {
            $path = "akademikprodi";
        } else {
            $path = "prodi";
        }
        $datapost = $request->all();
        try {
            $status = TrtPenguji::where('C_NPM', $request->c_npm)->where('tipe_ujian', 0)->update([
                'nomor_sk' => $request->nomor_sk
            ]);
            return redirect::to('' . $path . '/set_sk/' . $datapost['pendaftaran_id'] . '')->with('status', 'success');
        } catch (Exception $exception) {
            return redirect::to('' . $path . '/set_sk/' . $datapost['pendaftaran_id'] . '')->with('status', 'error');
        }
    }
    // Akhir Add Sk Pembimbing By Prodi


    // Menampilkan Status Bimbingan Mahasiswa
    public function detail_status_bimbingan_mahasiswa($status)
    {
        $data = DB::table('trt_bimbingan')
            ->select("*")
            ->where('trt_bimbingan.status_bimbingan', $status)
            ->get();

        return view('tugasakhir.prodi.detail_status_bimbingan_mahasiswa', compact('data', 'status'));
    }
    // Akhir Menampilkan Status Bimbingan Mahasiswa


    // Ubah Note Pada Prodi
    public function detail_note($id)
    {
        $data = DB::table("trt_topik")
            ->select("*")
            ->where("topik_id", $id)
            ->get();
        return view("tugasakhir.prodi.detail_note", compact('data'));
    }
    // Ubah Note Pada Prodi

    // Proses Ubah Note Pada Prodi
    public function note_update(Request $request, $id)
    {
        trt_topik::where("topik_id", $id)
            ->update([
                'note' => $request->note,
            ]);
        return redirect::to('prodi/topik');
    }
    // Akhir Proses Ubah Note Pada Prodi

    // Halaman Approve Hasil Ujian Proposal
    public function approve_hasilujian_proposal()
    {
        if (Auth::user()->name == "proditi") {
            $data = mst_pendaftaran::join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "mst_pendaftaran.pendaftaran_id")
                ->where('tipe_ujian', 0)
                ->where('mst_pendaftaran.status_prodi', 1)
                ->orwhere('tipe_ujian', 3)
                ->orderBy('mst_pendaftaran.created_at', 'desc')
                ->get();
        } else {
            $data = mst_pendaftaran::join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "mst_pendaftaran.pendaftaran_id")
                ->where('tipe_ujian', 0)
                ->where('mst_pendaftaran.status_prodi', 2)
                ->orwhere('tipe_ujian', 3)
                ->orderBy('mst_pendaftaran.created_at', 'desc')
                ->get();
        }
        return view('tugasakhir.prodi.approve_hasilujian_proposal', compact('data'));
    }
    // Akhir Approve Hasil Ujian Proposal

    // Halaman Approve Hasil Ujian Proposal
    public function detail_hasilujian_proposal($id)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $id)->first();
        $data = DB::select("SELECT * FROM mst_pendaftaran,trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_reg.pendaftaran_id = ? AND trt_reg.status = ?", [$id, $info->tipe_ujian]);



        return view('tugasakhir.prodi.detail_hasilujian_proposal', compact("data", "info"));
    }
    // Akhir Approve Hasil Ujian Proposal

    // Halaman Approve Hasil Ujian Proposal
    public function approve_hasilujian_proposal_post($id, $nim, $pendaftaran_id)
    {
        $bimbingan = DB::table('trt_bimbingan')
            ->select('pembimbing_I_id', 'pembimbing_II_id')
            ->where('C_NPM', $nim)
            ->first();

        $penguji = DB::table('trt_penguji')
            ->select('ketua_sidang_id', 'penguji_I_id', 'penguji_II_id', 'penguji_III_id')
            ->where([
                'C_NPM' => $nim,
                'tipe_ujian' => '0'
            ])
            ->first();

        if ($bimbingan && $penguji) {
            DB::table('trt_honorium')
                ->updateOrInsert(
                    [
                        'C_NPM' => $nim,
                        'tipe_ujian' => '0'
                    ],
                    [
                        'date' => now(),
                        'KS' => $penguji->ketua_sidang_id,
                        'PU' => $bimbingan->pembimbing_I_id,
                        'PP' => $bimbingan->pembimbing_II_id,
                        'P1' => $penguji->penguji_I_id,
                        'P2' => $penguji->penguji_II_id,
                        'P3' => $penguji->penguji_III_id,
                        'KS_H' => 0,
                        'PU_H' => 0,
                        'PP_H' => 0,
                        'P1_H' => 0,
                        'P2_H' => 0,
                        'P3_H' => 0,
                        'KS_Stat' => 0,
                        'PU_Stat' => 0,
                        'PP_Stat' => 0,
                        'P1_Stat' => 0,
                        'P2_Stat' => 0,
                        'P3_Stat' => 0
                    ]
                );
        }

        DB::table('trt_bimbingan')
            ->where([
                "bimbingan_id" => $id,
                "C_NPM" => $nim
            ])
            ->update(['status_bimbingan' => 2]);

        // Redirect setelah update
        return redirect('prodi/detail_hasilujian_proposal/' . $pendaftaran_id);
    }

    // Akhir Approve Hasil Ujian Proposal

    // Aprrove Semua Hasil Ujian
    public function approve_hasilujian_proposal_all_post()
    {
        $data = DB::table('trt_bimbingan')
            ->join("trt_reg", "trt_reg.bimbingan_id", "=", "trt_bimbingan.bimbingan_id")
            ->select("trt_reg.reg_id", "trt_bimbingan.bimbingan_id")
            ->where("trt_bimbingan.status_bimbingan", 0)
            ->get();
        $data_bimbingan_id = array();
        foreach ($data as $key => $value) {
            if (Helper::getJumlahTrtHasil($value->reg_id) == 5) {
                array_push($data_bimbingan_id, $value->bimbingan_id);
            }
        }

        try {
            DB::table("trt_bimbingan")
                ->whereIn("bimbingan_id", $data_bimbingan_id)
                ->update(["status_bimbingan" => 2]);

            return redirect()->back()->with(['status' => 'success', 'total' => count($data_bimbingan_id)]);
        } catch (\Exception $th) {
            return redirect()->back()->with(['status' => 'error', 'total' => count($data_bimbingan_id)]);
        }
    }
    // Akhir Approve Semua Hasil Ujian

    // Aprrove Semua Hasil Ujian
    public function approve_hasilujian_ta_all_post()
    {
        $data = DB::table('trt_bimbingan')
            ->join("trt_reg", "trt_reg.bimbingan_id", "=", "trt_bimbingan.bimbingan_id")
            ->select("trt_reg.reg_id", "trt_bimbingan.bimbingan_id")
            ->where("trt_bimbingan.status_bimbingan", 2)
            ->get();
        $data_bimbingan_id = array();
        foreach ($data as $key => $value) {
            if (Helper::getJumlahTrtHasil($value->reg_id) == 6) {
                array_push($data_bimbingan_id, $value->bimbingan_id);
            }
        }

        try {
            DB::table("trt_bimbingan")
                ->whereIn("bimbingan_id", $data_bimbingan_id)
                ->update(["status_bimbingan" => 3]);

            return redirect()->back()->with(['status' => 'success', 'total' => count($data_bimbingan_id)]);
        } catch (\Exception $th) {
            return redirect()->back()->with(['status' => 'error', 'total' => count($data_bimbingan_id)]);
        }
    }
    // Akhir Approve Semua Hasil Ujian

    // Halaman Approve Hasil Ujian Proposal
    public function tolak_hasilujian_proposal_post($id, $nim, $pendaftaran_id)
    {
        DB::table('trt_bimbingan')
            ->where([
                "bimbingan_id" => $id,
                "C_NPM" => $nim
            ])
            ->update([
                'status_tolak_proposal' => 1,
            ]);

        DB::table('trt_reg')
            ->where([
                "bimbingan_id" => $id,
                "status" => 0
            ])
            ->delete();

        return redirect('prodi/detail_hasilujian_proposal/' . $pendaftaran_id);
    }
    // Akhir Approve Hasil Ujian Proposal

    // Halaman Lembaran Hasil Ujian
    public function lembaran_hasilujian_proposal($pendaftaran_id, $nim, $reg_id)
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

        $reg_id = $reg_id;

        $data_hasil = trt_hasil::where('reg_id', $reg_id)->get();
        $data_dosen_selesai = DB::table('trt_penguji')
            ->select('*')
            ->where('trt_penguji.C_NPM', $nim)
            ->where('trt_penguji.tipe_ujian', 0)
            ->first();

        $data_dosen_pembimbing = DB::table('trt_bimbingan')
            ->select("*")
            ->where("trt_bimbingan.C_NPM", $nim)
            ->first();

        return view("tugasakhir.prodi.lembaran_hasilujian_proposal", compact(
            "nim",
            "trt_bimbingan",
            "trt_penguji",
            "tipe_ujian",
            "ruangan",
            "tgl_ujian",
            "data_hasil",
            'data_dosen_selesai',
            'reg_id',
            'data_dosen_pembimbing'
        ));
    }
    // Akhir Halaman Lembaran Hasil Ujian

    // Halaman Approve Hasil Ujian TA
    public function approve_hasilujian_ta()
    {
        if (Auth::user()->name == "proditi") {
            $data = mst_pendaftaran::join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "mst_pendaftaran.pendaftaran_id")
                ->where('tipe_ujian', 2)
                ->where('mst_pendaftaran.status_prodi', 1)
                ->orwhere('tipe_ujian', 3)
                ->orderBy('mst_pendaftaran.created_at', 'desc')
                ->get();
        } else {
            $data = mst_pendaftaran::join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "mst_pendaftaran.pendaftaran_id")
                ->where('tipe_ujian', 2)
                ->where('mst_pendaftaran.status_prodi', 2)
                ->orwhere('tipe_ujian', 3)
                ->orderBy('mst_pendaftaran.created_at', 'desc')
                ->get();
        }
        return view('tugasakhir.prodi.approve_hasilujian_ta', compact('data'));
    }
    // Akhir Approve Hasil Ujian TA

    // Halaman Approve Hasil Ujian TA
    public function detail_hasilujian_ta($id)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $id)->first();
        $data = DB::select("SELECT * FROM mst_pendaftaran,trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_reg.pendaftaran_id = ? AND trt_reg.status = ?", [$id, $info->tipe_ujian]);

        return view('tugasakhir.prodi.detail_hasilujian_ta', compact("data", "info"));
    }
    // Akhir Approve Hasil Ujian TA

    // Halaman Approve Hasil Ujian TA
    public function approve_hasilujian_ta_post($id, $nim, $pendaftaran_id)
    {
        $bimbingan = DB::table('trt_bimbingan')
            ->select('pembimbing_I_id', 'pembimbing_II_id')
            ->where('C_NPM', $nim)
            ->first();

        $penguji = DB::table('trt_penguji')
            ->select('ketua_sidang_id', 'penguji_I_id', 'penguji_II_id', 'penguji_III_id')
            ->where([
                'C_NPM' => $nim,
                'tipe_ujian' => '2'
            ])
            ->first();

        if ($bimbingan && $penguji) {
            DB::table('trt_honorium')
                ->updateOrInsert(
                    [
                        'C_NPM' => $nim,
                        'tipe_ujian' => '2'
                    ],
                    [
                        'Date' => now(),
                        'KS' => $penguji->ketua_sidang_id,
                        'PU' => $bimbingan->pembimbing_I_id,
                        'PP' => $bimbingan->pembimbing_II_id,
                        'P1' => $penguji->penguji_I_id,
                        'P2' => $penguji->penguji_II_id,
                        'P3' => $penguji->penguji_III_id,
                        'KS_H' => 0,
                        'PU_H' => 0,
                        'PP_H' => 0,
                        'P1_H' => 0,
                        'P2_H' => 0,
                        'P3_H' => 0,
                        'KS_Stat' => 0,
                        'PU_Stat' => 0,
                        'PP_Stat' => 0,
                        'P1_Stat' => 0,
                        'P2_Stat' => 0,
                        'P3_Stat' => 0
                    ]
                );
        }

        DB::table('trt_bimbingan')
            ->where([
                "bimbingan_id" => $id,
                "C_NPM" => $nim
            ])
            ->update(['status_bimbingan' => 3]);

        return redirect('prodi/detail_hasilujian_ta/' . $pendaftaran_id);
    }

    // Akhir Approve Hasil Ujian TA

    // Halaman Approve Hasil Ujian TA
    public function tolak_hasilujian_ta_post($id, $nim, $pendaftaran_id)
    {
        DB::table('trt_bimbingan')
            ->where([
                "bimbingan_id" => $id,
                "C_NPM" => $nim
            ])
            ->update([
                'status_tolak_meja' => 1,
            ]);

        DB::table('trt_reg')
            ->where([
                "bimbingan_id" => $id,
                "status" => 2
            ])
            ->delete();
        return redirect('prodi/detail_hasilujian_ta/' . $pendaftaran_id);
    }
    // Akhir Approve Hasil Ujian TA

    // Halaman Lembaran Hasil Ujian TA
    public function lembaran_hasilujian_ta($pendaftaran_id, $nim, $reg_id)
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
        $tanggal = Carbon::parse($trtjadwalujian->tgl_ujian)->formatLocalized("%d");
        $bulan = Carbon::parse($trtjadwalujian->tgl_ujian)->formatLocalized("%m");
        $tahun = Carbon::parse($trtjadwalujian->tgl_ujian)->formatLocalized("%Y");
        switch ($mst_pendaftaran->tipe_ujian) {
            case "0":
                $tipe_ujian = "Proposal";
                break;
            case "2":
                $tipe_ujian = "Meja";
                break;
        }

        $reg_id = $reg_id;

        $data_hasil = trt_hasil::where('reg_id', $reg_id)->get();
        $data_dosen_selesai = DB::table('trt_penguji')
            ->select('*')
            ->where('trt_penguji.C_NPM', $nim)
            ->where('trt_penguji.tipe_ujian', 2)
            ->first();

        $data_dosen_pembimbing = DB::table('trt_bimbingan')
            ->select("*")
            ->where("trt_bimbingan.C_NPM", $nim)
            ->first();

        return view("tugasakhir.prodi.lembaran_hasilujian_ta", compact(
            "nim",
            "trt_bimbingan",
            "trt_penguji",
            "tipe_ujian",
            "ruangan",
            "tgl_ujian",
            "data_hasil",
            "reg_id",
            "data_dosen_selesai",
            "data_dosen_pembimbing",
            "tanggal",
            "bulan",
            "tahun"
        ));
    }
    // Akhir Halaman Lembaran Hasil Ujian

    // Halaman Ubah Password
    public function ubah_password()
    {
        return view('tugasakhir.prodi.ubah_password');
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

    public function dosen_pembimbing()
    {
        $data = DB::table('t_mst_dosen')
            ->leftJoin("trt_level_pembimbing", "trt_level_pembimbing.C_KODE_DOSEN", "=", "t_mst_dosen.C_KODE_DOSEN")
            ->select('t_mst_dosen.*', 'trt_level_pembimbing.level')
            ->get();
        return view('tugasakhir.prodi.dosen_pembimbing', compact('data'));
    }

    public function detail_pembimbing($id)
    {
        $data = DB::table('t_mst_dosen')
            ->select('*')
            ->where('C_KODE_DOSEN', $id)
            ->first();

        $data_bimbingan1 = DB::table('trt_bimbingan')
            ->join('t_mst_mahasiswa', 'trt_bimbingan.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->where('pembimbing_I_id', $id)
            ->get();



        $data_bimbingan2 = DB::table('trt_bimbingan')
            ->join('t_mst_mahasiswa', 'trt_bimbingan.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->where('pembimbing_II_id', $id)
            ->get();
        $total = count($data_bimbingan1) + count($data_bimbingan2);

        $ppropI = DB::table('trt_bimbingan')
            ->where('pembimbing_I_id', $id)
            ->where('status_bimbingan', 0)
            ->get();
        $ppropII = DB::table('trt_bimbingan')
            ->where('pembimbing_II_id', $id)
            ->where('status_bimbingan', 0)
            ->get();
        $phasilI = DB::table('trt_bimbingan')
            ->where('pembimbing_I_id', $id)
            ->where('status_bimbingan', 1)
            ->get();
        $phasilII = DB::table('trt_bimbingan')
            ->where('pembimbing_II_id', $id)
            ->where('status_bimbingan', 1)
            ->get();
        $pmejaI = DB::table('trt_bimbingan')
            ->where('pembimbing_I_id', $id)
            ->where('status_bimbingan', 2)
            ->get();
        $pmejaII = DB::table('trt_bimbingan')
            ->where('pembimbing_II_id', $id)
            ->where('status_bimbingan', 2)
            ->get();
        $alumniI = DB::table('trt_bimbingan')
            ->where('pembimbing_I_id', $id)
            ->where('status_bimbingan', 3)
            ->get();
        $alumniII = DB::table('trt_bimbingan')
            ->where('pembimbing_II_id', $id)
            ->where('status_bimbingan', 3)
            ->get();
        return view('tugasakhir.prodi.detail_pembimbing', compact(
            'data',
            'total',
            'data_bimbingan1',
            'data_bimbingan2',
            'ppropI',
            'ppropII',
            'phasilI',
            'phasilII',
            'pmejaI',
            'pmejaII',
            'alumniI',
            'alumniII'
        ));
    }

    public function mahasiswa()
    {
        $status = '';
        if (auth()->user()->name == "prodisi") {
            $status = '131';
        } else if (auth()->user()->name == "proditi") {
            $status = '130';
        }


        $data = DB::table('t_mst_mahasiswa')
            ->select('t_mst_mahasiswa.C_NPM', 't_mst_mahasiswa.NAMA_MAHASISWA')
            ->where('C_NPM', 'LIKE', '' . $status . '%')
            ->get();
        return view('tugasakhir.prodi.mahasiswa', compact('data'));
    }

    public function detail_mahasiswa($id)
    {
        $datax = t_mst_mahasiswa::where('C_NPM', $id)->first();
        if (empty($datax)) {
            return redirect('prodi/mahasiswa')->with('error', 'Data mahasiswa tidak ditemukan');
        }

        return view('tugasakhir.prodi.detail_mahasiswa', compact('datax'));
    }

    public function update_status_bimbingan(Request $request)
    {
        try {
            $redirectUrl = url('prodi/detail_mahasiswa/' . $request->nim);

            $request->validate([
                'nim' => 'required',
                'bimbingan_id' => 'required',
                'status_bimbingan' => 'required|in:0,2,3,4',
            ]);

            $updated = DB::table('trt_bimbingan')
                ->where('bimbingan_id', $request->bimbingan_id)
                ->where('C_NPM', $request->nim)
                ->update([
                    'status_bimbingan' => $request->status_bimbingan,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'last_update' => date('Y-m-d H:i:s'),
                ]);

            if (!$updated) {
                return redirect($redirectUrl)->with('error', 'Data bimbingan mahasiswa tidak ditemukan / tidak berubah');
            }

            return redirect($redirectUrl)->with('success', 'Tahapan bimbingan berhasil diubah');
        } catch (Exception $e) {
            return redirect(url('prodi/detail_mahasiswa/' . $request->nim))->with('error', 'Gagal mengubah tahapan bimbingan');
        }
    }

    public function make_user_all()
    {
        $data = DB::table('t_mst_mahasiswa')
            ->select('t_mst_mahasiswa.C_NPM', 't_mst_mahasiswa.NAMA_MAHASISWA')
            ->orwhere('C_NPM', 'LIKE', '1302013%')
            ->orwhere('C_NPM', 'LIKE', '1302014%')
            ->orwhere('C_NPM', 'LIKE', '1302015%')
            ->orwhere('C_NPM', 'LIKE', '1302016%')
            ->get();

        foreach ($data as $value) {
            $datapost['name'] = $value->C_NPM;
            $datapost['email'] = $value->C_NPM;
            $datapost['password'] = '$2y$10$r.fqTwSMxeulBuEVYVGjP.onKuLoSVVPBN.ZSyV4ext75kSp8RE0S';
            $datapost['remeber_password'] = 'MEDGZU9Xzuq84ejg5awqIJxFYbaJ9YxFXJ05fx9MeZdAyjcHA94rIS19wcOF';
            $datapost['level'] = 8;
            users::create($datapost);
        }

        return $data;
    }

    public function make_user($id)
    {
        $cek = DB::table('users')
            ->select('*')
            ->where('name', $id)
            ->get();
        if ($cek->isEmpty()) {
            $datapost['name'] = $id;
            $datapost['email'] = $id;
            $datapost['password'] = '$2y$10$r.fqTwSMxeulBuEVYVGjP.onKuLoSVVPBN.ZSyV4ext75kSp8RE0S';
            $datapost['remeber_password'] = 'MEDGZU9Xzuq84ejg5awqIJxFYbaJ9YxFXJ05fx9MeZdAyjcHA94rIS19wcOF';
            $datapost['level'] = 8;
            users::create($datapost);
        }
        //        $data = DB::table('t_mst_mahasiswa')
        //                  ->select('*')
        //                  ->get();
        return redirect('/');
    }

    public function reset_user($id)
    {
        $cek = DB::table('users')
            ->select('*')
            ->where('name', $id)
            ->get();
        if ($cek->isNotEmpty()) {
            DB::table('users')
                ->where('name', $id)
                ->update(['password' => '$2y$10$r.fqTwSMxeulBuEVYVGjP.onKuLoSVVPBN.ZSyV4ext75kSp8RE0S']);
        }

        //        $data = DB::table('t_mst_mahasiswa')
        //                  ->select('*')
        //                  ->get();
        return redirect('/');
    }

    public function make_userx($id)
    {
        $cek = DB::table('users')
            ->select('*')
            ->where('name', $id)
            ->get();
        if ($cek->isEmpty()) {
            $datapost['name'] = $id;
            $datapost['email'] = $id;
            $datapost['password'] = '$2y$10$hfjF7eEk1buEJjOBGP1ununuy19tXPnJjJFvvNrq8cRH1rlKEfNhC';
            //            $datapost['password'] =  Hash::make("dosenfikom");
            $datapost['remeber_password'] = 'MEDGZU9Xzuq84ejg5awqIJxFYbaJ9YxFXJ05fx9MeZdAyjcHA94rIS19wcOF';
            $datapost['level'] = 7;
            users::create($datapost);
        }
        //        $data = DB::table('t_mst_dosen')
        //                  ->select('*')
        //                  ->get();
        return redirect()->back();
    }

    public function reset_userx($id)
    {
        $cek = DB::table('users')
            ->select('*')
            ->where('name', $id)
            ->get();
        if ($cek->isNotEmpty()) {
            DB::table('users')
                ->where('name', $id)
                ->update(['password' => '$2y$10$hfjF7eEk1buEJjOBGP1ununuy19tXPnJjJFvvNrq8cRH1rlKEfNhC']);
            //              ->update(['password' => Hash::make("dosenfikom")]);
        }

        //        $data = DB::table('t_mst_mahasiswa')
        //                  ->select('*')
        //                  ->get();
        return redirect()->back();
    }


    public function surat_pengusulanujianta()
    {
        return view('tugasakhir.prodi.surat_usulantimujian');
    }

    public function topik()
    {
        if (Auth::user()->name == 'proditi') {
            $data_pengusul = DB::table('trt_topik')
                ->join('t_mst_mahasiswa', 'trt_topik.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
                ->select('t_mst_mahasiswa.C_NPM', 't_mst_mahasiswa.C_NPM', 't_mst_mahasiswa.NAMA_MAHASISWA')
                ->where('trt_topik.status', 0)
                ->where('t_mst_mahasiswa.C_NPM', 'LIKE', '130%')
                ->distinct()
                ->get();
            $data_riwayat_usulan = DB::table('trt_topik')
                ->join('t_mst_mahasiswa', 'trt_topik.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
                ->select('t_mst_mahasiswa.C_NPM', 't_mst_mahasiswa.NAMA_MAHASISWA', 'trt_topik.topik', 'trt_topik.kerangka', 'trt_topik.status')
                ->where('t_mst_mahasiswa.C_NPM', 'LIKE', '130%')
                ->get();
        } else {
            $data_pengusul = DB::table('trt_topik')
                ->join('t_mst_mahasiswa', 'trt_topik.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
                ->select('t_mst_mahasiswa.C_NPM', 't_mst_mahasiswa.C_NPM', 't_mst_mahasiswa.NAMA_MAHASISWA')
                ->where('trt_topik.status', 0)
                ->where('t_mst_mahasiswa.C_NPM', 'LIKE', '131%')
                ->distinct()
                ->get();
            $data_riwayat_usulan = DB::table('trt_topik')
                ->join('t_mst_mahasiswa', 'trt_topik.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
                ->select('t_mst_mahasiswa.C_NPM', 't_mst_mahasiswa.NAMA_MAHASISWA', 'trt_topik.topik', 'trt_topik.kerangka', 'trt_topik.status')
                ->where('t_mst_mahasiswa.C_NPM', 'LIKE', '131%')
                ->get();
        }
        return view('tugasakhir.prodi.topik', compact('data_riwayat_usulan', 'data_pengusul'));
    }

    public function topikpost(Request $request)
    {
        $datapost = $request->all();

        DB::table('trt_topik')
            ->where('topik_id', $datapost['topik_id'])
            ->update(['status' => '1']);

        DB::table('trt_topik')
            ->where('C_NPM', $datapost['C_NPM'])
            ->where('status', 0)
            ->update(['status' => '2']);
        return redirect::to('prodi/topik');
    }

    public function detail_topikusulan($id)
    {
        $data = DB::table('t_mst_mahasiswa')
            ->select('*')
            ->where('C_NPM', $id)
            ->first();
        $data_usulan = DB::table('trt_topik')
            ->where('trt_topik.C_NPM', $id)
            ->join('t_mst_mahasiswa', 'trt_topik.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->select('*')
            ->get();
        return view('tugasakhir.prodi.detail_topikusulan', compact('data', 'data_usulan'));
    }

    public function usulan_pembimbing()
    {
        if (Auth::user()->name == 'proditi') {
            $data = DB::table('trt_topik')
                ->join('t_mst_mahasiswa', 'trt_topik.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
                ->select('t_mst_mahasiswa.*', 'trt_topik.*')
                ->where('trt_topik.status', 1)
                ->where('t_mst_mahasiswa.C_NPM', 'LIKE', '130%')
                ->whereNotIn(
                    't_mst_mahasiswa.C_NPM',
                    function ($q) {
                        $q
                            ->select('C_NPM')
                            ->from('trt_bimbingan');
                    }
                )
                ->get();
        } else {
            $data = DB::table('trt_topik')
                ->join('t_mst_mahasiswa', 'trt_topik.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
                ->select('t_mst_mahasiswa.*', 'trt_topik.*')
                ->where('trt_topik.status', 1)
                ->where('t_mst_mahasiswa.C_NPM', 'LIKE', '131%')
                ->whereNotIn(
                    't_mst_mahasiswa.C_NPM',
                    function ($q) {
                        $q
                            ->select('C_NPM')
                            ->from('trt_bimbingan');
                    }
                )
                ->get();
        }
        return view('tugasakhir.prodi.usulan_pembimbing', compact('data'));
    }



    public function usulan_pembimbingpostadd(Request $request)
    {
        $datapost = $request->all();
        $datapost['status_I'] = '0';
        $datapost['status_II'] = '0';
        $datapost['status_bimbingan'] = '0';
        $datapost['status_sk'] = '0';
        $datapost['user_id'] = '1';
        trt_bimbingan::updateOrCreate([
            "C_NPM" => $request->C_NPM,
        ], $datapost);
        mst_tmp_usulan::where("C_NPM", $request->C_NPM)->delete();

        return redirect::to('prodi/usulan_pembimbing');
    }

    public function set_pembimbing_sementara($nim, $dosen1, $dosen2)
    {
        try {
            $data_sekarang = mst_tmp_usulan::where("C_NPM", $nim)->get();

            // check null $data_sekarang for create new data in table mst_tmp_usulan
            if ($data_sekarang == '[]') {
                $data_sekarang = new mst_tmp_usulan;
                $data_sekarang->C_NPM = $nim;
                $data_sekarang->pembimbing_I_id = $dosen1;
                $data_sekarang->pembimbing_II_id = $dosen2;
                $data_sekarang->pembimbing_I_status = '2';
                $data_sekarang->pembimbing_II_status = '2';
                $data_sekarang->save();
            } else {
                $status_dosen_1 = $data_sekarang[0]->pembimbing_I_status;
                $status_dosen_2 = $data_sekarang[0]->pembimbing_II_status;

                if ($data_sekarang[0]->pembimbing_I_id == $dosen1) {
                    $status_dosen_2 = '2';
                }

                if ($data_sekarang[0]->pembimbing_II_id == $dosen2) {
                    $status_dosen_1 = '2';
                }

                if ($data_sekarang[0]->pembimbing_II_id != $dosen2 && $data_sekarang[0]->pembimbing_I_id != $dosen1) {
                    $status_dosen_1 = '2';
                    $status_dosen_2 = '2';
                }
                mst_tmp_usulan::where(
                    [
                        "C_NPM" => $nim,
                    ]
                )->update(
                    [
                        "pembimbing_I_id" => $dosen1,
                        "pembimbing_II_id" => $dosen2,
                        "pembimbing_I_status" => $status_dosen_1,
                        "pembimbing_II_status" => $status_dosen_2,
                    ]
                );
            }
            return response()->json("berhasil");
        } catch (Exception $exception) {
            return response()->json('gagal');
        }
    }

    public function set_pembimbing($id, $status)
    {
        $data_mahasiswa = DB::table('t_mst_mahasiswa')
            ->select('*')
            ->where('C_NPM', $id)
            ->first();
        $data_topik = DB::table('trt_topik')
            ->select('*')
            ->where('C_NPM', $id)
            ->where('status', 1)
            ->first();

        $data = DB::table('t_mst_dosen')
            ->leftJoin("trt_level_pembimbing", "trt_level_pembimbing.C_KODE_DOSEN", "=", "t_mst_dosen.C_KODE_DOSEN")
            ->select('t_mst_dosen.*', 'trt_level_pembimbing.level')
            ->get();


        if ($status == 1) {
            $cek = DB::table('mst_tmp_usulan')
                ->select('*')
                ->where('C_NPM', $id)
                ->get();
        } else if ($status == 2) {
            $cek = DB::table('trt_bimbingan')
                ->select('*')
                ->where('C_NPM', $id)
                ->get();
        }



        return view('tugasakhir.prodi.set_pembimbing', compact('data', 'data_mahasiswa', 'data_topik', 'cek'));
    }

    public function set_penguji($pendaftaran_id, $nim, $tipe_ujian)
    {
        $info = t_mst_mahasiswa::join("trt_bimbingan", "trt_bimbingan.C_NPM", "=", "t_mst_mahasiswa.C_NPM")
            ->join('trt_penguji', 'trt_penguji.C_NPM', '=', 'trt_bimbingan.C_NPM')
            ->where("t_mst_mahasiswa.C_NPM", $nim)
            ->where('trt_penguji.tipe_ujian', $tipe_ujian)
            ->first();
        $dosen = Dosen::whereNotIn("C_KODE_DOSEN", [$info->pembimbing_I_id, $info->pembimbing_II_id])->get();
        return view('tugasakhir.prodi.set_penguji', compact('dosen', "info", "pendaftaran_id"));
    }

    public function set_pengujipost($pendaftaran_id, Request $request)
    {
        $mst_pendaftaran = mst_pendaftaran::where("pendaftaran_id", $pendaftaran_id)->first();
        $request->merge(["tipe_ujian" => $mst_pendaftaran->tipe_ujian]);
        $trtpenguji = TrtPenguji::where([
            "C_NPM" => $request->C_NPM,
            "tipe_ujian" => $request->tipe_ujian
        ])->count();
        if (empty($trtpenguji)) :
            TrtPenguji::create($request->all());
        elseif (!empty($trtpenguji)) :
            TrtPenguji::where([
                "C_NPM" => $request->C_NPM,
                "tipe_ujian" => $request->tipe_ujian
            ])->update($request->except(["C_NPM", "tipe_ujian", "_token"]));
        endif;
        return redirect()->to("/prodi/daftar_peserta/$pendaftaran_id");
    }

    public function sk_pembimbing()
    {
        if (Auth::user()->name == 'proditi') {
            $data = DB::table('t_mst_mahasiswa')
                ->join('trt_bimbingan', 'trt_bimbingan.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
                ->join('t_mst_dosen', 'C_KODE_DOSEN', '=', 'trt_bimbingan.pembimbing_I_id')
                ->select('t_mst_mahasiswa.NAMA_MAHASISWA', 't_mst_dosen.NAMA_DOSEN')
                ->where('t_mst_mahasiswa.C_NPM', 'LIKE', '130%')
                ->get();

            $penetapan_pengusulan = DB::table('trt_bimbingan')
                ->join('users', 'trt_bimbingan.C_NPM', '=', 'users.name')
                ->select('*')
                ->where('status_sk', '<>', 1)
                ->where('trt_bimbingan.C_NPM', 'LIKE', '130%')
                ->get();

            $riwayat_usulan = DB::table('trt_sk')
                ->select('nomor', 'tgl_surat')
                ->distinct('nomor')
                ->get();
        } else {
            $data = DB::table('t_mst_mahasiswa')
                ->join('trt_bimbingan', 'trt_bimbingan.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
                ->join('t_mst_dosen', 'C_KODE_DOSEN', '=', 'trt_bimbingan.pembimbing_I_id')
                ->select('t_mst_mahasiswa.NAMA_MAHASISWA', 't_mst_dosen.NAMA_DOSEN')
                ->where('t_mst_mahasiswa.C_NPM', 'LIKE', '131%')
                ->get();

            $penetapan_pengusulan = DB::table('trt_bimbingan')
                ->join('users', 'trt_bimbingan.C_NPM', '=', 'users.name')
                ->select('*')
                ->where('status_sk', '<>', 1)
                ->where('trt_bimbingan.C_NPM', 'LIKE', '131%')
                ->get();

            $riwayat_usulan = DB::table('trt_sk')
                ->select('nomor', 'tgl_surat')
                ->distinct('nomor')
                ->get();
        }
        return view('tugasakhir.prodi.sk_pembimbing', compact('riwayat_usulan', 'penetapan_pengusulan', 'data'));
    }

    public function sk_pengusulanpost(Request $request)
    {
        $datapost = $request->all();
        if (isset($datapost["data"])) {
            $data = $datapost['data'];

            $datax = DB::table('trt_bimbingan')
                ->join('t_mst_mahasiswa', 'trt_bimbingan.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
                ->select('*')
                ->whereIn('trt_bimbingan.C_NPM', $data)
                ->get();

            return view('tugasakhir.prodi.sk_pengusulan', compact('datax', 'data'));
        }
        return redirect()->back();
    }

    public function sk_pengusulan_tim_ujian_tapost(Request $request)
    {
        $datapost = $request->all();
        if (isset($datapost["data"])) {
            $data = $datapost['data'];

            $datax = DB::table('mst_pendaftaran')
                ->select('*')
                ->whereIn('mst_pendaftaran.pendaftaran_id', $data)
                ->get();

            return view('tugasakhir.prodi.sk_pengusulan_tim_ujian_ta', compact('datax', 'data'));
        }
        return redirect()->back();
    }


    public function get_surat_pengusulan($nomor)
    {
        $datax = DB::table("trt_sk")
            ->select("*")
            ->join('trt_bimbingan', 'trt_bimbingan.bimbingan_id', '=', 'trt_sk.bimbingan_id')
            ->join('t_mst_mahasiswa', 'trt_bimbingan.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->where('trt_sk.nomor', '=', str_replace("$", "/", $nomor))
            ->get();

        $perihal = $datax[0]->perihal;
        $tgl = $datax[0]->tgl_surat;
        $nomor = $datax[0]->nomor;


        return view('tugasakhir.prodi.suratpengusulan', compact('nomor', 'perihal', 'tgl', 'datax'));
    }

    public function surat_pengusulan(Request $request)
    {
        $datapost = $request->all();
        $nomor = $datapost['nomor'];
        $perihal = $datapost['perihal'];
        $tgl = $datapost['tgl'];
        $tgl = substr($tgl, 6, 4) . "-" . substr($tgl, 3, 2) . "-" . substr($tgl, 0, 2);

        $data = $datapost['data'];
        $datax = DB::table('trt_bimbingan')
            ->join('t_mst_mahasiswa', 'trt_bimbingan.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->select('*')
            ->whereIn('trt_bimbingan.C_NPM', $data)
            ->get();
        $a = 0;
        foreach ($datax as $key => $value) {
            $simpan['bimbingan_id'] = $datax[$a]->bimbingan_id;
            $simpan['tipe'] = 1;
            $simpan['nomor'] = $nomor;
            $simpan['perihal'] = $perihal;
            $simpan['tgl_surat'] = $tgl;
            $simpan['user_id'] = 0;
            trt_sk::create($simpan);

            DB::table('trt_bimbingan')
                ->where('bimbingan_id', $datax[$a]->bimbingan_id)
                ->update(['status_sk' => '1']);
            $a++;
        }

        $tgl = helper::tgl_indo_lengkap($tgl);

        return view('tugasakhir.prodi.suratpengusulan', compact('nomor', 'perihal', 'tgl', 'datax'));
    }

    public function surat_pengusulan_ujian_ta(Request $request)
    {
        $datapost = $request->all();
        $nomor = $datapost['nomor'];
        $perihal = $datapost['perihal'];
        $tgl = $datapost['tgl'];
        $tgl = substr($tgl, 6, 4) . "-" . substr($tgl, 3, 2) . "-" . substr($tgl, 0, 2);
        $data = $datapost['data'];
        $datax = DB::table('mst_pendaftaran')
            ->select('*')
            ->whereIn('mst_pendaftaran.pendaftaran_id', $data)
            ->get();
        $a = 0;
        foreach ($datax as $key => $value) {
            $simpan['pendaftaran_id'] = $datax[$a]->pendaftaran_id;
            $simpan['nomor'] = $nomor;
            $simpan['perihal'] = $perihal;
            $simpan['tgl_surat'] = $tgl;
            trt_sk_ujian_ta::create($simpan);

            DB::table('mst_pendaftaran')
                ->where('pendaftaran_id', $datax[$a]->pendaftaran_id)
                ->update(['status_sk' => '1']);
            $a++;
        }

        $tgl_ujian = helper::tgl_indo_lengkap($tgl);

        return view('tugasakhir.prodi.surat_usulantimujian', compact('nomor', 'perihal', 'tgl', 'datax', 'tgl_ujian'));
    }

    public function cetakskpenguji($pendaftaran_id, $nim)
    {
        try {
            $trtjadwalujian = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where("trt_jadwal_ujian.pendaftaran_id", $pendaftaran_id)->first();
            $trtjadwalujianpermhs = TrtJadwalUjianPerMhs::join("mst_ruangan", "mst_ruangan.id", "trt_jadwal_ujian_per_mhs.ruangan")
                ->where([
                    "C_NPM" => $nim,
                    "jadwal_ujian" => $trtjadwalujian->id
                ])->first();
            $ruangan = $trtjadwalujianpermhs->nama_ruangan;
            $jam_ujian = $trtjadwalujianpermhs->jam_ujian;
            $tgl_ujian = Carbon::parse($trtjadwalujian->tgl_ujian)->formatLocalized("%A, %d %B %Y");
            $tanggal = Carbon::parse($trtjadwalujian->tgl_ujian)->formatLocalized("%d");
            $bulan = Carbon::parse($trtjadwalujian->tgl_ujian)->formatLocalized("%m");
            $tahun = Carbon::parse($trtjadwalujian->tgl_ujian)->formatLocalized("%Y");
            $penguji = TrtPenguji::where([
                "C_NPM" => $nim,
                "tipe_ujian" => $trtjadwalujian->tipe_ujian
            ])->first();
            $bimbingan = trt_bimbingan::where("C_NPM", $nim)->first();
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
                    $tipe_ujian = "Meja";
                    $count_jam_ujian = strlen($jam_ujian);
                    if ($count_jam_ujian == 5) {
                        $waktu = $jam_ujian . "-" . sprintf('%02d', substr($jam_ujian, 0, 2) + 2) . ":30";
                    } else {
                        $waktu = $jam_ujian;
                    }
                    break;
            }
            $tgl_sekarang = helper::tgl_indo_lengkap(date('Y-m-d'));

            return view('tugasakhir.prodi.cetakskpenguji', compact("tanggal", "bulan", "tahun", "nim", "penguji", "bimbingan", "tipe_ujian", "tgl_ujian", "waktu", "ruangan", 'tgl_sekarang'));
        } catch (Exception $e) {
            return redirect::to('prodi/sk_ujian');
        }
    }


    public function surat_pengusulanold(Request $request)
    {
        $datapost = $request->all();

        $datask = DB::table('trt_sk')
            ->select('*')
            ->where('nomor', $datapost['nomor'])
            ->get();

        foreach ($datask as $key => $value) {
            $tes[++$key] = $value->bimbingan_id;
        }


        $datax = DB::table('trt_bimbingan')
            ->select('*')
            ->whereIn('trt_bimbingan.bimbingan_id', $tes)
            ->get();

        return view('tugasakhir.prodi.suratpengusulanold', compact('datask', 'datax'));
    }


    public function peserta_proposal()
    {
        if (Auth::user()->name == 'proditi') {
            $pendaftaran = mst_pendaftaran::join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "mst_pendaftaran.pendaftaran_id")
                ->where('tipe_ujian', 0)
                ->where('mst_pendaftaran.status_prodi', 1)
                ->orwhere('tipe_ujian', 3)
                ->orderBy('mst_pendaftaran.created_at', 'desc')
                ->get();
        } else {
            $pendaftaran = mst_pendaftaran::join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "mst_pendaftaran.pendaftaran_id")
                ->where('tipe_ujian', 0)
                ->where('mst_pendaftaran.status_prodi', 2)
                ->orwhere('tipe_ujian', 3)
                ->orderBy('mst_pendaftaran.created_at', 'desc')
                ->get();
        }
        return view('tugasakhir.prodi.peserta_proposal', compact('pendaftaran'));
    }

    public function peserta_seminarhasil()
    {
        $pendaftaran = mst_pendaftaran::join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "mst_pendaftaran.pendaftaran_id")
            ->where('tipe_ujian', 1)
            ->orwhere('tipe_ujian', 3)
            ->get();
        return view('tugasakhir.prodi.peserta_seminarhasil', compact('pendaftaran'));
    }

    public function peserta_ujianmeja()
    {
        if (Auth::user()->name == 'proditi') {
            $pendaftaran = mst_pendaftaran::join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "mst_pendaftaran.pendaftaran_id")
                ->where('tipe_ujian', 2)
                ->where('mst_pendaftaran.status_prodi', 1)
                ->orwhere('tipe_ujian', 3)
                ->orderBy('mst_pendaftaran.created_at', 'desc')
                ->get();
        } else {
            $pendaftaran = mst_pendaftaran::join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "mst_pendaftaran.pendaftaran_id")
                ->where('tipe_ujian', 2)
                ->where('mst_pendaftaran.status_prodi', 2)
                ->orwhere('tipe_ujian', 3)
                ->orderBy('mst_pendaftaran.created_at', 'desc')
                ->get();
        }
        return view('tugasakhir.prodi.peserta_ujianmeja', compact('pendaftaran'));
    }

    public function daftar_peserta($id)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $id)->first();
        $data = DB::select("SELECT * FROM mst_pendaftaran,trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_reg.pendaftaran_id = ? AND trt_reg.status = ?", [$id, $info->tipe_ujian]);


        return view('tugasakhir.prodi.daftar_peserta', compact("data", "info"));
    }

    public function temp_daftar_peserta($id)
    {
        $info = DB::select("SELECT * FROM mst_pendaftaran WHERE mst_pendaftaran.pendaftaran_id = ?", [$id]);

        $data = DB::select("SELECT * FROM mst_pendaftaran,trt_reg, trt_bimbingan, t_mst_mahasiswa WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_reg.pendaftaran_id = ? AND trt_reg.status = ?", [$id, $info[0]->tipe_ujian]);



        return view('tugasakhir.prodi.temp_daftar_peserta', compact("data", "info"));
    }

    public function syarat_ujian()
    {
        $data0 = DB::table('mst_syarat_ujian')
            ->select('*')
            ->where('tipe_ujian', 0)
            ->get();
        $data1 = DB::table('mst_syarat_ujian')
            ->select('*')
            ->where('tipe_ujian', 1)
            ->get();
        $data2 = DB::table('mst_syarat_ujian')
            ->select('*')
            ->where('tipe_ujian', 2)
            ->get();
        return view('tugasakhir.prodi.syarat_ujian', compact('data0', 'data1', 'data2'));
    }

    public function jadwal()
    {
        if (Auth::user()->name == 'proditi') {
            $pendaftaran = Collection::make(mst_pendaftaran::get())
                ->where('status_ujian', 0)
                ->where('status_prodi', 1)
                ->unique("nama_periode")
                ->sortByDesc('created_at');
            $jadwalujian = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where('mst_pendaftaran.status_prodi', 1)
                ->orderBy('mst_pendaftaran.created_at', 'desc')
                ->get();
        } else {
            $pendaftaran = Collection::make(mst_pendaftaran::get())
                ->where('status_ujian', 0)
                ->where('status_prodi', 2)
                ->unique("nama_periode")
                ->sortByDesc('created_at');
            $jadwalujian = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where('mst_pendaftaran.status_prodi', 2)
                ->orderBy('mst_pendaftaran.created_at', 'desc')
                ->get();
        }
        return view('tugasakhir.prodi.jadwal', compact('pendaftaran', "jadwalujian"));
    }

    public function scope_ta()
    {
        $data = DB::table('mst_bidangilmu')
            ->select('*')
            ->get();
        return view('tugasakhir.prodi.scope_ta', compact('data'));
    }

    public function master_jenis_tugas_akhir()
    {
        $data = DB::table('mst_jenis_tugas_akhir')
            ->select('*')
            ->orderBy('kode_jenis_tugas_akhir')
            ->get();

        return view('tugasakhir.prodi.master_jenis_tugas_akhir', compact('data'));
    }

    public function jadwalpostadd(Request $request)
    {
        $mst = mst_pendaftaran::where("nama_periode", $request->nama_periode)->first();
        if (empty($mst)) {
            if ($request->tipe_ujian == "3") {
                for ($i = 0; $i < 3; $i++) {
                    if (Auth::user()->name == "proditi") {
                        $request->merge([
                            "tipe_ujian" => $i,
                            "user_id" => "00",
                            "jml_peserta" => 0,
                            "status_prodi" => 1
                        ]);
                    } else {
                        $request->merge([
                            "tipe_ujian" => $i,
                            "user_id" => "00",
                            "jml_peserta" => 0,
                            "status_prodi" => 2
                        ]);
                    }

                    mst_pendaftaran::create($request->all());
                }
            } else {
                if (Auth::user()->name == "proditi") {
                    $request->merge([
                        "user_id" => "00",
                        "jml_peserta" => 0,
                        'status_prodi' => 1
                    ]);
                } else {
                    $request->merge([
                        "user_id" => "00",
                        "jml_peserta" => 0,
                        'status_prodi' => 2
                    ]);
                }
                mst_pendaftaran::create($request->all());
            }
        }
        return redirect::to('prodi/jadwal');
    }

    public function syaratadd(Request $request)
    {
        $datapost = $request->all();
        mst_syarat_ujian::create($datapost);
        return redirect::to('prodi/syarat_ujian');
    }

    public function scope_add(Request $request)
    {
        $datapost = $request->all();
        mst_bidangilmu::create($datapost);
        return redirect::to('prodi/scope_ta');
    }

    public function master_jenis_tugas_akhir_store(Request $request)
    {
        $this->validate($request, [
            'kode_jenis_tugas_akhir' => 'required|max:50|unique:mst_jenis_tugas_akhir,kode_jenis_tugas_akhir',
            'deskripsi' => 'required|max:255',
        ], [
            'kode_jenis_tugas_akhir.required' => 'Kode jenis tugas akhir wajib diisi.',
            'kode_jenis_tugas_akhir.unique' => 'Kode jenis tugas akhir sudah digunakan.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
        ]);

        try {
            mst_jenis_tugas_akhir::create([
                'kode_jenis_tugas_akhir' => trim($request->kode_jenis_tugas_akhir),
                'deskripsi' => trim($request->deskripsi),
            ]);

            return redirect::to('prodi/master/jenis_tugas_akhir')->with('success', 'Data berhasil disimpan.');
        } catch (Exception $e) {
            return redirect::to('prodi/master/jenis_tugas_akhir')->with('danger', 'Data gagal disimpan.');
        }
    }

    public function scope_del($id)
    {
        DB::table('mst_bidangilmu')
            ->where('bidangilmu_id', $id)
            ->delete();
        return redirect::to('prodi/scope_ta');
    }

    public function master_jenis_tugas_akhir_delete($id)
    {
        try {
            DB::table('mst_jenis_tugas_akhir')
                ->where('jenis_tugas_akhir_id', $id)
                ->delete();

            return redirect::to('prodi/master/jenis_tugas_akhir')->with('success', 'Data berhasil dihapus.');
        } catch (Exception $e) {
            return redirect::to('prodi/master/jenis_tugas_akhir')->with('danger', 'Data gagal dihapus.');
        }
    }

    public function syaratdel($id)
    {
        DB::table('mst_syarat_ujian')
            ->where('syarat_ujian_id', $id)
            ->delete();
        return redirect::to('prodi/syarat_ujian');
    }

    public function pendaftarandel($id)
    {
        $namaperiode = mst_pendaftaran::find($id)->nama_periode;
        $countname = mst_pendaftaran::where('nama_periode', $namaperiode)->count();
        if ($countname == 3) {
            mst_pendaftaran::where('nama_periode', $namaperiode)->delete();
        } else {
            mst_pendaftaran::where('pendaftaran_id', $id)->delete();
        }
        return redirect::to('prodi/jadwal');
    }


    public function sk_ujian()
    {
        if (Auth::user()->name == "proditi" || Auth::user()->name == "akademikproditi") {
            $pendaftaran = mst_pendaftaran::where('status_prodi', 1)
                ->get();
            $jadwalujian = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where('mst_pendaftaran.tipe_ujian', '=', 0)
                ->where('mst_pendaftaran.status_prodi', 1)
                ->orderBy('mst_pendaftaran.created_at', 'desc')
                ->get();
        } else {
            $pendaftaran = mst_pendaftaran::where('status_prodi', 2)
                ->get();
            $jadwalujian = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where('mst_pendaftaran.tipe_ujian', '=', 0)
                ->where('mst_pendaftaran.status_prodi', 2)
                ->orderBy('mst_pendaftaran.created_at', 'desc')
                ->get();
        }
        return view('tugasakhir.prodi.sk_ujian', compact('pendaftaran', "jadwalujian"));
    }

    public function sk_ujian_ta()
    {
        if (Auth::user()->name == 'proditi') {
            $pendaftaran = mst_pendaftaran::where('status_prodi', 1)
                ->get();
            $jadwalujian = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where('tipe_ujian', '=', 2)
                ->where('status_sk', '=', 0)
                ->where('mst_pendaftaran.status_prodi', '=', 1)
                ->orderBy('mst_pendaftaran.created_at', 'desc')
                ->get();
        } else {
            $pendaftaran = mst_pendaftaran::where('status_prodi', 2)
                ->get();
            $jadwalujian = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where('tipe_ujian', '=', 2)
                ->where('status_sk', '=', 0)
                ->where('mst_pendaftaran.status_prodi', '=', 2)
                ->orderBy('mst_pendaftaran.created_at', 'desc')
                ->get();
        }
        return view('tugasakhir.prodi.sk_ujian_ta', compact('pendaftaran', "jadwalujian"));
    }

    public function detail_skujian($id)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $id)->first();
        $data = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
            ->join("t_mst_mahasiswa", "t_mst_mahasiswa.C_NPM", "=", "trt_reg.C_NPM")
            ->join("trt_penguji", "trt_penguji.C_NPM", "=", "t_mst_mahasiswa.C_NPM")
            ->where([
                "trt_reg.pendaftaran_id" => $id,
                "trt_penguji.tipe_ujian" => $info->tipe_ujian
            ])->get();
        return view('tugasakhir.prodi.detail_skujian', compact("info", "data"));
    }

    public function pengumuman()
    {
        $data = mst_pengumuman::orderBy('created_at', 'desc')->get();
        return view('tugasakhir.prodi.pengumuman', compact('data'));
    }



    public function pengumumanpost(Request $request)
    {
        $datapost = $request->all();
        try {
            $gambar = isset($datapost['gambar']) ? $datapost['gambar'] : '';
            $datapost['gambar'] = Helper::uploadImage($gambar, 'public/gambar/', '');
            $datapost['last_update'] = Helper::tgl($datapost['last_update']);
            $datapost['user_id'] = '1';
            mst_pengumuman::create($datapost);
            return redirect::to('prodi/pengumuman/')->with('status', 'success');
        } catch (Exception $exception) {
            return redirect::to('prodi/pengumuman/')->with('status', 'error');
        }
    }

    public function edit_pengumuman($id)
    {
        $data = DB::table("mst_pengumuman")
            ->where('pengumuman_id', $id)
            ->first();
        return view('tugasakhir.prodi.edit_pengumuman', compact('data'));
    }

    public function edit_pengumuman_post(Request $request)
    {
        try {
            $datapost = $request->all();
            $gambar = isset($datapost['gambar']) ? $datapost['gambar'] : '';
            $datapost['gambar'] = Helper::uploadImage($gambar, 'public/gambar/', '');
            $datapost['last_update'] = Helper::tgl($request->last_update);
            $datapost['user_id'] = '1';

            if ($gambar != '') {
                mst_pengumuman::where('pengumuman_id', $request->id)->update([
                    'judul' => $request->judul,
                    'gambar' => $datapost['gambar'],
                    'last_update' => $datapost['last_update'],
                    'isi' => $request->isi,
                ]);
            } else {
                mst_pengumuman::where('pengumuman_id', $request->id)->update([
                    'judul' => $request->judul,
                    'last_update' => $datapost['last_update'],
                    'isi' => $request->isi,
                ]);
            }
            return redirect::to('prodi/pengumuman/')->with('status', 'success');
        } catch (Exception $exception) {
            return $exception;
            return redirect::to('prodi/pengumuman/')->with('status', 'error');
        }
    }

    public function pengumumandel($id)
    {
        DB::table('mst_pengumuman')
            ->where('pengumuman_id', $id)
            ->delete();
        return redirect::to('prodi/pengumuman');
    }

    public function setlevelpembimbing($dosen, $level)
    {
        $cek = TrtLevelPembimbing::where("C_KODE_DOSEN", $dosen)->get();
        if ($cek->isNotEmpty()) {
            TrtLevelPembimbing::where("C_KODE_DOSEN", $dosen)->update([
                "level" => $level
            ]);
        } else {
            TrtLevelPembimbing::create([
                "C_KODE_DOSEN" => $dosen,
                "level" => $level
            ]);
        }
        return redirect()->back();
    }

    public function getPembimbingStatus($index, $id, $mahasiswa)
    {
        if ($index == "0") {
            $pembimbing = mst_tmp_usulan::where(["pembimbing_I_id" => $id, "C_NPM" => $mahasiswa])->firstOrFail();
            return response()->json($pembimbing->pembimbing_I_status);
        } elseif ($index == "1") {
            $pembimbing = mst_tmp_usulan::where(["pembimbing_II_id" => $id, "C_NPM" => $mahasiswa])->firstOrFail();
            return response()->json($pembimbing->pembimbing_II_status);
        }
        return abort(404);
    }

    public function statusBimbinganAll()
    {
        $data = (object) [
            "y" => "",
            "PP" => trt_bimbingan::where("status_bimbingan", 0)->count(),
            "PUM" => trt_bimbingan::where("status_bimbingan", 2)->count(),
            "L" => trt_bimbingan::where("status_bimbingan", 3)->count()
        ];

        return response()->json($data);
    }

    public function statusBimbingan($nim)
    {


        $data = (object) [
            "y" => "",
            "PP" => trt_bimbingan::where("status_bimbingan", 0)->where('C_NPM', 'LIKE', $nim)->count(),
            "PUM" => trt_bimbingan::where("status_bimbingan", 2)->where('C_NPM', 'LIKE', $nim)->count(),
            "L" => trt_bimbingan::where("status_bimbingan", 3)->where('C_NPM', 'LIKE', $nim)->count()
        ];

        return response()->json($data);
    }

    public function report()
    {
        $reportContext = $this->getReportContext();
        $nimLike = $reportContext['nim_like'];
        $statusProdi = $reportContext['status_prodi'];

        $statusCounts = DB::table('trt_bimbingan')
            ->select('status_bimbingan', DB::raw('COUNT(DISTINCT C_NPM) as total'))
            ->where('C_NPM', 'LIKE', $nimLike)
            ->groupBy('status_bimbingan')
            ->pluck('total', 'status_bimbingan')
            ->toArray();

        $statusBimbinganChart = [
            ['label' => 'Persiapan Proposal', 'value' => (int) ($statusCounts[0] ?? 0)],
            ['label' => 'Persiapan Ujian Meja', 'value' => (int) ($statusCounts[2] ?? 0)],
            ['label' => 'Lulus', 'value' => (int) ($statusCounts[3] ?? 0)],
            ['label' => 'Non Aktif', 'value' => (int) ($statusCounts[4] ?? 0)],
        ];

        $topikCounts = DB::table('trt_topik')
            ->join('t_mst_mahasiswa', 't_mst_mahasiswa.C_NPM', '=', 'trt_topik.C_NPM')
            ->where('t_mst_mahasiswa.C_NPM', 'LIKE', $nimLike)
            ->select('trt_topik.status', DB::raw('COUNT(*) as total'))
            ->groupBy('trt_topik.status')
            ->pluck('total', 'trt_topik.status')
            ->toArray();

        $topikStatusChart = [
            ['label' => 'Belum dikonfirmasi', 'value' => (int) ($topikCounts[0] ?? 0)],
            ['label' => 'Diterima', 'value' => (int) ($topikCounts[1] ?? 0)],
            ['label' => 'Ditolak', 'value' => (int) ($topikCounts[2] ?? 0)],
        ];

        $bidangIlmuRaw = DB::table('trt_topik')
            ->join('t_mst_mahasiswa', 't_mst_mahasiswa.C_NPM', '=', 'trt_topik.C_NPM')
            ->where('t_mst_mahasiswa.C_NPM', 'LIKE', $nimLike)
            ->where('trt_topik.status', 1)
            ->select('trt_topik.bidang_ilmu_peminatan', DB::raw('COUNT(*) as total'))
            ->groupBy('trt_topik.bidang_ilmu_peminatan')
            ->get();

        $bidangIlmuChart = $bidangIlmuRaw
            ->groupBy(function ($item) {
                $label = trim((string) $item->bidang_ilmu_peminatan);
                return $label === '' ? 'Belum diisi' : $label;
            })
            ->map(function ($items, $label) {
                return (object) [
                    'y' => $label,
                    'total' => $items->sum('total'),
                ];
            })
            ->sortByDesc('total')
            ->take(10)
            ->values()
            ->map(function ($item) {
                return [
                    'y' => $this->shortenChartLabel($item->y, 28),
                    'total' => (int) $item->total,
                    'full_label' => $item->y,
                ];
            })
            ->values()
            ->all();

        $dosenPembimbingChart = Dosen::select('C_KODE_DOSEN', 'NAMA_DOSEN')
            ->selectRaw(
                "(
                    (SELECT COUNT(*) FROM trt_bimbingan WHERE trt_bimbingan.C_NPM LIKE ? AND trt_bimbingan.pembimbing_I_id = t_mst_dosen.C_KODE_DOSEN AND trt_bimbingan.status_bimbingan IN (0,2))
                    +
                    (SELECT COUNT(*) FROM trt_bimbingan WHERE trt_bimbingan.C_NPM LIKE ? AND trt_bimbingan.pembimbing_II_id = t_mst_dosen.C_KODE_DOSEN AND trt_bimbingan.status_bimbingan IN (0,2))
                ) as aktif",
                [$nimLike, $nimLike]
            )
            ->selectRaw(
                "(
                    (SELECT COUNT(*) FROM trt_bimbingan WHERE trt_bimbingan.C_NPM LIKE ? AND trt_bimbingan.pembimbing_I_id = t_mst_dosen.C_KODE_DOSEN AND trt_bimbingan.status_bimbingan = 3)
                    +
                    (SELECT COUNT(*) FROM trt_bimbingan WHERE trt_bimbingan.C_NPM LIKE ? AND trt_bimbingan.pembimbing_II_id = t_mst_dosen.C_KODE_DOSEN AND trt_bimbingan.status_bimbingan = 3)
                ) as lulus",
                [$nimLike, $nimLike]
            )
            ->get()
            ->filter(function ($item) {
                return ((int) $item->aktif + (int) $item->lulus) > 0;
            })
            ->sortByDesc(function ($item) {
                return (((int) $item->aktif) * 1000) + (int) $item->lulus;
            })
            ->take(10)
            ->values()
            ->map(function ($item) {
                return [
                    'y' => $this->shortenChartLabel($item->NAMA_DOSEN, 24),
                    'aktif' => (int) $item->aktif,
                    'lulus' => (int) $item->lulus,
                    'full_label' => $item->NAMA_DOSEN,
                ];
            })
            ->all();

        $periodePesertaRaw = DB::table('mst_pendaftaran')
            ->whereIn('tipe_ujian', [0, 2])
            ->when(!is_null($statusProdi), function ($query) use ($statusProdi) {
                $query->where('status_prodi', $statusProdi);
            })
            ->orderBy('created_at', 'asc')
            ->get(['nama_periode', 'tipe_ujian', 'jml_peserta']);

        $periodePesertaMap = [];
        foreach ($periodePesertaRaw as $item) {
            if (!isset($periodePesertaMap[$item->nama_periode])) {
                $periodePesertaMap[$item->nama_periode] = [
                    'y' => $this->shortenChartLabel($item->nama_periode, 22),
                    'proposal' => 0,
                    'ujian_meja' => 0,
                    'full_label' => $item->nama_periode,
                ];
            }

            if ((int) $item->tipe_ujian === 0) {
                $periodePesertaMap[$item->nama_periode]['proposal'] = (int) $item->jml_peserta;
            }

            if ((int) $item->tipe_ujian === 2) {
                $periodePesertaMap[$item->nama_periode]['ujian_meja'] = (int) $item->jml_peserta;
            }
        }

        $periodePesertaChart = array_slice(array_values($periodePesertaMap), -10);

        $nilaiRataRaw = DB::table('trt_hasil')
            ->join('trt_reg', 'trt_reg.reg_id', '=', 'trt_hasil.reg_id')
            ->where('trt_reg.C_NPM', 'LIKE', $nimLike)
            ->whereIn('trt_reg.status', [0, 2])
            ->selectRaw('
                trt_reg.status as status,
                AVG(trt_hasil.nilai_1) as nilai_1,
                AVG(trt_hasil.nilai_2) as nilai_2,
                AVG(trt_hasil.nilai_3) as nilai_3,
                AVG(trt_hasil.nilai_4) as nilai_4,
                AVG(trt_hasil.nilai_5) as nilai_5,
                AVG(trt_hasil.nilai_1 + trt_hasil.nilai_2 + trt_hasil.nilai_3 + trt_hasil.nilai_4 + trt_hasil.nilai_5) as rata_total
            ')
            ->groupBy('trt_reg.status')
            ->get()
            ->keyBy('status');

        $proposalNilai = $nilaiRataRaw->get(0);
        $ujianMejaNilai = $nilaiRataRaw->get(2);

        $nilaiKomponenChart = [
            ['y' => 'Komponen 1', 'proposal' => round((float) ($proposalNilai->nilai_1 ?? 0), 2), 'ujian_meja' => round((float) ($ujianMejaNilai->nilai_1 ?? 0), 2)],
            ['y' => 'Komponen 2', 'proposal' => round((float) ($proposalNilai->nilai_2 ?? 0), 2), 'ujian_meja' => round((float) ($ujianMejaNilai->nilai_2 ?? 0), 2)],
            ['y' => 'Komponen 3', 'proposal' => round((float) ($proposalNilai->nilai_3 ?? 0), 2), 'ujian_meja' => round((float) ($ujianMejaNilai->nilai_3 ?? 0), 2)],
            ['y' => 'Komponen 4', 'proposal' => round((float) ($proposalNilai->nilai_4 ?? 0), 2), 'ujian_meja' => round((float) ($ujianMejaNilai->nilai_4 ?? 0), 2)],
            ['y' => 'Komponen 5', 'proposal' => round((float) ($proposalNilai->nilai_5 ?? 0), 2), 'ujian_meja' => round((float) ($ujianMejaNilai->nilai_5 ?? 0), 2)],
        ];

        $dokumenProposalChart = $this->getSyaratStatusChart($nimLike, 0);
        $dokumenUjianMejaChart = $this->getSyaratStatusChart($nimLike, 2);
        $skPembimbingChart = $this->getSkStatusChart($nimLike, 'mst_sk_pembimbing');
        $skPenugasanChart = $this->getSkStatusChart($nimLike, 'mst_sk_penugasan');

        $summaryCards = [
            ['label' => 'Persiapan Proposal', 'value' => (int) ($statusCounts[0] ?? 0), 'class' => 'success', 'icon' => 'fa-file-text-o'],
            ['label' => 'Persiapan Ujian Meja', 'value' => (int) ($statusCounts[2] ?? 0), 'class' => 'primary', 'icon' => 'fa-graduation-cap'],
            ['label' => 'Lulus', 'value' => (int) ($statusCounts[3] ?? 0), 'class' => 'danger', 'icon' => 'fa-trophy'],
            ['label' => 'Non Aktif', 'value' => (int) ($statusCounts[4] ?? 0), 'class' => 'warning', 'icon' => 'fa-user-times'],
        ];

        $queueCards = [
            [
                'label' => 'Total Mahasiswa TA',
                'value' => DB::table('trt_bimbingan')->where('C_NPM', 'LIKE', $nimLike)->distinct()->count('C_NPM'),
                'icon' => 'fa-users',
            ],
            [
                'label' => 'Topik Menunggu',
                'value' => (int) ($topikCounts[0] ?? 0),
                'icon' => 'fa-files-o',
            ],
            [
                'label' => 'Antrian Proposal',
                'value' => TrtPengajuanDokumen::join('t_mst_mahasiswa', 't_mst_mahasiswa.C_NPM', '=', 'trt_pengajuan_dokumen.C_NPM')
                    ->where('type', 0)
                    ->where('t_mst_mahasiswa.C_NPM', 'LIKE', $nimLike)
                    ->count(),
                'icon' => 'fa-upload',
            ],
            [
                'label' => 'Antrian Ujian Meja',
                'value' => TrtPengajuanDokumen::join('t_mst_mahasiswa', 't_mst_mahasiswa.C_NPM', '=', 'trt_pengajuan_dokumen.C_NPM')
                    ->where('type', 2)
                    ->where('t_mst_mahasiswa.C_NPM', 'LIKE', $nimLike)
                    ->count(),
                'icon' => 'fa-folder-open-o',
            ],
        ];

        return view('tugasakhir.prodi.report', compact(
            'reportContext',
            'summaryCards',
            'queueCards',
            'statusBimbinganChart',
            'topikStatusChart',
            'bidangIlmuChart',
            'dosenPembimbingChart',
            'periodePesertaChart',
            'nilaiKomponenChart',
            'dokumenProposalChart',
            'dokumenUjianMejaChart',
            'skPembimbingChart',
            'skPenugasanChart'
        ));
    }

    protected function getReportContext()
    {
        $username = strtolower(trim((string) auth()->user()->name));

        if ($username === 'proditi') {
            return [
                'nim_like' => '130%',
                'status_prodi' => 1,
                'label' => 'Teknik Informatika',
            ];
        }

        if ($username === 'prodisi') {
            return [
                'nim_like' => '131%',
                'status_prodi' => 2,
                'label' => 'Sistem Informasi',
            ];
        }

        return [
            'nim_like' => '%',
            'status_prodi' => null,
            'label' => Helper::getProgramStudiByAuthUser($username) ?: 'Program Studi',
        ];
    }

    protected function getSyaratStatusChart($nimLike, $tipeUjian)
    {
        $rows = DB::table('trt_syarat_ujian')
            ->join('mst_syarat_ujian', 'mst_syarat_ujian.syarat_ujian_id', '=', 'trt_syarat_ujian.syarat_ujian_id')
            ->join('t_mst_mahasiswa', 't_mst_mahasiswa.C_NPM', '=', 'trt_syarat_ujian.C_NPM')
            ->where('mst_syarat_ujian.tipe_ujian', $tipeUjian)
            ->where('t_mst_mahasiswa.C_NPM', 'LIKE', $nimLike)
            ->select('trt_syarat_ujian.status', DB::raw('COUNT(*) as total'))
            ->groupBy('trt_syarat_ujian.status')
            ->pluck('total', 'trt_syarat_ujian.status')
            ->toArray();

        return [
            ['label' => 'Diterima', 'value' => (int) ($rows[1] ?? 0)],
            ['label' => 'Ditolak', 'value' => (int) ($rows[0] ?? 0)],
            ['label' => 'Menunggu', 'value' => (int) ($rows[2] ?? 0)],
        ];
    }

    protected function getSkStatusChart($nimLike, $table)
    {
        $rows = DB::table($table)
            ->join('trt_bimbingan', 'trt_bimbingan.bimbingan_id', '=', $table . '.bimbingan_id')
            ->where('trt_bimbingan.C_NPM', 'LIKE', $nimLike)
            ->select($table . '.status', DB::raw('COUNT(*) as total'))
            ->groupBy($table . '.status')
            ->pluck('total', $table . '.status')
            ->toArray();

        return [
            ['label' => 'Menunggu WD', 'value' => (int) ($rows[0] ?? 0)],
            ['label' => 'Menunggu Dekan', 'value' => (int) ($rows[1] ?? 0)],
            ['label' => 'Selesai', 'value' => (int) ($rows[2] ?? 0)],
        ];
    }

    protected function shortenChartLabel($text, $limit = 24)
    {
        $text = preg_replace('/\s+/', ' ', trim((string) $text));

        if ($text === '') {
            return 'Tanpa Label';
        }

        if (strlen($text) <= $limit) {
            return $text;
        }

        return substr($text, 0, $limit - 3) . '...';
    }

    public function persyaratan_proposal()
    {
        if (Auth::user()->name == 'proditi') {
            $data = TrtPengajuanDokumen::join("t_mst_mahasiswa", "trt_pengajuan_dokumen.C_NPM", "=", "t_mst_mahasiswa.C_NPM")
                ->where("type", 0)
                ->where('t_mst_mahasiswa.C_NPM', 'LIKE', '130%')
                ->get(["NAMA_MAHASISWA", "t_mst_mahasiswa.C_NPM"]);
        } else {
            $data = TrtPengajuanDokumen::join("t_mst_mahasiswa", "trt_pengajuan_dokumen.C_NPM", "=", "t_mst_mahasiswa.C_NPM")
                ->where("type", 0)
                ->where('t_mst_mahasiswa.C_NPM', 'LIKE', '131%')
                ->get(["NAMA_MAHASISWA", "t_mst_mahasiswa.C_NPM"]);
        }
        return view("tugasakhir.prodi.persyaratan_proposal", compact("data"));
    }

    public function persyaratan_seminarhasil()
    {
        $data = TrtPengajuanDokumen::join("t_mst_mahasiswa", "trt_pengajuan_dokumen.C_NPM", "=", "t_mst_mahasiswa.C_NPM")->where("type", 1)->get(["NAMA_MAHASISWA", "t_mst_mahasiswa.C_NPM"]);
        return view("tugasakhir.prodi.persyaratan_seminarhasil", compact("data"));
    }

    public function persyaratan_ujianmeja()
    {
        if (Auth::user()->name == 'proditi') {
            $data = TrtPengajuanDokumen::join("t_mst_mahasiswa", "trt_pengajuan_dokumen.C_NPM", "=", "t_mst_mahasiswa.C_NPM")
                ->where("type", 2)
                ->where('t_mst_mahasiswa.C_NPM', 'LIKE', '130%')
                ->get(["NAMA_MAHASISWA", "t_mst_mahasiswa.C_NPM"]);
        } else {
            $data = TrtPengajuanDokumen::join("t_mst_mahasiswa", "trt_pengajuan_dokumen.C_NPM", "=", "t_mst_mahasiswa.C_NPM")
                ->where("type", 2)
                ->where('t_mst_mahasiswa.C_NPM', 'LIKE', '131%')
                ->get(["NAMA_MAHASISWA", "t_mst_mahasiswa.C_NPM"]);
        }
        return view("tugasakhir.prodi.persyaratan_ujianmeja", compact("data"));
    }

    public function detail_persyaratan_proposal($id)
    {
        $mhs = t_mst_mahasiswa::where("C_NPM", $id)->first();
        $data = TrtSyaratUjian::join("mst_syarat_ujian", "trt_syarat_ujian.syarat_ujian_id", "=", "mst_syarat_ujian.syarat_ujian_id")->where(["tipe_ujian" => 0, "C_NPM" => $id])->get();
        return view("tugasakhir.prodi.detail_persyaratan_proposal", compact("data", "mhs"));
    }

    public function detail_persyaratan_seminarhasil($id)
    {
        $mhs = t_mst_mahasiswa::where("C_NPM", $id)->first();
        $data = TrtSyaratUjian::join("mst_syarat_ujian", "trt_syarat_ujian.syarat_ujian_id", "=", "mst_syarat_ujian.syarat_ujian_id")->where(["tipe_ujian" => 1, "C_NPM" => $id])->get();
        return view("tugasakhir.prodi.detail_persyaratan_seminarhasil", compact("data", "mhs"));
    }

    public function detail_persyaratan_ujianmeja($id)
    {
        $mhs = t_mst_mahasiswa::where("C_NPM", $id)->first();
        $data = TrtSyaratUjian::join("mst_syarat_ujian", "trt_syarat_ujian.syarat_ujian_id", "=", "mst_syarat_ujian.syarat_ujian_id")->where(["tipe_ujian" => 2, "C_NPM" => $id])->get();
        return view("tugasakhir.prodi.detail_persyaratan_ujianmeja", compact("data", "mhs"));
    }

    public function konfirmasi_persyaratan_ujian($status, $id, $nim)
    {
        TrtSyaratUjian::where([
            "syarat_ujian_id" => $id,
            "C_NPM" => $nim
        ])->update([
            "status" => $status
        ]);
        return redirect()->back();
    }

    public function getJumlahPeserta($pendaftaran_id)
    {
        $data = mst_pendaftaran::where("pendaftaran_id", $pendaftaran_id)->first();
        return response()->json($data->jml_peserta);
    }

    public function getTipeUjian($pendaftaran_id)
    {
        $data = mst_pendaftaran::where("pendaftaran_id", $pendaftaran_id)->first();
        return response()->json($data->tipe_ujian);
    }



    public function jadwalUjianPost(Request $request)
    {
        if (count($request->all()) == 4) {
            $namaperiode = mst_pendaftaran::find($request->pendaftaran_id)->nama_periode;
            $countname = mst_pendaftaran::where("nama_periode", $namaperiode)->count();
            mst_pendaftaran::where([
                "nama_periode" => $namaperiode,
            ])->update([
                'status_ujian' => 1,
            ]);
            $request->merge(["status" => $request->tipe_ujian]);
            if ($countname == 3) {
                $pendaftaran = mst_pendaftaran::where("nama_periode", $namaperiode)->get();
                foreach ($pendaftaran as $p) {
                    $request->merge([
                        "pendaftaran_id" => $p->pendaftaran_id
                    ]);
                    TrtJadwalUjian::create($request->all());
                }
            } else {
                TrtJadwalUjian::create($request->all());
            }
        }


        return redirect()->back();
    }

    public function jadwalUjianDel($id)
    {
        mst_pendaftaran::where('pendaftaran_id', $id)->update(
            [
                "status_ujian" => 0
            ]
        );
        TrtJadwalUjian::where('pendaftaran_id', $id)->delete();
        return redirect()->back();
    }

    public function ubah_periode_pendaftaran(Request $request)
    {

        try {
            for ($i = 0; $i < count($request->C_NPM); $i++) {
                DB::table('trt_reg')->where('C_NPM', $request->C_NPM[$i])->update([
                    "pendaftaran_id" => $request->pindah_periode[$i],
                ]);
            }


            foreach (helper::getPeriodePendaftaranByStatusUjian($request->status_ujian, $request->tipe_ujian) as $item) {
                $data_pendaftar = DB::table('trt_reg')
                    ->select('*')
                    ->where('pendaftaran_id', $item->pendaftaran_id)
                    ->get();

                DB::table('mst_pendaftaran')->where('pendaftaran_id', $item->pendaftaran_id)->update([
                    "jml_peserta" => count($data_pendaftar),
                ]);
            }

            return redirect()->back();
        } catch (Exception $e) {
            return $e;
        }

        return $request;
    }

    public function jadwalPerMhs($tipe_ujian)
    {
        switch ($tipe_ujian):
            case "proposal":
                $type = 0;
                break;
            case "seminarhasil":
                $type = 1;
                break;
            case "ujianmeja":
                $type = 2;
                break;
        endswitch;

        if (Auth::user()->name == 'proditi') {
            $data = mst_pendaftaran::join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "mst_pendaftaran.pendaftaran_id")
                ->where('tipe_ujian', $type)
                ->where('mst_pendaftaran.status_prodi', 1)
                ->orwhere('tipe_ujian', 3)
                ->orderBy('mst_pendaftaran.created_at', 'desc')
                ->get();
        } else {
            $data = mst_pendaftaran::join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "mst_pendaftaran.pendaftaran_id")
                ->where('tipe_ujian', $type)
                ->where('mst_pendaftaran.status_prodi', 2)
                ->orwhere('tipe_ujian', 3)
                ->orderBy('mst_pendaftaran.created_at', 'desc')
                ->get();
        }
        return view('tugasakhir.prodi.jadwalpermhs', compact('data'));
    }

    public function detailJadwalPermhs($pendaftaran_id)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $pendaftaran_id)->first();
        $data = DB::select("SELECT * FROM mst_pendaftaran,trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_reg.pendaftaran_id = ? AND trt_reg.status = ?", [$pendaftaran_id, $info->tipe_ujian]);

        // return $data;

        return view('tugasakhir.prodi.detail_jadwalpermhs', compact("data", "info"));
    }

    public function set_jadwalujianpermhs($pendaftaran_id, $nim)
    {
        $xinfo = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $pendaftaran_id)->first();
        $info = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
            ->join("t_mst_mahasiswa", "t_mst_mahasiswa.C_NPM", "=", "trt_bimbingan.C_NPM")
            ->join("trt_penguji", "trt_penguji.C_NPM", "=", "t_mst_mahasiswa.C_NPM")
            ->where([
                "trt_reg.pendaftaran_id" => $pendaftaran_id,
                "trt_reg.status" => $xinfo->tipe_ujian,
                "trt_penguji.tipe_ujian" => $xinfo->tipe_ujian,
                "t_mst_mahasiswa.C_NPM" => $nim
            ])->first();

        $jadwal = TrtJadwalUjianPerMhs::where([
            "C_NPM" => $nim,
            "jadwal_ujian" => $xinfo->id
        ])->first();


        return view('tugasakhir.prodi.set_jadwalpermhs', compact("info", "pendaftaran_id", "jadwal"));
    }

    public function set_jadwalujianpermhspost($pendaftaran_id, Request $request)
    {
        $trtjadwalujian = TrtJadwalUjian::where("pendaftaran_id", $pendaftaran_id)->first();
        $trtjadwalujianpermhs = TrtJadwalUjianPerMhs::where(["C_NPM" => $request->C_NPM, "jadwal_ujian" => $trtjadwalujian->id])->first();
        $request->merge(["jadwal_ujian" => $trtjadwalujian->id]);
        $request->merge(["jam_ujian" => $request->jam_ujian]);
        if (empty($trtjadwalujianpermhs)) {
            TrtJadwalUjianPerMhs::create($request->all());
        } else {
            TrtJadwalUjianPerMhs::where(["C_NPM" => $request->C_NPM, "jadwal_ujian" => $trtjadwalujian->id])
                ->update($request->except([
                    "C_NPM",
                    "jadwal_ujian",
                    "_token"
                ]));
        }

        return redirect()->to("/prodi/detail_jadwalpermhs/$pendaftaran_id");
    }

    public function cekJamUjian($tipe_ujian, $ruangan, $nim, $pendaftaran_id)
    {
        $namaperiode = mst_pendaftaran::find($pendaftaran_id)->nama_periode;
        $countname = mst_pendaftaran::where("nama_periode", $namaperiode)->count();

        if ($countname == 3) {
            $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where("mst_pendaftaran.pendaftaran_id", $pendaftaran_id)->first();
            $xdata = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
                ->join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "trt_reg.pendaftaran_id")
                ->join("trt_jadwal_ujian_per_mhs", "trt_jadwal_ujian_per_mhs.C_NPM", "=", "trt_bimbingan.C_NPM")
                ->join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where([
                    "trt_jadwal_ujian.tgl_ujian" => $info->tgl_ujian,
                    "trt_jadwal_ujian_per_mhs.ruangan" => $ruangan
                ])->get(["jam_ujian"]);
            $jamujian = [];
            foreach ($xdata as $d) {
                if ($d->tipe_ujian == 2) {
                    $jamujian[] = $d->jam_ujian;
                    $jamujian[] = sprintf('%02d', substr($d->jam_ujian, 0, 2) + 1) . ":30";
                } else {
                    $jamujian[] = $d->jam_ujian;
                }
            }

            $trtpenguji = TrtPenguji::where("C_NPM", $nim)->first();
            $trtbimbingan = trt_bimbingan::where("C_NPM", $nim)->first();
            $pembimbing1 = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
                ->join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "trt_reg.pendaftaran_id")
                ->join("trt_jadwal_ujian_per_mhs", "trt_jadwal_ujian_per_mhs.C_NPM", "=", "trt_bimbingan.C_NPM")
                ->join("trt_penguji", "trt_penguji.C_NPM", "=", "trt_jadwal_ujian_per_mhs.C_NPM")
                ->join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where([
                    "trt_bimbingan.pembimbing_I_id" => $trtbimbingan->pembimbing_I_id,
                    "trt_jadwal_ujian.tgl_ujian" => $info->tgl_ujian
                ])
                ->get(["jam_ujian"]);
            $pembimbing2 = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
                ->join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "trt_reg.pendaftaran_id")
                ->join("trt_jadwal_ujian_per_mhs", "trt_jadwal_ujian_per_mhs.C_NPM", "=", "trt_bimbingan.C_NPM")
                ->join("trt_penguji", "trt_penguji.C_NPM", "=", "trt_jadwal_ujian_per_mhs.C_NPM")
                ->join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where([
                    "trt_bimbingan.pembimbing_II_id" => $trtbimbingan->pembimbing_II_id,
                    "trt_jadwal_ujian.tgl_ujian" => $info->tgl_ujian
                ])
                ->get(["jam_ujian"]);
            $penguji1 = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
                ->join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "trt_reg.pendaftaran_id")
                ->join("trt_jadwal_ujian_per_mhs", "trt_jadwal_ujian_per_mhs.C_NPM", "=", "trt_bimbingan.C_NPM")
                ->join("trt_penguji", "trt_penguji.C_NPM", "=", "trt_jadwal_ujian_per_mhs.C_NPM")
                ->join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where([
                    "trt_penguji.penguji_I_id" => $trtpenguji->penguji_I_id,
                    "trt_jadwal_ujian.tgl_ujian" => $info->tgl_ujian
                ])
                ->get(["jam_ujian"]);
            $penguji2 = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
                ->join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "trt_reg.pendaftaran_id")
                ->join("trt_jadwal_ujian_per_mhs", "trt_jadwal_ujian_per_mhs.C_NPM", "=", "trt_bimbingan.C_NPM")
                ->join("trt_penguji", "trt_penguji.C_NPM", "=", "trt_jadwal_ujian_per_mhs.C_NPM")
                ->join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where([
                    "trt_penguji.penguji_II_id" => $trtpenguji->penguji_II_id,
                    "trt_jadwal_ujian.tgl_ujian" => $info->tgl_ujian
                ])
                ->get(["jam_ujian"]);
            $penguji3 = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
                ->join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "trt_reg.pendaftaran_id")
                ->join("trt_jadwal_ujian_per_mhs", "trt_jadwal_ujian_per_mhs.C_NPM", "=", "trt_bimbingan.C_NPM")
                ->join("trt_penguji", "trt_penguji.C_NPM", "=", "trt_jadwal_ujian_per_mhs.C_NPM")
                ->join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where([
                    "trt_penguji.penguji_III_id" => $trtpenguji->penguji_III_id,
                    "trt_jadwal_ujian.tgl_ujian" => $info->tgl_ujian
                ])
                ->get(["jam_ujian"]);
            $ketuasidang = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
                ->join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "trt_reg.pendaftaran_id")
                ->join("trt_jadwal_ujian_per_mhs", "trt_jadwal_ujian_per_mhs.C_NPM", "=", "trt_bimbingan.C_NPM")
                ->join("trt_penguji", "trt_penguji.C_NPM", "=", "trt_jadwal_ujian_per_mhs.C_NPM")
                ->join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where([
                    "trt_penguji.ketua_sidang_id" => $trtpenguji->ketua_sidang_id,
                    "trt_jadwal_ujian.tgl_ujian" => $info->tgl_ujian
                ])
                ->get(["jam_ujian"]);
            $sekretaris = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
                ->join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "trt_reg.pendaftaran_id")
                ->join("trt_jadwal_ujian_per_mhs", "trt_jadwal_ujian_per_mhs.C_NPM", "=", "trt_bimbingan.C_NPM")
                ->join("trt_penguji", "trt_penguji.C_NPM", "=", "trt_jadwal_ujian_per_mhs.C_NPM")
                ->join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where([
                    "trt_penguji.sekretaris_id" => $trtpenguji->sekretaris_id,
                    "trt_jadwal_ujian.tgl_ujian" => $info->tgl_ujian
                ])
                ->get(["jam_ujian"]);
            foreach ($pembimbing1 as $d) {
                if ($d->tipe_ujian == 2) {
                    $jamujian[] = $d->jam_ujian;
                    $jamujian[] = sprintf('%02d', substr($d->jam_ujian, 0, 2) + 1) . ":30";
                } else {
                    $jamujian[] = $d->jam_ujian;
                }
            }
            foreach ($pembimbing2 as $d) {
                if ($d->tipe_ujian == 2) {
                    $jamujian[] = $d->jam_ujian;
                    $jamujian[] = sprintf('%02d', substr($d->jam_ujian, 0, 2) + 1) . ":30";
                } else {
                    $jamujian[] = $d->jam_ujian;
                }
            }
            foreach ($penguji1 as $d) {
                if ($d->tipe_ujian == 2) {
                    $jamujian[] = $d->jam_ujian;
                    $jamujian[] = sprintf('%02d', substr($d->jam_ujian, 0, 2) + 1) . ":30";
                } else {
                    $jamujian[] = $d->jam_ujian;
                }
            }
            foreach ($penguji2 as $d) {
                if ($d->tipe_ujian == 2) {
                    $jamujian[] = $d->jam_ujian;
                    $jamujian[] = sprintf('%02d', substr($d->jam_ujian, 0, 2) + 1) . ":30";
                } else {
                    $jamujian[] = $d->jam_ujian;
                }
            }
            foreach ($penguji3 as $d) {
                if ($d->tipe_ujian == 2) {
                    $jamujian[] = $d->jam_ujian;
                    $jamujian[] = sprintf('%02d', substr($d->jam_ujian, 0, 2) + 1) . ":30";
                } else {
                    $jamujian[] = $d->jam_ujian;
                }
            }
            foreach ($ketuasidang as $d) {
                if ($d->tipe_ujian == 2) {
                    $jamujian[] = $d->jam_ujian;
                    $jamujian[] = sprintf('%02d', substr($d->jam_ujian, 0, 2) + 1) . ":30";
                } else {
                    $jamujian[] = $d->jam_ujian;
                }
            }
            foreach ($sekretaris as $d) {
                if ($d->tipe_ujian == 2) {
                    $jamujian[] = $d->jam_ujian;
                    $jamujian[] = sprintf('%02d', substr($d->jam_ujian, 0, 2) + 1) . ":30";
                } else {
                    $jamujian[] = $d->jam_ujian;
                }
            }
            $jamujian = array_unique($jamujian);
            $data = [];
            for ($i = 8; $i < 18; $i++) {
                $time = sprintf('%02d', $i) . ":30";
                $timex = sprintf('%02d', $i + 1) . ":30";
                if ($i != 12 && $i != 15) {
                    if (!empty($xdata)) {
                        if (!in_array($time, $jamujian)) {
                            $data[] = $time . "-" . $timex;
                        }
                    } else {
                        $data[] = $time . "-" . $timex;
                    }
                }
            }
        } else {
            $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where("mst_pendaftaran.pendaftaran_id", $pendaftaran_id)->first();
            $xdata = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
                ->join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "trt_reg.pendaftaran_id")
                ->join("trt_jadwal_ujian_per_mhs", "trt_jadwal_ujian_per_mhs.C_NPM", "=", "trt_bimbingan.C_NPM")
                ->join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where([
                    "mst_pendaftaran.tipe_ujian" => $tipe_ujian,
                    "trt_jadwal_ujian.tgl_ujian" => $info->tgl_ujian,
                    "trt_jadwal_ujian_per_mhs.ruangan" => $ruangan
                ])->get(["jam_ujian"]);
            $jamujian = [];
            foreach ($xdata as $d) {
                $jamujian[] = $d->jam_ujian;
            }

            $trtpenguji = TrtPenguji::where("C_NPM", $nim)->first();
            $trtbimbingan = trt_bimbingan::where("C_NPM", $nim)->first();
            $pembimbing1 = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
                ->join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "trt_reg.pendaftaran_id")
                ->join("trt_jadwal_ujian_per_mhs", "trt_jadwal_ujian_per_mhs.C_NPM", "=", "trt_bimbingan.C_NPM")
                ->join("trt_penguji", "trt_penguji.C_NPM", "=", "trt_jadwal_ujian_per_mhs.C_NPM")
                ->join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where([
                    "trt_bimbingan.pembimbing_I_id" => $trtbimbingan->pembimbing_I_id,
                    "trt_jadwal_ujian.tgl_ujian" => $info->tgl_ujian,
                    "mst_pendaftaran.tipe_ujian" => $tipe_ujian
                ])
                ->get(["jam_ujian"]);
            $pembimbing2 = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
                ->join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "trt_reg.pendaftaran_id")
                ->join("trt_jadwal_ujian_per_mhs", "trt_jadwal_ujian_per_mhs.C_NPM", "=", "trt_bimbingan.C_NPM")
                ->join("trt_penguji", "trt_penguji.C_NPM", "=", "trt_jadwal_ujian_per_mhs.C_NPM")
                ->join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where([
                    "trt_bimbingan.pembimbing_II_id" => $trtbimbingan->pembimbing_II_id,
                    "trt_jadwal_ujian.tgl_ujian" => $info->tgl_ujian,
                    "mst_pendaftaran.tipe_ujian" => $tipe_ujian
                ])
                ->get(["jam_ujian"]);
            $penguji1 = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
                ->join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "trt_reg.pendaftaran_id")
                ->join("trt_jadwal_ujian_per_mhs", "trt_jadwal_ujian_per_mhs.C_NPM", "=", "trt_bimbingan.C_NPM")
                ->join("trt_penguji", "trt_penguji.C_NPM", "=", "trt_jadwal_ujian_per_mhs.C_NPM")
                ->join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where([
                    "trt_penguji.penguji_I_id" => $trtpenguji->penguji_I_id,
                    "trt_jadwal_ujian.tgl_ujian" => $info->tgl_ujian,
                    "mst_pendaftaran.tipe_ujian" => $tipe_ujian
                ])
                ->get(["jam_ujian"]);
            $penguji2 = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
                ->join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "trt_reg.pendaftaran_id")
                ->join("trt_jadwal_ujian_per_mhs", "trt_jadwal_ujian_per_mhs.C_NPM", "=", "trt_bimbingan.C_NPM")
                ->join("trt_penguji", "trt_penguji.C_NPM", "=", "trt_jadwal_ujian_per_mhs.C_NPM")
                ->join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where([
                    "trt_penguji.penguji_II_id" => $trtpenguji->penguji_II_id,
                    "trt_jadwal_ujian.tgl_ujian" => $info->tgl_ujian,
                    "mst_pendaftaran.tipe_ujian" => $tipe_ujian
                ])
                ->get(["jam_ujian"]);
            $penguji3 = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
                ->join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "trt_reg.pendaftaran_id")
                ->join("trt_jadwal_ujian_per_mhs", "trt_jadwal_ujian_per_mhs.C_NPM", "=", "trt_bimbingan.C_NPM")
                ->join("trt_penguji", "trt_penguji.C_NPM", "=", "trt_jadwal_ujian_per_mhs.C_NPM")
                ->join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where([
                    "trt_penguji.penguji_III_id" => $trtpenguji->penguji_III_id,
                    "trt_jadwal_ujian.tgl_ujian" => $info->tgl_ujian,
                    "mst_pendaftaran.tipe_ujian" => $tipe_ujian
                ])
                ->get(["jam_ujian"]);
            $ketuasidang = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
                ->join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "trt_reg.pendaftaran_id")
                ->join("trt_jadwal_ujian_per_mhs", "trt_jadwal_ujian_per_mhs.C_NPM", "=", "trt_bimbingan.C_NPM")
                ->join("trt_penguji", "trt_penguji.C_NPM", "=", "trt_jadwal_ujian_per_mhs.C_NPM")
                ->join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where([
                    "trt_penguji.ketua_sidang_id" => $trtpenguji->ketua_sidang_id,
                    "trt_jadwal_ujian.tgl_ujian" => $info->tgl_ujian,
                    "mst_pendaftaran.tipe_ujian" => $tipe_ujian
                ])
                ->get(["jam_ujian"]);
            $sekretaris = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
                ->join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "trt_reg.pendaftaran_id")
                ->join("trt_jadwal_ujian_per_mhs", "trt_jadwal_ujian_per_mhs.C_NPM", "=", "trt_bimbingan.C_NPM")
                ->join("trt_penguji", "trt_penguji.C_NPM", "=", "trt_jadwal_ujian_per_mhs.C_NPM")
                ->join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
                ->where([
                    "trt_penguji.sekretaris_id" => $trtpenguji->sekretaris_id,
                    "trt_jadwal_ujian.tgl_ujian" => $info->tgl_ujian,
                    "mst_pendaftaran.tipe_ujian" => $tipe_ujian
                ])
                ->get(["jam_ujian"]);
            foreach ($pembimbing1 as $d) {
                $jamujian[] = $d->jam_ujian;
            }
            foreach ($pembimbing2 as $d) {
                $jamujian[] = $d->jam_ujian;
            }
            foreach ($penguji1 as $d) {
                $jamujian[] = $d->jam_ujian;
            }
            foreach ($penguji2 as $d) {
                $jamujian[] = $d->jam_ujian;
            }
            foreach ($penguji3 as $d) {
                $jamujian[] = $d->jam_ujian;
            }
            foreach ($ketuasidang as $d) {
                $jamujian[] = $d->jam_ujian;
            }
            foreach ($sekretaris as $d) {
                $jamujian[] = $d->jam_ujian;
            }
            $jamujian = array_unique($jamujian);
            $data = [];
            if ($tipe_ujian == 0 || $tipe_ujian == 1) {
                for ($i = 8; $i < 18; $i++) {
                    $time = sprintf('%02d', $i) . ":30";
                    $timex = sprintf('%02d', $i + 1) . ":30";
                    if ($i != 12 && $i != 15) {
                        if (!empty($xdata)) {
                            if (!in_array($time, $jamujian)) {
                                $data[] = $time . "-" . $timex;
                            }
                        } else {
                            $data[] = $time . "-" . $timex;
                        }
                    }
                }
            } elseif ($tipe_ujian == 2) {
                for ($i = 8; $i < 18; $i = $i + 2) {
                    if ($i != 14) {
                        $time = sprintf('%02d', $i) . ":30";
                        $timex = sprintf('%02d', $i + 2) . ":30";
                    } else {
                        $time = sprintf('%02d', $i - 1) . ":30";
                        $timex = sprintf('%02d', $i + 1) . ":30";
                    }
                    if ($i != 12 && $i != 15) {
                        if (!empty($xdata)) {
                            if (!in_array($time, $jamujian)) {
                                $data[] = $time . "-" . $timex;
                            }
                        } else {
                            $data[] = $time . "-" . $timex;
                        }
                    }
                }
            }
        }
        return response()->json($data);
    }

    public function cetakBeritaAcara($pendaftaran_id, $nim)
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
        return view("tugasakhir.prodi.cetak_berita_acara", compact(
            "nim",
            "trt_bimbingan",
            "trt_penguji",
            "tipe_ujian",
            "ruangan",
            "tgl_ujian"
        ));
    }

    public function selesaiKonfirmasi($nim, $type)
    {
        TrtPengajuanDokumen::where([
            "C_NPM" => $nim,
            "type" => $type
        ])->delete();

        switch ($type) {
            case "0":
                $to = "persyaratan_proposal";
                break;
            case "1":
                $to = "persyaratan_seminarhasil";
                break;
            case "2":
                $to = "persyaratan_ujianmeja";
                break;
        }
        return redirect("/prodi/$to");
    }

    public function konfirmasi_persyaratan_ujian_by_nim($status, $nim)
    {
        $data = TrtSyaratUjian::where([
            "C_NPM" => $nim
        ])->update([
            "status" => $status
        ]);
        return redirect()->back();
    }

    // Cek Nomor SK
    public function cek_nomor_sk_pembimbing($nomor)
    {
        $data = DB::select("SELECT DISTINCT(nomor) FROM `trt_sk`");

        $status = 'tidak';

        foreach ($data as $value) {
            if (str_replace("/", "", $value->nomor) == $nomor) {
                $status = "ada";
            }
        }

        return response()->json($status);
    }

    // Cek Nomor SK
    public function cek_nomor_sk_ujian_ta($nomor)
    {
        $data = DB::select("SELECT DISTINCT(nomor) FROM `trt_sk_ujian_ta`");

        $status = 'tidak';

        foreach ($data as $value) {
            if (str_replace("/", "", $value->nomor) == $nomor) {
                $status = "ada";
            }
        }

        return response()->json($status);
    }

    // Riwayat SK Pengusulan Pembimbing TA

    function riwayat_sk_pengusulan()
    {
        $data = DB::select('SELECT DISTINCT nomor, tgl_surat, perihal FROM trt_sk');
        return view('tugasakhir.prodi.riwayat_sk_pengusulan', compact('data'));
    }

    function detail_riwayat_sk_pengusulan($nomor)
    {
        $data = DB::table("trt_sk")
            ->select("*")
            ->join('trt_bimbingan', 'trt_bimbingan.bimbingan_id', '=', 'trt_sk.bimbingan_id')
            ->where('trt_sk.nomor', '=', str_replace("$", "/", $nomor))
            ->get();

        return view('tugasakhir.prodi.detail_riwayat_sk_pengusulan', compact('data'));
    }

    // Riwayat SK Pengusulan Ujain TA

    function riwayat_sk_pengusulan_tim_ujian_ta()
    {
        $data = DB::select('SELECT DISTINCT nomor, tgl_surat FROM trt_sk_ujian_ta');

        return view('tugasakhir.prodi.riwayat_sk_pengusulan_tim_ujian_ta', compact('data'));
    }

    function detail_riwayat_sk_pengusulan_tim_ujian_ta($nomor)
    {
        $data = DB::table("trt_sk_ujian_ta")
            ->select("*")
            ->join('trt_reg', 'trt_reg.pendaftaran_id', '=', 'trt_sk_ujian_ta.pendaftaran_id')
            ->join('trt_bimbingan', 'trt_bimbingan.bimbingan_id', '=', 'trt_reg.bimbingan_id')
            ->where('trt_sk_ujian_ta.nomor', '=', str_replace("$", "/", $nomor))
            ->get();

        return view('tugasakhir.prodi.detail_riwayat_sk_pengusulan_tim_ujian_ta', compact('data'));
    }

    // 20 Oktober 2020
    // Detail Status Bimbingan Dengan Fungsi Filter Dengan Tanggal
    public function tampilDetailStatusBimbinganDenganFilterTanggal()
    {



        $tanggal_dari = Input::get('tanggal_dari');
        $tanggal_sampai = Input::get('tanggal_sampai');
        $status = Input::get('status');
        $data = DB::table('trt_bimbingan')
            ->where('status_bimbingan', $status)
            ->whereBetween('updated_at', [$tanggal_dari, $tanggal_sampai])
            ->get();
        if ($data->isEmpty()) {
            return redirect('prodi/detail_status_bimbingan_mahasiswa/' . $status . '');
        }

        return view('tugasakhir.prodi.detail_status_bimbingan_mahasiswa', compact('data', 'status'));
    }
    // // Menu Download
    // Menampilkan Menu Downloads
    public function tampilDownload()
    {
        $data = DB::table('mst_download')->get();
        return view('tugasakhir.prodi.menu-download', compact('data'));
    }
    // Menambahkan Daftar Download
    public function kirimDownload(Request $request)
    {
        try {
            DB::table('mst_download')
                ->updateOrInsert(
                    [
                        'nama_dokumen' => $request->nama_dokumen,
                    ],
                    [
                        'nama_dokumen' => $request->nama_dokumen,
                        'link_download' => $request->link_download,
                    ]
                );
            return redirect()->back()->with(['status' => "berhasil"]);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function hapusDownload($id)
    {
        try {
            DB::table('mst_download')->where('id_download', $id)->delete();
            return redirect()->back()->with(['status' => "berhasil_hapus"]);
        } catch (\Exception $e) {
            return redirect()->back()->with(['status' => "gagal_hapus"]);
        }
    }
    // // Menu Jadwal Ujian
    public function hapusJadwalUjianPerMahasiswa($C_NPM, $pendaftaran_id)
    {
        try {
            trt_reg::where('C_NPM', $C_NPM)->where('pendaftaran_id', $pendaftaran_id)->delete();
            $data_pendaftaran = mst_pendaftaran::where('pendaftaran_id', $pendaftaran_id)->first();
            mst_pendaftaran::where('pendaftaran_id', $pendaftaran_id)->update(
                [
                    "jml_peserta" => $data_pendaftaran->jml_peserta - 1,
                ]
            );
            TrtPenguji::where('C_NPM', $C_NPM)->where('tipe_ujian', $data_pendaftaran->tipe_ujian)->delete();
            return redirect()->back()->with(['status' => "berhasil_hapus"]);
        } catch (\Exception $e) {
            return redirect()->back()->with(['status' => "gagal_hapus"]);
        }
    }
    // Surat Keputusan Pembimbing
    public function surat_keputusan_pembimbing()
    {
        if (Auth::user()->name == "proditi") {
            $data = DB::table('t_mst_mahasiswa')
                ->join('trt_bimbingan', 'trt_bimbingan.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
                ->join('t_mst_dosen', 'C_KODE_DOSEN', '=', 'trt_bimbingan.pembimbing_I_id')
                ->select('t_mst_mahasiswa.NAMA_MAHASISWA', 't_mst_dosen.NAMA_DOSEN')
                ->where('t_mst_mahasiswa.C_NPM', 'LIKE', '130%')
                ->get();

            $penetapan_pengusulan = DB::table('trt_bimbingan')
                ->join('t_mst_mahasiswa', 'trt_bimbingan.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
                ->select('*')
                ->where('t_mst_mahasiswa.C_NPM', 'LIKE', '130%')
                ->where('status_sk', '<>', 1)
                ->get();

            $riwayat_usulan = DB::table('trt_sk')
                ->select('nomor', 'tgl_surat')
                ->distinct('nomor')
                ->orderBy('trt_sk.sk_id', 'DESC')
                ->get();

            $data_sk = DB::table('mst_sk_pembimbing')
                ->join('trt_bimbingan', 'mst_sk_pembimbing.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
                ->select('*')
                ->where('trt_bimbingan.C_NPM', 'LIKE', '130%')
                ->orderBy('mst_sk_pembimbing.sk_pembimbing_id', 'DESC')
                ->get();
        } else {
            $data = DB::table('t_mst_mahasiswa')
                ->join('trt_bimbingan', 'trt_bimbingan.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
                ->join('t_mst_dosen', 'C_KODE_DOSEN', '=', 'trt_bimbingan.pembimbing_I_id')
                ->select('t_mst_mahasiswa.NAMA_MAHASISWA', 't_mst_dosen.NAMA_DOSEN')
                ->where('t_mst_mahasiswa.C_NPM', 'LIKE', '131%')
                ->get();

            $penetapan_pengusulan = DB::table('trt_bimbingan')
                ->join('t_mst_mahasiswa', 'trt_bimbingan.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
                ->select('*')
                ->where('t_mst_mahasiswa.C_NPM', 'LIKE', '131%')
                ->where('status_sk', '<>', 1)
                ->get();

            $riwayat_usulan = DB::table('trt_sk')
                ->select('nomor', 'tgl_surat')
                ->distinct('nomor')
                ->orderBy('trt_sk.sk_id', 'DESC')
                ->get();

            $data_sk = DB::table('mst_sk_pembimbing')
                ->join('trt_bimbingan', 'mst_sk_pembimbing.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
                ->select('*')
                ->where('trt_bimbingan.C_NPM', 'LIKE', '131%')
                ->orderBy('mst_sk_pembimbing.sk_pembimbing_id', 'DESC')
                ->get();
        }

        return view('tugasakhir.prodi.surat_keputusan_pembimbing', compact('riwayat_usulan', 'penetapan_pengusulan', 'data', 'data_sk'));
    }

    public function surat_penugasan_ujian_tugas_akhir()
    {
        if (Auth::user()->name == "proditi") {
            $data_sk_penugasan = DB::table('mst_sk_penugasan')
                ->select('*')
                ->join('trt_bimbingan', 'trt_bimbingan.bimbingan_id', '=', 'mst_sk_penugasan.bimbingan_id')
                ->where('trt_bimbingan.C_NPM', 'LIKE', '130%')
                ->orderBy('mst_sk_penugasan.sk_penugasan_id', 'DESC')
                ->get();

            $daftar_surat_usulan = DB::table('trt_sk_ujian_ta')
                ->select('*')
                ->join('mst_pendaftaran', 'mst_pendaftaran.pendaftaran_id', '=', 'trt_sk_ujian_ta.pendaftaran_id')
                ->orderBy('trt_sk_ujian_ta.sk_id', 'DESC')
                ->get();
        } else {
            $data_sk_penugasan = DB::table('mst_sk_penugasan')
                ->select('*')
                ->join('trt_bimbingan', 'trt_bimbingan.bimbingan_id', '=', 'mst_sk_penugasan.bimbingan_id')
                ->where('trt_bimbingan.C_NPM', 'LIKE', '131%')
                ->orderBy('mst_sk_penugasan.sk_penugasan_id', 'DESC')
                ->get();

            $daftar_surat_usulan = DB::table('trt_sk_ujian_ta')
                ->select('*')
                ->join('mst_pendaftaran', 'mst_pendaftaran.pendaftaran_id', '=', 'trt_sk_ujian_ta.pendaftaran_id')
                ->orderBy('trt_sk_ujian_ta.sk_id', 'DESC')
                ->get();
        }
        return view('tugasakhir.prodi.surat_penugasan_ujian_tugas_akhir', compact('daftar_surat_usulan', 'data_sk_penugasan'));
    }

    public function tolak_topik_penelitian($id)
    {
        try {

            DB::table('trt_topik')
                ->where('topik_id', $id)
                ->update([
                    'status' => 2
                ]);

            return redirect()->back()->with(['status' => "berhasil"]);
        } catch (\Exception $e) {
            return redirect()->back()->with(['status' => "gagal"]);
        }
    }

    public function cetak_riwayat_sk_pengusulan_tim_ujian_ta($nomor)
    {
        $nomor = str_replace("$", "/", $nomor);

        $data = DB::table("trt_sk_ujian_ta")
            ->select("*")
            ->join('trt_reg', 'trt_reg.pendaftaran_id', '=', 'trt_sk_ujian_ta.pendaftaran_id')
            ->join('trt_bimbingan', 'trt_bimbingan.bimbingan_id', '=', 'trt_reg.bimbingan_id')
            ->where('trt_sk_ujian_ta.nomor', '=', $nomor)
            ->get();



        $perihal = $data[0]->perihal;
        $tgl_ujian = $data[0]->tgl_surat;
        $data = $data[0]->pendaftaran_id;
        $tgl_ujian = helper::tgl_indo_lengkap($tgl_ujian);


        $datax = DB::table('mst_pendaftaran')
            ->select('*')
            ->where('mst_pendaftaran.pendaftaran_id', '=', $data)
            ->get();

        return view('tugasakhir.prodi.surat_usulantimujian', compact('nomor', 'perihal', 'datax', 'tgl_ujian'));
    }
}
