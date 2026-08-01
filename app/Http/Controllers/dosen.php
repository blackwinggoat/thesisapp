<?php

namespace App\Http\Controllers;

use App\Helper;
use App\LampiranPesan;
use App\Model\mst_bidangilmu;
use App\Model\mst_pendaftaran;
use App\Model\mst_pengumuman;
use App\Model\mst_syarat_ujian;
use App\Model\mst_tmp_usulan;
use App\Model\mst_pesan;
use App\Model\t_mst_mahasiswa;
use App\Model\trt_bimbingan;
use App\Model\trt_reg;
use App\Model\trt_sk;
use App\Model\trt_topik;
use App\Model\trt_hasil;
use App\Model\trt_sk_ujian_ta;
use App\Model\trt_konsultasi;
use App\Model\users;
use App\MstRuangan;
use App\TrtJadwalUjian;
use App\TrtJadwalUjianPerMhs;
use App\TrtLevelPembimbing;
use App\TrtPengajuanDokumen;
use App\TrtPenguji;
use App\TrtSyaratUjian;
use App\TrtUsulanJudul;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Auth;
use Exception;

class dosen extends Controller
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

    public function back_to_prodi(Request $request)
    {
        $sourceUserId = $request->session()->get('login_as_source_user_id');
        $sourceUserLevel = (int) $request->session()->get('login_as_source_user_level', 0);

        if (empty($sourceUserId) || $sourceUserLevel !== 5) {
            return redirect('/')->with('danger', 'Session login as prodi tidak ditemukan.');
        }

        $sourceUser = DB::table('users')
            ->select('id', 'level')
            ->where('id', $sourceUserId)
            ->first();

        if (!$sourceUser || (int) $sourceUser->level !== 5) {
            $request->session()->forget([
                'login_as_source_user_id',
                'login_as_source_user_name',
                'login_as_source_user_level',
            ]);
            return redirect('/')->with('danger', 'Akun prodi asal tidak valid.');
        }

        Auth::loginUsingId($sourceUserId);
        $request->session()->regenerate();
        $request->session()->forget([
            'login_as_source_user_id',
            'login_as_source_user_name',
            'login_as_source_user_level',
        ]);

        return redirect('/')->with('success', 'Berhasil kembali ke akun prodi.');
    }

    public function detail_ujian($nim, $tipe_ujian)
    {
        $data = DB::select("SELECT * FROM trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_penguji.C_NPM = ? AND trt_reg.status = ?", [$nim, $tipe_ujian]);

        if (empty($data)) {
            return response('Data detail ujian tidak ditemukan.', 404);
        }

        return view('tugasakhir.mhs.detail_ujian', compact('data'));
    }

    // Halaman Approve Hasil Ujian TA
    public function rekap_nilai_proposal()
    {
        $kode = auth()->user()->name;
        $data = DB::select("SELECT DISTINCT mst_pendaftaran.pendaftaran_id, mst_pendaftaran.nama_periode, mst_pendaftaran.kuota, mst_pendaftaran.jml_peserta, trt_jadwal_ujian.tgl_ujian FROM mst_pendaftaran, trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa, trt_jadwal_ujian, trt_jadwal_ujian_per_mhs , mst_ruangan WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND mst_ruangan.id =  trt_jadwal_ujian_per_mhs.ruangan AND trt_bimbingan.C_NPM = trt_jadwal_ujian_per_mhs.C_NPM AND trt_jadwal_ujian.id = trt_jadwal_ujian_per_mhs.jadwal_ujian AND trt_jadwal_ujian.pendaftaran_id = mst_pendaftaran.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_penguji.ketua_sidang_id = ? AND mst_pendaftaran.tipe_ujian = ? AND  trt_penguji.tipe_ujian = ? AND trt_reg.status = ? AND trt_bimbingan.status_bimbingan = ? ORDER BY mst_pendaftaran.pendaftaran_id", [$kode, 0, 0, 0, 0]);
        return view('tugasakhir.dosen.rekap_nilai_proposal', compact('data'));
    }
    // Akhir Approve Hasil Ujian TA

    public function rekap_nilai_proposal_history()
    {
        $kode = auth()->user()->name;
        $data = DB::select("SELECT DISTINCT mst_pendaftaran.pendaftaran_id, mst_pendaftaran.nama_periode, mst_pendaftaran.kuota, mst_pendaftaran.jml_peserta, trt_jadwal_ujian.tgl_ujian FROM mst_pendaftaran, trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa, trt_jadwal_ujian, trt_jadwal_ujian_per_mhs, mst_ruangan WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND mst_ruangan.id = trt_jadwal_ujian_per_mhs.ruangan AND trt_bimbingan.C_NPM = trt_jadwal_ujian_per_mhs.C_NPM AND trt_jadwal_ujian.id = trt_jadwal_ujian_per_mhs.jadwal_ujian AND trt_jadwal_ujian.pendaftaran_id = mst_pendaftaran.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_penguji.ketua_sidang_id = ? AND mst_pendaftaran.tipe_ujian = ? AND trt_penguji.tipe_ujian = ? AND trt_reg.status = ? AND trt_bimbingan.status_bimbingan IN (?, ?) ORDER BY mst_pendaftaran.pendaftaran_id", [$kode, 0, 0, 0, 2, 3]);
        return view('tugasakhir.dosen.rekap_nilai_proposal_history', compact('data'));
    }

    // Halaman Approve Hasil Ujian TA
    public function detail_rekap_nilai_proposal($id)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $id)->first();
        if (!$info || !isset($info->tipe_ujian)) {
            return response('Data rekap nilai proposal tidak ditemukan.', 404);
        }
        $data = DB::select("SELECT * FROM mst_pendaftaran,trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_reg.pendaftaran_id = ? AND trt_reg.status = ? AND trt_penguji.ketua_sidang_id = ? AND trt_bimbingan.status_bimbingan = ?", [$id, $info->tipe_ujian, auth()->user()->name, 0]);
        return view('tugasakhir.dosen.detail_rekap_nilai_proposal', compact("data", "info"));
    }
    // Akhir Approve Hasil Ujian TA

    public function detail_rekap_nilai_proposal_history($id)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $id)->first();
        if (!$info || !isset($info->tipe_ujian)) {
            return response('Data rekap nilai proposal history tidak ditemukan.', 404);
        }
        $data = DB::select("SELECT * FROM mst_pendaftaran,trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_reg.pendaftaran_id = ? AND trt_reg.status = ? AND trt_penguji.ketua_sidang_id = ? AND trt_bimbingan.status_bimbingan IN (?, ?)", [$id, $info->tipe_ujian, auth()->user()->name, 2, 3]);
        return view('tugasakhir.dosen.detail_rekap_nilai_proposal_history', compact("data", "info"));
    }

    // Halaman Lembaran Hasil Ujian
    public function lembaran_hasilujian_proposal($pendaftaran_id, $nim, $reg_id)
    {
        $trtjadwalujian = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("trt_jadwal_ujian.pendaftaran_id", $pendaftaran_id)->first();
        if (!$trtjadwalujian) {
            return response('Data jadwal ujian proposal tidak ditemukan.', 404);
        }

        $trtjadwalujianpermhs = TrtJadwalUjianPerMhs::join("mst_ruangan", "mst_ruangan.id", "trt_jadwal_ujian_per_mhs.ruangan")
            ->where([
                "C_NPM" => $nim,
                "jadwal_ujian" => $trtjadwalujian->id
            ])->first();
        $trt_bimbingan = trt_bimbingan::where("C_NPM", $nim)->first();
        $mst_pendaftaran = mst_pendaftaran::find($pendaftaran_id);
        if (!$mst_pendaftaran || !$trt_bimbingan) {
            return response('Data lembaran hasil ujian proposal tidak lengkap.', 404);
        }

        $trt_penguji = TrtPenguji::where([
            "C_NPM" => $nim,
            "tipe_ujian" => $mst_pendaftaran->tipe_ujian
        ])->first();

        $ruangan = '-';
        if ($trtjadwalujianpermhs && !empty($trtjadwalujianpermhs->ruangan)) {
            $ruanganModel = MstRuangan::find($trtjadwalujianpermhs->ruangan);
            $ruangan = $ruanganModel ? $ruanganModel->nama_ruangan : '-';
        }
        $tgl_ujian = Carbon::parse($trtjadwalujian->tgl_ujian)->formatLocalized("%A, %d %B %Y");
        switch ($mst_pendaftaran->tipe_ujian) {
            case "0":
                $tipe_ujian = "Proposal";
                break;
            case "2":
                $tipe_ujian = "Tugas Akhir";
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

        if (!$data_dosen_selesai || !$data_dosen_pembimbing) {
            return response('Data dosen pada lembaran hasil ujian proposal belum lengkap.', 404);
        }

        return view("tugasakhir.dosen.lembaran_hasilujian_proposal", compact(
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
    public function rekap_nilai_ujian_ta()
    {
        $kode = auth()->user()->name;
        $data = DB::select("SELECT DISTINCT mst_pendaftaran.pendaftaran_id, mst_pendaftaran.nama_periode, mst_pendaftaran.kuota, mst_pendaftaran.jml_peserta, trt_jadwal_ujian.tgl_ujian FROM mst_pendaftaran, trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa, trt_jadwal_ujian, trt_jadwal_ujian_per_mhs , mst_ruangan WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND mst_ruangan.id =  trt_jadwal_ujian_per_mhs.ruangan AND trt_bimbingan.C_NPM = trt_jadwal_ujian_per_mhs.C_NPM AND trt_jadwal_ujian.id = trt_jadwal_ujian_per_mhs.jadwal_ujian AND trt_jadwal_ujian.pendaftaran_id = mst_pendaftaran.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_penguji.ketua_sidang_id = ? AND mst_pendaftaran.tipe_ujian = ? AND  trt_penguji.tipe_ujian = ? AND trt_reg.status = ? AND trt_bimbingan.status_bimbingan = ? ORDER BY mst_pendaftaran.pendaftaran_id", [$kode, 2, 2, 2, 2]);
        return view('tugasakhir.dosen.rekap_nilai_ujian_ta', compact('data'));
    }
    // Akhir Approve Hasil Ujian TA

    public function rekap_nilai_ujian_ta_history()
    {
        $kode = auth()->user()->name;
        $data = DB::select("SELECT DISTINCT mst_pendaftaran.pendaftaran_id, mst_pendaftaran.nama_periode, mst_pendaftaran.kuota, mst_pendaftaran.jml_peserta, trt_jadwal_ujian.tgl_ujian FROM mst_pendaftaran, trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa, trt_jadwal_ujian, trt_jadwal_ujian_per_mhs, mst_ruangan WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND mst_ruangan.id = trt_jadwal_ujian_per_mhs.ruangan AND trt_bimbingan.C_NPM = trt_jadwal_ujian_per_mhs.C_NPM AND trt_jadwal_ujian.id = trt_jadwal_ujian_per_mhs.jadwal_ujian AND trt_jadwal_ujian.pendaftaran_id = mst_pendaftaran.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_penguji.ketua_sidang_id = ? AND mst_pendaftaran.tipe_ujian = ? AND trt_penguji.tipe_ujian = ? AND trt_reg.status = ? AND trt_bimbingan.status_bimbingan = ? ORDER BY mst_pendaftaran.pendaftaran_id", [$kode, 2, 2, 2, 3]);
        return view('tugasakhir.dosen.rekap_nilai_ujian_ta_history', compact('data'));
    }

    // Halaman Approve Hasil Ujian TA
    public function detail_rekap_nilai_ujian_ta($id)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $id)->first();
        if (!$info || !isset($info->tipe_ujian)) {
            return response('Data rekap nilai ujian TA tidak ditemukan.', 404);
        }
        $data = DB::select("SELECT * FROM mst_pendaftaran,trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_reg.pendaftaran_id = ? AND trt_reg.status = ? AND trt_penguji.ketua_sidang_id = ? AND trt_bimbingan.status_bimbingan = ?", [$id, $info->tipe_ujian, auth()->user()->name, 2]);
        return view('tugasakhir.dosen.detail_rekap_nilai_ujian_ta', compact("data", "info"));
    }
    // Akhir Approve Hasil Ujian TA

    public function detail_rekap_nilai_ujian_ta_history($id)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $id)->first();
        if (!$info || !isset($info->tipe_ujian)) {
            return response('Data rekap nilai ujian TA history tidak ditemukan.', 404);
        }
        $data = DB::select("SELECT * FROM mst_pendaftaran,trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_reg.pendaftaran_id = ? AND trt_reg.status = ? AND trt_penguji.ketua_sidang_id = ? AND trt_bimbingan.status_bimbingan = ?", [$id, $info->tipe_ujian, auth()->user()->name, 3]);
        return view('tugasakhir.dosen.detail_rekap_nilai_ujian_ta_history', compact("data", "info"));
    }

    // Halaman Lembaran Hasil Ujian
    public function lembaran_hasilujian_ujian_ta($pendaftaran_id, $nim, $reg_id)
    {
        $trtjadwalujian = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("trt_jadwal_ujian.pendaftaran_id", $pendaftaran_id)->first();
        if (!$trtjadwalujian) {
            return response('Data jadwal ujian TA tidak ditemukan.', 404);
        }

        $trtjadwalujianpermhs = TrtJadwalUjianPerMhs::join("mst_ruangan", "mst_ruangan.id", "trt_jadwal_ujian_per_mhs.ruangan")
            ->where([
                "C_NPM" => $nim,
                "jadwal_ujian" => $trtjadwalujian->id
            ])->first();
        $trt_bimbingan = trt_bimbingan::where("C_NPM", $nim)->first();
        $mst_pendaftaran = mst_pendaftaran::find($pendaftaran_id);
        if (!$mst_pendaftaran || !$trt_bimbingan) {
            return response('Data lembaran hasil ujian TA tidak lengkap.', 404);
        }

        $trt_penguji = TrtPenguji::where([
            "C_NPM" => $nim,
            "tipe_ujian" => $mst_pendaftaran->tipe_ujian
        ])->first();

        $ruangan = '-';
        if ($trtjadwalujianpermhs && !empty($trtjadwalujianpermhs->ruangan)) {
            $ruanganModel = MstRuangan::find($trtjadwalujianpermhs->ruangan);
            $ruangan = $ruanganModel ? $ruanganModel->nama_ruangan : '-';
        }
        $tgl_ujian = Carbon::parse($trtjadwalujian->tgl_ujian)->formatLocalized("%A, %d %B %Y");
        switch ($mst_pendaftaran->tipe_ujian) {
            case "0":
                $tipe_ujian = "Proposal";
                break;
            case "2":
                $tipe_ujian = "Tugas Akhir";
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

        if (!$data_dosen_selesai || !$data_dosen_pembimbing) {
            return response('Data dosen pada lembaran hasil ujian TA belum lengkap.', 404);
        }

        return view("tugasakhir.dosen.lembaran_hasilujian_ujian_ta", compact(
            "nim",
            "trt_bimbingan",
            "trt_penguji",
            "tipe_ujian",
            "ruangan",
            "tgl_ujian",
            "data_hasil",
            "reg_id",
            "data_dosen_selesai",
            "data_dosen_pembimbing"
        ));
    }
    // Akhir Halaman Lembaran Hasil Ujian

    public function usul_judul()
    {
        $data = DB::table('trt_usulan_judul')
            ->select('*')
            ->join('t_mst_mahasiswa', 'trt_usulan_judul.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->where('KODE_DOSEN', auth()->user()->name)
            ->get();
        return view('tugasakhir.dosen.usul_judul', compact('data'));
    }

    public function usul_judul_post(Request $request)
    {
        $data_mahasiswa_belum_ada_judul = DB::select("SELECT users.* from users left join trt_topik on users.name = trt_topik.C_NPM where trt_topik.C_NPM IS NULL AND users.name LIKE '130%' OR users.name LIKE '131%'");
        foreach ($request->penerima_id as $value) {
            if ($value == "semua_mahasiswa") {
                foreach ($data_mahasiswa_belum_ada_judul as $value_2) {
                    TrtUsulanJudul::create([
                        "judul" => $request->usulan_judul,
                        "C_NPM" => $value_2->name,
                        "KODE_DOSEN" => auth()->user()->name,
                    ]);
                }
            } else {
                TrtUsulanJudul::create([
                    "judul" => $request->usulan_judul,
                    "C_NPM" => $value,
                    "KODE_DOSEN" => auth()->user()->name,
                ]);
            }
        }
        return redirect('dsn/usul_judul');
    }

    public function add_usul_judul()
    {
        $data = DB::table('trt_bimbingan')
            ->select('C_NPM')
            ->where('pembimbing_I_id', auth()->user()->name)
            ->orWhere('pembimbing_II_id', auth()->user()->name)
            ->get();

        $data_semua_mahasiswa = DB::select("SELECT * FROM `users` WHERE name LIKE '130%' OR name LIKE '131%'");
        $data_mahasiswa_belum_ada_judul = DB::select("SELECT users.* from users left join trt_topik on users.name = trt_topik.C_NPM where trt_topik.C_NPM IS NULL AND users.name LIKE '130%' OR users.name LIKE '131%'");
        $data_mahasiswa_belum_menerima_usulan_judul = DB::select("SELECT users.* from users left join trt_usulan_judul on users.name = trt_usulan_judul.C_NPM where trt_usulan_judul.C_NPM IS NULL AND users.name LIKE '130%' OR users.name LIKE '131%'");


        $data2 = DB::table('t_mst_dosen')
            ->select('C_KODE_DOSEN')
            ->get();
        return view('tugasakhir.dosen.add_usul_judul', compact('data', 'data2', 'data_semua_mahasiswa', 'data_mahasiswa_belum_ada_judul', 'data_mahasiswa_belum_menerima_usulan_judul'));
    }

    // Halaman Hasili Ujian Proposal
    public function hasil_proposal()
    {
        $kode = auth()->user()->name;
        $data = DB::select("SELECT * FROM trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND (trt_penguji.penguji_I_id  = ? OR trt_penguji.penguji_II_id  = ? OR trt_penguji.penguji_III_id  = ? OR trt_penguji.ketua_sidang_id = ? OR trt_bimbingan.pembimbing_I_id = ? OR trt_bimbingan.pembimbing_II_id = ?) AND trt_reg.status = ? AND trt_bimbingan.status_bimbingan NOT IN (?, ?)", [$kode, $kode, $kode, $kode, $kode, $kode, 0, 2, 3]);

        return view('tugasakhir.dosen.hasil_proposal', compact('data'));
    }
    // Akhir Halaman Hasil Ujian Proposal

    public function hasil_proposal_history()
    {
        $kode = auth()->user()->name;
        $data = DB::select("SELECT * FROM trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND trt_penguji.C_NPM = trt_bimbingan.C_NPM AND (trt_penguji.penguji_I_id = ? OR trt_penguji.penguji_II_id = ? OR trt_penguji.penguji_III_id = ? OR trt_penguji.ketua_sidang_id = ? OR trt_bimbingan.pembimbing_I_id = ? OR trt_bimbingan.pembimbing_II_id = ?) AND trt_reg.status = ? AND trt_bimbingan.status_bimbingan IN (?, ?)", [$kode, $kode, $kode, $kode, $kode, $kode, 0, 2, 3]);

        return view('tugasakhir.dosen.hasil_proposal_history', compact('data'));
    }

    // Detail Halaman Hasil Ujian
    public function detailhasil_proposal($regid)
    {
        $kodeDosen = Helper::getKodeDosenForTrtHasil();
        $data_hasil = trt_hasil::where('reg_id', $regid)->where('nidn', $kodeDosen)->first();
        $nilai = array();
        if ($data_hasil != null) {
            $data = DB::select('SELECT * FROM trt_reg, trt_bimbingan, trt_hasil, t_mst_mahasiswa WHERE trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND t_mst_mahasiswa.C_NPM = trt_bimbingan.C_NPM AND trt_reg.reg_id = trt_hasil.reg_id AND trt_reg.reg_id = ? AND trt_hasil.nidn = ?', [$regid, $kodeDosen]);
            if (empty($data)) {
                return response('Data hasil ujian proposal tidak ditemukan.', 404);
            }

            $nilai = [
                "nilai_1" => $data[0]->nilai_1,
                "nilai_2" => $data[0]->nilai_2,
                "nilai_3" => $data[0]->nilai_3,
                "nilai_4" => $data[0]->nilai_4,
                "nilai_5" => $data[0]->nilai_5,
                "saran" => $data[0]->saran,
            ];
        } else {
            $data = DB::select('SELECT * FROM trt_reg, trt_bimbingan, t_mst_mahasiswa WHERE trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND t_mst_mahasiswa.C_NPM = trt_bimbingan.C_NPM AND trt_reg.reg_id = ?', [$regid]);
            if (empty($data)) {
                return response('Data hasil ujian proposal tidak ditemukan.', 404);
            }
            $nilai = [
                "nilai_1" => null,
                "nilai_2" => null,
                "nilai_3" => null,
                "nilai_4" => null,
                "nilai_5" => null,
                "saran" => null,
            ];
        }

        return view('tugasakhir.dosen.detailhasil_proposal', compact('data', 'nilai', 'kodeDosen'));
    }
    // Akhir Detail Halaman Hasil Ujian

    // Kirim Detail Hasil Proposal
    public function detailhasil_proposalpost(Request $request)
    {
        $kodeDosen = Helper::getKodeDosenForTrtHasil();

        if ($kodeDosen === '') {
            Log::warning('detailhasil_proposalpost dosen code not found', [
                'user_name' => auth()->user()->name ?? null,
                'user_email' => auth()->user()->email ?? null,
                'resolved_kode_dosen' => $kodeDosen,
                'reg_id' => $request->reg_id,
            ]);
            return redirect()->back()->with('error', 'Kode dosen akun login tidak ditemukan. Silakan periksa akun dosen ini.');
        }

        try {
            $data_hasil = trt_hasil::where('reg_id', $request->reg_id)->where('nidn', $kodeDosen)->first();
            if ($data_hasil != null) {
                trt_hasil::where('reg_id', $request->reg_id)->where('nidn', $kodeDosen)->update([
                    'reg_id' => $request->reg_id,
                    'nidn' => $kodeDosen,
                    'nilai_1' => $request->nilai_1,
                    'nilai_2' => $request->nilai_2,
                    'nilai_3' => $request->nilai_3,
                    'nilai_4' => $request->nilai_4,
                    'nilai_5' => $request->nilai_5,
                    'saran' => $request->saran,
                ]);
            } else {
                trt_hasil::create([
                    'reg_id' => $request->reg_id,
                    'nidn' => $kodeDosen,
                    'nilai_1' => $request->nilai_1,
                    'nilai_2' => $request->nilai_2,
                    'nilai_3' => $request->nilai_3,
                    'nilai_4' => $request->nilai_4,
                    'nilai_5' => $request->nilai_5,
                    'saran' => $request->saran,
                ]);
            }
            return redirect::to('dsn/hasil_proposal')->with('status', 'Nilai proposal berhasil disimpan.');
        } catch (\Throwable $th) {
            Log::error('detailhasil_proposalpost error', [
                'user_name' => auth()->user()->name ?? null,
                'user_email' => auth()->user()->email ?? null,
                'resolved_kode_dosen' => $kodeDosen,
                'reg_id' => $request->reg_id,
                'message' => $th->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan nilai proposal.');
        }
    }
    // Hasil Kirim Detail Hasil Proposal

    // Halaman Hasili Ujian Proposal
    public function hasil_ujianmeja()
    {
        $kode = auth()->user()->name;
        $data = DB::select("SELECT * FROM trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND (trt_penguji.penguji_I_id  = ? OR trt_penguji.penguji_II_id  = ? OR trt_penguji.penguji_III_id  = ? OR trt_penguji.ketua_sidang_id = ? OR trt_bimbingan.pembimbing_I_id = ? OR trt_bimbingan.pembimbing_II_id = ?) AND trt_reg.status = ? AND trt_bimbingan.status_bimbingan <> ?", [$kode, $kode, $kode, $kode, $kode, $kode, 2, 3]);

        return view('tugasakhir.dosen.hasil_ujianmeja', compact('data'));
    }
    // Akhir Halaman Hasil Ujian Proposal

    public function hasil_ujianmeja_history()
    {
        $kode = auth()->user()->name;
        $data = DB::select("SELECT * FROM trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND trt_penguji.C_NPM = trt_bimbingan.C_NPM AND (trt_penguji.penguji_I_id = ? OR trt_penguji.penguji_II_id = ? OR trt_penguji.penguji_III_id = ? OR trt_penguji.ketua_sidang_id = ? OR trt_bimbingan.pembimbing_I_id = ? OR trt_bimbingan.pembimbing_II_id = ?) AND trt_reg.status = ? AND trt_bimbingan.status_bimbingan = ?", [$kode, $kode, $kode, $kode, $kode, $kode, 2, 3]);

        return view('tugasakhir.dosen.hasil_ujianmeja_history', compact('data'));
    }

    // Detail Halaman Hasil Ujian
    public function detailhasil_ujianmeja($regid)
    {
        $kodeDosen = Helper::getKodeDosenForTrtHasil();
        $data_hasil = trt_hasil::where('reg_id', $regid)->where('nidn', $kodeDosen)->first();
        $nilai = array();



        if ($data_hasil != null) {
            $data = DB::select('SELECT * FROM trt_reg, trt_bimbingan, trt_hasil, t_mst_mahasiswa WHERE trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND t_mst_mahasiswa.C_NPM = trt_bimbingan.C_NPM AND trt_reg.reg_id = trt_hasil.reg_id AND trt_reg.reg_id = ? AND trt_hasil.nidn = ?', [$regid, $kodeDosen]);
            if (empty($data)) {
                return response('Data hasil ujian meja tidak ditemukan.', 404);
            }

            $nilai = [
                "nilai_1" => $data[0]->nilai_1,
                "nilai_2" => $data[0]->nilai_2,
                "nilai_3" => $data[0]->nilai_3,
                "nilai_4" => $data[0]->nilai_4,
                "nilai_5" => $data[0]->nilai_5,
                "saran" => $data[0]->saran,
            ];
        } else {
            $data = DB::select('SELECT * FROM trt_reg, trt_bimbingan, t_mst_mahasiswa WHERE trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND t_mst_mahasiswa.C_NPM = trt_bimbingan.C_NPM AND trt_reg.reg_id = ?', [$regid]);
            if (empty($data)) {
                return response('Data hasil ujian meja tidak ditemukan.', 404);
            }
            $nilai = [
                "nilai_1" => null,
                "nilai_2" => null,
                "nilai_3" => null,
                "nilai_4" => null,
                "nilai_5" => null,
                "saran" => null,
            ];
        }

        return view('tugasakhir.dosen.detailhasil_ujianmeja', compact('data', 'nilai', 'kodeDosen'));
    }
    // Akhir Detail Halaman Hasil Ujian

    // Kirim Detail Hasil Proposal
    public function detailhasil_ujianmejapost(Request $request)
    {
        $kodeDosen = Helper::getKodeDosenForTrtHasil();

        if ($kodeDosen === '') {
            Log::warning('detailhasil_ujianmejapost dosen code not found', [
                'user_name' => auth()->user()->name ?? null,
                'user_email' => auth()->user()->email ?? null,
                'resolved_kode_dosen' => $kodeDosen,
                'reg_id' => $request->reg_id,
            ]);
            return redirect()->back()->with('error', 'Kode dosen akun login tidak ditemukan. Silakan periksa akun dosen ini.');
        }

        try {
            $data_hasil = trt_hasil::where('reg_id', $request->reg_id)->where('nidn', $kodeDosen)->first();
            if ($data_hasil != null) {
                trt_hasil::where('reg_id', $request->reg_id)->where('nidn', $kodeDosen)->update([
                    'reg_id' => $request->reg_id,
                    'nidn' => $kodeDosen,
                    'nilai_1' => $request->nilai_1,
                    'nilai_2' => $request->nilai_2,
                    'nilai_3' => $request->nilai_3,
                    'nilai_4' => $request->nilai_4,
                    'nilai_5' => $request->nilai_5,
                    'saran' => $request->saran,
                ]);
            } else {
                trt_hasil::create([
                    'reg_id' => $request->reg_id,
                    'nidn' => $kodeDosen,
                    'nilai_1' => $request->nilai_1,
                    'nilai_2' => $request->nilai_2,
                    'nilai_3' => $request->nilai_3,
                    'nilai_4' => $request->nilai_4,
                    'nilai_5' => $request->nilai_5,
                    'saran' => $request->saran,
                ]);
            }
            return redirect::to('dsn/hasil_ujianmeja')->with('status', 'Nilai ujian meja berhasil disimpan.');
        } catch (\Throwable $th) {
            Log::error('detailhasil_ujianmejapost error', [
                'user_name' => auth()->user()->name ?? null,
                'user_email' => auth()->user()->email ?? null,
                'resolved_kode_dosen' => $kodeDosen,
                'reg_id' => $request->reg_id,
                'message' => $th->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan nilai ujian meja.');
        }
    }
    // Hasil Kirim Detail Hasil Proposal


    // Halaman Jadwal Proposal
    public function jadwal_proposal()
    {
        $kode = auth()->user()->name;
        $data = DB::select("SELECT * FROM trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa, trt_jadwal_ujian, trt_jadwal_ujian_per_mhs , mst_ruangan WHERE mst_ruangan.id =  trt_jadwal_ujian_per_mhs.ruangan AND trt_bimbingan.C_NPM = trt_jadwal_ujian_per_mhs.C_NPM AND trt_jadwal_ujian.id = trt_jadwal_ujian_per_mhs.jadwal_ujian AND trt_jadwal_ujian.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND (trt_penguji.penguji_I_id  = ? OR trt_penguji.penguji_II_id  = ? OR trt_penguji.penguji_III_id  = ? OR trt_penguji.ketua_sidang_id = ? OR trt_bimbingan.pembimbing_I_id = ? OR trt_bimbingan.pembimbing_II_id = ?) AND trt_reg.status = ? ", [$kode, $kode, $kode, $kode, $kode, $kode, 0]);
        return view('tugasakhir.dosen.jadwal_proposal', compact('data'));
    }
    // Akhir Halaman Jadwal Proposal

    // Halaman Jadwal Ujian Meja
    public function jadwal_ujianmeja()
    {
        $kode = auth()->user()->name;
        $data = DB::select("SELECT * FROM trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa, trt_jadwal_ujian, trt_jadwal_ujian_per_mhs , mst_ruangan WHERE mst_ruangan.id =  trt_jadwal_ujian_per_mhs.ruangan AND trt_bimbingan.C_NPM = trt_jadwal_ujian_per_mhs.C_NPM AND trt_jadwal_ujian.pendaftaran_id = trt_reg.pendaftaran_id AND trt_jadwal_ujian.id = trt_jadwal_ujian_per_mhs.jadwal_ujian AND  trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND (trt_penguji.penguji_I_id  = ? OR trt_penguji.penguji_II_id  = ? OR trt_penguji.penguji_III_id  = ? OR trt_penguji.ketua_sidang_id = ? OR trt_bimbingan.pembimbing_I_id = ? OR trt_bimbingan.pembimbing_II_id = ?) AND trt_reg.status = ? ", [$kode, $kode, $kode, $kode, $kode, $kode, 2]);

        return view('tugasakhir.dosen.jadwal_ujianmeja', compact('data'));
    }
    // Akhir Halaman Ujian Meja

    // Halaman Ubah Password
    public function ubah_password()
    {
        return view('tugasakhir.dosen.ubah_password');
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

    public function kelengkapan_profil_post(Request $request)
    {
        $this->validate($request, [
            'C_KODE_PRODI' => 'required|in:55201,57201',
            'JENIS_KELAMIN' => 'required|in:Pria,Wanita',
            'NO_HP' => 'required|max:15',
            'EMAIL' => 'required|email|max:50',
            'pangkat' => 'required|max:100',
            'jabatan_fungsional' => 'required|in:Asisten Ahli,Lektor,Lektor Kepala,Guru Besar',
        ], [
            'C_KODE_PRODI.required' => 'Program studi wajib dipilih.',
            'C_KODE_PRODI.in' => 'Program studi tidak valid.',
            'JENIS_KELAMIN.required' => 'Jenis kelamin wajib dipilih.',
            'NO_HP.required' => 'No. HP wajib diisi.',
            'EMAIL.required' => 'Email wajib diisi.',
            'EMAIL.email' => 'Format email tidak valid.',
            'pangkat.required' => 'Pangkat wajib diisi.',
            'jabatan_fungsional.required' => 'Jabatan fungsional wajib dipilih.',
        ]);

        $profile = Helper::getCurrentDosenProfileByAuthUser();
        $kodeDosen = trim((string) ($profile->C_KODE_DOSEN ?? Helper::getKodeDosenFromUser()));

        if ($kodeDosen === '') {
            return redirect()->to('/')->with('dosen_profile_error', 'Kode dosen akun login tidak ditemukan.');
        }

        try {
            $payload = $this->buildKelengkapanProfilDosenPayload($request, $profile, $kodeDosen);
            $this->syncKelengkapanProfilDosen($payload, $kodeDosen);

            return redirect()->to('/')->with('dosen_profile_success', 'Kelengkapan profil berhasil disimpan.');
        } catch (Exception $e) {
            Log::error('kelengkapan_profil_post error', [
                'kode_dosen' => $kodeDosen,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return redirect()->to('/')->withInput()->with('dosen_profile_error', 'Kelengkapan profil gagal disimpan.');
        }
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

    public function detail_note($id)
    {
        $data = DB::table("trt_topik")
            ->select("*")
            ->where("topik_id", $id)
            ->get();
        return view("tugasakhir.dosen.detail_note", compact('data'));
    }

    public function note_update(Request $request, $id)
    {
        trt_topik::where("topik_id", $id)
            ->update([
                'note' => $request->note,
            ]);
        return redirect::to('dsn/request_pembimbing');
    }

    public function detail_pembimbing($id)
    {
        $data = DB::table('t_mst_dosen')
            ->select('*')
            ->where('C_KODE_DOSEN', $id)
            ->first();
        if (!$data) {
            $data = helper::getDosenRecordByKode($id);
        }
        if (!$data) {
            return response('Data dosen pembimbing tidak ditemukan.', 404);
        }

        $data_bimbingan1 = $this->getMahasiswaBimbinganByPeran($id, 'pembimbing_I_id', 'Pembimbing Utama');
        $data_bimbingan2 = $this->getMahasiswaBimbinganByPeran($id, 'pembimbing_II_id', 'Pembimbing Pendamping');
        $total = count($data_bimbingan1) + count($data_bimbingan2);

        $semuaBimbingan = $data_bimbingan1->merge($data_bimbingan2)->values();
        $data_bimbingan_aktif = $semuaBimbingan
            ->filter(function ($item) {
                return (string) ($item->status_bimbingan ?? '') !== '3';
            })
            ->sortBy(function ($item) {
                return $this->getStatusBimbinganSortOrder($item->status_bimbingan ?? null) . '|' . strtolower(trim((string) ($item->NAMA_MAHASISWA ?? '')));
            })
            ->values();
        $data_bimbingan_lulusan = $semuaBimbingan
            ->filter(function ($item) {
                return (string) ($item->status_bimbingan ?? '') === '3';
            })
            ->sortBy(function ($item) {
                return strtolower(trim((string) ($item->NAMA_MAHASISWA ?? '')));
            })
            ->values();

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
        return view('tugasakhir.dosen.detail_pembimbing', compact(
            'data',
            'total',
            'data_bimbingan_aktif',
            'data_bimbingan_lulusan',
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
        return view('tugasakhir.prodi.mail_inbox', compact('data', 'datax'));
    }

    public function mail_new()
    {
        $data = DB::table('trt_bimbingan')
            ->select('C_NPM')
            ->where('pembimbing_I_id', auth()->user()->name)
            ->orWhere('pembimbing_II_id', auth()->user()->name)
            ->get();

        $data2 = DB::table('t_mst_dosen')
            ->select('C_KODE_DOSEN')
            ->get();
        return view('tugasakhir.prodi.mail_new', compact('data', 'data2'));
    }

    public function pesanpost(Request $request)
    {
        if ($request->lampiran != null) {
            foreach ($request->lampiran as $lampiran) {
                $size = round($lampiran->getSize() / 1024);
                if ($size > 10240) {
                    session()->flash("error", "Setiap file tidak lebih dari 10MB, silahkan sediakan link alternatif.");
                    return redirect()->back();
                }
            }
        }
        $datapost = $request->all();
        $mstpesan = mst_pesan::create($datapost);
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



        return redirect::to('dsn/mail_sent');
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
        return view('tugasakhir.prodi.mail_sent', compact('data', 'datax'));
    }


    public function mail_read($id, $status)
    {
        $data = DB::table('mst_pesan')
            ->join('trt_konsultasi', 'mst_pesan.pesan_id', '=', 'trt_konsultasi.pesan_id')
            ->select('*')
            ->where('mst_pesan.pesan_id', $id)
            ->first();
        if (!$data) {
            return response('Data pesan tidak ditemukan.', 404);
        }
        trt_konsultasi::where(["pesan_id" => $id, "penerima_id" => auth()->user()->name])->update([
            "status_baca" => 1
        ]);
        return view('tugasakhir.prodi.mail_read', compact('data', 'status'));
    }

    public function request_pembimbing()
    {
        $data = trt_topik::join('t_mst_mahasiswa', 'trt_topik.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->join('mst_tmp_usulan', 'mst_tmp_usulan.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->where('trt_topik.status', 1)
            ->where('mst_tmp_usulan.pembimbing_I_id', Auth::user()->name)
            ->whereNotIn('t_mst_mahasiswa.C_NPM', trt_bimbingan::select("C_NPM"))
            ->orWhere('mst_tmp_usulan.pembimbing_II_id', Auth::user()->name)
            ->where('trt_topik.status', 1)
            ->whereNotIn('t_mst_mahasiswa.C_NPM', trt_bimbingan::select("C_NPM"))
            ->select('t_mst_mahasiswa.*', 'trt_topik.*', 'mst_tmp_usulan.*')
            ->get();



        return view('tugasakhir.dosen.request_pembimbing', compact("data"));
    }

    public function request_konfirmasi($status, $mahasiswa)
    {
        $data = trt_topik::join('t_mst_mahasiswa', 'trt_topik.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->join('mst_tmp_usulan', 'mst_tmp_usulan.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->where("t_mst_mahasiswa.C_NPM", $mahasiswa)
            ->where('trt_topik.status', 1)
            ->where('mst_tmp_usulan.pembimbing_I_id', Auth::user()->name)
            ->whereNotIn('t_mst_mahasiswa.C_NPM', trt_bimbingan::select("C_NPM"))
            ->orWhere('mst_tmp_usulan.pembimbing_II_id', Auth::user()->name)
            ->where("t_mst_mahasiswa.C_NPM", $mahasiswa)
            ->where('trt_topik.status', 1)
            ->whereNotIn('t_mst_mahasiswa.C_NPM', trt_bimbingan::select("C_NPM"))
            ->select('t_mst_mahasiswa.*', 'trt_topik.*', 'mst_tmp_usulan.*')
            ->first();
        if (!$data) {
            return redirect()->back()->with('error', 'Data permintaan pembimbing tidak ditemukan');
        }
        if ($data->pembimbing_I_id == Auth::user()->name) {
            mst_tmp_usulan::where(["C_NPM" => $mahasiswa, "pembimbing_I_id" => Auth::user()->name])->update([
                "pembimbing_I_status" => $status
            ]);
        } elseif ($data->pembimbing_II_id == Auth::user()->name) {
            mst_tmp_usulan::where(["C_NPM" => $mahasiswa, "pembimbing_II_id" => Auth::user()->name])->update([
                "pembimbing_II_status" => $status
            ]);
        }
        return redirect()->back();
    }

    // Hapus Usulan Judul
    public function hapus_usulan_judul($usulan_judul_id)
    {
        try {
            DB::table('trt_usulan_judul')
                ->where('trt_usulan_judul.usulan_judul_id', $usulan_judul_id)
                ->delete();
            return redirect::to('dsn/usul_judul')->with('status', 'success');
        } catch (Exception $exception) {
            return redirect::to('dsn/usul_judul')->with('status', 'error');
        }
    }

    // Halaman Show Pengumuman
    public function show_pengumuman($id)
    {
        $data = DB::table('mst_pengumuman')
            ->where('pengumuman_id', $id)
            ->first();
        if (!$data) {
            return response('Data pengumuman tidak ditemukan.', 404);
        }
        return view('tugasakhir.mhs.single_pengumuman', compact('data'));
    }

    // Halaman Menampilkan Semua Daftar Pengumuman
    public function pengumuman()
    {
        $data = mst_pengumuman::orderBy('last_update', 'desc')->get();
        return view('tugasakhir.mhs.detail_pengumuman', compact('data'));
    }

    // Cetak SK Pembimbing
    public function cetak_sk_pembimbing(Request $request)
    {
        $datapost = $request->all();
        $data_sk = DB::table('mst_sk_pembimbing')
            ->join('trt_bimbingan', 'mst_sk_pembimbing.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->select('*')
            ->where('mst_sk_pembimbing.nomor_sk', $datapost["nomor"])
            ->get();

        if ($data_sk->isEmpty()) {
            return response('Data surat SK pembimbing belum lengkap atau tidak ditemukan.', 404);
        }

        $tgl_ujian = helper::tgl_indo_lengkap(date('Y-m-d'));
        return view('tugasakhir.dosen.cetak_sk_pembimbing', compact('data_sk', 'tgl_ujian'));
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
        return view('tugasakhir.dosen.mail_reply', compact('data', 'data_reply'));
    }

    // Surat Ujian Proposal
    public static function surat_sk_proposal($pendaftaran_id, $nim)
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
                    $tipe_ujian = "Tugas Akhir";
                    $count_jam_ujian = strlen($jam_ujian);
                    if ($count_jam_ujian == 5) {
                        $waktu = $jam_ujian . "-" . sprintf('%02d', substr($jam_ujian, 0, 2) + 2) . ":30";
                    } else {
                        $waktu = $jam_ujian;
                    }
                    break;
            }
            $nim = $nim;
            $tgl_sekarang = helper::tgl_indo_lengkap(date('Y-m-d'));

            return view('tugasakhir.prodi.cetakskpenguji', compact("nim", "penguji", "bimbingan", "tipe_ujian", "tgl_ujian", "waktu", "ruangan", 'tgl_sekarang', 'tanggal', 'bulan', 'tahun'));
        } catch (Exception $error) {
            return redirect('dsn/jadwal_proposal');
        }
    }

    // SK Ujian Meja
    public function surat_sk_ujian_meja($nim)
    {

        $data = DB::table('mst_sk_penugasan')
            ->join('trt_bimbingan', 'mst_sk_penugasan.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->select('*')
            ->where("trt_bimbingan.C_NPM", $nim)
            ->first();

        if (!$data || !isset($data->bimbingan_id)) {
            return response('Data surat SK ujian meja tidak ditemukan.', 404);
        }

        $data_sk = DB::table('mst_sk_penugasan')
            ->join('trt_bimbingan', 'mst_sk_penugasan.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->join('trt_penguji', 'trt_penguji.C_NPM', '=', 'trt_bimbingan.C_NPM')
            ->join('trt_jadwal_ujian_per_mhs', 'trt_jadwal_ujian_per_mhs.C_NPM', '=', 'trt_bimbingan.C_NPM')
            ->join('trt_jadwal_ujian', 'trt_jadwal_ujian.id', '=', 'trt_jadwal_ujian_per_mhs.jadwal_ujian')
            ->join('mst_ruangan', 'mst_ruangan.id', '=', 'trt_jadwal_ujian_per_mhs.ruangan')
            ->select(['mst_sk_penugasan.created_at', 'mst_sk_penugasan.sk_penugasan_id', 'mst_sk_penugasan.nomor_sk', 'trt_bimbingan.pembimbing_I_id', "trt_bimbingan.pembimbing_II_id", "trt_penguji.ketua_sidang_id", "trt_penguji.penguji_I_id", "trt_penguji.penguji_II_id", "trt_penguji.penguji_III_id", "trt_penguji.C_NPM", "trt_jadwal_ujian.tgl_ujian", "trt_jadwal_ujian_per_mhs.jam_ujian", "mst_ruangan.nama_ruangan", "trt_jadwal_ujian.pendaftaran_id"])
            ->where('trt_bimbingan.bimbingan_id', $data->bimbingan_id)
            ->where('trt_penguji.tipe_ujian', 2)
            ->where('trt_jadwal_ujian.status', 2)
            ->get();

        if ($data_sk->isEmpty()) {
            return response('Data surat SK ujian meja belum lengkap atau tidak ditemukan.', 404);
        }

        return view('tugasakhir.fakultas.cetakskpenugasan', compact('data_sk'));
    }

    // Tanda Tangan

    public function tanda_tangan()
    {
        // Misalnya, C_KODE_DOSEN disimpan dalam session atau bisa diganti dengan cara lain sesuai kebutuhan
        $kodeDosen = auth()->user()->name;

        // Query untuk mendapatkan tanda tangan dari database
        $tandaTangan = DB::table('mst_tanda_tangan')
            ->where('C_KODE_DOSEN', $kodeDosen)
            ->first(); // Mengambil satu record

        // Melempar data ke view
        return view('tugasakhir.dosen.tanda_tangan', compact('tandaTangan'));
    }


    public function upload_ttd_post(Request $request)
    {
        try {
            $C_KODE_DOSEN = auth()->user()->name;
            $tanda_tangan = null;

            if ($request->hasFile('upload_ttd')) {
                $file = $request->file('upload_ttd');
                $tanda_tangan = file_get_contents($file->getRealPath());
            } elseif ($request->has('ttd_image')) {
                $dataUrl = $request->input('ttd_image');
                $tanda_tangan = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $dataUrl));
            }

            DB::table('mst_tanda_tangan')->updateOrInsert(
                ['C_KODE_DOSEN' => $C_KODE_DOSEN],
                [
                    'tanda_tangan' => $tanda_tangan,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Tanda tangan berhasil diunggah!',
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengunggah tanda tangan!',
            ]);
        }
    }

    public function honorarium()
    {
        $C_KODE_DOSEN = auth()->user()->name;
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

        return view('tugasakhir.dosen.honorarium', compact('data'));
    }


    public function honorarium_save_all_dosen(Request $request)
    {
        foreach ($request->honorariums as $honorarium) {
            if (isset($honorarium['status']) && $honorarium['status'] == "on") {
                $C_NPM = $honorarium['C_NPM'];
                $role = $honorarium['role'];

                // Determine which status field to update based on the role
                switch ($role) {
                    case 'Ketua Sidang':
                        DB::table('trt_honorium')
                            ->where('C_NPM', $C_NPM)
                            ->update(['KS_Stat' => 3]);
                        break;
                    case 'Pembimbing Utama':
                        DB::table('trt_honorium')
                            ->where('C_NPM', $C_NPM)
                            ->update(['PU_Stat' => 3]);
                        break;
                    case 'Pembimbing Pendamping':
                        DB::table('trt_honorium')
                            ->where('C_NPM', $C_NPM)
                            ->update(['PP_Stat' => 3]);
                        break;
                    case 'Penguji I':
                        DB::table('trt_honorium')
                            ->where('C_NPM', $C_NPM)
                            ->update(['P1_Stat' => 3]);
                        break;
                    case 'Penguji II':
                        DB::table('trt_honorium')
                            ->where('C_NPM', $C_NPM)
                            ->update(['P2_Stat' => 3]);
                        break;
                    case 'Penguji III':
                        DB::table('trt_honorium')
                            ->where('C_NPM', $C_NPM)
                            ->update(['P3_Stat' => 3]);
                        break;
                }
            }
        }

        return redirect()->back()->with('status', 'success')->with('message', 'Honorarium updated successfully.');
    }

    public function history_honorarium()
    {
        $C_KODE_DOSEN = auth()->user()->name;
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
            ->having('status', '=', 3) // Exclude records where status is 3
            ->get();

        return view('tugasakhir.dosen.history_honorarium', compact('data'));
    }

    public function set_session_status(Request $request)
    {
        $request->session()->flash('status', $request->status);
        $request->session()->flash('message', $request->message);

        return response()->json(['success' => true]);
    }

    protected function buildKelengkapanProfilDosenPayload(Request $request, $profile, $kodeDosen)
    {
        $now = Carbon::now();
        $profile = (array) $profile;

        $defaults = [
            'C_KODE_DOSEN' => $kodeDosen,
            'C_NIP' => null,
            'NAMA_DOSEN' => trim((string) ($profile['NAMA_DOSEN'] ?? auth()->user()->name ?? '')),
            'C_KODE_PRODI' => trim((string) ($profile['C_KODE_PRODI'] ?? '')),
            'jabatan_id' => null,
            'JENIS_KELAMIN' => null,
            'TEMPAT_LAHIR' => null,
            'TGL_LAHIR' => null,
            'kota' => null,
            'ALAMAT' => null,
            'NO_HP' => null,
            'website' => null,
            'pendidikan_terakhir' => null,
            'waktu_masuk' => null,
            'foto' => null,
            'jabatan_fungsional' => null,
            'ruang' => null,
            'user_id' => auth()->id(),
            'created_at' => $this->normalizeSqlDateTimeOrNull($profile['created_at'] ?? null) ?: $now,
            'updated_at' => $now,
            'C_KODE_KAB_KOTA' => '',
            'C_KODE_PROPINSI' => '',
            'KODE_POS' => '',
            'NO_TELP' => null,
            'EMAIL' => trim((string) ($profile['EMAIL'] ?? auth()->user()->email ?? '')),
            'GOLONGAN_DARAH' => '',
            'NO_KTP' => '',
            'C_KODE_AGAMA' => '',
            'NO_NPWP' => '',
            'NO_REK_BANK' => '',
            'ATAS_NAMA_REK' => '',
            'NAMA_BANK' => '',
            'NAMA_CAB_BANK' => '',
            'AKRONIM_DOSEN' => $this->generateAkronimDosen($profile['NAMA_DOSEN'] ?? auth()->user()->name ?? ''),
            'C_KODE_STATUS_IKATAN_KERJA' => '',
            'C_KODE_STATUS_BEBAN_KERJA_DOSEN' => '',
            'SEMESTER_DOSEN_MULAI' => '',
            'ADA_SERTIFIKAT_MENGAJAR' => '',
            'ADA_SURAT_IJIN_MENGAJAR' => '',
            'NIP_PNS' => '',
            'KODE_INSTANSI_INDUK' => '',
            'C_KODE_STATUS_AKTIF_DOSEN' => 'A',
            'SEMESTER_DOSEN_KELUAR' => '',
            'D_FOTO_DOSEN' => '',
            'F_AKTIF' => 1,
            'F_IS_C' => 0,
            'F_IS_U' => 0,
            'F_IS_D' => 0,
            'F_CHANGE_LOG' => 0,
        ];

        $payload = array_merge($defaults, $profile, [
            'C_KODE_DOSEN' => $kodeDosen,
            'C_NIP' => trim((string) ($profile['C_NIP'] ?? '')),
            'NAMA_DOSEN' => trim((string) ($profile['NAMA_DOSEN'] ?? auth()->user()->name ?? '')),
            'C_KODE_PRODI' => trim((string) $request->C_KODE_PRODI),
            'JENIS_KELAMIN' => trim((string) $request->JENIS_KELAMIN),
            'ALAMAT' => trim((string) ($profile['ALAMAT'] ?? '')),
            'NO_HP' => trim((string) $request->NO_HP),
            'EMAIL' => trim((string) $request->EMAIL),
            'website' => trim((string) $request->pangkat),
            'jabatan_fungsional' => trim((string) $request->jabatan_fungsional),
            'AKRONIM_DOSEN' => $this->generateAkronimDosen($profile['NAMA_DOSEN'] ?? auth()->user()->name ?? ''),
            'C_KODE_STATUS_AKTIF_DOSEN' => ((int) ($profile['F_AKTIF'] ?? 1) === 1 ? 'A' : 'N'),
            'updated_at' => $now,
        ]);

        unset($payload['id'], $payload['exists_t_mst_dosen'], $payload['exists_mig_t_mst_dosen'], $payload['nama_prodi']);

        $payload['TGL_LAHIR'] = $this->normalizeSqlDateOrNull($payload['TGL_LAHIR'] ?? null);
        $payload['waktu_masuk'] = $this->normalizeSqlDateOrNull($payload['waktu_masuk'] ?? null);
        $payload['created_at'] = $this->normalizeSqlDateTimeOrNull($payload['created_at'] ?? null) ?: $now;

        return $payload;
    }

    protected function syncKelengkapanProfilDosen(array $payload, $kodeDosen)
    {
        DB::beginTransaction();
        try {
            $updatePayload = $this->filterKelengkapanProfilDosenUpdatePayload($payload);

            $existsUtama = Schema::hasTable('t_mst_dosen')
                ? DB::table('t_mst_dosen')->where('C_KODE_DOSEN', $kodeDosen)->exists()
                : false;

            if ($existsUtama) {
                DB::table('t_mst_dosen')
                    ->where('C_KODE_DOSEN', $kodeDosen)
                    ->update($updatePayload);
            } elseif (Schema::hasTable('t_mst_dosen')) {
                DB::table('t_mst_dosen')->insert($payload);
            }

            $existsMigrasi = Schema::hasTable('mig_t_mst_dosen')
                ? DB::table('mig_t_mst_dosen')->where('C_KODE_DOSEN', $kodeDosen)->exists()
                : false;

            if ($existsMigrasi) {
                DB::table('mig_t_mst_dosen')
                    ->where('C_KODE_DOSEN', $kodeDosen)
                    ->update($updatePayload);
            } elseif (Schema::hasTable('mig_t_mst_dosen')) {
                DB::table('mig_t_mst_dosen')->insert($payload);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function filterKelengkapanProfilDosenUpdatePayload(array $payload)
    {
        return array_intersect_key($payload, array_flip([
            'C_KODE_DOSEN',
            'C_NIP',
            'NAMA_DOSEN',
            'C_KODE_PRODI',
            'JENIS_KELAMIN',
            'ALAMAT',
            'NO_HP',
            'EMAIL',
            'website',
            'jabatan_fungsional',
            'AKRONIM_DOSEN',
            'user_id',
            'C_KODE_STATUS_AKTIF_DOSEN',
            'F_AKTIF',
            'updated_at',
        ]));
    }

    protected function getMahasiswaBimbinganByPeran($kodeDosen, $kolomPembimbing, $peranPembimbing)
    {
        $query = DB::table('trt_bimbingan')
            ->leftJoin('t_mst_mahasiswa', 'trt_bimbingan.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->leftJoin('mst_sk_pembimbing', 'trt_bimbingan.bimbingan_id', '=', 'mst_sk_pembimbing.bimbingan_id')
            ->select(
                'trt_bimbingan.bimbingan_id',
                'trt_bimbingan.C_NPM',
                'trt_bimbingan.status_bimbingan',
                't_mst_mahasiswa.NAMA_MAHASISWA',
                'mst_sk_pembimbing.nomor_sk',
                'mst_sk_pembimbing.created_at as tanggal_sk'
            )
            ->where('trt_bimbingan.' . $kolomPembimbing, $kodeDosen)
            ->orderBy('t_mst_mahasiswa.NAMA_MAHASISWA', 'asc')
            ->distinct();

        if (Schema::hasTable('trt_kontak_mahasiswa')) {
            $query->leftJoin('trt_kontak_mahasiswa', 'trt_kontak_mahasiswa.C_NPM', '=', 'trt_bimbingan.C_NPM')
                ->addSelect('trt_kontak_mahasiswa.no_wa', 'trt_kontak_mahasiswa.id_telegram');
        }

        return $query->get()->map(function ($item) use ($peranPembimbing) {
            $item->peran_pembimbing = $peranPembimbing;
            $item->label_status_bimbingan = $this->getStatusBimbinganLabel($item->status_bimbingan ?? null);
            $item->kontak_mahasiswa = $this->formatKontakMahasiswa($item->no_wa ?? null, $item->id_telegram ?? null);
            return $item;
        });
    }

    protected function getStatusBimbinganLabel($status)
    {
        switch ((string) $status) {
            case '0':
                return 'Persiapan Proposal';
            case '1':
                return 'Persiapan Seminar Hasil';
            case '2':
                return 'Persiapan Ujian Meja';
            case '3':
                return 'Lulusan';
            case '4':
                return 'Non Aktif';
            default:
                return '-';
        }
    }

    protected function getStatusBimbinganSortOrder($status)
    {
        switch ((string) $status) {
            case '0':
                return '01';
            case '1':
                return '02';
            case '2':
                return '03';
            case '4':
                return '04';
            case '3':
                return '05';
            default:
                return '99';
        }
    }

    protected function formatKontakMahasiswa($noWa = null, $idTelegram = null)
    {
        $parts = [];
        $noWa = trim((string) $noWa);
        $idTelegram = trim((string) $idTelegram);

        if ($noWa !== '') {
            $parts[] = 'WA: ' . $noWa;
        }

        if ($idTelegram !== '') {
            $parts[] = 'Telegram: ' . $idTelegram;
        }

        return empty($parts) ? '-' : implode("\n", $parts);
    }

    protected function normalizeSqlDateOrNull($value)
    {
        $value = trim((string) $value);

        if ($value === '' || $value === '0000-00-00') {
            return null;
        }

        return $value;
    }

    protected function normalizeSqlDateTimeOrNull($value)
    {
        $value = trim((string) $value);

        if ($value === '' || $value === '0000-00-00 00:00:00') {
            return null;
        }

        return $value;
    }

    protected function generateAkronimDosen($namaDosen)
    {
        $parts = preg_split('/[^A-Za-z0-9]+/', strtoupper(trim((string) $namaDosen)));
        $akronim = '';

        foreach ($parts as $part) {
            if ($part !== '') {
                $akronim .= substr($part, 0, 1);
            }
        }

        $akronim = substr($akronim, 0, 20);
        return $akronim !== '' ? $akronim : substr(strtoupper(preg_replace('/\s+/', '', (string) $namaDosen)), 0, 20);
    }
}
