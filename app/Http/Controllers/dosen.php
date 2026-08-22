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
use App\RequestPembimbing;
use App\TrtJadwalUjian;
use App\TrtJadwalUjianPerMhs;
use App\TrtLevelPembimbing;
use App\TrtPengajuanDokumen;
use App\TrtPenguji;
use App\TrtSyaratUjian;
use App\TrtUsulanJudul;
use Barryvdh\DomPDF\Facade as PDF;
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

    private function prepareRekapNilaiViewData($data)
    {
        $data = collect($data);
        $dosenColumns = [
            'pembimbing_I_id',
            'pembimbing_II_id',
            'penguji_I_id',
            'penguji_II_id',
            'penguji_III_id',
            'ketua_sidang_id',
        ];

        $dosenIds = $data->flatMap(function ($row) use ($dosenColumns) {
            return collect($dosenColumns)->map(function ($column) use ($row) {
                return isset($row->{$column}) ? $row->{$column} : null;
            });
        })->filter()->unique()->values();

        $dosenByKode = $dosenIds->isEmpty()
            ? collect()
            : DB::table('t_mst_dosen')
                ->whereIn('C_KODE_DOSEN', $dosenIds)
                ->get()
                ->keyBy('C_KODE_DOSEN');

        $missingDosenIds = $dosenIds->reject(function ($kodeDosen) use ($dosenByKode) {
            return $dosenByKode->has($kodeDosen);
        });
        if ($missingDosenIds->isNotEmpty() && Schema::hasTable('mig_t_mst_dosen')) {
            DB::table('mig_t_mst_dosen')
                ->whereIn('C_KODE_DOSEN', $missingDosenIds)
                ->get()
                ->each(function ($dosen) use ($dosenByKode) {
                    $dosenByKode->put($dosen->C_KODE_DOSEN, $dosen);
                });
        }

        $regIds = $data->pluck('reg_id')->filter()->unique()->values();
        $penilaianLengkap = $regIds->isEmpty()
            ? collect()
            : trt_hasil::whereIn('reg_id', $regIds)
                ->whereNotNull('nilai_1')
                ->whereNotNull('nilai_2')
                ->whereNotNull('nilai_3')
                ->whereNotNull('nilai_4')
                ->whereNotNull('nilai_5')
                ->where('nilai_1', '>', 0)
                ->where('nilai_2', '>', 0)
                ->where('nilai_3', '>', 0)
                ->where('nilai_4', '>', 0)
                ->where('nilai_5', '>', 0)
                ->get(['reg_id', 'nidn'])
                ->mapWithKeys(function ($hasil) {
                    return [$hasil->reg_id . ':' . $hasil->nidn => true];
                });

        return compact('dosenByKode', 'penilaianLengkap');
    }

    public function back_to_prodi(Request $request)
    {
        $sourceUserId = $request->session()->get('login_as_source_user_id');
        $sourceUserLevel = (int) $request->session()->get('login_as_source_user_level', 0);

        if (empty($sourceUserId) || !in_array($sourceUserLevel, [1, 5], true)) {
            return redirect('/')->with('danger', 'Session login as tidak ditemukan.');
        }

        $sourceUser = DB::table('users')
            ->select('id', 'level')
            ->where('id', $sourceUserId)
            ->first();

        if (!$sourceUser || !in_array((int) $sourceUser->level, [1, 5], true)) {
            $request->session()->forget([
                'login_as_source_user_id',
                'login_as_source_user_name',
                'login_as_source_user_level',
            ]);
            return redirect('/')->with('danger', 'Akun asal tidak valid.');
        }

        Auth::loginUsingId($sourceUserId);
        $request->session()->regenerate();
        $request->session()->forget([
            'login_as_source_user_id',
            'login_as_source_user_name',
            'login_as_source_user_level',
        ]);

        return redirect('/')->with('success', 'Berhasil kembali ke akun asal.');
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
        return view('tugasakhir.dosen.detail_rekap_nilai_proposal', array_merge(
            compact('data', 'info'),
            $this->prepareRekapNilaiViewData($data)
        ));
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
        return view('tugasakhir.dosen.detail_rekap_nilai_proposal_history', array_merge(
            compact('data', 'info'),
            $this->prepareRekapNilaiViewData($data)
        ));
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
        return view('tugasakhir.dosen.detail_rekap_nilai_ujian_ta', array_merge(
            compact('data', 'info'),
            $this->prepareRekapNilaiViewData($data)
        ));
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
        return view('tugasakhir.dosen.detail_rekap_nilai_ujian_ta_history', array_merge(
            compact('data', 'info'),
            $this->prepareRekapNilaiViewData($data)
        ));
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
            ->join('t_mst_mahasiswa', 'trt_usulan_judul.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->leftJoin('mst_jenis_tugas_akhir', 'trt_usulan_judul.jenis_tugas_akhir_id', '=', 'mst_jenis_tugas_akhir.jenis_tugas_akhir_id')
            ->select('trt_usulan_judul.*', 't_mst_mahasiswa.NAMA_MAHASISWA', 'mst_jenis_tugas_akhir.kode_jenis_tugas_akhir')
            ->where('trt_usulan_judul.KODE_DOSEN', auth()->user()->name)
            ->get();
        return view('tugasakhir.dosen.usul_judul', compact('data'));
    }

    public function usul_judul_post(Request $request)
    {
        $request->merge([
            'usulan_judul' => $this->judulTanpaKodeJenisTugasAkhir($request->usulan_judul),
        ]);
        $this->validate($request, [
            'penerima_id' => 'required|array|min:1',
            'usulan_judul' => 'required|max:1000',
            'jenis_tugas_akhir_id' => 'required|exists:mst_jenis_tugas_akhir,jenis_tugas_akhir_id',
        ]);

        $data_mahasiswa_belum_ada_judul = DB::table('t_mst_mahasiswa as mahasiswa')
            ->leftJoin('trt_topik', 'mahasiswa.C_NPM', '=', 'trt_topik.C_NPM')
            ->whereNull('trt_topik.C_NPM')
            ->select('mahasiswa.C_NPM as name')
            ->get();
        foreach ($request->penerima_id as $value) {
            if ($value == "semua_mahasiswa") {
                foreach ($data_mahasiswa_belum_ada_judul as $value_2) {
                    TrtUsulanJudul::create([
                        "judul" => $request->usulan_judul,
                        "jenis_tugas_akhir_id" => $request->jenis_tugas_akhir_id,
                        "C_NPM" => $value_2->name,
                        "KODE_DOSEN" => auth()->user()->name,
                    ]);
                }
            } elseif (DB::table('t_mst_mahasiswa')->where('C_NPM', $value)->exists()) {
                TrtUsulanJudul::create([
                    "judul" => $request->usulan_judul,
                    "jenis_tugas_akhir_id" => $request->jenis_tugas_akhir_id,
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
            ->select('trt_bimbingan.C_NPM')
            ->where(function ($query) {
                $query->where('trt_bimbingan.pembimbing_I_id', auth()->user()->name)
                    ->orWhere('trt_bimbingan.pembimbing_II_id', auth()->user()->name);
            })
            ->get();

        $data_semua_mahasiswa = DB::table('t_mst_mahasiswa')
            ->select('C_NPM as name')
            ->get();
        $data_mahasiswa_belum_ada_judul = DB::table('t_mst_mahasiswa as mahasiswa')
            ->leftJoin('trt_topik', 'mahasiswa.C_NPM', '=', 'trt_topik.C_NPM')
            ->whereNull('trt_topik.C_NPM')
            ->select('mahasiswa.C_NPM as name')
            ->get();
        $data_mahasiswa_belum_menerima_usulan_judul = DB::table('t_mst_mahasiswa as mahasiswa')
            ->leftJoin('trt_usulan_judul', 'mahasiswa.C_NPM', '=', 'trt_usulan_judul.C_NPM')
            ->whereNull('trt_usulan_judul.C_NPM')
            ->select('mahasiswa.C_NPM as name')
            ->get();


        $data2 = DB::table('t_mst_dosen')
            ->select('C_KODE_DOSEN')
            ->get();
        $jenisTugasAkhir = DB::table('mst_jenis_tugas_akhir')
            ->orderBy('kode_jenis_tugas_akhir')
            ->get();
        return view('tugasakhir.dosen.add_usul_judul', compact('data', 'data2', 'data_semua_mahasiswa', 'data_mahasiswa_belum_ada_judul', 'data_mahasiswa_belum_menerima_usulan_judul', 'jenisTugasAkhir'));
    }

    private function judulTanpaKodeJenisTugasAkhir($judul)
    {
        return trim((string) preg_replace(
            '/^(?:\(\s*[A-Za-z]{2}\s*(?:-|_|\s|\/)\s*[A-Za-z0-9]{2,}\s*\)\s*)+/',
            '',
            trim((string) $judul)
        ));
    }

    // Halaman Hasili Ujian Proposal
    public function hasil_proposal()
    {
        $data = $this->assessmentCards(0, [2, 3, 4]);

        return view('tugasakhir.dosen.hasil_proposal', compact('data'));
    }

    public function rekap_hasil_proposal()
    {
        return $this->rekapHasilPenilaian(0, [2, 3, 4], 'Proposal', 'dsn/hasil_proposal');
    }
    // Akhir Halaman Hasil Ujian Proposal

    public function hasil_proposal_history()
    {
        $kode = auth()->user()->name;
        $data = DB::select("SELECT * FROM trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND trt_penguji.C_NPM = trt_bimbingan.C_NPM AND (trt_penguji.penguji_I_id = ? OR trt_penguji.penguji_II_id = ? OR trt_penguji.penguji_III_id = ? OR trt_penguji.ketua_sidang_id = ? OR trt_bimbingan.pembimbing_I_id = ? OR trt_bimbingan.pembimbing_II_id = ?) AND trt_reg.status = ? AND trt_bimbingan.status_bimbingan IN (?, ?)", [$kode, $kode, $kode, $kode, $kode, $kode, 0, 2, 3]);

        return view('tugasakhir.dosen.hasil_proposal_history', compact('data'));
    }

    private function assessmentStudentData($regid)
    {
        return DB::select('SELECT * FROM trt_reg, trt_bimbingan, t_mst_mahasiswa WHERE trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND t_mst_mahasiswa.C_NPM = trt_bimbingan.C_NPM AND trt_reg.reg_id = ?', [$regid]);
    }

    private function assessmentScores($assessment)
    {
        return [
            'nilai_1' => $assessment->nilai_1 ?? null,
            'nilai_2' => $assessment->nilai_2 ?? null,
            'nilai_3' => $assessment->nilai_3 ?? null,
            'nilai_4' => $assessment->nilai_4 ?? null,
            'nilai_5' => $assessment->nilai_5 ?? null,
            'saran' => $assessment->saran ?? null,
        ];
    }

    private function hasValidAssessmentScores(Request $request, array $ranges)
    {
        foreach ($ranges as $field => $range) {
            $value = str_replace(',', '.', trim((string) $request->input($field, '')));

            if (!is_numeric($value) || (float) $value < $range[0] || (float) $value > $range[1]) {
                return false;
            }
        }

        return true;
    }

    // Detail Halaman Hasil Ujian
    public function detailhasil_proposal($regid)
    {
        $kodeDosen = Helper::getKodeDosenForTrtHasil();
        $data_hasil = trt_hasil::where('reg_id', $regid)->where('nidn', $kodeDosen)->first();
        $data = $this->assessmentStudentData($regid);
        if (empty($data)) {
            return response('Data hasil ujian proposal tidak ditemukan.', 404);
        }
        $nilai = $this->assessmentScores($data_hasil);

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

        if (!$this->hasValidAssessmentScores($request, [
            'nilai_1' => [10, 15],
            'nilai_2' => [16, 25],
            'nilai_3' => [15, 20],
            'nilai_4' => [15, 20],
            'nilai_5' => [15, 20],
        ])) {
            return redirect()->back()->with('error', 'Lengkapi semua komponen nilai sesuai rentang penilaian sebelum menyimpan.');
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

    public function hasil_ujianmeja()
    {
        $data = $this->assessmentCards(2, [3, 4]);

        return view('tugasakhir.dosen.hasil_ujianmeja', compact('data'));
    }

    public function rekap_hasil_ujianmeja()
    {
        return $this->rekapHasilPenilaian(2, [3, 4], 'Ujian Akhir', 'dsn/hasil_ujianmeja');
    }

    private function rekapHasilPenilaian($tipeUjian, array $excludedBimbinganStatuses, $namaUjian, $backPath)
    {
        $data = $this->assessmentCards($tipeUjian, $excludedBimbinganStatuses)
            ->sortBy(function ($item) {
                return implode('|', [
                    $item->tanggal_ujian ?: '9999-12-31',
                    $this->getJamMulaiUjianSortKey($item->jam_ujian),
                    $item->nama_ruangan ?: '',
                    $item->NAMA_MAHASISWA,
                ]);
            })
            ->values();

        $rekapPerTanggal = $data->groupBy(function ($item) {
            return $item->tanggal_ujian ?: 'tanpa-jadwal';
        });

        return view('tugasakhir.dosen.rekap_hasil_penilaian', compact(
            'data',
            'rekapPerTanggal',
            'namaUjian',
            'backPath'
        ));
    }

    private function assessmentCards($tipeUjian, array $excludedBimbinganStatuses)
    {
        $kode = Helper::getKodeDosenForTrtHasil();
        $data = DB::table('trt_reg as rg')
            ->join('trt_bimbingan as tb', 'tb.bimbingan_id', '=', 'rg.bimbingan_id')
            ->join('trt_penguji as pu', function ($join) {
                $join->on('pu.C_NPM', '=', 'tb.C_NPM')
                    ->on('pu.tipe_ujian', '=', 'rg.status');
            })
            ->join('t_mst_mahasiswa as mhs', 'mhs.C_NPM', '=', 'tb.C_NPM')
            ->leftJoin('trt_hasil as hasil', function ($join) use ($kode) {
                $join->on('hasil.reg_id', '=', 'rg.reg_id')
                    ->where('hasil.nidn', '=', $kode);
            })
            ->where(function ($query) use ($kode) {
                $query->where('pu.penguji_I_id', $kode)
                    ->orWhere('pu.penguji_II_id', $kode)
                    ->orWhere('pu.penguji_III_id', $kode)
                    ->orWhere('pu.ketua_sidang_id', $kode)
                    ->orWhere('tb.pembimbing_I_id', $kode)
                    ->orWhere('tb.pembimbing_II_id', $kode);
            })
            ->where('rg.status', $tipeUjian)
            ->whereNotIn('tb.status_bimbingan', $excludedBimbinganStatuses)
            ->select([
                'rg.reg_id',
                'rg.pendaftaran_id',
                'tb.C_NPM',
                'mhs.NAMA_MAHASISWA',
                'mhs.D_FOTO_MAHASISWA',
                'mhs.JENIS_KELAMIN',
                'tb.judul',
                'tb.jenis_tugas_akhir_id',
                'tb.status_bimbingan',
                'tb.pembimbing_I_id',
                'tb.pembimbing_II_id',
                'pu.penguji_I_id',
                'pu.penguji_II_id',
                'pu.penguji_III_id',
                'pu.ketua_sidang_id',
                'hasil.nilai_id',
                'hasil.nilai_1',
                'hasil.nilai_2',
                'hasil.nilai_3',
                'hasil.nilai_4',
                'hasil.nilai_5',
                DB::raw('(SELECT ju.tgl_ujian FROM trt_jadwal_ujian_per_mhs AS jpm INNER JOIN trt_jadwal_ujian AS ju ON ju.id = jpm.jadwal_ujian WHERE jpm.C_NPM = tb.C_NPM AND ju.pendaftaran_id = rg.pendaftaran_id ORDER BY ju.tgl_ujian DESC, ju.id DESC LIMIT 1) AS tanggal_ujian'),
                DB::raw('(SELECT jpm.jam_ujian FROM trt_jadwal_ujian_per_mhs AS jpm INNER JOIN trt_jadwal_ujian AS ju ON ju.id = jpm.jadwal_ujian WHERE jpm.C_NPM = tb.C_NPM AND ju.pendaftaran_id = rg.pendaftaran_id ORDER BY ju.tgl_ujian DESC, ju.id DESC LIMIT 1) AS jam_ujian'),
                DB::raw('(SELECT ruangan.nama_ruangan FROM trt_jadwal_ujian_per_mhs AS jpm INNER JOIN trt_jadwal_ujian AS ju ON ju.id = jpm.jadwal_ujian LEFT JOIN mst_ruangan AS ruangan ON ruangan.id = jpm.ruangan WHERE jpm.C_NPM = tb.C_NPM AND ju.pendaftaran_id = rg.pendaftaran_id ORDER BY ju.tgl_ujian DESC, ju.id DESC LIMIT 1) AS nama_ruangan'),
            ])
            ->orderBy('tanggal_ujian', 'asc')
            ->orderBy('mhs.NAMA_MAHASISWA', 'asc')
            ->get();

        $kodeDosen = $data->flatMap(function ($item) {
            return [
                $item->pembimbing_I_id,
                $item->pembimbing_II_id,
                $item->penguji_I_id,
                $item->penguji_II_id,
                $item->penguji_III_id,
                $item->ketua_sidang_id,
            ];
        })->filter()->unique()->values();

        $namaDosen = DB::table('t_mst_dosen')
            ->whereIn('C_KODE_DOSEN', $kodeDosen)
            ->pluck('NAMA_DOSEN', 'C_KODE_DOSEN');

        $hasilPerReg = $data->pluck('reg_id')->filter()->unique()->isEmpty()
            ? collect()
            : trt_hasil::whereIn('reg_id', $data->pluck('reg_id')->filter()->unique()->values())
                ->get(['reg_id', 'nidn', 'nilai_1', 'nilai_2', 'nilai_3', 'nilai_4', 'nilai_5'])
                ->groupBy('reg_id');

        $peran = [
            'ketua_sidang_id' => 'Ketua Sidang',
            'penguji_I_id' => 'Penguji I',
            'penguji_II_id' => 'Penguji II',
            'penguji_III_id' => 'Penguji III',
            'pembimbing_I_id' => 'Pembimbing Utama',
            'pembimbing_II_id' => 'Pembimbing Pendamping',
        ];

        $data->transform(function ($item) use ($kode, $namaDosen, $peran, $hasilPerReg) {
            $item->peran_login = [];
            $item->highlight_roles = [];
            $item->tim_ujian = [];
            $item->tim_ujian_by_peran = [];
            $item->penilaian_lengkap_by_dosen = [];
            $item->penilaian_status_by_dosen = [];

            foreach ($hasilPerReg->get($item->reg_id, collect()) as $hasil) {
                $kodePenilai = trim((string) $hasil->nidn);
                $statusPenilaian = $this->isCompleteAssessment($hasil) ? 'complete' : 'incomplete';
                if (!isset($item->penilaian_status_by_dosen[$kodePenilai]) || $statusPenilaian === 'complete') {
                    $item->penilaian_status_by_dosen[$kodePenilai] = $statusPenilaian;
                }
                if ($statusPenilaian === 'complete') {
                    $item->penilaian_lengkap_by_dosen[$kodePenilai] = true;
                }
            }

            foreach ($peran as $field => $label) {
                $kodeTim = trim((string) $item->{$field});
                if ($kodeTim === '') {
                    continue;
                }

                $item->tim_ujian[] = [
                    'peran' => $label,
                    'nama' => $namaDosen->get($kodeTim, '--'),
                    'kode' => $kodeTim,
                ];
                $item->tim_ujian_by_peran[$field] = $namaDosen->get($kodeTim, '--');

                if ($kodeTim === $kode) {
                    $item->peran_login[] = $label;
                    $item->highlight_roles[$field] = true;
                }
            }

            $item->tanggal_ujian_label = $item->tanggal_ujian
                ? Carbon::parse($item->tanggal_ujian)->format('d/m/Y')
                : '';
            $item->jam_ujian_label = $item->jam_ujian
                ? substr((string) $item->jam_ujian, 0, 5)
                : '';
            $item->jadwal_ujian_label = trim($item->tanggal_ujian_label . ($item->jam_ujian_label ? ' pukul ' . $item->jam_ujian_label : ''));
            if ($item->jadwal_ujian_label === '') {
                $item->jadwal_ujian_label = 'Jadwal belum tersedia';
            }

            if (empty($item->nilai_id)) {
                $item->status_penilaian = 'Belum menilai';
                $item->status_penilaian_class = 'pending';
                $item->status_penilaian_icon = 'fa-clock-o';
            } else {
                $belumLengkap = !$this->isCompleteAssessment((object) [
                    'nilai_1' => $item->nilai_1,
                    'nilai_2' => $item->nilai_2,
                    'nilai_3' => $item->nilai_3,
                    'nilai_4' => $item->nilai_4,
                    'nilai_5' => $item->nilai_5,
                ]);

                $item->status_penilaian = $belumLengkap ? 'Belum lengkap' : 'Sudah menilai';
                $item->status_penilaian_class = $belumLengkap ? 'incomplete' : 'complete';
                $item->status_penilaian_icon = $belumLengkap ? 'fa-exclamation-circle' : 'fa-check-circle';
            }
            $item->foto_url = Helper::mahasiswaPhotoUrl($item->D_FOTO_MAHASISWA, $item->JENIS_KELAMIN);
            $item->boleh_menilai = !empty($item->peran_login);

            return $item;
        });

        return $data->sortBy(function ($item) {
            return implode('|', [
                $item->tanggal_ujian ?: '9999-12-31',
                $this->getJamMulaiUjianSortKey($item->jam_ujian),
                $item->nama_ruangan ?: '',
                $item->NAMA_MAHASISWA,
            ]);
        })->values();
    }

    private function isCompleteAssessment($hasil)
    {
        foreach (['nilai_1', 'nilai_2', 'nilai_3', 'nilai_4', 'nilai_5'] as $field) {
            if ($hasil->{$field} === null || (float) $hasil->{$field} <= 0) {
                return false;
            }
        }

        return true;
    }

    private function getJamMulaiUjianSortKey($jamUjian)
    {
        $jamUjian = trim((string) $jamUjian);
        if (preg_match('/([0-2]?\d)[:.]([0-5]\d)/', $jamUjian, $matches)) {
            return sprintf('%02d:%02d', $matches[1], $matches[2]);
        }

        return $jamUjian;
    }

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
        $data = $this->assessmentStudentData($regid);
        if (empty($data)) {
            return response('Data hasil ujian meja tidak ditemukan.', 404);
        }
        $nilai = $this->assessmentScores($data_hasil);

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

        if (!$this->hasValidAssessmentScores($request, [
            'nilai_1' => [6, 10],
            'nilai_2' => [10, 15],
            'nilai_3' => [15, 20],
            'nilai_4' => [20, 30],
            'nilai_5' => [20, 25],
        ])) {
            return redirect()->back()->with('error', 'Lengkapi semua komponen nilai sesuai rentang penilaian sebelum menyimpan.');
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
        $data = $this->lecturerExamSchedule(0);

        return view('tugasakhir.dosen.jadwal_proposal', compact('data'));
    }
    // Akhir Halaman Jadwal Proposal

    // Halaman Jadwal Ujian Meja
    public function jadwal_ujianmeja()
    {
        $data = $this->lecturerExamSchedule(2);

        return view('tugasakhir.dosen.jadwal_ujianmeja', compact('data'));
    }
    // Akhir Halaman Ujian Meja

    /**
     * Fetch a lecturer's exam schedule without per-row database lookups in the view.
     */
    private function lecturerExamSchedule($tipeUjian)
    {
        $kode = auth()->user()->name;
        $lecturerNims = DB::table('trt_bimbingan')
            ->select('C_NPM')
            ->where('pembimbing_I_id', $kode)
            ->union(
                DB::table('trt_bimbingan')
                    ->select('C_NPM')
                    ->where('pembimbing_II_id', $kode)
            )
            ->union(
                DB::table('trt_penguji')
                    ->select('C_NPM')
                    ->where('tipe_ujian', $tipeUjian)
                    ->where(function ($query) use ($kode) {
                        $query->where('penguji_I_id', $kode)
                            ->orWhere('penguji_II_id', $kode)
                            ->orWhere('penguji_III_id', $kode)
                            ->orWhere('ketua_sidang_id', $kode);
                    })
            );

        $data = DB::table('trt_reg as rg')
            ->join('trt_bimbingan as tb', 'tb.bimbingan_id', '=', 'rg.bimbingan_id')
            ->join('trt_penguji as pu', function ($join) {
                $join->on('pu.C_NPM', '=', 'tb.C_NPM')
                    ->on('pu.tipe_ujian', '=', 'rg.status');
            })
            ->join('t_mst_mahasiswa as mhs', 'mhs.C_NPM', '=', 'tb.C_NPM')
            ->join('trt_jadwal_ujian_per_mhs as jpm', 'jpm.C_NPM', '=', 'tb.C_NPM')
            ->join('trt_jadwal_ujian as ju', function ($join) {
                $join->on('ju.id', '=', 'jpm.jadwal_ujian')
                    ->on('ju.pendaftaran_id', '=', 'rg.pendaftaran_id');
            })
            ->join('mst_ruangan as ruangan', 'ruangan.id', '=', 'jpm.ruangan')
            ->whereIn('tb.C_NPM', $lecturerNims)
            ->where('rg.status', $tipeUjian)
            ->select([
                'rg.pendaftaran_id',
                'tb.C_NPM',
                'mhs.NAMA_MAHASISWA',
                'rg.status as tipe_ujian',
                'ruangan.nama_ruangan',
                'jpm.jam_ujian',
                'ju.tgl_ujian',
                'tb.pembimbing_I_id',
                'tb.pembimbing_II_id',
                'pu.penguji_I_id',
                'pu.penguji_II_id',
                'pu.penguji_III_id',
                'pu.ketua_sidang_id',
                'pu.nomor_sk as nomor_sk_proposal',
            ])
            ->orderBy('ju.tgl_ujian', 'desc')
            ->orderBy('jpm.jam_ujian', 'asc')
            ->get();

        $kodeDosen = $data->flatMap(function ($item) {
            return [
                $item->pembimbing_I_id,
                $item->pembimbing_II_id,
                $item->penguji_I_id,
                $item->penguji_II_id,
                $item->penguji_III_id,
                $item->ketua_sidang_id,
            ];
        })->filter()->unique()->values();

        $namaDosen = DB::table('t_mst_dosen')
            ->whereIn('C_KODE_DOSEN', $kodeDosen)
            ->pluck('NAMA_DOSEN', 'C_KODE_DOSEN');

        $nimDenganSkUjianMeja = collect();
        if ((int) $tipeUjian === 2 && $data->isNotEmpty()) {
            $nimDenganSkUjianMeja = DB::table('mst_sk_penugasan as sk')
                ->join('trt_bimbingan as tb', 'tb.bimbingan_id', '=', 'sk.bimbingan_id')
                ->whereIn('tb.C_NPM', $data->pluck('C_NPM')->unique()->values())
                ->pluck('tb.C_NPM')
                ->flip();
        }

        $data->transform(function ($item) use ($namaDosen, $nimDenganSkUjianMeja, $tipeUjian) {
            foreach ([
                'pembimbing_I_id',
                'pembimbing_II_id',
                'penguji_I_id',
                'penguji_II_id',
                'penguji_III_id',
                'ketua_sidang_id',
            ] as $field) {
                $item->{$field . '_nama'} = $namaDosen->get($item->{$field}, '--');
            }

            $item->memiliki_sk = (int) $tipeUjian === 0
                ? trim((string) $item->nomor_sk_proposal) !== ''
                : $nimDenganSkUjianMeja->has($item->C_NPM);

            return $item;
        });

        return $data;
    }

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

    public function profil()
    {
        $profil = Helper::getCurrentDosenProfileByAuthUser();

        return view('tugasakhir.dosen.profil', compact('profil'));
    }

    public function kelengkapan_profil_post(Request $request)
    {
        $this->validate($request, [
            'C_KODE_PRODI' => 'required|in:55201,57201',
            'JENIS_KELAMIN' => 'required|in:Pria,Wanita',
            'NO_HP' => 'required|max:15',
            'EMAIL' => 'required|email|max:50',
            'pangkat' => 'required|max:100',
            'jabatan_fungsional' => 'required|in:Asisten Ahli,Lektor,Lektor Kepala,Guru Besar',
            'foto_dosen' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ], [
            'C_KODE_PRODI.required' => 'Program studi wajib dipilih.',
            'C_KODE_PRODI.in' => 'Program studi tidak valid.',
            'JENIS_KELAMIN.required' => 'Jenis kelamin wajib dipilih.',
            'NO_HP.required' => 'No. HP wajib diisi.',
            'EMAIL.required' => 'Email wajib diisi.',
            'EMAIL.email' => 'Format email tidak valid.',
            'pangkat.required' => 'Pangkat wajib diisi.',
            'jabatan_fungsional.required' => 'Jabatan fungsional wajib dipilih.',
            'foto_dosen.image' => 'Foto profil dosen harus berupa gambar.',
            'foto_dosen.uploaded' => 'Upload foto profil dosen gagal diproses server. Coba gunakan file yang lebih kecil lalu unggah kembali.',
            'foto_dosen.mimes' => 'Foto profil dosen harus berformat JPEG, JPG, PNG, atau WebP.',
            'foto_dosen.max' => 'Ukuran foto profil dosen maksimal 2 MB.',
        ]);

        $profile = Helper::getCurrentDosenProfileByAuthUser();
        $kodeDosen = trim((string) ($profile->C_KODE_DOSEN ?? Helper::getKodeDosenFromUser()));

        if ($kodeDosen === '') {
            return redirect()->to('/')->with('dosen_profile_error', 'Kode dosen akun login tidak ditemukan.');
        }

        try {
            $payload = $this->buildKelengkapanProfilDosenPayload($request, $profile, $kodeDosen);
            if ($request->hasFile('foto_dosen')) {
                $payload['D_FOTO_DOSEN'] = $request->file('foto_dosen')->store('dosen', 'public');
            }
            $this->syncKelengkapanProfilDosen($payload, $kodeDosen);

            return redirect()->to($this->dosenProfileRedirectPath($request))
                ->with('dosen_profile_success', 'Profil dosen berhasil disimpan.');
        } catch (Exception $e) {
            Log::error('kelengkapan_profil_post error', [
                'kode_dosen' => $kodeDosen,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return redirect()->to($this->dosenProfileRedirectPath($request))
                ->withInput()->with('dosen_profile_error', 'Profil dosen gagal disimpan.');
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

        $laporanAktifByNim = collect();
        if (Schema::hasTable('trt_laporan_mahasiswa')) {
            $laporanAktifByNim = DB::table('trt_laporan_mahasiswa')
                ->where('C_KODE_DOSEN', $id)
                ->whereIn('status', ['baru', 'ditinjau'])
                ->orderBy('updated_at', 'desc')
                ->get()
                ->keyBy('C_NPM');
        }

        $bimbinganAktif = DB::table('trt_bimbingan');
        $ppropI = (clone $bimbinganAktif)
            ->where('pembimbing_I_id', $id)
            ->where('status_bimbingan', 0)
            ->get();
        $ppropII = (clone $bimbinganAktif)
            ->where('pembimbing_II_id', $id)
            ->where('status_bimbingan', 0)
            ->get();
        $phasilI = (clone $bimbinganAktif)
            ->where('pembimbing_I_id', $id)
            ->where('status_bimbingan', 1)
            ->get();
        $phasilII = (clone $bimbinganAktif)
            ->where('pembimbing_II_id', $id)
            ->where('status_bimbingan', 1)
            ->get();
        $pmejaI = (clone $bimbinganAktif)
            ->where('pembimbing_I_id', $id)
            ->where('status_bimbingan', 2)
            ->get();
        $pmejaII = (clone $bimbinganAktif)
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
        $kategoriLaporanMahasiswa = $this->kategoriLaporanMahasiswa();
        $canLaporKeProdi = (string) $id === (string) auth()->user()->name;
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
            'alumniII',
            'laporanAktifByNim',
            'kategoriLaporanMahasiswa',
            'canLaporKeProdi'
        ));
    }

    public function laporan_mahasiswa()
    {
        if (!Schema::hasTable('trt_laporan_mahasiswa')) {
            return redirect()->to('dsn/detail_pembimbing/' . auth()->user()->name)
                ->with('error', 'Fitur laporan mahasiswa belum tersedia.');
        }

        $laporan = $this->queryLaporanMahasiswaDosen()
            ->orderBy('trt_laporan_mahasiswa.updated_at', 'desc')
            ->get();

        return view('tugasakhir.dosen.laporan_mahasiswa', compact('laporan'));
    }

    public function laporan_mahasiswa_store(Request $request)
    {
        if (!Schema::hasTable('trt_laporan_mahasiswa') || !Schema::hasTable('trt_laporan_mahasiswa_pesan')) {
            return redirect()->back()->with('error', 'Fitur laporan mahasiswa belum tersedia.');
        }

        $this->validate($request, [
            'C_NPM' => 'required|max:15',
            'kategori' => 'required|in:' . implode(',', array_keys($this->kategoriLaporanMahasiswa())),
            'perihal' => 'required|string|max:255',
            'uraian' => 'required|string|min:10|max:5000',
        ]);

        $nim = trim((string) $request->C_NPM);
        $kodeDosen = trim((string) auth()->user()->name);
        $bimbingan = DB::table('trt_bimbingan')
            ->where('trt_bimbingan.C_NPM', $nim)
            ->where(function ($query) use ($kodeDosen) {
                $query->where('trt_bimbingan.pembimbing_I_id', $kodeDosen)
                    ->orWhere('trt_bimbingan.pembimbing_II_id', $kodeDosen);
            })
            ->where('trt_bimbingan.status_bimbingan', '<>', 3)
            ->first();

        $mahasiswa = DB::table('t_mst_mahasiswa')
            ->select('C_NPM', 'C_KODE_PRODI')
            ->where('C_NPM', $nim)
            ->first();

        if (!$bimbingan || !$mahasiswa || trim((string) $mahasiswa->C_KODE_PRODI) === '') {
            return redirect()->back()->withInput()->with('error', 'Mahasiswa bimbingan aktif tidak ditemukan atau program studi belum tersedia.');
        }

        $laporanAktif = DB::table('trt_laporan_mahasiswa')
            ->where('C_NPM', $nim)
            ->where('C_KODE_DOSEN', $kodeDosen)
            ->whereIn('status', ['baru', 'ditinjau'])
            ->orderBy('updated_at', 'desc')
            ->first();

        if ($laporanAktif) {
            return redirect()->to('dsn/laporan_mahasiswa/' . $laporanAktif->laporan_mahasiswa_id)
                ->with('error', 'Masih ada laporan aktif untuk mahasiswa ini. Lanjutkan pada ruang diskusi yang sama.');
        }

        $laporanId = DB::transaction(function () use ($request, $mahasiswa, $bimbingan, $kodeDosen) {
            return DB::table('trt_laporan_mahasiswa')->insertGetId([
                'C_NPM' => $mahasiswa->C_NPM,
                'C_KODE_DOSEN' => $kodeDosen,
                'C_KODE_PRODI' => $mahasiswa->C_KODE_PRODI,
                'bimbingan_id' => $bimbingan->bimbingan_id,
                'kategori' => $request->kategori,
                'perihal' => trim((string) $request->perihal),
                'uraian' => trim((string) $request->uraian),
                'status' => 'baru',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        });

        return redirect()->to('dsn/laporan_mahasiswa/' . $laporanId)
            ->with('success', 'Laporan berhasil dikirim ke Program Studi.');
    }

    public function laporan_mahasiswa_detail($id)
    {
        $laporan = $this->findLaporanMahasiswaDosen($id);
        if (!$laporan) {
            return response('Laporan mahasiswa tidak ditemukan.', 404);
        }

        $pesan = DB::table('trt_laporan_mahasiswa_pesan')
            ->where('laporan_mahasiswa_id', $laporan->laporan_mahasiswa_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('tugasakhir.dosen.laporan_mahasiswa_detail', compact('laporan', 'pesan'));
    }

    public function laporan_mahasiswa_pesan_post(Request $request, $id)
    {
        $laporan = $this->findLaporanMahasiswaDosen($id);
        if (!$laporan) {
            return response('Laporan mahasiswa tidak ditemukan.', 404);
        }

        if ($laporan->status === 'selesai') {
            return redirect()->back()->with('error', 'Laporan telah selesai. Hubungi Prodi untuk membuat tindak lanjut baru bila diperlukan.');
        }

        $this->validate($request, [
            'pesan' => 'required|string|min:3|max:5000',
        ]);

        DB::transaction(function () use ($request, $laporan) {
            DB::table('trt_laporan_mahasiswa_pesan')->insert([
                'laporan_mahasiswa_id' => $laporan->laporan_mahasiswa_id,
                'pengirim_user_id' => auth()->id(),
                'pengirim_peran' => 'dosen',
                'nama_pengirim' => trim((string) (auth()->user()->name ?? 'Dosen')),
                'isi_pesan' => trim((string) $request->pesan),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            DB::table('trt_laporan_mahasiswa')
                ->where('laporan_mahasiswa_id', $laporan->laporan_mahasiswa_id)
                ->update(['updated_at' => Carbon::now()]);
        });

        return redirect()->back()->with('success', 'Pesan untuk Program Studi berhasil dikirim.');
    }

    protected function queryLaporanMahasiswaDosen()
    {
        return DB::table('trt_laporan_mahasiswa')
            ->join('t_mst_mahasiswa', 't_mst_mahasiswa.C_NPM', '=', 'trt_laporan_mahasiswa.C_NPM')
            ->leftJoin('trt_prodi', 'trt_prodi.kode_prodi', '=', 'trt_laporan_mahasiswa.C_KODE_PRODI')
            ->select(
                'trt_laporan_mahasiswa.*',
                't_mst_mahasiswa.NAMA_MAHASISWA',
                'trt_prodi.nama as nama_prodi'
            )
            ->where('trt_laporan_mahasiswa.C_KODE_DOSEN', auth()->user()->name);
    }

    protected function findLaporanMahasiswaDosen($id)
    {
        return $this->queryLaporanMahasiswaDosen()
            ->where('trt_laporan_mahasiswa.laporan_mahasiswa_id', $id)
            ->first();
    }

    protected function kategoriLaporanMahasiswa()
    {
        return [
            'bimbingan' => 'Proses Bimbingan',
            'kehadiran' => 'Kehadiran atau Respons',
            'administrasi' => 'Administrasi Akademik',
            'etik' => 'Etik atau Perilaku',
            'lainnya' => 'Lainnya',
        ];
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
            ->select('trt_bimbingan.C_NPM')
            ->where(function ($query) {
                $query->where('trt_bimbingan.pembimbing_I_id', auth()->user()->name)
                    ->orWhere('trt_bimbingan.pembimbing_II_id', auth()->user()->name);
            })
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
            ->whereNotIn('t_mst_mahasiswa.C_NPM', trt_bimbingan::select("C_NPM"))
            ->where(function ($query) {
                $query->where('mst_tmp_usulan.pembimbing_I_id', Auth::user()->name)
                    ->orWhere('mst_tmp_usulan.pembimbing_II_id', Auth::user()->name);
            })
            ->select('t_mst_mahasiswa.*', 'trt_topik.*', 'mst_tmp_usulan.*')
            ->get();

        $topikIds = $data->pluck('topik_id')->filter()->unique()->values();
        $requestPembimbingByTopik = $topikIds->isEmpty()
            ? collect()
            : RequestPembimbing::whereIn('topik', $topikIds)->get()->groupBy('topik');

        $bidangIlmuIds = $requestPembimbingByTopik->flatten(1)
            ->pluck('bidang_ilmu')
            ->filter()
            ->unique()
            ->values();
        $bidangIlmuById = $bidangIlmuIds->isEmpty()
            ? collect()
            : mst_bidangilmu::whereIn('bidangilmu_id', $bidangIlmuIds)
                ->pluck('bidang_ilmu', 'bidangilmu_id');

        $dosenIds = $data->flatMap(function ($row) {
            return [$row->pembimbing_I_id, $row->pembimbing_II_id];
        })->filter()->unique()->values();
        $dosenByKode = $dosenIds->isEmpty()
            ? collect()
            : DB::table('t_mst_dosen')
                ->whereIn('C_KODE_DOSEN', $dosenIds)
                ->get()
                ->keyBy('C_KODE_DOSEN');

        $missingDosenIds = $dosenIds->reject(function ($kodeDosen) use ($dosenByKode) {
            return $dosenByKode->has($kodeDosen);
        });
        if ($missingDosenIds->isNotEmpty() && Schema::hasTable('mig_t_mst_dosen')) {
            DB::table('mig_t_mst_dosen')
                ->whereIn('C_KODE_DOSEN', $missingDosenIds)
                ->get()
                ->each(function ($dosen) use ($dosenByKode) {
                    $dosenByKode->put($dosen->C_KODE_DOSEN, $dosen);
                });
        }

        return view('tugasakhir.dosen.request_pembimbing', compact(
            'data',
            'requestPembimbingByTopik',
            'bidangIlmuById',
            'dosenByKode'
        ));
    }

    public function request_konfirmasi($status, $mahasiswa)
    {
        $data = trt_topik::join('t_mst_mahasiswa', 'trt_topik.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->join('mst_tmp_usulan', 'mst_tmp_usulan.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->where("t_mst_mahasiswa.C_NPM", $mahasiswa)
            ->where('trt_topik.status', 1)
            ->whereNotIn('t_mst_mahasiswa.C_NPM', trt_bimbingan::select("C_NPM"))
            ->where(function ($query) {
                $query->where('mst_tmp_usulan.pembimbing_I_id', Auth::user()->name)
                    ->orWhere('mst_tmp_usulan.pembimbing_II_id', Auth::user()->name);
            })
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
        return redirect('sk_pembimbing/' . str_replace('/', '', (string) $request->input('nomor')));
    }

    public function cetak_sk_pembimbing_pdf(Request $request)
    {
        return redirect('sk_pembimbing_pdf/' . str_replace('/', '', (string) $request->input('nomor')));
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

    protected function getHonorariumAssignmentsForDosen($kodeDosen)
    {
        $roles = [
            'KS' => ['label' => 'Ketua Sidang', 'amount' => 'KS_H', 'status' => 'KS_Stat'],
            'PU' => ['label' => 'Pembimbing Utama', 'amount' => 'PU_H', 'status' => 'PU_Stat'],
            'PP' => ['label' => 'Pembimbing Pendamping', 'amount' => 'PP_H', 'status' => 'PP_Stat'],
            'P1' => ['label' => 'Penguji I', 'amount' => 'P1_H', 'status' => 'P1_Stat'],
            'P2' => ['label' => 'Penguji II', 'amount' => 'P2_H', 'status' => 'P2_Stat'],
            'P3' => ['label' => 'Penguji III', 'amount' => 'P3_H', 'status' => 'P3_Stat'],
        ];

        $records = DB::table('trt_honorium')
            ->where(function ($query) use ($kodeDosen) {
                $query->where('KS', $kodeDosen)
                    ->orWhere('PU', $kodeDosen)
                    ->orWhere('PP', $kodeDosen)
                    ->orWhere('P1', $kodeDosen)
                    ->orWhere('P2', $kodeDosen)
                    ->orWhere('P3', $kodeDosen);
            })
            ->orderBy('date', 'desc')
            ->orderBy('C_NPM')
            ->get();

        $jumlahSanksiByTanggal = $records->pluck('date')
            ->filter(function ($tanggal) {
                return preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $tanggal);
            })
            ->unique()
            ->mapWithKeys(function ($tanggal) {
                return [$tanggal => $this->jumlahSanksiPembayaranHonorariumDosenPadaTanggal($tanggal)];
            });

        $assignments = collect();
        foreach ($records as $record) {
            $penyesuaianHonor = $this->penyesuaianHonorPembimbingDosen(
                $record,
                (float) $jumlahSanksiByTanggal->get((string) $record->date, 0)
            );
            foreach ($roles as $role => $definition) {
                if ((string) $record->{$role} !== (string) $kodeDosen) {
                    continue;
                }

                $honorAwal = isset($penyesuaianHonor['base_amounts'][$role])
                    ? (float) $penyesuaianHonor['base_amounts'][$role]
                    : (float) $record->{$definition['amount']};
                $honorAkhir = isset($penyesuaianHonor['amounts'][$role])
                    ? (float) $penyesuaianHonor['amounts'][$role]
                    : (float) $record->{$definition['amount']};
                $assignment = clone $record;
                $assignment->role = $definition['label'];
                $assignment->base_amount = $honorAwal;
                $assignment->adjustment_amount = $honorAkhir - $honorAwal;
                $assignment->amount = $honorAkhir;
                $assignment->adjustment_note = isset($penyesuaianHonor['notes'][$role])
                    ? $penyesuaianHonor['notes'][$role]
                    : '';
                $assignment->status = $record->{$definition['status']};
                $assignments->push($assignment);
            }
        }

        return $assignments;
    }

    protected function jumlahSanksiPembayaranHonorariumDosenPadaTanggal($tanggal)
    {
        if (!Schema::hasTable('mst_sanksi_pembayaran') || !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $tanggal)) {
            return 0;
        }

        $sanksi = DB::table('mst_sanksi_pembayaran')
            ->where('tanggal_mulai', '<=', $tanggal)
            ->where('tanggal_selesai', '>=', $tanggal)
            ->orderBy('tanggal_mulai', 'desc')
            ->orderBy('id_sanksi_pembayaran', 'desc')
            ->first();

        return $sanksi ? (float) $sanksi->jumlah_sanksi : 0;
    }

    protected function penyesuaianHonorPembimbingDosen($honorarium, $jumlahSanksi)
    {
        $roles = [
            'KS' => ['amount' => 'KS_H'],
            'PU' => ['amount' => 'PU_H'],
            'PP' => ['amount' => 'PP_H'],
            'P1' => ['amount' => 'P1_H'],
            'P2' => ['amount' => 'P2_H'],
            'P3' => ['amount' => 'P3_H'],
        ];
        $amounts = [];
        $baseAmounts = [];
        $notes = [];
        foreach ($roles as $role => $definition) {
            $amounts[$role] = (float) $honorarium->{$definition['amount']};
            $baseAmounts[$role] = $amounts[$role];
            $notes[$role] = '';
        }

        $jumlahSanksi = max(0, (float) $jumlahSanksi);
        if ($jumlahSanksi <= 0) {
            return [
                'amounts' => $amounts,
                'base_amounts' => $baseAmounts,
                'notes' => $notes,
            ];
        }

        $adaPembimbingUtama = trim((string) $honorarium->PU) !== '';
        $adaPembimbingPendamping = trim((string) $honorarium->PP) !== '';
        $pembimbingUtamaHadir = !isset($honorarium->pembimbing_utama_hadir)
            || (int) $honorarium->pembimbing_utama_hadir === 1;
        $pembimbingPendampingHadir = !isset($honorarium->pembimbing_pendamping_hadir)
            || (int) $honorarium->pembimbing_pendamping_hadir === 1;

        if ($adaPembimbingUtama && !$pembimbingUtamaHadir) {
            $potongan = min($jumlahSanksi, $amounts['PU']);
            $amounts['PU'] -= $potongan;
            $notes['PU'] = 'Dikurangi ' . Helper::formatRupiah($potongan) . ' karena Pembimbing Utama tidak hadir.';

            if ($adaPembimbingPendamping && $pembimbingPendampingHadir) {
                $amounts['PP'] += $potongan;
                $notes['PP'] = trim($notes['PP'] . ' Ditambah ' . Helper::formatRupiah($potongan) . ' dari sanksi Pembimbing Utama.');
            }
        }

        if ($adaPembimbingPendamping && !$pembimbingPendampingHadir) {
            $potongan = min($jumlahSanksi, $amounts['PP']);
            $amounts['PP'] -= $potongan;
            $notes['PP'] = trim($notes['PP'] . ' Dikurangi ' . Helper::formatRupiah($potongan) . ' karena Pembimbing Pendamping tidak hadir.');

            if ($adaPembimbingUtama && $pembimbingUtamaHadir) {
                $amounts['PU'] += $potongan;
                $notes['PU'] = trim($notes['PU'] . ' Ditambah ' . Helper::formatRupiah($potongan) . ' dari sanksi Pembimbing Pendamping.');
            }
        }

        return [
            'amounts' => $amounts,
            'base_amounts' => $baseAmounts,
            'notes' => $notes,
        ];
    }

    public function honorarium()
    {
        $C_KODE_DOSEN = auth()->user()->name;
        $data = $this->getHonorariumAssignmentsForDosen($C_KODE_DOSEN)
            ->filter(function ($assignment) {
                return (int) $assignment->status !== 3;
            })
            ->values();

        $honorariumByDate = $data->groupBy('date')->map(function ($items, $date) {
            return (object) [
                'date' => $date,
                'items' => $items,
                'student_count' => $items->pluck('C_NPM')->unique()->count(),
                'assignment_count' => $items->count(),
                'available_total' => $items->where('status', 1)->sum('amount'),
                'available_count' => $items->where('status', 1)->count(),
            ];
        });

        return view('tugasakhir.dosen.honorarium', compact('honorariumByDate'));
    }


    public function honorarium_save_all_dosen(Request $request)
    {
        $roleColumns = [
            'Ketua Sidang' => ['dosen' => 'KS', 'status' => 'KS_Stat'],
            'Pembimbing Utama' => ['dosen' => 'PU', 'status' => 'PU_Stat'],
            'Pembimbing Pendamping' => ['dosen' => 'PP', 'status' => 'PP_Stat'],
            'Penguji I' => ['dosen' => 'P1', 'status' => 'P1_Stat'],
            'Penguji II' => ['dosen' => 'P2', 'status' => 'P2_Stat'],
            'Penguji III' => ['dosen' => 'P3', 'status' => 'P3_Stat'],
        ];
        $kodeDosen = (string) auth()->user()->name;

        try {
            DB::transaction(function () use ($request, $roleColumns, $kodeDosen) {
                foreach ((array) $request->honorariums as $honorarium) {
                    if (!isset($honorarium['status']) || $honorarium['status'] !== 'on') {
                        continue;
                    }

                    $role = isset($honorarium['role']) ? $honorarium['role'] : '';
                    $definition = isset($roleColumns[$role]) ? $roleColumns[$role] : null;
                    $honorariumId = isset($honorarium['id']) ? (int) $honorarium['id'] : 0;
                    $nim = isset($honorarium['C_NPM']) ? $honorarium['C_NPM'] : '';
                    if (!$definition || $honorariumId < 1 || $nim === '') {
                        throw new \RuntimeException('Data konfirmasi honorarium tidak valid.');
                    }

                    $record = DB::table('trt_honorium')
                        ->where('id', $honorariumId)
                        ->where('C_NPM', $nim)
                        ->lockForUpdate()
                        ->first();
                    if (!$record || (string) $record->{$definition['dosen']} !== $kodeDosen) {
                        throw new \RuntimeException('Anda tidak memiliki akses untuk mengonfirmasi penugasan honorarium ini.');
                    }
                    if ((int) $record->{$definition['status']} !== 1) {
                        throw new \RuntimeException('Honorarium belum tersedia atau sudah dikonfirmasi.');
                    }

                    DB::table('trt_honorium')
                        ->where('id', $honorariumId)
                        ->where('C_NPM', $nim)
                        ->update([$definition['status'] => 3]);
                }
            });
        } catch (\RuntimeException $exception) {
            return redirect()->back()->with('status', 'danger')->with('message', $exception->getMessage());
        }

        return redirect()->back()->with('status', 'success')->with('message', 'Honorarium berhasil dikonfirmasi.');
    }

    public function history_honorarium()
    {
        $C_KODE_DOSEN = auth()->user()->name;
        $data = $this->getHonorariumAssignmentsForDosen($C_KODE_DOSEN)
            ->filter(function ($assignment) {
                return (int) $assignment->status === 3;
            })
            ->values();

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
            'D_FOTO_DOSEN',
            'updated_at',
        ]));
    }

    protected function dosenProfileRedirectPath(Request $request)
    {
        return $request->input('return_to') === 'profil' ? '/dsn/profil' : '/';
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
                'trt_bimbingan.jenis_tugas_akhir_id',
                't_mst_mahasiswa.NAMA_MAHASISWA',
                'mst_sk_pembimbing.nomor_sk',
                'mst_sk_pembimbing.created_at as tanggal_sk'
            )
            ->where('trt_bimbingan.' . $kolomPembimbing, $kodeDosen)
            ->whereNotNull('mst_sk_pembimbing.nomor_sk')
            ->whereRaw("TRIM(mst_sk_pembimbing.nomor_sk) <> ''")
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
                return 'Tahapan Belum Ditentukan';
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
