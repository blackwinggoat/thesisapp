<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Dosen;
use App\Helper;
use App\Model\mst_bidangilmu;
use App\Model\mst_pendaftaran;
use App\Model\mst_pengumuman;
use App\Model\mst_syarat_ujian;
use App\Model\mst_tmp_usulan;
use App\Model\t_mst_mahasiswa;
use App\Model\trt_bimbingan;
use App\Model\trt_reg;
use App\Model\trt_sk;
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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Exception;
use Illuminate\Support\Facades\Auth;

class AkademikProdi extends Controller
{
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
            : Dosen::whereIn('C_KODE_DOSEN', $dosenIds)->get()->keyBy('C_KODE_DOSEN');

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

    public function ubah_password()
    {
        return view('tugasakhir.fakultas.ubah_password', [
            'passwordAction' => url('akademikprodi/ubah_password'),
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

    // Halaman Approve Hasil Ujian TA
    public function rekap_nilai_proposal()
    {
        if (Auth::user()->name == "akademikproditi") {
            $data = DB::select("SELECT DISTINCT mst_pendaftaran.pendaftaran_id, mst_pendaftaran.nama_periode, mst_pendaftaran.kuota, mst_pendaftaran.jml_peserta, trt_jadwal_ujian.tgl_ujian FROM mst_pendaftaran, trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa, trt_jadwal_ujian, trt_jadwal_ujian_per_mhs , mst_ruangan WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND mst_ruangan.id =  trt_jadwal_ujian_per_mhs.ruangan AND mst_pendaftaran.status_prodi = 1 AND trt_bimbingan.C_NPM = trt_jadwal_ujian_per_mhs.C_NPM AND trt_jadwal_ujian.id = trt_jadwal_ujian_per_mhs.jadwal_ujian AND trt_jadwal_ujian.pendaftaran_id = mst_pendaftaran.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND mst_pendaftaran.tipe_ujian = ? AND  trt_penguji.tipe_ujian = ? AND trt_reg.status = ? ORDER BY mst_pendaftaran.pendaftaran_id", [0, 0, 0]);
        }else{
            $data = DB::select("SELECT DISTINCT mst_pendaftaran.pendaftaran_id, mst_pendaftaran.nama_periode, mst_pendaftaran.kuota, mst_pendaftaran.jml_peserta, trt_jadwal_ujian.tgl_ujian FROM mst_pendaftaran, trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa, trt_jadwal_ujian, trt_jadwal_ujian_per_mhs , mst_ruangan WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND mst_ruangan.id =  trt_jadwal_ujian_per_mhs.ruangan AND mst_pendaftaran.status_prodi = 2 AND trt_bimbingan.C_NPM = trt_jadwal_ujian_per_mhs.C_NPM AND trt_jadwal_ujian.id = trt_jadwal_ujian_per_mhs.jadwal_ujian AND trt_jadwal_ujian.pendaftaran_id = mst_pendaftaran.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND mst_pendaftaran.tipe_ujian = ? AND  trt_penguji.tipe_ujian = ? AND trt_reg.status = ? ORDER BY mst_pendaftaran.pendaftaran_id", [0, 0, 0]);
        }
        return view('tugasakhir.akademikprodi.rekap_nilai_proposal', compact('data'));
    }
    // Akhir Approve Hasil Ujian TA

    // Halaman Approve Hasil Ujian TA
    public function detail_rekap_nilai_proposal($id)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $id)->first();
        $data = DB::select("SELECT * FROM mst_pendaftaran,trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_reg.pendaftaran_id = ? AND trt_reg.status = ?", [$id, $info->tipe_ujian]);
        return view('tugasakhir.akademikprodi.detail_rekap_nilai_proposal', array_merge(
            compact('data', 'info'),
            $this->prepareRekapNilaiViewData($data)
        ));
    }
    // Akhir Approve Hasil Ujian TA

    public function detail_ujian($nim, $tipe_ujian)
    {
        $data = DB::select("SELECT * FROM trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_penguji.C_NPM = ? AND trt_reg.status = ?", [$nim, $tipe_ujian]);
        return view('tugasakhir.mhs.detail_ujian', compact('data'));
    }

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

        return view("tugasakhir.akademikprodi.lembaran_hasilujian_proposal", compact(
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
        if (Auth::user()->name == 'akademikproditi') {
            $data = DB::select("SELECT DISTINCT mst_pendaftaran.pendaftaran_id, mst_pendaftaran.nama_periode, mst_pendaftaran.kuota, mst_pendaftaran.jml_peserta, trt_jadwal_ujian.tgl_ujian FROM mst_pendaftaran, trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa, trt_jadwal_ujian, trt_jadwal_ujian_per_mhs , mst_ruangan WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND mst_pendaftaran.status_prodi = 1 AND mst_ruangan.id =  trt_jadwal_ujian_per_mhs.ruangan AND trt_bimbingan.C_NPM = trt_jadwal_ujian_per_mhs.C_NPM AND trt_jadwal_ujian.id = trt_jadwal_ujian_per_mhs.jadwal_ujian AND trt_jadwal_ujian.pendaftaran_id = mst_pendaftaran.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM  AND mst_pendaftaran.tipe_ujian = ? AND  trt_penguji.tipe_ujian = ? AND trt_reg.status = ? ORDER BY mst_pendaftaran.pendaftaran_id", [2, 2, 2]);
        }else{
            $data = DB::select("SELECT DISTINCT mst_pendaftaran.pendaftaran_id, mst_pendaftaran.nama_periode, mst_pendaftaran.kuota, mst_pendaftaran.jml_peserta, trt_jadwal_ujian.tgl_ujian FROM mst_pendaftaran, trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa, trt_jadwal_ujian, trt_jadwal_ujian_per_mhs , mst_ruangan WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND mst_pendaftaran.status_prodi = 2 AND mst_ruangan.id =  trt_jadwal_ujian_per_mhs.ruangan AND trt_bimbingan.C_NPM = trt_jadwal_ujian_per_mhs.C_NPM AND trt_jadwal_ujian.id = trt_jadwal_ujian_per_mhs.jadwal_ujian AND trt_jadwal_ujian.pendaftaran_id = mst_pendaftaran.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM  AND mst_pendaftaran.tipe_ujian = ? AND  trt_penguji.tipe_ujian = ? AND trt_reg.status = ? ORDER BY mst_pendaftaran.pendaftaran_id", [2, 2, 2]);
        }
        return view('tugasakhir.akademikprodi.rekap_nilai_ujian_ta', compact('data'));
    }
    // Akhir Approve Hasil Ujian TA

    // Halaman Approve Hasil Ujian TA
    public function detail_rekap_nilai_ujian_ta($id)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $id)->first();
        $data = DB::select("SELECT * FROM mst_pendaftaran,trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_reg.pendaftaran_id = ? AND trt_reg.status = ? ", [$id, $info->tipe_ujian]);
        return view('tugasakhir.akademikprodi.detail_rekap_nilai_ujian_ta', array_merge(
            compact('data', 'info'),
            $this->prepareRekapNilaiViewData($data)
        ));
    }
    // Akhir Approve Hasil Ujian TA

    // Halaman Lembaran Hasil Ujian
    public function lembaran_hasilujian_ujian_ta($pendaftaran_id, $nim, $reg_id)
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

        return view("tugasakhir.akademikprodi.lembaran_hasilujian_ujian_ta", compact(
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

    public function persyaratan_proposal()
    {
        if (Auth::user()->name == 'akademikproditi') {
            $data = TrtPengajuanDokumen::join("t_mst_mahasiswa", "trt_pengajuan_dokumen.C_NPM", "=", "t_mst_mahasiswa.C_NPM")
            ->where("type", 0)
            ->where('t_mst_mahasiswa.C_NPM', 'LIKE', '130%')
            ->get(["NAMA_MAHASISWA", "t_mst_mahasiswa.C_NPM"]);
        }else{
            $data = TrtPengajuanDokumen::join("t_mst_mahasiswa", "trt_pengajuan_dokumen.C_NPM", "=", "t_mst_mahasiswa.C_NPM")
            ->where("type", 0)
            ->where('t_mst_mahasiswa.C_NPM', 'LIKE', '131%')
            ->get(["NAMA_MAHASISWA", "t_mst_mahasiswa.C_NPM"]);
        }
        return view("tugasakhir.prodi.persyaratan_proposal", compact("data"));
    }


    public function persyaratan_ujianmeja()
    {
        if (Auth::user()->name == 'akademikproditi') {
            $data = TrtPengajuanDokumen::join("t_mst_mahasiswa", "trt_pengajuan_dokumen.C_NPM", "=", "t_mst_mahasiswa.C_NPM")
            ->where("type", 2)
            ->where('t_mst_mahasiswa.C_NPM', 'LIKE', '130%')
            ->get(["NAMA_MAHASISWA", "t_mst_mahasiswa.C_NPM"]);
        }else{
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

    public function detail_persyaratan_ujianmeja($id)
    {
        $mhs = t_mst_mahasiswa::where("C_NPM", $id)->first();
        $data = TrtSyaratUjian::join("mst_syarat_ujian", "trt_syarat_ujian.syarat_ujian_id", "=", "mst_syarat_ujian.syarat_ujian_id")->where(["tipe_ujian" => 2, "C_NPM" => $id])->get();
        return view("tugasakhir.prodi.detail_persyaratan_ujianmeja", compact("data", "mhs"));
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

    public function mahasiswa(Request $request)
    {
        $nimPrefix = '';
        if (auth()->user()->name == "prodisi" || auth()->user()->name == "akademikprodisi") {
            $nimPrefix = '131';
        } else if (auth()->user()->name == "proditi" || auth()->user()->name == "akademikproditi") {
            $nimPrefix = '130';
        }

        $q = trim((string) $request->get('q', ''));
        $angkatan = trim((string) $request->get('angkatan', ''));
        $statusAkun = trim((string) $request->get('status_akun', 'semua'));
        $perPage = (int) $request->get('per_page', 25);
        $allowedPerPage = [25, 50, 100, 200];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 25;
        }

        if (!in_array($statusAkun, ['semua', 'aktif', 'belum'], true)) {
            $statusAkun = 'semua';
        }

        $query = DB::table('t_mst_mahasiswa')
            ->select(
                't_mst_mahasiswa.C_NPM',
                't_mst_mahasiswa.NAMA_MAHASISWA',
                't_mst_mahasiswa.TAHUN_MASUK'
            );

        if ($nimPrefix !== '') {
            $query->where('t_mst_mahasiswa.C_NPM', 'LIKE', $nimPrefix . '%');
        }

        if ($q !== '') {
            $query->where(function ($subQuery) use ($q) {
                $subQuery->where('t_mst_mahasiswa.C_NPM', 'LIKE', '%' . $q . '%')
                    ->orWhere('t_mst_mahasiswa.NAMA_MAHASISWA', 'LIKE', '%' . $q . '%');
            });
        }

        if ($angkatan !== '' && preg_match('/^[0-9]{4}$/', $angkatan)) {
            $query->where('t_mst_mahasiswa.TAHUN_MASUK', $angkatan);
        } else {
            $angkatan = '';
        }

        $accountNames = null;
        if ($statusAkun !== 'semua') {
            $accountNames = DB::table('users')
                ->whereNotNull('name')
                ->pluck('name')
                ->all();

            if (empty($accountNames)) {
                if ($statusAkun === 'aktif') {
                    $query->whereRaw('1 = 0');
                }
            } elseif ($statusAkun === 'aktif') {
                $query->whereIn('t_mst_mahasiswa.C_NPM', $accountNames);
            } else {
                $query->whereNotIn('t_mst_mahasiswa.C_NPM', $accountNames);
            }
        }

        $data = $query
            ->orderBy('t_mst_mahasiswa.C_NPM', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        if ($accountNames === null) {
            $pageNims = $data->pluck('C_NPM')->all();
            $accountNames = empty($pageNims)
                ? []
                : DB::table('users')->whereIn('name', $pageNims)->pluck('name')->all();
        }

        $activeAccounts = array_fill_keys($accountNames, true);
        foreach ($data as $mahasiswa) {
            $mahasiswa->has_user = isset($activeAccounts[$mahasiswa->C_NPM]);
        }

        $listAngkatan = DB::table('t_mst_mahasiswa')
            ->whereNotNull('TAHUN_MASUK')
            ->where('TAHUN_MASUK', 'REGEXP', '^[0-9]{4}$')
            ->when($nimPrefix !== '', function ($angkatanQuery) use ($nimPrefix) {
                return $angkatanQuery->where('C_NPM', 'LIKE', $nimPrefix . '%');
            })
            ->distinct()
            ->orderBy('TAHUN_MASUK', 'desc')
            ->pluck('TAHUN_MASUK')
            ->toArray();

        return view('tugasakhir.akademikprodi.mahasiswa', compact(
            'data',
            'listAngkatan',
            'q',
            'angkatan',
            'statusAkun',
            'perPage'
        ));
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
        return redirect("/akademikprodi/$to");
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
            return redirect::to('akademikprodi/detail_persyaratan_proposal/' . $request->C_NPM)->with('status', 'success');
        } catch (Exception $exception) {
            return redirect::to('akademikprodi/detail_persyaratan_proposal/' . $request->C_NPM)->with('status', 'error');
        }
    }

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
            return redirect::to('akademikprodi/detail_persyaratan_ujianmeja/' . $request->C_NPM)->with('status', 'success');
        } catch (Exception $exception) {
            return redirect::to('akademikprodi/detail_persyaratan_ujianmeja/' . $request->C_NPM)->with('status', 'error');
        }
    }

    // Menampilkan Status Bimbingan Mahasiswa
    public function detail_status_bimbingan_mahasiswa($status)
    {
        $query = DB::table('trt_bimbingan')
            ->select("*")
            ->where('trt_bimbingan.status_bimbingan', $status);

        $nimPrefix = $this->getAkademikProdiNimPrefix();
        if ($nimPrefix !== null) {
            $query->where('trt_bimbingan.C_NPM', 'LIKE', $nimPrefix);
        }

        $data = $query
            ->orderBy('updated_at', 'desc')
            ->get();

        $filterActionRoute = route('akademikprodi.tampilDetailStatusBimbinganDenganFilterTanggal');
        $resetUrl = url('akademikprodi/detail_status_bimbingan_mahasiswa/' . $status);

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

        $nimPrefix = $this->getAkademikProdiNimPrefix();
        if ($nimPrefix !== null) {
            $query->where('trt_bimbingan.C_NPM', 'LIKE', $nimPrefix);
        }

        $data = $query
            ->orderBy('updated_at', 'desc')
            ->get();

        $filterActionRoute = route('akademikprodi.tampilDetailStatusBimbinganDenganFilterTanggal');
        $resetUrl = url('akademikprodi/detail_status_bimbingan_mahasiswa/' . $status);

        return view('tugasakhir.prodi.detail_status_bimbingan_mahasiswa', compact('data', 'status', 'filterActionRoute', 'resetUrl'));
    }

    protected function getAkademikProdiNimPrefix()
    {
        $username = Auth::user()->name;

        if ($username === 'akademikproditi' || $username === 'proditi') {
            return '130%';
        }

        if ($username === 'akademikprodisi' || $username === 'prodisi') {
            return '131%';
        }

        return null;
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

    public function detail_mahasiswa($id)
    {

        $datax = t_mst_mahasiswa::find($id);
        return view('tugasakhir.akademikprodi.detail_mahasiswa', compact('datax'));
    }

}
