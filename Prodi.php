<?php

namespace App\Http\Controllers;

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
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;


class prodi extends Controller
{

    // Halaman Approve Hasil Ujian Proposal
    public function approve_hasilujian_proposal()
    {
        $data = mst_pendaftaran::join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "mst_pendaftaran.pendaftaran_id")
            ->where('tipe_ujian', 0)
            ->orwhere('tipe_ujian', 3)
            ->get();
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
        DB::table('trt_bimbingan')
            ->where([
                "bimbingan_id" => $id,
                "C_NPM" => $nim
            ])
            ->update(['status_bimbingan' => 2]);
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

        $data_hasil = trt_hasil::where('reg_id', $reg_id)->get();
        return view("tugasakhir.prodi.lembaran_hasilujian_proposal", compact(
            "nim",
            "trt_bimbingan",
            "trt_penguji",
            "tipe_ujian",
            "ruangan",
            "tgl_ujian",
            "data_hasil"
        ));
    }
    // Akhir Halaman Lembaran Hasil Ujian

    // Halaman Approve Hasil Ujian TA
    public function approve_hasilujian_ta()
    {
        $data = mst_pendaftaran::join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "mst_pendaftaran.pendaftaran_id")
            ->where('tipe_ujian', 2)
            ->orwhere('tipe_ujian', 3)
            ->get();
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
        DB::table('trt_bimbingan')
            ->where([
                "bimbingan_id" => $id,
                "C_NPM" => $nim
            ])
            ->update(['status_bimbingan' => 3]);
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
        switch ($mst_pendaftaran->tipe_ujian) {
            case "0":
                $tipe_ujian = "Proposal";
                break;
            case "2":
                $tipe_ujian = "Meja";
                break;
        }

        $data_hasil = trt_hasil::where('reg_id', $reg_id)->get();
        return view("tugasakhir.prodi.lembaran_hasilujian_proposal", compact(
            "nim",
            "trt_bimbingan",
            "trt_penguji",
            "tipe_ujian",
            "ruangan",
            "tgl_ujian",
            "data_hasil"
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
        if ($request->password_baru == $request->ulangi_password) {
            $status = DB::update('update users set password = ? where name = ?', [Hash::make($request->password_baru), $request->name]);
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
        } catch (\Exception $e) {
            return redirect(url('prodi/detail_mahasiswa/' . $request->nim))->with('error', 'Gagal mengubah tahapan bimbingan');
        }
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
        return redirect()->back();
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
        $data_pengusul = DB::table('trt_topik')
            ->join('t_mst_mahasiswa', 'trt_topik.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->select('t_mst_mahasiswa.C_NPM', 't_mst_mahasiswa.C_NPM', 't_mst_mahasiswa.NAMA_MAHASISWA')
            ->where('trt_topik.status', 0)
            ->distinct()
            ->get();

        $data_riwayat_usulan = DB::table('trt_topik')
            ->join('t_mst_mahasiswa', 'trt_topik.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->select('t_mst_mahasiswa.C_NPM', 't_mst_mahasiswa.NAMA_MAHASISWA', 'trt_topik.topik', 'trt_topik.kerangka', 'trt_topik.status')
            ->get();
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
        $data = DB::table('trt_topik')
            ->join('t_mst_mahasiswa', 'trt_topik.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->select('t_mst_mahasiswa.*', 'trt_topik.*')
            ->where('trt_topik.status', 1)
            ->whereNotIn(
                't_mst_mahasiswa.C_NPM',
                function ($q) {
                    $q
                        ->select('C_NPM')
                        ->from('trt_bimbingan');
                }
            )
            ->get();
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
        trt_bimbingan::updateOrCreate(
            [
                "C_NPM" => $datapost['C_NPM'],

            ],
            [
                "judul" => $datapost['judul'],
                "pembimbing_I_id" => $datapost['pembimbing_I_id'],
                "pembimbing_II_id" => $datapost['pembimbing_II_id'],
                "status_I" => $datapost['status_I'],
                "status_II" => $datapost['status_II'],
                "status_bimbingan" => $datapost['status_bimbingan'],
                "status_sk" => $datapost['status_sk'],
                "user_id" => $datapost['user_id'],
            ]
        );
        mst_tmp_usulan::where("C_NPM", $request->C_NPM)->delete();

        return redirect::to('prodi/sk_pembimbing');
    }

    public function set_pembimbing($id)
    {
        $cek = DB::table('mst_tmp_usulan')
            ->select('*')
            ->where('C_NPM', $id)
            ->get();
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

        return view('tugasakhir.prodi.set_pembimbing', compact('data', 'data_mahasiswa', 'data_topik', 'cek'));
    }

    public function set_penguji($pendaftaran_id, $nim)
    {
        $info = t_mst_mahasiswa::join("trt_bimbingan", "trt_bimbingan.C_NPM", "=", "t_mst_mahasiswa.C_NPM")
            ->join('trt_penguji', 'trt_penguji.C_NPM', '=', 'trt_bimbingan.C_NPM')
            ->where("t_mst_mahasiswa.C_NPM", $nim)
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
                ->where('bimbingan_id', $datax[$a]->bimbingan_id)
                ->update(['status_sk' => '1']);
            $a++;
        }
        return redirect::to('prodi/sk_ujian_ta');
    }

    public function cetakskpenguji($pendaftaran_id, $nim)
    {
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
        $penguji = TrtPenguji::where([
            "C_NPM" => $nim,
            "tipe_ujian" => $trtjadwalujian->tipe_ujian
        ])->first();
        $bimbingan = trt_bimbingan::where("C_NPM", $nim)->first();
        switch ($trtjadwalujian->tipe_ujian) {
            case "0":
                $tipe_ujian = "Proposal";
                $waktu = $jam_ujian . "-" . sprintf('%02d', substr($jam_ujian, 0, 2) + 1) . ":30";;
                break;
            case "2":
                $tipe_ujian = "Meja";
                $waktu = $jam_ujian . "-" . sprintf('%02d', substr($jam_ujian, 0, 2) + 2) . ":30";;
                break;
        }
        return view('tugasakhir.prodi.cetakskpenguji', compact("nim", "penguji", "bimbingan", "tipe_ujian", "tgl_ujian", "waktu", "ruangan"));
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
        $pendaftaran = mst_pendaftaran::join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "mst_pendaftaran.pendaftaran_id")
            ->where('tipe_ujian', 0)
            ->orwhere('tipe_ujian', 3)
            ->get();
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
        $pendaftaran = mst_pendaftaran::join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "mst_pendaftaran.pendaftaran_id")
            ->where('tipe_ujian', 2)
            ->orwhere('tipe_ujian', 3)
            ->get();
        return view('tugasakhir.prodi.peserta_ujianmeja', compact('pendaftaran'));
    }

    public function daftar_peserta($id)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $id)->first();
        $data = DB::select("SELECT * FROM mst_pendaftaran,trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_reg.pendaftaran_id = ? AND trt_reg.status = ?", [$id, $info->tipe_ujian]);


        return view('tugasakhir.prodi.daftar_peserta', compact("data", "info"));
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
        $pendaftaran = Collection::make(mst_pendaftaran::get())->where('status_ujian', 0)->unique("nama_periode");
        $jadwalujian = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")->get();
        return view('tugasakhir.prodi.jadwal', compact('pendaftaran', "jadwalujian"));
    }

    public function scope_ta()
    {
        $data = DB::table('mst_bidangilmu')
            ->select('*')
            ->get();
        return view('tugasakhir.prodi.scope_ta', compact('data'));
    }

    public function jadwalpostadd(Request $request)
    {
        $mst = mst_pendaftaran::where("nama_periode", $request->nama_periode)->first();
        if (empty($mst)) {
            if ($request->tipe_ujian == "3") {
                for ($i = 0; $i < 3; $i++) {
                    $request->merge([
                        "tipe_ujian" => $i,
                        "user_id" => "00",
                        "jml_peserta" => 0
                    ]);
                    mst_pendaftaran::create($request->all());
                }
            } else {
                $request->merge([
                    "user_id" => "00",
                    "jml_peserta" => 0
                ]);
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

    public function scope_del($id)
    {
        DB::table('mst_bidangilmu')
            ->where('bidangilmu_id', $id)
            ->delete();
        return redirect::to('prodi/scope_ta');
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
        $pendaftaran = mst_pendaftaran::get();
        $jadwalujian = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where('mst_pendaftaran.tipe_ujian', '=', 0)
            ->get();
        return view('tugasakhir.prodi.sk_ujian', compact('pendaftaran', "jadwalujian"));
    }

    public function sk_ujian_ta()
    {
        $pendaftaran = mst_pendaftaran::get();
        $jadwalujian = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where('tipe_ujian', '=', 2)
            ->where('status_sk', '=', 0)
            ->get();
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
        $data = mst_pengumuman::all();
        return view('tugasakhir.prodi.pengumuman', compact('data'));
    }

    public function pengumumanpost(Request $request)
    {
        $datapost = $request->all();
        $gambar = isset($datapost['gambar']) ? $datapost['gambar'] : '';
        $datapost['gambar'] = Helper::uploadImage($gambar, 'gambar/', '');
        $datapost['last_update'] = Helper::tgl($datapost['last_update']);
        $datapost['user_id'] = '1';
        mst_pengumuman::create($datapost);
        return redirect::to('prodi/pengumuman');
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

    public function statusBimbingan()
    {
        $data = (object) [
            "y" => "",
            "PP" => trt_bimbingan::where("status_bimbingan", 0)->count(),
            "PUM" => trt_bimbingan::where("status_bimbingan", 2)->count(),
            "L" => trt_bimbingan::where("status_bimbingan", 3)->count()
        ];

        return response()->json($data);
    }

    public function persyaratan_proposal()
    {
        $data = TrtPengajuanDokumen::join("t_mst_mahasiswa", "trt_pengajuan_dokumen.C_NPM", "=", "t_mst_mahasiswa.C_NPM")->where("type", 0)->get(["NAMA_MAHASISWA", "t_mst_mahasiswa.C_NPM"]);
        return view("tugasakhir.prodi.persyaratan_proposal", compact("data"));
    }

    public function persyaratan_seminarhasil()
    {
        $data = TrtPengajuanDokumen::join("t_mst_mahasiswa", "trt_pengajuan_dokumen.C_NPM", "=", "t_mst_mahasiswa.C_NPM")->where("type", 1)->get(["NAMA_MAHASISWA", "t_mst_mahasiswa.C_NPM"]);
        return view("tugasakhir.prodi.persyaratan_seminarhasil", compact("data"));
    }

    public function persyaratan_ujianmeja()
    {
        $data = TrtPengajuanDokumen::join("t_mst_mahasiswa", "trt_pengajuan_dokumen.C_NPM", "=", "t_mst_mahasiswa.C_NPM")->where("type", 2)->get(["NAMA_MAHASISWA", "t_mst_mahasiswa.C_NPM"]);
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
            $request->merge(["status" => 0]);
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
        TrtJadwalUjian::find($id)->delete();
        return redirect()->back();
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

        $data = mst_pendaftaran::join("trt_jadwal_ujian", "trt_jadwal_ujian.pendaftaran_id", "=", "mst_pendaftaran.pendaftaran_id")
            ->where('tipe_ujian', $type)
            ->orwhere('tipe_ujian', 3)
            ->get();
        return view('tugasakhir.prodi.jadwalpermhs', compact('data'));
    }

    public function detailJadwalPermhs($pendaftaran_id)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $pendaftaran_id)->first();
        $data = DB::select("SELECT * FROM mst_pendaftaran,trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_reg.pendaftaran_id = ? AND trt_reg.status = ?", [$pendaftaran_id, $info->tipe_ujian]);


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
        $request->merge(["jam_ujian" => substr($request->jam_ujian, 0, 5)]);
        if (empty($trtjadwalujianpermhs)) {
            TrtJadwalUjianPerMhs::create($request->all());
        } else {
            TrtJadwalUjianPerMhs::where(["C_NPM" => $request->C_NPM, "jadwal_ujian" => $trtjadwalujian->id])
                ->update($request->except([
                    "C_NPM", "jadwal_ujian", "_token"
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
}
