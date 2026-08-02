<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Exception;
use RuntimeException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class Prodi extends Controller
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
        $jenisTugasAkhir = DB::table('mst_jenis_tugas_akhir')
            ->orderBy('kode_jenis_tugas_akhir')
            ->get();
        return view("tugasakhir.prodi.edit_judul_detail_mahasiswa", compact('data', 'jenisTugasAkhir'));
    }
    // Akhir Edit Judul mahasiswa

    // Edit Judul Mahasiswa
    public function ubah_judul(Request $request, $nim)
    {
        $request->merge([
            'topik' => $this->judulTanpaKodeJenisTugasAkhir($request->topik),
        ]);
        $this->validate($request, [
            'topik' => 'required|max:1000',
            'jenis_tugas_akhir_id' => 'required|exists:mst_jenis_tugas_akhir,jenis_tugas_akhir_id',
        ]);

        DB::transaction(function () use ($request, $nim) {
            $topikIds = DB::table('trt_bimbingan')
                ->where('C_NPM', $nim)
                ->whereNotNull('topik_id')
                ->pluck('topik_id');

            DB::table("trt_bimbingan")->where('C_NPM', $nim)->update([
                'judul' => trim((string) $request->topik),
                'jenis_tugas_akhir_id' => $request->jenis_tugas_akhir_id,
            ]);

            if ($topikIds->isNotEmpty()) {
                DB::table('trt_topik')
                    ->whereIn('topik_id', $topikIds)
                    ->where('C_NPM', $nim)
                    ->update([
                        'topik' => trim((string) $request->topik),
                        'jenis_tugas_akhir_id' => $request->jenis_tugas_akhir_id,
                    ]);
            }
        });
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
        $query = DB::table('trt_bimbingan')
            ->select("*")
            ->where('trt_bimbingan.status_bimbingan', $status);

        if (Auth::user()->name == 'proditi') {
            $query->where('trt_bimbingan.C_NPM', 'LIKE', '130%');
        } elseif (Auth::user()->name == 'prodisi') {
            $query->where('trt_bimbingan.C_NPM', 'LIKE', '131%');
        }

        $data = $query
            ->orderBy('updated_at', 'desc')
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
        $data = $this->getDaftarApproveHasilUjianProposalPeriode(false);
        $isHistory = false;
        return view('tugasakhir.prodi.approve_hasilujian_proposal', compact('data', 'isHistory'));
    }

    public function approve_hasilujian_proposal_history()
    {
        $data = $this->getDaftarApproveHasilUjianProposalPeriode(true);
        $isHistory = true;
        return view('tugasakhir.prodi.approve_hasilujian_proposal', compact('data', 'isHistory'));
    }
    // Akhir Approve Hasil Ujian Proposal

    // Halaman Approve Hasil Ujian Proposal
    public function detail_hasilujian_proposal($id)
    {
        return $this->renderDetailHasilUjianProposal($id, false);
    }

    public function detail_hasilujian_proposal_history($id)
    {
        return $this->renderDetailHasilUjianProposal($id, true);
    }

    protected function renderDetailHasilUjianProposal($id, $isHistory = false)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $id)->first();

        if (!$info) {
            return response('Data jadwal/pendaftaran proposal tidak ditemukan.', 404);
        }

        $query = DB::table('mst_pendaftaran')
            ->join('trt_reg', 'mst_pendaftaran.pendaftaran_id', '=', 'trt_reg.pendaftaran_id')
            ->join('trt_bimbingan', 'trt_reg.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->join('trt_penguji', function ($join) {
                $join->on('trt_penguji.C_NPM', '=', 'trt_bimbingan.C_NPM')
                    ->on('trt_penguji.tipe_ujian', '=', 'trt_reg.status');
            })
            ->join('t_mst_mahasiswa', 'trt_bimbingan.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->where('trt_reg.pendaftaran_id', $id)
            ->where('trt_reg.status', $info->tipe_ujian);

        if ($isHistory) {
            $query->where('trt_bimbingan.status_bimbingan', '<>', 0);
        } else {
            $query->where('trt_bimbingan.status_bimbingan', 0);
        }

        $data = $query->select(
            'mst_pendaftaran.*',
            'trt_reg.*',
            'trt_bimbingan.*',
            'trt_penguji.*',
            't_mst_mahasiswa.C_NPM as C_NPM',
            't_mst_mahasiswa.NAMA_MAHASISWA'
        )->get();

        return view('tugasakhir.prodi.detail_hasilujian_proposal', compact("data", "info", "isHistory"));
    }

    protected function getDaftarApproveHasilUjianProposalPeriode($isHistory = false)
    {
        $statusProdi = Auth::user()->name == "proditi" ? 1 : 2;

        $query = mst_pendaftaran::join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "mst_pendaftaran.pendaftaran_id")
            ->where(function ($q) {
                $q->where('mst_pendaftaran.tipe_ujian', 0)
                    ->orWhere('mst_pendaftaran.tipe_ujian', 3);
            })
            ->where('mst_pendaftaran.status_prodi', $statusProdi)
            ->orderBy('mst_pendaftaran.created_at', 'desc');

        if ($isHistory) {
            $query->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('trt_reg')
                    ->join('trt_bimbingan', 'trt_reg.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
                    ->whereRaw('trt_reg.pendaftaran_id = mst_pendaftaran.pendaftaran_id')
                    ->where('trt_reg.status', 0)
                    ->where('trt_bimbingan.status_bimbingan', '<>', 0);
            });
        } else {
            $query->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('trt_reg')
                    ->join('trt_bimbingan', 'trt_reg.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
                    ->whereRaw('trt_reg.pendaftaran_id = mst_pendaftaran.pendaftaran_id')
                    ->where('trt_reg.status', 0)
                    ->where('trt_bimbingan.status_bimbingan', 0);
            });
        }

        return $this->attachPenilaianCountsToPeriods($query->get(), 0, 0, $isHistory);
    }
    // Akhir Approve Hasil Ujian Proposal

    // Halaman Approve Hasil Ujian Proposal
    public function approve_hasilujian_proposal_post($id, $nim, $pendaftaran_id)
    {
        if (!$this->isPenilaianUjianLengkap($id, $nim, $pendaftaran_id, 0)) {
            return redirect('prodi/detail_hasilujian_proposal/' . $pendaftaran_id)
                ->with([
                    'status' => 'warning',
                    'message' => 'Hasil proposal belum dapat dikonfirmasi karena masih ada penilai yang belum mengisi nilai.',
                ]);
        }

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
                "C_NPM" => $nim,
                "status_bimbingan" => 0,
            ])
            ->update(['status_bimbingan' => 2]);

        // Redirect setelah update
        return redirect('prodi/detail_hasilujian_proposal/' . $pendaftaran_id);
    }

    // Akhir Approve Hasil Ujian Proposal

    // Aprrove Semua Hasil Ujian
    public function approve_hasilujian_proposal_all_post()
    {
        return $this->approveSemuaHasilUjian(0, 0, 2);
    }
    // Akhir Approve Semua Hasil Ujian

    // Aprrove Semua Hasil Ujian
    public function approve_hasilujian_ta_all_post()
    {
        return $this->approveSemuaHasilUjian(2, 2, 3);
    }
    // Akhir Approve Semua Hasil Ujian

    protected function approveSemuaHasilUjian($tipeUjian, $statusSaatIni, $statusTujuan)
    {
        $statusProdi = Auth::user()->name == "proditi" ? 1 : 2;
        $peserta = DB::table('trt_reg as rg')
            ->join('trt_bimbingan as tb', 'tb.bimbingan_id', '=', 'rg.bimbingan_id')
            ->join('mst_pendaftaran as mp', 'mp.pendaftaran_id', '=', 'rg.pendaftaran_id')
            ->select('rg.reg_id', 'tb.bimbingan_id')
            ->where('rg.status', $tipeUjian)
            ->where('tb.status_bimbingan', $statusSaatIni)
            ->where('mp.status_prodi', $statusProdi)
            ->where(function ($query) use ($tipeUjian) {
                $query->where('mp.tipe_ujian', $tipeUjian)
                    ->orWhere('mp.tipe_ujian', 3);
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('trt_jadwal_ujian as ju')
                    ->whereRaw('ju.pendaftaran_id = rg.pendaftaran_id');
            })
            ->whereRaw(
                'rg.reg_id = (SELECT MAX(rg_latest.reg_id) FROM trt_reg AS rg_latest WHERE rg_latest.bimbingan_id = rg.bimbingan_id AND rg_latest.status = ?)',
                [$tipeUjian]
            )
            ->get();

        $bimbinganIds = [];
        $totalBelumLengkap = 0;
        foreach ($peserta as $rowPeserta) {
            if (Helper::isPenilaianLengkapByRegId($rowPeserta->reg_id)) {
                $bimbinganIds[] = $rowPeserta->bimbingan_id;
            } else {
                $totalBelumLengkap++;
            }
        }
        $bimbinganIds = array_values(array_unique($bimbinganIds));

        if (empty($bimbinganIds)) {
            return redirect()->back()->with([
                'status' => 'warning',
                'total' => 0,
                'total_belum_lengkap' => $totalBelumLengkap,
            ]);
        }

        try {
            $total = DB::table('trt_bimbingan')
                ->where('status_bimbingan', $statusSaatIni)
                ->whereIn('bimbingan_id', $bimbinganIds)
                ->update(['status_bimbingan' => $statusTujuan]);

            return redirect()->back()->with([
                'status' => 'success',
                'total' => $total,
                'total_belum_lengkap' => $totalBelumLengkap,
            ]);
        } catch (\Exception $th) {
            return redirect()->back()->with([
                'status' => 'error',
                'total' => 0,
                'total_belum_lengkap' => $totalBelumLengkap,
            ]);
        }
    }

    protected function isPenilaianUjianLengkap($bimbinganId, $nim, $pendaftaranId, $tipeUjian)
    {
        $regId = DB::table('trt_reg as rg')
            ->join('trt_bimbingan as tb', 'tb.bimbingan_id', '=', 'rg.bimbingan_id')
            ->where('rg.bimbingan_id', $bimbinganId)
            ->where('rg.pendaftaran_id', $pendaftaranId)
            ->where('rg.status', $tipeUjian)
            ->where('tb.C_NPM', $nim)
            ->max('rg.reg_id');

        return $regId && Helper::isPenilaianLengkapByRegId($regId);
    }

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
        $data = $this->getDaftarApproveHasilUjianTaPeriode(false);
        $isHistory = false;
        return view('tugasakhir.prodi.approve_hasilujian_ta', compact('data', 'isHistory'));
    }

    public function approve_hasilujian_ta_history()
    {
        $data = $this->getDaftarApproveHasilUjianTaPeriode(true);
        $isHistory = true;
        return view('tugasakhir.prodi.approve_hasilujian_ta', compact('data', 'isHistory'));
    }
    // Akhir Approve Hasil Ujian TA

    // Halaman Approve Hasil Ujian TA
    public function detail_hasilujian_ta($id)
    {
        return $this->renderDetailHasilUjianTa($id, false);
    }

    public function detail_hasilujian_ta_history($id)
    {
        return $this->renderDetailHasilUjianTa($id, true);
    }

    protected function renderDetailHasilUjianTa($id, $isHistory = false)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $id)->first();

        if (!$info) {
            return response('Data jadwal/pendaftaran ujian TA tidak ditemukan.', 404);
        }

        $query = DB::table('mst_pendaftaran')
            ->join('trt_reg', 'mst_pendaftaran.pendaftaran_id', '=', 'trt_reg.pendaftaran_id')
            ->join('trt_bimbingan', 'trt_reg.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->join('trt_penguji', function ($join) {
                $join->on('trt_penguji.C_NPM', '=', 'trt_bimbingan.C_NPM')
                    ->on('trt_penguji.tipe_ujian', '=', 'trt_reg.status');
            })
            ->join('t_mst_mahasiswa', 'trt_bimbingan.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->where('trt_reg.pendaftaran_id', $id)
            ->where('trt_reg.status', $info->tipe_ujian);

        if ($isHistory) {
            $query->where('trt_bimbingan.status_bimbingan', '<>', 2);
        } else {
            $query->where('trt_bimbingan.status_bimbingan', 2);
        }

        $data = $query->select(
            'mst_pendaftaran.*',
            'trt_reg.*',
            'trt_bimbingan.*',
            'trt_penguji.*',
            't_mst_mahasiswa.C_NPM as C_NPM',
            't_mst_mahasiswa.NAMA_MAHASISWA'
        )->get();

        return view('tugasakhir.prodi.detail_hasilujian_ta', compact("data", "info", "isHistory"));
    }
    // Akhir Approve Hasil Ujian TA

    protected function getDaftarApproveHasilUjianTaPeriode($isHistory = false)
    {
        $statusProdi = Auth::user()->name == "proditi" ? 1 : 2;

        $query = mst_pendaftaran::join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "mst_pendaftaran.pendaftaran_id")
            ->where(function ($q) {
                $q->where('mst_pendaftaran.tipe_ujian', 2)
                    ->orWhere('mst_pendaftaran.tipe_ujian', 3);
            })
            ->where('mst_pendaftaran.status_prodi', $statusProdi)
            ->orderBy('mst_pendaftaran.created_at', 'desc');

        if ($isHistory) {
            $query->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('trt_reg')
                    ->join('trt_bimbingan', 'trt_reg.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
                    ->whereRaw('trt_reg.pendaftaran_id = mst_pendaftaran.pendaftaran_id')
                    ->where('trt_reg.status', 2)
                    ->where('trt_bimbingan.status_bimbingan', '<>', 2);
            });
        } else {
            $query->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('trt_reg')
                    ->join('trt_bimbingan', 'trt_reg.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
                    ->whereRaw('trt_reg.pendaftaran_id = mst_pendaftaran.pendaftaran_id')
                    ->where('trt_reg.status', 2)
                    ->where('trt_bimbingan.status_bimbingan', 2);
            });
        }

        return $this->attachPenilaianCountsToPeriods($query->get(), 2, 2, $isHistory);
    }

    protected function attachPenilaianCountsToPeriods($periods, $tipeUjian, $statusAktif, $isHistory)
    {
        if ($periods->isEmpty()) {
            return $periods;
        }

        $registrations = DB::table('trt_reg as rg')
            ->join('trt_bimbingan as tb', 'tb.bimbingan_id', '=', 'rg.bimbingan_id')
            ->leftJoin('trt_penguji as tp', function ($join) {
                $join->on('tp.C_NPM', '=', 'tb.C_NPM')
                    ->on('tp.tipe_ujian', '=', 'rg.status');
            })
            ->whereIn('rg.pendaftaran_id', $periods->pluck('pendaftaran_id')->all())
            ->where('rg.status', $tipeUjian)
            ->when($isHistory, function ($query) use ($statusAktif) {
                $query->where('tb.status_bimbingan', '<>', $statusAktif);
            }, function ($query) use ($statusAktif) {
                $query->where('tb.status_bimbingan', $statusAktif);
            })
            ->select(
                'rg.reg_id',
                'rg.pendaftaran_id',
                'tb.pembimbing_I_id',
                'tb.pembimbing_II_id',
                'tp.penguji_I_id',
                'tp.penguji_II_id',
                'tp.penguji_III_id',
                'tp.ketua_sidang_id'
            )
            ->get()
            ->unique('reg_id');

        $countsByPeriod = [];
        foreach ($registrations as $registration) {
            $periodId = (string) $registration->pendaftaran_id;
            if (!isset($countsByPeriod[$periodId])) {
                $countsByPeriod[$periodId] = [
                    'confirmed' => 0,
                    'complete' => 0,
                    'incomplete' => 0,
                ];
            }
            $countsByPeriod[$periodId]['confirmed']++;
        }

        if (!$isHistory && $registrations->isNotEmpty()) {
            $completedAssessments = DB::table('trt_hasil')
                ->select('reg_id', 'nidn')
                ->whereIn('reg_id', $registrations->pluck('reg_id')->all())
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
                ->distinct()
                ->get()
                ->groupBy('reg_id');

            foreach ($registrations as $registration) {
                $requiredAssessors = array_filter(array_unique([
                    trim((string) $registration->pembimbing_I_id),
                    trim((string) $registration->pembimbing_II_id),
                    trim((string) $registration->penguji_I_id),
                    trim((string) $registration->penguji_II_id),
                    trim((string) $registration->penguji_III_id),
                    trim((string) $registration->ketua_sidang_id),
                ]), function ($value) {
                    return $value !== '' && $value !== '--';
                });
                $completedNidns = $completedAssessments
                    ->get($registration->reg_id, collect())
                    ->pluck('nidn')
                    ->map(function ($nidn) {
                        return trim((string) $nidn);
                    })
                    ->all();
                $isComplete = !empty($requiredAssessors)
                    && empty(array_diff($requiredAssessors, $completedNidns));
                $periodId = (string) $registration->pendaftaran_id;
                $countsByPeriod[$periodId][$isComplete ? 'complete' : 'incomplete']++;
            }
        }

        return $periods->map(function ($period) use ($countsByPeriod) {
            $counts = $countsByPeriod[(string) $period->pendaftaran_id] ?? [
                'confirmed' => 0,
                'complete' => 0,
                'incomplete' => 0,
            ];
            $period->total_terkonfirmasi = $counts['confirmed'];
            $period->total_penilaian_lengkap = $counts['complete'];
            $period->total_penilaian_tidak_lengkap = $counts['incomplete'];
            return $period;
        });
    }

    // Halaman Approve Hasil Ujian TA
    public function approve_hasilujian_ta_post($id, $nim, $pendaftaran_id)
    {
        if (!$this->isPenilaianUjianLengkap($id, $nim, $pendaftaran_id, 2)) {
            return redirect('prodi/detail_hasilujian_ta/' . $pendaftaran_id)
                ->with([
                    'status' => 'warning',
                    'message' => 'Hasil ujian TA belum dapat dikonfirmasi karena masih ada penilai yang belum mengisi nilai.',
                ]);
        }

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
                "C_NPM" => $nim,
                "status_bimbingan" => 2,
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
        $semesterRange = Helper::getCurrentSemesterDateRange();
        $ringkasanBimbingan = $this->getRingkasanBimbinganPerDosen();
        $ringkasanBimbinganSemester = $this->getRingkasanBimbinganPerDosen($semesterRange);

        foreach ($data as $dosen) {
            $kodeDosen = (string) $dosen->C_KODE_DOSEN;
            $dosen->ringkasan_bimbingan = isset($ringkasanBimbingan[$kodeDosen])
                ? $ringkasanBimbingan[$kodeDosen]
                : $this->emptyRingkasanBimbingan();
            $dosen->ringkasan_bimbingan_semester = isset($ringkasanBimbinganSemester[$kodeDosen])
                ? $ringkasanBimbinganSemester[$kodeDosen]
                : $this->emptyRingkasanBimbingan();
        }

        return view('tugasakhir.prodi.dosen_pembimbing', compact('data', 'semesterRange'));
    }

    protected function getRingkasanBimbinganPerDosen($semesterRange = null)
    {
        $ringkasan = [];

        foreach (['pembimbing_I_id', 'pembimbing_II_id'] as $kolomPembimbing) {
            $query = DB::table('trt_bimbingan as tb')
                ->leftJoin('t_mst_mahasiswa as mhs', 'mhs.C_NPM', '=', 'tb.C_NPM')
                ->whereNotNull('tb.' . $kolomPembimbing)
                ->where('tb.' . $kolomPembimbing, '<>', '')
                ->whereIn('tb.status_bimbingan', [0, 2, 3])
                ->select(
                    'tb.' . $kolomPembimbing . ' as kode_dosen',
                    DB::raw("SUM(CASE WHEN tb.status_bimbingan = 0 AND mhs.C_KODE_STATUS_AKTIF_MHS = 'A' THEN 1 ELSE 0 END) as pp"),
                    DB::raw("SUM(CASE WHEN tb.status_bimbingan = 2 AND mhs.C_KODE_STATUS_AKTIF_MHS = 'A' THEN 1 ELSE 0 END) as pum"),
                    DB::raw('SUM(CASE WHEN tb.status_bimbingan = 3 THEN 1 ELSE 0 END) as l')
                )
                ->groupBy('tb.' . $kolomPembimbing);

            if ($semesterRange) {
                $query->whereBetween('tb.created_at', [$semesterRange->start, $semesterRange->end]);
            }

            foreach ($query->get() as $item) {
                $kodeDosen = (string) $item->kode_dosen;

                if (!isset($ringkasan[$kodeDosen])) {
                    $ringkasan[$kodeDosen] = $this->emptyRingkasanBimbingan();
                }

                $ringkasan[$kodeDosen]->pp += (int) $item->pp;
                $ringkasan[$kodeDosen]->pum += (int) $item->pum;
                $ringkasan[$kodeDosen]->l += (int) $item->l;
            }
        }

        return $ringkasan;
    }

    protected function emptyRingkasanBimbingan()
    {
        return (object) [
            'pp' => 0,
            'pum' => 0,
            'l' => 0,
        ];
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
        return view('tugasakhir.prodi.detail_pembimbing', compact(
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

    public function laporan_mahasiswa()
    {
        if (!Schema::hasTable('trt_laporan_mahasiswa')) {
            return redirect()->to('/')->with('error', 'Fitur laporan mahasiswa belum tersedia.');
        }

        $laporan = $this->queryLaporanMahasiswaProdi()
            ->orderBy('trt_laporan_mahasiswa.updated_at', 'desc')
            ->get();

        return view('tugasakhir.prodi.laporan_mahasiswa', compact('laporan'));
    }

    public function laporan_mahasiswa_detail($id)
    {
        $laporan = $this->findLaporanMahasiswaProdi($id);
        if (!$laporan) {
            return response('Laporan mahasiswa tidak ditemukan.', 404);
        }

        $pesan = DB::table('trt_laporan_mahasiswa_pesan')
            ->where('laporan_mahasiswa_id', $laporan->laporan_mahasiswa_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('tugasakhir.prodi.laporan_mahasiswa_detail', compact('laporan', 'pesan'));
    }

    public function laporan_mahasiswa_tindakan_post(Request $request, $id)
    {
        $laporan = $this->findLaporanMahasiswaProdi($id);
        if (!$laporan) {
            return response('Laporan mahasiswa tidak ditemukan.', 404);
        }

        $this->validate($request, [
            'status' => 'required|in:baru,ditinjau,selesai',
            'pesan' => 'required|string|min:3|max:5000',
            'tindakan' => 'nullable|string|max:2000',
        ]);

        DB::transaction(function () use ($request, $laporan) {
            $now = Carbon::now();

            DB::table('trt_laporan_mahasiswa_pesan')->insert([
                'laporan_mahasiswa_id' => $laporan->laporan_mahasiswa_id,
                'pengirim_user_id' => auth()->id(),
                'pengirim_peran' => 'prodi',
                'nama_pengirim' => trim((string) (auth()->user()->name ?? 'Program Studi')),
                'isi_pesan' => trim((string) $request->pesan),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $payload = [
                'status' => $request->status,
                'updated_at' => $now,
            ];
            $tindakan = trim((string) $request->tindakan);
            if ($tindakan !== '') {
                $payload['tindakan_terakhir'] = $tindakan;
                $payload['tindakan_oleh_user_id'] = auth()->id();
                $payload['tindakan_pada'] = $now;
            }

            DB::table('trt_laporan_mahasiswa')
                ->where('laporan_mahasiswa_id', $laporan->laporan_mahasiswa_id)
                ->update($payload);
        });

        return redirect()->back()->with('success', 'Respons dan status laporan berhasil diperbarui.');
    }

    protected function queryLaporanMahasiswaProdi()
    {
        $kodeProdi = $this->kodeProdiLaporanMahasiswa();
        if ($kodeProdi === null) {
            abort(403, 'Akun Program Studi tidak memiliki cakupan laporan mahasiswa.');
        }

        return DB::table('trt_laporan_mahasiswa')
            ->join('t_mst_mahasiswa', 't_mst_mahasiswa.C_NPM', '=', 'trt_laporan_mahasiswa.C_NPM')
            ->leftJoin('t_mst_dosen', 't_mst_dosen.C_KODE_DOSEN', '=', 'trt_laporan_mahasiswa.C_KODE_DOSEN')
            ->leftJoin('trt_prodi', 'trt_prodi.kode_prodi', '=', 'trt_laporan_mahasiswa.C_KODE_PRODI')
            ->select(
                'trt_laporan_mahasiswa.*',
                't_mst_mahasiswa.NAMA_MAHASISWA',
                't_mst_dosen.NAMA_DOSEN',
                'trt_prodi.nama as nama_prodi'
            )
            ->where('trt_laporan_mahasiswa.C_KODE_PRODI', $kodeProdi);
    }

    protected function findLaporanMahasiswaProdi($id)
    {
        return $this->queryLaporanMahasiswaProdi()
            ->where('trt_laporan_mahasiswa.laporan_mahasiswa_id', $id)
            ->first();
    }

    protected function kodeProdiLaporanMahasiswa()
    {
        switch ((string) auth()->user()->name) {
            case 'proditi':
                return '55201';
            case 'prodisi':
                return '57201';
            default:
                return null;
        }
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

    public function mahasiswa(Request $request)
    {
        $nimPrefix = '';
        if (auth()->user()->name == "prodisi") {
            $nimPrefix = '131';
        } else if (auth()->user()->name == "proditi") {
            $nimPrefix = '130';
        }

        $q = trim((string) $request->get('q', ''));
        $statusAkun = trim((string) $request->get('status_akun', 'semua'));
        $perPage = (int) $request->get('per_page', 25);
        $allowedPerPage = [25, 50, 100, 200];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 25;
        }

        $query = DB::table('t_mst_mahasiswa')
            ->leftJoin('users', function ($join) {
                $join->on('users.name', '=', 't_mst_mahasiswa.C_NPM');
            })
            ->select(
                't_mst_mahasiswa.C_NPM',
                't_mst_mahasiswa.NAMA_MAHASISWA',
                DB::raw('CASE WHEN users.id IS NULL THEN 0 ELSE 1 END AS has_user')
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

        if ($statusAkun === 'aktif') {
            $query->whereNotNull('users.id');
        } elseif ($statusAkun === 'belum') {
            $query->whereNull('users.id');
        }

        $data = $query
            ->orderBy('t_mst_mahasiswa.C_NPM', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        return view('tugasakhir.prodi.mahasiswa', compact('data', 'q', 'statusAkun', 'perPage'));
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

    public function login_as_dosen(Request $request, $id)
    {
        $authUser = auth()->user();
        if (!$authUser || (int) $authUser->level !== 5) {
            return redirect('/')->with('danger', 'Akses login as hanya untuk akun prodi.');
        }

        $dosenUser = DB::table('users')
            ->select('id', 'name', 'level')
            ->where('name', $id)
            ->where('level', 7)
            ->first();

        if (!$dosenUser) {
            return redirect()->back()->with('danger', 'Akun dosen belum tersedia. Silakan daftarkan akun dosen terlebih dahulu.');
        }

        $request->session()->put('login_as_source_user_id', $authUser->id);
        $request->session()->put('login_as_source_user_name', $authUser->name);
        $request->session()->put('login_as_source_user_level', (int) $authUser->level);

        Auth::loginUsingId($dosenUser->id);
        $request->session()->regenerate();

        return redirect('/')->with('success', 'Berhasil login sebagai dosen.');
    }

    public function login_as_mahasiswa(Request $request, $nim)
    {
        $authUser = auth()->user();
        if (!$authUser || (int) $authUser->level !== 5) {
            return redirect('/')->with('danger', 'Akses login as hanya untuk akun prodi.');
        }

        $nimPrefix = $authUser->name === 'prodisi' ? '131' : '130';
        $mahasiswa = DB::table('t_mst_mahasiswa')
            ->select('C_NPM')
            ->where('C_NPM', $nim)
            ->where('C_NPM', 'LIKE', $nimPrefix . '%')
            ->first();

        if (!$mahasiswa) {
            return redirect()->back()->with('danger', 'Mahasiswa tidak ditemukan pada program studi Anda.');
        }

        $mahasiswaUser = DB::table('users')
            ->select('id', 'name', 'level')
            ->where('name', $nim)
            ->where('level', 8)
            ->first();

        if (!$mahasiswaUser) {
            return redirect()->back()->with('danger', 'Akun mahasiswa belum tersedia. Silakan aktifkan akun mahasiswa terlebih dahulu.');
        }

        $request->session()->put('login_as_source_user_id', $authUser->id);
        $request->session()->put('login_as_source_user_name', $authUser->name);
        $request->session()->put('login_as_source_user_level', (int) $authUser->level);

        Auth::loginUsingId($mahasiswaUser->id);
        $request->session()->regenerate();

        return redirect('/')->with('success', 'Berhasil login sebagai mahasiswa.');
    }


    public function surat_pengusulanujianta()
    {
        return view('tugasakhir.prodi.surat_usulantimujian');
    }

    public function topik()
    {
        $nimPrefix = Auth::user()->name == 'proditi' ? '130%' : '131%';

        $data_pengusul = DB::table('trt_topik')
            ->join('t_mst_mahasiswa', 'trt_topik.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->select('t_mst_mahasiswa.C_NPM', 't_mst_mahasiswa.NAMA_MAHASISWA')
            ->where('trt_topik.status', 0)
            ->where('t_mst_mahasiswa.C_NPM', 'LIKE', $nimPrefix)
            ->distinct()
            ->orderBy('t_mst_mahasiswa.C_NPM', 'desc')
            ->get();

        return view('tugasakhir.prodi.topik', compact('data_pengusul'));
    }

    public function topik_riwayat(Request $request)
    {
        $nimPrefix = Auth::user()->name == 'proditi' ? '130%' : '131%';
        $q = trim((string) $request->get('q', ''));
        $jenisTugasAkhirId = (int) $request->get('jenis_tugas_akhir_id', 0);
        $perPage = (int) $request->get('per_page', 50);
        $allowedPerPage = [25, 50, 100, 200];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 50;
        }

        $query = DB::table('trt_topik')
            ->join('t_mst_mahasiswa', 'trt_topik.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->leftJoin('mst_jenis_tugas_akhir', 'trt_topik.jenis_tugas_akhir_id', '=', 'mst_jenis_tugas_akhir.jenis_tugas_akhir_id')
            ->select(
                'trt_topik.topik_id',
                't_mst_mahasiswa.C_NPM',
                't_mst_mahasiswa.NAMA_MAHASISWA',
                'trt_topik.topik',
                'trt_topik.jenis_tugas_akhir_id',
                'mst_jenis_tugas_akhir.kode_jenis_tugas_akhir',
                'trt_topik.kerangka',
                'trt_topik.status'
            )
            ->where('t_mst_mahasiswa.C_NPM', 'LIKE', $nimPrefix);

        if ($q !== '') {
            $query->where(function ($subQuery) use ($q) {
                $subQuery->where('t_mst_mahasiswa.C_NPM', 'LIKE', '%' . $q . '%')
                    ->orWhere('t_mst_mahasiswa.NAMA_MAHASISWA', 'LIKE', '%' . $q . '%')
                    ->orWhere('trt_topik.topik', 'LIKE', '%' . $q . '%');
            });
        }

        if ($jenisTugasAkhirId > 0) {
            $query->where('trt_topik.jenis_tugas_akhir_id', $jenisTugasAkhirId);
        }

        $data_riwayat_usulan = $query
            ->orderBy('trt_topik.topik_id', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        $jenisTugasAkhir = DB::table('mst_jenis_tugas_akhir')
            ->orderBy('kode_jenis_tugas_akhir')
            ->get();

        return view('tugasakhir.prodi.topik_riwayat', compact(
            'data_riwayat_usulan',
            'q',
            'perPage',
            'jenisTugasAkhir',
            'jenisTugasAkhirId'
        ));
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
            ->leftJoin('mst_jenis_tugas_akhir', 'trt_topik.jenis_tugas_akhir_id', '=', 'mst_jenis_tugas_akhir.jenis_tugas_akhir_id')
            ->select('trt_topik.*', 't_mst_mahasiswa.*', 'mst_jenis_tugas_akhir.kode_jenis_tugas_akhir')
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
        $this->validate($request, [
            'C_NPM' => 'required|max:50',
            'topik_id' => 'required|exists:trt_topik,topik_id',
        ]);

        $topik = trt_topik::where('topik_id', $request->topik_id)
            ->where('C_NPM', $request->C_NPM)
            ->where('status', 1)
            ->firstOrFail();

        $datapost = $request->only(['C_NPM', 'pembimbing_I_id', 'pembimbing_II_id']);
        $datapost['topik_id'] = $topik->topik_id;
        $datapost['judul'] = $topik->topik;
        $datapost['jenis_tugas_akhir_id'] = $topik->jenis_tugas_akhir_id;
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

    private function judulTanpaKodeJenisTugasAkhir($judul)
    {
        return trim((string) preg_replace(
            '/^(?:\(\s*[A-Za-z]{2}\s*(?:-|_|\s)\s*[A-Za-z0-9]{2,}\s*\)\s*)+/',
            '',
            trim((string) $judul)
        ));
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
        try {
            $mst_pendaftaran = mst_pendaftaran::where("pendaftaran_id", $pendaftaran_id)->first();

            if (empty($mst_pendaftaran)) {
                return response('Data pendaftaran ujian tidak ditemukan.', 404);
            }

            $info = t_mst_mahasiswa::join("trt_bimbingan", "trt_bimbingan.C_NPM", "=", "t_mst_mahasiswa.C_NPM")
                ->where("t_mst_mahasiswa.C_NPM", $nim)
                ->select(
                    't_mst_mahasiswa.C_NPM',
                    't_mst_mahasiswa.NAMA_MAHASISWA',
                    'trt_bimbingan.judul',
                    'trt_bimbingan.jenis_tugas_akhir_id',
                    'trt_bimbingan.pembimbing_I_id',
                    'trt_bimbingan.pembimbing_II_id'
                )
                ->orderBy('trt_bimbingan.bimbingan_id', 'desc')
                ->first();

            if (empty($info)) {
                return response('Data mahasiswa bimbingan tidak ditemukan.', 404);
            }

            $tipeUjianAktif = isset($mst_pendaftaran->tipe_ujian) ? $mst_pendaftaran->tipe_ujian : $tipe_ujian;
            $currentPenguji = TrtPenguji::where([
                "C_NPM" => $nim,
                "tipe_ujian" => $tipeUjianAktif
            ])->first();

            $namaPembimbing1 = $this->getNamaDosenSetPenguji($info->pembimbing_I_id);
            $namaPembimbing2 = $this->getNamaDosenSetPenguji($info->pembimbing_II_id);

            $excludeDosen = array_values(array_filter([$info->pembimbing_I_id, $info->pembimbing_II_id]));
            $dosen = collect();

            try {
                $queryDosenUtama = DB::table('t_mst_dosen')
                    ->select('C_KODE_DOSEN', 'NAMA_DOSEN');

                if (!empty($excludeDosen)) {
                    $queryDosenUtama->whereNotIn('C_KODE_DOSEN', $excludeDosen);
                }

                $dosen = collect($queryDosenUtama->get());
            } catch (Exception $e) {
                $dosen = collect();
            }

            try {
                $queryDosenMigrasi = DB::table('mig_t_mst_dosen')
                    ->select('C_KODE_DOSEN', 'NAMA_DOSEN');

                if (!empty($excludeDosen)) {
                    $queryDosenMigrasi->whereNotIn('C_KODE_DOSEN', $excludeDosen);
                }

                $dosenMigrasi = collect($queryDosenMigrasi->get());

                foreach ($dosenMigrasi as $row) {
                    if (!$dosen->firstWhere('C_KODE_DOSEN', $row->C_KODE_DOSEN)) {
                        $dosen->push($row);
                    }
                }
            } catch (Exception $e) {
            }

            $dosen = $dosen->sortBy('NAMA_DOSEN')->values();

            return view('tugasakhir.prodi.set_penguji', compact('dosen', 'info', 'pendaftaran_id', 'mst_pendaftaran', 'currentPenguji', 'tipeUjianAktif', 'namaPembimbing1', 'namaPembimbing2'));
        } catch (Exception $e) {
            Log::error('set_penguji error', [
                'pendaftaran_id' => $pendaftaran_id,
                'nim' => $nim,
                'tipe_ujian' => $tipe_ujian,
                'message' => $e->getMessage(),
            ]);
            return response('Terjadi kesalahan saat memuat data set penguji.', 500);
        }
    }

    protected function getNamaDosenSetPenguji($kodeDosen)
    {
        $kodeDosen = trim((string) $kodeDosen);

        if ($kodeDosen === '') {
            return '--';
        }

        $dosen = null;

        try {
            $dosen = DB::table('t_mst_dosen')
                ->select('NAMA_DOSEN')
                ->where('C_KODE_DOSEN', $kodeDosen)
                ->first();
        } catch (Exception $e) {
            $dosen = null;
        }

        if (!$dosen) {
            try {
            $dosen = DB::table('mig_t_mst_dosen')
                ->select('NAMA_DOSEN')
                ->where('C_KODE_DOSEN', $kodeDosen)
                ->first();
            } catch (Exception $e) {
                $dosen = null;
            }
        }

        return !empty($dosen->NAMA_DOSEN) ? $dosen->NAMA_DOSEN : '--';
    }

    public function set_pengujipost($pendaftaran_id, Request $request)
    {
        $mst_pendaftaran = mst_pendaftaran::where("pendaftaran_id", $pendaftaran_id)->first();
        if (empty($mst_pendaftaran)) {
            return redirect()->back()->with('error', 'Data pendaftaran ujian tidak ditemukan.');
        }
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

        if ($datax->isEmpty()) {
            return response('Data surat pengusulan tidak ditemukan atau belum tersedia.', 404);
        }

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

    public function daftar_peserta_index()
    {
        return redirect()->to('/prodi/jadwal');
    }

    public function daftar_peserta($id)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $id)->first();

        if (!$info || !isset($info->tipe_ujian)) {
            return response('Data jadwal ujian tidak ditemukan.', 404);
        }

        $data = DB::select("SELECT * FROM mst_pendaftaran,trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_reg.pendaftaran_id = ? AND trt_reg.status = ?", [$id, $info->tipe_ujian]);


        return view('tugasakhir.prodi.daftar_peserta', compact("data", "info"));
    }

    public function temp_daftar_peserta($id)
    {
        $info = DB::select("SELECT * FROM mst_pendaftaran WHERE mst_pendaftaran.pendaftaran_id = ?", [$id]);

        if (empty($info) || !isset($info[0]->tipe_ujian)) {
            return response('Data periode pendaftaran tidak ditemukan.', 404);
        }

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
        $statusProdi = Auth::user()->name == 'proditi' ? 1 : 2;

        $pendaftaran = mst_pendaftaran::where('status_ujian', 0)
            ->where('status_prodi', $statusProdi)
            ->orderBy('pendaftaran_id', 'asc')
            ->get()
            ->unique('nama_periode')
            ->sortByDesc('created_at')
            ->values();

        $jumlahTipePeriode = DB::table('mst_pendaftaran')
            ->whereIn('nama_periode', $pendaftaran->pluck('nama_periode')->filter()->values())
            ->select('nama_periode', DB::raw('COUNT(*) AS jumlah_tipe_ujian'))
            ->groupBy('nama_periode')
            ->pluck('jumlah_tipe_ujian', 'nama_periode');

        $pendaftaran->each(function ($periode) use ($jumlahTipePeriode) {
            $periode->jumlah_tipe_ujian = (int) $jumlahTipePeriode->get($periode->nama_periode, 0);
        });

        $mstpendaftaran = mst_pendaftaran::whereNotIn('pendaftaran_id', TrtJadwalUjian::select('pendaftaran_id'))
            ->where('status_prodi', $statusProdi)
            ->orderBy('pendaftaran_id', 'asc')
            ->get()
            ->unique('nama_periode')
            ->values();

        $jadwalujian = TrtJadwalUjian::join('mst_pendaftaran', 'mst_pendaftaran.pendaftaran_id', '=', 'trt_jadwal_ujian.pendaftaran_id')
            ->where('mst_pendaftaran.status_prodi', $statusProdi)
            ->orderBy('mst_pendaftaran.created_at', 'desc')
            ->get();

        return view('tugasakhir.prodi.jadwal', compact('pendaftaran', 'mstpendaftaran', 'jadwalujian'));
    }

    public function scope_ta()
    {
        $queryBidangIlmu = DB::table('mst_bidangilmu')
            ->select('*')
            ->orderBy('bidang_ilmu');

        $data = $queryBidangIlmu->get();

        $hasStatusAktif = Schema::hasColumn('mst_bidangilmu', 'status_aktif');

        foreach ($data as $row) {
            if (!$hasStatusAktif) {
                $row->status_aktif = 1;
            }

            $row->status_label = (int) ($row->status_aktif ?? 1) === 1 ? 'Aktif' : 'Tidak Aktif';
        }

        $lulusanPeriodeChart = [];
        $lulusanBidangChart = [];

        $nimLike = '%';
        if (Auth::user()->name == 'proditi') {
            $nimLike = '130%';
        } elseif (Auth::user()->name == 'prodisi') {
            $nimLike = '131%';
        }

        try {
            $lulusanRows = DB::table('trt_bimbingan')
                ->select('C_NPM', 'last_update', 'updated_at', 'created_at')
                ->where('status_bimbingan', 3)
                ->where('C_NPM', 'LIKE', $nimLike)
                ->orderBy('bimbingan_id', 'desc')
                ->get();

            $lulusanNims = [];
            $periodeCounts = [];

            foreach ($lulusanRows as $row) {
                if (in_array($row->C_NPM, $lulusanNims)) {
                    continue;
                }

                $lulusanNims[] = $row->C_NPM;
                $tanggalAcuan = '';

                foreach (['last_update', 'updated_at', 'created_at'] as $field) {
                    if (isset($row->$field) && $row->$field) {
                        $candidate = substr((string) $row->$field, 0, 10);
                        if ($candidate !== '' && $candidate !== '0000-00-00') {
                            $tanggalAcuan = $candidate;
                            break;
                        }
                    }
                }

                $label = 'Tidak diketahui';

                if ($tanggalAcuan !== '') {
                    try {
                        $label = Helper::getSemesterAkademik($tanggalAcuan)->tahun_akademik;
                    } catch (Exception $e) {
                        $label = 'Tidak diketahui';
                    }
                }

                if (!isset($periodeCounts[$label])) {
                    $periodeCounts[$label] = 0;
                }

                $periodeCounts[$label]++;
            }

            uksort($periodeCounts, function ($a, $b) {
                if ($a === 'Tidak diketahui') {
                    return 1;
                }
                if ($b === 'Tidak diketahui') {
                    return -1;
                }
                return strcmp($a, $b);
            });

            foreach ($periodeCounts as $label => $total) {
                $lulusanPeriodeChart[] = [
                    'y' => $label,
                    'total' => (int) $total,
                ];
            }

            $topikRows = DB::table('trt_topik')
                ->select('C_NPM', 'bidang_ilmu_peminatan')
                ->where('status', 1)
                ->where('C_NPM', 'LIKE', $nimLike)
                ->orderBy('topik_id', 'desc')
                ->get();

            $topikByMahasiswa = [];
            foreach ($topikRows as $row) {
                if (!isset($topikByMahasiswa[$row->C_NPM])) {
                    $topikByMahasiswa[$row->C_NPM] = trim((string) $row->bidang_ilmu_peminatan);
                }
            }

            $bidangCounts = [];
            foreach ($lulusanNims as $nim) {
                $label = isset($topikByMahasiswa[$nim]) && $topikByMahasiswa[$nim] !== '' ? $topikByMahasiswa[$nim] : 'Belum diisi';

                if (!isset($bidangCounts[$label])) {
                    $bidangCounts[$label] = 0;
                }

                $bidangCounts[$label]++;
            }

            arsort($bidangCounts);
            $bidangCounts = array_slice($bidangCounts, 0, 10, true);

            foreach ($bidangCounts as $label => $total) {
                $lulusanBidangChart[] = [
                    'y' => $label,
                    'total' => (int) $total,
                ];
            }
        } catch (Exception $e) {
            Log::error('scope_ta chart error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
        }

        return view('tugasakhir.prodi.scope_ta', compact('data', 'lulusanPeriodeChart', 'lulusanBidangChart'));
    }

    public function master_jenis_tugas_akhir()
    {
        $hasMahasiswaAvailability = Schema::hasColumn('mst_jenis_tugas_akhir', 'tersedia_untuk_mahasiswa');
        $data = DB::table('mst_jenis_tugas_akhir')
            ->select('*')
            ->orderBy('kode_jenis_tugas_akhir')
            ->get();

        if (!$hasMahasiswaAvailability) {
            $data->each(function ($jenis) {
                $jenis->tersedia_untuk_mahasiswa = 1;
            });
        }

        return view('tugasakhir.prodi.master_jenis_tugas_akhir', compact('data', 'hasMahasiswaAvailability'));
    }

    public function master_dosen()
    {
        return $this->renderMasterDosenPage();
    }

    public function master_dosen_edit($kode_dosen)
    {
        return $this->renderMasterDosenPage($kode_dosen);
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
        $this->validate($request, [
            'bidang_ilmu' => 'required|max:255',
            'status_aktif' => 'nullable|in:0,1',
        ], [
            'bidang_ilmu.required' => 'Nama bidang ilmu wajib diisi.',
        ]);

        $datapost = [
            'bidang_ilmu' => trim((string) $request->bidang_ilmu),
        ];

        if (Schema::hasColumn('mst_bidangilmu', 'status_aktif')) {
            $datapost['status_aktif'] = (int) $request->input('status_aktif', 1);
        }

        mst_bidangilmu::create($datapost);
        return redirect::to('prodi/scope_ta');
    }

    public function scope_update(Request $request, $id)
    {
        $this->validate($request, [
            'bidang_ilmu' => 'required|max:255',
            'status_aktif' => 'nullable|in:0,1',
        ], [
            'bidang_ilmu.required' => 'Nama bidang ilmu wajib diisi.',
        ]);

        $record = DB::table('mst_bidangilmu')
            ->where('bidangilmu_id', $id)
            ->first();

        if (!$record) {
            return redirect::to('prodi/scope_ta')->with('danger', 'Data bidang ilmu tidak ditemukan.');
        }

        $payload = [
            'bidang_ilmu' => trim((string) $request->bidang_ilmu),
        ];

        if (Schema::hasColumn('mst_bidangilmu', 'status_aktif')) {
            $payload['status_aktif'] = (int) $request->input('status_aktif', 1);
        }

        DB::table('mst_bidangilmu')
            ->where('bidangilmu_id', $id)
            ->update($payload);

        return redirect::to('prodi/scope_ta')->with('success', 'Data bidang ilmu berhasil diperbarui.');
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
            $payload = [
                'kode_jenis_tugas_akhir' => trim($request->kode_jenis_tugas_akhir),
                'deskripsi' => trim($request->deskripsi),
            ];

            if (Schema::hasColumn('mst_jenis_tugas_akhir', 'tersedia_untuk_mahasiswa')) {
                $payload['tersedia_untuk_mahasiswa'] = (int) $request->input('tersedia_untuk_mahasiswa', 1);
            }

            mst_jenis_tugas_akhir::create($payload);

            return redirect::to('prodi/master/jenis_tugas_akhir')->with('success', 'Data berhasil disimpan.');
        } catch (Exception $e) {
            return redirect::to('prodi/master/jenis_tugas_akhir')->with('danger', 'Data gagal disimpan.');
        }
    }

    public function master_dosen_store(Request $request)
    {
        $this->validateMasterDosenRequest($request);

        $kodeDosen = trim((string) $request->C_KODE_DOSEN);
        $noHp = trim((string) $request->NO_HP);

        if ($this->isMasterDosenKodeUsed($kodeDosen)) {
            return redirect()->back()->withInput()->withErrors([
                'C_KODE_DOSEN' => 'Kode dosen sudah digunakan.',
            ]);
        }

        if ($noHp !== '' && $this->isMasterDosenNoHpUsed($noHp)) {
            return redirect()->back()->withInput()->withErrors([
                'NO_HP' => 'Nomor HP sudah digunakan oleh dosen lain.',
            ]);
        }

        try {
            $payload = $this->buildMasterDosenPayload($request);
            if ($request->hasFile('foto_dosen')) {
                $payload['D_FOTO_DOSEN'] = $request->file('foto_dosen')->store('dosen', 'public');
            }
            $this->syncMasterDosenTables($payload);

            return redirect::to('prodi/master/dosen')->with('success', 'Data dosen berhasil disimpan dan disinkronkan.');
        } catch (Exception $e) {
            Log::error('master_dosen_store error', [
                'kode_dosen' => $kodeDosen,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return redirect::to('prodi/master/dosen')->withInput()->with('danger', 'Data dosen gagal disimpan.');
        }
    }

    public function master_dosen_update(Request $request, $kode_dosen)
    {
        $this->validateMasterDosenRequest($request);

        $kodeDosenBaru = trim((string) $request->C_KODE_DOSEN);
        $noHp = trim((string) $request->NO_HP);
        $existing = $this->findMasterDosenByKode($kode_dosen);

        if (!$existing) {
            return redirect::to('prodi/master/dosen')->with('danger', 'Data dosen tidak ditemukan.');
        }

        if ($this->isMasterDosenKodeUsed($kodeDosenBaru, $kode_dosen)) {
            return redirect()->back()->withInput()->withErrors([
                'C_KODE_DOSEN' => 'Kode dosen sudah digunakan.',
            ]);
        }

        if ($noHp !== '' && $this->isMasterDosenNoHpUsed($noHp, $kode_dosen)) {
            return redirect()->back()->withInput()->withErrors([
                'NO_HP' => 'Nomor HP sudah digunakan oleh dosen lain.',
            ]);
        }

        try {
            $payload = $this->buildMasterDosenPayload($request, $existing);
            if ($request->hasFile('foto_dosen')) {
                $payload['D_FOTO_DOSEN'] = $request->file('foto_dosen')->store('dosen', 'public');
            }
            $this->syncMasterDosenTables($payload, $kode_dosen);

            return redirect::to('prodi/master/dosen')->with('success', 'Data dosen berhasil diperbarui.');
        } catch (Exception $e) {
            Log::error('master_dosen_update error', [
                'kode_dosen_lama' => $kode_dosen,
                'kode_dosen_baru' => $kodeDosenBaru,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return redirect::to('prodi/master/dosen/edit/' . $kode_dosen)->withInput()->with('danger', 'Data dosen gagal diperbarui.');
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

    public function master_jenis_tugas_akhir_availability(Request $request, $id)
    {
        if (!Schema::hasColumn('mst_jenis_tugas_akhir', 'tersedia_untuk_mahasiswa')) {
            return redirect::to('prodi/master/jenis_tugas_akhir')->with('danger', 'Pengaturan ketersediaan belum tersedia.');
        }

        $this->validate($request, [
            'tersedia_untuk_mahasiswa' => 'required|in:0,1',
        ]);

        $updated = DB::table('mst_jenis_tugas_akhir')
            ->where('jenis_tugas_akhir_id', $id)
            ->update([
                'tersedia_untuk_mahasiswa' => (int) $request->tersedia_untuk_mahasiswa,
                'updated_at' => Carbon::now(),
            ]);

        if (!$updated) {
            return redirect::to('prodi/master/jenis_tugas_akhir')->with('danger', 'Jenis tugas akhir tidak ditemukan.');
        }

        return redirect::to('prodi/master/jenis_tugas_akhir')->with('success', 'Ketersediaan jenis tugas akhir berhasil diperbarui.');
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

        if (!$info || !isset($info->tipe_ujian)) {
            return response('Data SK ujian tidak ditemukan atau belum memiliki jadwal ujian.', 404);
        }

        $data = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
            ->join("t_mst_mahasiswa", "t_mst_mahasiswa.C_NPM", "=", "trt_reg.C_NPM")
            ->join("trt_penguji", "trt_penguji.C_NPM", "=", "t_mst_mahasiswa.C_NPM")
            ->where([
                "trt_reg.pendaftaran_id" => $id,
                "trt_penguji.tipe_ujian" => $info->tipe_ujian
            ])->get();

        if ($data->isEmpty()) {
            return response('Data peserta SK ujian tidak ditemukan atau belum lengkap.', 404);
        }

        return view('tugasakhir.prodi.detail_skujian', compact("info", "data"));
    }

    public function pengumuman()
    {
        $data = mst_pengumuman::orderBy('created_at', 'desc')->get();
        return view('tugasakhir.prodi.pengumuman', compact('data'));
    }



    public function pengumumanpost(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'last_update' => 'required',
            'isi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,gif,png|max:5120',
        ]);
        $imagePath = '';

        try {
            if ($request->hasFile('gambar')) {
                $imagePath = Helper::storeAnnouncementImage($request->file('gambar'));
            }

            mst_pengumuman::create([
                'judul' => $validated['judul'],
                'gambar' => $imagePath,
                'last_update' => Helper::tgl($validated['last_update']),
                'isi' => $validated['isi'],
                'user_id' => '1',
            ]);

            return redirect::to('prodi/pengumuman/')->with('status', 'success');
        } catch (Exception $exception) {
            Helper::deleteAnnouncementImage($imagePath);
            Log::error('Gagal membuat pengumuman.', ['exception' => $exception]);
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
        $validated = $request->validate([
            'id' => 'required|integer',
            'judul' => 'required|string|max:255',
            'last_update' => 'required',
            'isi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,gif,png|max:5120',
        ]);
        $announcement = mst_pengumuman::findOrFail($validated['id']);
        $oldImagePath = $announcement->gambar;
        $newImagePath = null;

        try {
            if ($request->hasFile('gambar')) {
                $newImagePath = Helper::storeAnnouncementImage($request->file('gambar'));
            }

            $update = [
                'judul' => $validated['judul'],
                'last_update' => Helper::tgl($validated['last_update']),
                'isi' => $validated['isi'],
            ];

            if ($newImagePath !== null) {
                $update['gambar'] = $newImagePath;
            }

            if (!$announcement->update($update)) {
                throw new RuntimeException('Pengumuman gagal diperbarui.');
            }

            if ($newImagePath !== null) {
                Helper::deleteAnnouncementImage($oldImagePath);
            }

            return redirect::to('prodi/pengumuman/')->with('status', 'success');
        } catch (Exception $exception) {
            Helper::deleteAnnouncementImage($newImagePath);
            Log::error('Gagal memperbarui pengumuman.', ['exception' => $exception]);
            return redirect::to('prodi/pengumuman/')->with('status', 'error');
        }
    }

    public function pengumumandel($id)
    {
        $announcement = mst_pengumuman::findOrFail($id);
        $imagePath = $announcement->gambar;

        try {
            if (!$announcement->delete()) {
                throw new RuntimeException('Pengumuman gagal dihapus.');
            }

            Helper::deleteAnnouncementImage($imagePath);
            return redirect::to('prodi/pengumuman')->with('status', 'success');
        } catch (Exception $exception) {
            Log::error('Gagal menghapus pengumuman.', ['exception' => $exception]);
            return redirect::to('prodi/pengumuman')->with('status', 'error');
        }
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

        $reportWarnings = [];

        $statusCounts = $this->safeReportSection(
            'status_bimbingan',
            function () use ($nimLike) {
                return DB::table('trt_bimbingan')
                    ->select('status_bimbingan', DB::raw('COUNT(DISTINCT C_NPM) as total'))
                    ->where('C_NPM', 'LIKE', $nimLike)
                    ->groupBy('status_bimbingan')
                    ->pluck('total', 'status_bimbingan')
                    ->toArray();
            },
            [],
            $reportWarnings
        );

        $statusBimbinganChart = [
            ['label' => 'Persiapan Proposal', 'value' => (int) ($statusCounts[0] ?? 0)],
            ['label' => 'Persiapan Ujian Meja', 'value' => (int) ($statusCounts[2] ?? 0)],
            ['label' => 'Lulus', 'value' => (int) ($statusCounts[3] ?? 0)],
            ['label' => 'Non Aktif', 'value' => (int) ($statusCounts[4] ?? 0)],
        ];

        $topikCounts = $this->safeReportSection(
            'status_topik',
            function () use ($nimLike) {
                return DB::table('trt_topik')
                    ->join('t_mst_mahasiswa', 't_mst_mahasiswa.C_NPM', '=', 'trt_topik.C_NPM')
                    ->where('t_mst_mahasiswa.C_NPM', 'LIKE', $nimLike)
                    ->select('trt_topik.status', DB::raw('COUNT(*) as total'))
                    ->groupBy('trt_topik.status')
                    ->pluck('total', 'trt_topik.status')
                    ->toArray();
            },
            [],
            $reportWarnings
        );

        $topikStatusChart = [
            ['label' => 'Belum dikonfirmasi', 'value' => (int) ($topikCounts[0] ?? 0)],
            ['label' => 'Diterima', 'value' => (int) ($topikCounts[1] ?? 0)],
            ['label' => 'Ditolak', 'value' => (int) ($topikCounts[2] ?? 0)],
        ];

        $bidangIlmuChart = $this->safeReportSection(
            'bidang_ilmu',
            function () use ($nimLike) {
                $bidangIlmuRaw = DB::table('trt_topik')
                    ->join('t_mst_mahasiswa', 't_mst_mahasiswa.C_NPM', '=', 'trt_topik.C_NPM')
                    ->where('t_mst_mahasiswa.C_NPM', 'LIKE', $nimLike)
                    ->where('trt_topik.status', 1)
                    ->select('trt_topik.bidang_ilmu_peminatan', DB::raw('COUNT(*) as total'))
                    ->groupBy('trt_topik.bidang_ilmu_peminatan')
                    ->get();

                return $bidangIlmuRaw
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
            },
            [],
            $reportWarnings
        );

        $dosenPembimbingChart = $this->safeReportSection(
            'dosen_pembimbing',
            function () use ($nimLike) {
                $rows = DB::select(
                    "
                    SELECT
                        pembimbing.kode_dosen,
                        COALESCE(td.NAMA_DOSEN, md.NAMA_DOSEN, pembimbing.kode_dosen) AS nama_dosen,
                        SUM(CASE WHEN pembimbing.status_bimbingan IN (0,1,2) THEN 1 ELSE 0 END) AS aktif,
                        SUM(CASE WHEN pembimbing.status_bimbingan = 3 THEN 1 ELSE 0 END) AS lulus
                    FROM (
                        SELECT pembimbing_I_id AS kode_dosen, status_bimbingan
                        FROM trt_bimbingan
                        WHERE C_NPM LIKE ? AND pembimbing_I_id IS NOT NULL AND pembimbing_I_id <> ''

                        UNION ALL

                        SELECT pembimbing_II_id AS kode_dosen, status_bimbingan
                        FROM trt_bimbingan
                        WHERE C_NPM LIKE ? AND pembimbing_II_id IS NOT NULL AND pembimbing_II_id <> ''
                    ) AS pembimbing
                    LEFT JOIN t_mst_dosen td ON td.C_KODE_DOSEN = pembimbing.kode_dosen
                    LEFT JOIN mig_t_mst_dosen md ON md.C_KODE_DOSEN = pembimbing.kode_dosen
                    GROUP BY pembimbing.kode_dosen, td.NAMA_DOSEN, md.NAMA_DOSEN
                    HAVING aktif > 0 OR lulus > 0
                    ORDER BY aktif DESC, lulus DESC, nama_dosen ASC
                    LIMIT 10
                    ",
                    [$nimLike, $nimLike]
                );

                return collect($rows)
                    ->map(function ($item) {
                        $namaDosen = trim((string) $item->nama_dosen);

                        return [
                            'y' => $this->shortenChartLabel($namaDosen, 24),
                            'aktif' => (int) $item->aktif,
                            'lulus' => (int) $item->lulus,
                            'full_label' => $namaDosen,
                            'kode_dosen' => $item->kode_dosen,
                        ];
                    })
                    ->all();
            },
            [],
            $reportWarnings
        );

        $lamaBimbinganPerDosen = $this->safeReportSection(
            'lama_bimbingan_dosen',
            function () use ($nimLike) {
                $rows = DB::select(
                    "
                    SELECT
                        pembimbing.kode_dosen,
                        COALESCE(td.NAMA_DOSEN, md.NAMA_DOSEN, pembimbing.kode_dosen) AS nama_dosen,
                        ROUND(AVG(pembimbing.lama_hari), 1) AS rata_hari,
                        COUNT(*) AS total_lulus
                    FROM (
                        SELECT
                            tb.pembimbing_I_id AS kode_dosen,
                            DATEDIFF(DATE(tb.last_update), sk.tgl_sk_penetapan) AS lama_hari
                        FROM trt_bimbingan tb
                        INNER JOIN (
                            SELECT bimbingan_id, MIN(created_at) AS tgl_sk_penetapan
                            FROM mst_sk_pembimbing
                            GROUP BY bimbingan_id
                        ) sk ON sk.bimbingan_id = tb.bimbingan_id
                        WHERE tb.C_NPM LIKE ?
                          AND tb.status_bimbingan = 3
                          AND tb.pembimbing_I_id IS NOT NULL
                          AND tb.pembimbing_I_id <> ''
                          AND DATE(tb.last_update) >= sk.tgl_sk_penetapan

                        UNION ALL

                        SELECT
                            tb.pembimbing_II_id AS kode_dosen,
                            DATEDIFF(DATE(tb.last_update), sk.tgl_sk_penetapan) AS lama_hari
                        FROM trt_bimbingan tb
                        INNER JOIN (
                            SELECT bimbingan_id, MIN(created_at) AS tgl_sk_penetapan
                            FROM mst_sk_pembimbing
                            GROUP BY bimbingan_id
                        ) sk ON sk.bimbingan_id = tb.bimbingan_id
                        WHERE tb.C_NPM LIKE ?
                          AND tb.status_bimbingan = 3
                          AND tb.pembimbing_II_id IS NOT NULL
                          AND tb.pembimbing_II_id <> ''
                          AND DATE(tb.last_update) >= sk.tgl_sk_penetapan
                    ) AS pembimbing
                    LEFT JOIN t_mst_dosen td ON td.C_KODE_DOSEN = pembimbing.kode_dosen
                    LEFT JOIN mig_t_mst_dosen md ON md.C_KODE_DOSEN = pembimbing.kode_dosen
                    GROUP BY pembimbing.kode_dosen, td.NAMA_DOSEN, md.NAMA_DOSEN
                    HAVING total_lulus > 0
                    ORDER BY rata_hari DESC, nama_dosen ASC
                    ",
                    [$nimLike, $nimLike]
                );

                return collect($rows)
                    ->map(function ($item) {
                        $namaDosen = trim((string) $item->nama_dosen);
                        $rataHari = (float) $item->rata_hari;

                        return [
                            'kode_dosen' => $item->kode_dosen,
                            'nama_dosen' => $namaDosen,
                            'rata_hari' => $rataHari,
                            'rata_label' => $this->formatAverageDurationDays($rataHari),
                            'total_lulus' => (int) $item->total_lulus,
                        ];
                    })
                    ->all();
            },
            [],
            $reportWarnings
        );

        $lamaBimbinganChart = collect($lamaBimbinganPerDosen)
            ->take(10)
            ->map(function ($item) {
                return [
                    'y' => $this->shortenChartLabel($item['nama_dosen'], 24),
                    'rata_hari' => (float) $item['rata_hari'],
                    'full_label' => $item['nama_dosen'],
                ];
            })
            ->values()
            ->all();

        $periodePesertaChart = $this->safeReportSection(
            'periode_peserta',
            function () use ($statusProdi) {
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

                return array_slice(array_values($periodePesertaMap), -10);
            },
            [],
            $reportWarnings
        );

        $nilaiRataRaw = $this->safeReportSection(
            'nilai_komponen',
            function () use ($nimLike) {
                return DB::table('trt_hasil')
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
            },
            collect(),
            $reportWarnings
        );

        $proposalNilai = $nilaiRataRaw->get(0);
        $ujianMejaNilai = $nilaiRataRaw->get(2);

        $nilaiKomponenChart = [
            ['y' => 'Komponen 1', 'proposal' => round((float) ($proposalNilai->nilai_1 ?? 0), 2), 'ujian_meja' => round((float) ($ujianMejaNilai->nilai_1 ?? 0), 2)],
            ['y' => 'Komponen 2', 'proposal' => round((float) ($proposalNilai->nilai_2 ?? 0), 2), 'ujian_meja' => round((float) ($ujianMejaNilai->nilai_2 ?? 0), 2)],
            ['y' => 'Komponen 3', 'proposal' => round((float) ($proposalNilai->nilai_3 ?? 0), 2), 'ujian_meja' => round((float) ($ujianMejaNilai->nilai_3 ?? 0), 2)],
            ['y' => 'Komponen 4', 'proposal' => round((float) ($proposalNilai->nilai_4 ?? 0), 2), 'ujian_meja' => round((float) ($ujianMejaNilai->nilai_4 ?? 0), 2)],
            ['y' => 'Komponen 5', 'proposal' => round((float) ($proposalNilai->nilai_5 ?? 0), 2), 'ujian_meja' => round((float) ($ujianMejaNilai->nilai_5 ?? 0), 2)],
        ];

        $dokumenProposalChart = $this->safeReportSection(
            'dokumen_proposal',
            function () use ($nimLike) {
                return $this->getSyaratStatusChart($nimLike, 0);
            },
            [
                ['label' => 'Diterima', 'value' => 0],
                ['label' => 'Ditolak', 'value' => 0],
                ['label' => 'Menunggu', 'value' => 0],
            ],
            $reportWarnings
        );
        $dokumenUjianMejaChart = $this->safeReportSection(
            'dokumen_ujian_meja',
            function () use ($nimLike) {
                return $this->getSyaratStatusChart($nimLike, 2);
            },
            [
                ['label' => 'Diterima', 'value' => 0],
                ['label' => 'Ditolak', 'value' => 0],
                ['label' => 'Menunggu', 'value' => 0],
            ],
            $reportWarnings
        );
        $skPembimbingChart = $this->safeReportSection(
            'sk_pembimbing',
            function () use ($nimLike) {
                return $this->getSkStatusChart($nimLike, 'mst_sk_pembimbing');
            },
            [
                ['label' => 'Menunggu WD', 'value' => 0],
                ['label' => 'Menunggu Dekan', 'value' => 0],
                ['label' => 'Selesai', 'value' => 0],
            ],
            $reportWarnings
        );
        $skPenugasanChart = $this->safeReportSection(
            'sk_penugasan',
            function () use ($nimLike) {
                return $this->getSkStatusChart($nimLike, 'mst_sk_penugasan');
            },
            [
                ['label' => 'Menunggu WD', 'value' => 0],
                ['label' => 'Menunggu Dekan', 'value' => 0],
                ['label' => 'Selesai', 'value' => 0],
            ],
            $reportWarnings
        );

        $summaryCards = [
            ['label' => 'Persiapan Proposal', 'value' => (int) ($statusCounts[0] ?? 0), 'class' => 'success', 'icon' => 'fa-file-text-o'],
            ['label' => 'Persiapan Ujian Meja', 'value' => (int) ($statusCounts[2] ?? 0), 'class' => 'primary', 'icon' => 'fa-graduation-cap'],
            ['label' => 'Lulus', 'value' => (int) ($statusCounts[3] ?? 0), 'class' => 'danger', 'icon' => 'fa-trophy'],
            ['label' => 'Non Aktif', 'value' => (int) ($statusCounts[4] ?? 0), 'class' => 'warning', 'icon' => 'fa-user-times'],
        ];

        $queueCards = [
            [
                'label' => 'Total Mahasiswa TA',
                'value' => $this->safeReportSection(
                    'summary_total_mahasiswa_ta',
                    function () use ($nimLike) {
                        return DB::table('trt_bimbingan')->where('C_NPM', 'LIKE', $nimLike)->distinct()->count('C_NPM');
                    },
                    0,
                    $reportWarnings
                ),
                'icon' => 'fa-users',
            ],
            [
                'label' => 'Topik Menunggu',
                'value' => (int) ($topikCounts[0] ?? 0),
                'icon' => 'fa-files-o',
            ],
            [
                'label' => 'Antrian Proposal',
                'value' => $this->safeReportSection(
                    'queue_proposal',
                    function () use ($nimLike) {
                        return TrtPengajuanDokumen::join('t_mst_mahasiswa', 't_mst_mahasiswa.C_NPM', '=', 'trt_pengajuan_dokumen.C_NPM')
                            ->where('type', 0)
                            ->where('t_mst_mahasiswa.C_NPM', 'LIKE', $nimLike)
                            ->count();
                    },
                    0,
                    $reportWarnings
                ),
                'icon' => 'fa-upload',
            ],
            [
                'label' => 'Antrian Ujian Meja',
                'value' => $this->safeReportSection(
                    'queue_ujian_meja',
                    function () use ($nimLike) {
                        return TrtPengajuanDokumen::join('t_mst_mahasiswa', 't_mst_mahasiswa.C_NPM', '=', 'trt_pengajuan_dokumen.C_NPM')
                            ->where('type', 2)
                            ->where('t_mst_mahasiswa.C_NPM', 'LIKE', $nimLike)
                            ->count();
                    },
                    0,
                    $reportWarnings
                ),
                'icon' => 'fa-folder-open-o',
            ],
        ];

        return view('tugasakhir.prodi.report', compact(
            'reportContext',
            'reportWarnings',
            'summaryCards',
            'queueCards',
            'statusBimbinganChart',
            'topikStatusChart',
            'bidangIlmuChart',
            'dosenPembimbingChart',
            'lamaBimbinganChart',
            'lamaBimbinganPerDosen',
            'periodePesertaChart',
            'nilaiKomponenChart',
            'dokumenProposalChart',
            'dokumenUjianMejaChart',
            'skPembimbingChart',
            'skPenugasanChart'
        ));
    }

    protected function safeReportSection($section, callable $callback, $fallback, array &$warnings = [])
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            $warnings[] = $section;
            Log::error('Report Prodi gagal memuat section', [
                'section' => $section,
                'user' => optional(auth()->user())->email,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $fallback;
        }
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

    protected function formatAverageDurationDays($days)
    {
        $days = (int) round((float) $days);

        if ($days <= 0) {
            return '0 hari';
        }

        $months = (int) floor($days / 30);
        $remainingDays = $days % 30;
        $parts = [];

        if ($months > 0) {
            $parts[] = $months . ' bulan';
        }

        if ($remainingDays > 0 || empty($parts)) {
            $parts[] = $remainingDays . ' hari';
        }

        return implode(' ', $parts);
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

        if (empty($info) || !isset($info->tipe_ujian)) {
            return response('Data jadwal ujian per mahasiswa tidak ditemukan.', 404);
        }

        $data = DB::select("SELECT * FROM mst_pendaftaran,trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_reg.pendaftaran_id = ? AND trt_reg.status = ?", [$pendaftaran_id, $info->tipe_ujian]);

        // return $data;

        return view('tugasakhir.prodi.detail_jadwalpermhs', compact("data", "info"));
    }

    public function set_jadwalujianpermhs($pendaftaran_id, $nim)
    {
        $xinfo = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $pendaftaran_id)->first();

        if (empty($xinfo) || !isset($xinfo->tipe_ujian)) {
            return response('Data jadwal ujian tidak ditemukan.', 404);
        }

        $info = trt_reg::join("trt_bimbingan", "trt_bimbingan.bimbingan_id", "=", "trt_reg.bimbingan_id")
            ->join("t_mst_mahasiswa", "t_mst_mahasiswa.C_NPM", "=", "trt_bimbingan.C_NPM")
            ->leftJoin("trt_penguji", function ($join) use ($xinfo) {
                $join->on("trt_penguji.C_NPM", "=", "t_mst_mahasiswa.C_NPM")
                    ->where("trt_penguji.tipe_ujian", "=", $xinfo->tipe_ujian);
            })
            ->where([
                "trt_reg.pendaftaran_id" => $pendaftaran_id,
                "trt_reg.status" => $xinfo->tipe_ujian,
                "t_mst_mahasiswa.C_NPM" => $nim
            ])->first();

        if (empty($info)) {
            return response('Data peserta jadwal ujian tidak ditemukan.', 404);
        }

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
        $request->merge(["jam_ujian" => $this->normalizeJamUjian($request->jam_ujian)]);
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

    protected function normalizeJamUjian($jamInput)
    {
        $jamInput = trim((string) $jamInput);
        if ($jamInput === '') {
            return $jamInput;
        }

        // Ambil format jam dasar HH:MM jika input berupa rentang "HH:MM-XX:YY"
        if (preg_match('/^([0-2][0-9]:[0-5][0-9])/', $jamInput, $matches)) {
            return $matches[1];
        }

        return $jamInput;
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
        $query = DB::table('trt_bimbingan')
            ->where('status_bimbingan', $status)
            ->whereBetween('updated_at', [$tanggal_dari, $tanggal_sampai]);

        if (Auth::user()->name == 'proditi') {
            $query->where('trt_bimbingan.C_NPM', 'LIKE', '130%');
        } elseif (Auth::user()->name == 'prodisi') {
            $query->where('trt_bimbingan.C_NPM', 'LIKE', '131%');
        }

        $data = $query
            ->orderBy('updated_at', 'desc')
            ->get();

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
                ->orderBy('tgl_surat', 'DESC')
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
                ->orderBy('tgl_surat', 'DESC')
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

        if ($data->isEmpty()) {
            return response('Data surat usulan tim ujian tidak ditemukan atau belum tersedia.', 404);
        }

        $perihal = $data[0]->perihal;
        $tgl_ujian = $data[0]->tgl_surat;
        $data = $data[0]->pendaftaran_id;
        $tgl_ujian = helper::tgl_indo_lengkap($tgl_ujian);


        $datax = DB::table('mst_pendaftaran')
            ->select('*')
            ->where('mst_pendaftaran.pendaftaran_id', '=', $data)
            ->get();

        if ($datax->isEmpty()) {
            return response('Data pendaftaran untuk surat usulan tim ujian tidak ditemukan.', 404);
        }

        return view('tugasakhir.prodi.surat_usulantimujian', compact('nomor', 'perihal', 'datax', 'tgl_ujian'));
    }

    protected function renderMasterDosenPage($editKodeDosen = null)
    {
        $currentProdi = $this->getCurrentMasterDosenProdi();
        $prodiList = $this->getMasterDosenProdiList();
        $data = $this->getMasterDosenList();
        $editData = null;

        if ($editKodeDosen) {
            $editData = $this->findMasterDosenByKode($editKodeDosen);

            if (!$editData) {
                return redirect::to('prodi/master/dosen')->with('danger', 'Data dosen tidak ditemukan.');
            }
        }

        return view('tugasakhir.prodi.master_dosen', compact('data', 'prodiList', 'editData', 'currentProdi'));
    }

    protected function getMasterDosenList($kodeProdi = null)
    {
        $rowsUtama = collect(
            DB::table('t_mst_dosen')
                ->select('*')
                ->when($kodeProdi, function ($query) use ($kodeProdi) {
                    return $query->where('C_KODE_PRODI', $kodeProdi);
                })
                ->get()
        )->keyBy('C_KODE_DOSEN');

        $rowsMig = collect();
        if (Schema::hasTable('mig_t_mst_dosen')) {
            $rowsMig = collect(
                DB::table('mig_t_mst_dosen')
                    ->select('*')
                    ->when($kodeProdi, function ($query) use ($kodeProdi) {
                        return $query->where('C_KODE_PRODI', $kodeProdi);
                    })
                    ->get()
            )->keyBy('C_KODE_DOSEN');
        }

        $prodiMap = [];
        if (Schema::hasTable('trt_prodi')) {
            $prodiMap = DB::table('trt_prodi')
                ->pluck('nama', 'kode_prodi')
                ->toArray();
        }

        $data = collect();
        $allKodes = $rowsUtama->keys()->merge($rowsMig->keys())->unique()->filter();

        foreach ($allKodes as $kodeDosen) {
            $utama = $rowsUtama->get($kodeDosen);
            $mig = $rowsMig->get($kodeDosen);
            $merged = (object) array_merge((array) $mig, (array) $utama);

            $merged->exists_t_mst_dosen = $utama ? 1 : 0;
            $merged->exists_mig_t_mst_dosen = $mig ? 1 : 0;
            $merged->status_sinkron = $utama && $mig ? 'Lengkap' : 'Perlu Sinkron';
            $merged->nama_prodi = isset($prodiMap[$merged->C_KODE_PRODI]) ? $prodiMap[$merged->C_KODE_PRODI] : $merged->C_KODE_PRODI;
            $merged->status_aktif_label = (int) ($merged->F_AKTIF ?? 0) === 1 ? 'Aktif' : 'Non Aktif';
            $merged->foto_url = Helper::dosenPhotoUrl($merged->D_FOTO_DOSEN ?? '');

            $data->push($merged);
        }

        return $data->sortBy(function ($row) {
            return strtolower(trim((string) ($row->NAMA_DOSEN ?? '')));
        })->values();
    }

    protected function getMasterDosenProdiList()
    {
        if (!Schema::hasTable('trt_prodi')) {
            return collect();
        }

        return DB::table('trt_prodi')
            ->select('kode_prodi', 'nama')
            ->orderBy('nama')
            ->get();
    }

    protected function getCurrentMasterDosenProdi()
    {
        if (!Schema::hasTable('trt_prodi')) {
            return null;
        }

        $namaProdi = Helper::getProgramStudiByAuthUser(Auth::user()->name);
        if ($namaProdi === '') {
            return null;
        }

        return DB::table('trt_prodi')
            ->select('kode_prodi', 'nama')
            ->whereRaw('LOWER(TRIM(nama)) = ?', [strtolower(trim($namaProdi))])
            ->first();
    }

    protected function findMasterDosenByKode($kodeDosen)
    {
        $utama = DB::table('t_mst_dosen')
            ->select('*')
            ->where('C_KODE_DOSEN', $kodeDosen)
            ->first();

        $mig = null;
        if (Schema::hasTable('mig_t_mst_dosen')) {
            $mig = DB::table('mig_t_mst_dosen')
                ->select('*')
                ->where('C_KODE_DOSEN', $kodeDosen)
                ->first();
        }

        if (!$utama && !$mig) {
            return null;
        }

        $data = (object) array_merge((array) $mig, (array) $utama);
        $data->exists_t_mst_dosen = $utama ? 1 : 0;
        $data->exists_mig_t_mst_dosen = $mig ? 1 : 0;

        return $data;
    }

    protected function validateMasterDosenRequest(Request $request)
    {
        $this->validate($request, [
            'C_KODE_DOSEN' => 'required|max:20',
            'C_NIP' => 'nullable|max:50',
            'NAMA_DOSEN' => 'required|max:100',
            'C_KODE_PRODI' => 'required|max:20',
            'JENIS_KELAMIN' => 'nullable|in:Pria,Wanita',
            'NO_HP' => 'nullable|max:15',
            'EMAIL' => 'required|email|max:50',
            'pangkat' => 'nullable|max:100',
            'ALAMAT' => 'nullable',
            'jabatan_fungsional' => 'nullable|in:Asisten Ahli,Lektor,Lektor Kepala,Guru Besar',
            'F_AKTIF' => 'nullable|in:0,1',
            'foto_dosen' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ], [
            'C_KODE_DOSEN.required' => 'Kode dosen wajib diisi.',
            'NAMA_DOSEN.required' => 'Nama dosen wajib diisi.',
            'C_KODE_PRODI.required' => 'Program studi wajib dipilih.',
            'EMAIL.required' => 'Email wajib diisi.',
            'EMAIL.email' => 'Format email tidak valid.',
        ]);
    }

    protected function isMasterDosenKodeUsed($kodeDosen, $ignoreKodeDosen = null)
    {
        $kodeDosen = trim((string) $kodeDosen);
        $ignoreKodeDosen = trim((string) $ignoreKodeDosen);

        $usedInUtama = DB::table('t_mst_dosen')
            ->where('C_KODE_DOSEN', $kodeDosen)
            ->when($ignoreKodeDosen !== '', function ($query) use ($ignoreKodeDosen) {
                return $query->where('C_KODE_DOSEN', '<>', $ignoreKodeDosen);
            })
            ->exists();

        if ($usedInUtama) {
            return true;
        }

        if (Schema::hasTable('mig_t_mst_dosen')) {
            return DB::table('mig_t_mst_dosen')
                ->where('C_KODE_DOSEN', $kodeDosen)
                ->when($ignoreKodeDosen !== '', function ($query) use ($ignoreKodeDosen) {
                    return $query->where('C_KODE_DOSEN', '<>', $ignoreKodeDosen);
                })
                ->exists();
        }

        return false;
    }

    protected function isMasterDosenNoHpUsed($noHp, $ignoreKodeDosen = null)
    {
        $noHp = trim((string) $noHp);
        $ignoreKodeDosen = trim((string) $ignoreKodeDosen);

        if ($noHp === '') {
            return false;
        }

        $usedInUtama = DB::table('t_mst_dosen')
            ->where('NO_HP', $noHp)
            ->when($ignoreKodeDosen !== '', function ($query) use ($ignoreKodeDosen) {
                return $query->where('C_KODE_DOSEN', '<>', $ignoreKodeDosen);
            })
            ->exists();

        if ($usedInUtama) {
            return true;
        }

        if (Schema::hasTable('mig_t_mst_dosen')) {
            return DB::table('mig_t_mst_dosen')
                ->where('NO_HP', $noHp)
                ->when($ignoreKodeDosen !== '', function ($query) use ($ignoreKodeDosen) {
                    return $query->where('C_KODE_DOSEN', '<>', $ignoreKodeDosen);
                })
                ->exists();
        }

        return false;
    }

    protected function buildMasterDosenPayload(Request $request, $existing = null)
    {
        $existing = $existing ? (array) $existing : [];
        $aktif = (int) $request->get('F_AKTIF', 1) === 1 ? 1 : 0;
        $namaDosen = trim((string) $request->NAMA_DOSEN);
        $now = Carbon::now();

        $defaults = [
            'C_NIP' => null,
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
            'user_id' => null,
            'C_KODE_KAB_KOTA' => '',
            'C_KODE_PROPINSI' => '',
            'KODE_POS' => '',
            'NO_TELP' => null,
            'GOLONGAN_DARAH' => '',
            'NO_KTP' => '',
            'C_KODE_AGAMA' => '',
            'NO_NPWP' => '',
            'NO_REK_BANK' => '',
            'ATAS_NAMA_REK' => '',
            'NAMA_BANK' => '',
            'NAMA_CAB_BANK' => '',
            'AKRONIM_DOSEN' => '',
            'C_KODE_STATUS_IKATAN_KERJA' => '',
            'C_KODE_STATUS_BEBAN_KERJA_DOSEN' => '',
            'SEMESTER_DOSEN_MULAI' => '',
            'ADA_SERTIFIKAT_MENGAJAR' => '',
            'ADA_SURAT_IJIN_MENGAJAR' => '',
            'NIP_PNS' => '',
            'KODE_INSTANSI_INDUK' => '',
            'C_KODE_STATUS_AKTIF_DOSEN' => '',
            'SEMESTER_DOSEN_KELUAR' => '',
            'D_FOTO_DOSEN' => '',
            'F_AKTIF' => 1,
            'F_IS_C' => 0,
            'F_IS_U' => 0,
            'F_IS_D' => 0,
            'F_CHANGE_LOG' => 0,
        ];

        $managed = [
            'C_KODE_DOSEN' => trim((string) $request->C_KODE_DOSEN),
            'C_NIP' => $this->emptyStringToNull($request->C_NIP),
            'NAMA_DOSEN' => $namaDosen,
            'C_KODE_PRODI' => trim((string) $request->C_KODE_PRODI),
            'JENIS_KELAMIN' => $this->emptyStringToNull($request->JENIS_KELAMIN),
            'ALAMAT' => $this->emptyStringToNull($request->ALAMAT),
            'NO_HP' => $this->emptyStringToNull($request->NO_HP),
            'EMAIL' => trim((string) $request->EMAIL),
            'website' => $this->emptyStringToNull($request->pangkat),
            'jabatan_fungsional' => $this->emptyStringToNull($request->jabatan_fungsional),
            'AKRONIM_DOSEN' => $this->generateAkronimDosen($namaDosen),
            'F_AKTIF' => $aktif,
            'C_KODE_STATUS_AKTIF_DOSEN' => $aktif === 1 ? 'A' : 'N',
            'updated_at' => $now,
        ];

        $payload = array_merge($defaults, $existing, $managed);

        $payload['TGL_LAHIR'] = $this->normalizeSqlDateOrNull($payload['TGL_LAHIR'] ?? null);
        $payload['waktu_masuk'] = $this->normalizeSqlDateOrNull($payload['waktu_masuk'] ?? null);
        $payload['created_at'] = $this->normalizeSqlDateTimeOrNull($payload['created_at'] ?? null);

        if (!isset($existing['created_at']) || empty($existing['created_at'])) {
            $payload['created_at'] = $now;
        }

        unset($payload['id']);
        unset($payload['exists_t_mst_dosen']);
        unset($payload['exists_mig_t_mst_dosen']);
        unset($payload['status_sinkron']);
        unset($payload['nama_prodi']);
        unset($payload['status_aktif_label']);

        return $payload;
    }

    protected function syncMasterDosenTables(array $payload, $lookupKodeDosen = null)
    {
        $lookupKodeDosen = trim((string) ($lookupKodeDosen ?: $payload['C_KODE_DOSEN']));
        $updatePayload = $this->filterMasterDosenUpdatePayload($payload);

        DB::beginTransaction();
        try {
            $existsUtama = DB::table('t_mst_dosen')
                ->where('C_KODE_DOSEN', $lookupKodeDosen)
                ->exists();

            if ($existsUtama) {
                DB::table('t_mst_dosen')
                    ->where('C_KODE_DOSEN', $lookupKodeDosen)
                    ->update($updatePayload);
            } else {
                DB::table('t_mst_dosen')->insert($payload);
            }

            if (Schema::hasTable('mig_t_mst_dosen')) {
                $existsMig = DB::table('mig_t_mst_dosen')
                    ->where('C_KODE_DOSEN', $lookupKodeDosen)
                    ->exists();

                if ($existsMig) {
                    DB::table('mig_t_mst_dosen')
                        ->where('C_KODE_DOSEN', $lookupKodeDosen)
                        ->update($updatePayload);
                } else {
                    DB::table('mig_t_mst_dosen')->insert($payload);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function filterMasterDosenUpdatePayload(array $payload)
    {
        $columns = [
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
            'F_AKTIF',
            'C_KODE_STATUS_AKTIF_DOSEN',
            'D_FOTO_DOSEN',
            'updated_at',
        ];

        return array_intersect_key($payload, array_flip($columns));
    }

    protected function emptyStringToNull($value)
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    protected function normalizeNoTelp($value)
    {
        $value = preg_replace('/[^0-9]/', '', (string) $value);

        if ($value === '' || strlen($value) > 10) {
            return null;
        }

        $number = (int) $value;
        return $number > 2147483647 ? null : $number;
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
