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
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Exception;

class mhs extends Controller
{

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

    public function chat()
    {
        return Redirect::to('mhs/mail_inbox');
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
            ->join('t_mst_dosen', 'trt_usulan_judul.KODE_DOSEN', '=', 't_mst_dosen.C_KODE_DOSEN')
            ->leftJoin('mst_jenis_tugas_akhir', 'trt_usulan_judul.jenis_tugas_akhir_id', '=', 'mst_jenis_tugas_akhir.jenis_tugas_akhir_id')
            ->select('trt_usulan_judul.*', 't_mst_dosen.NAMA_DOSEN', 'mst_jenis_tugas_akhir.kode_jenis_tugas_akhir')
            ->where('trt_usulan_judul.C_NPM', auth()->user()->name)
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
        $data = DB::table('trt_usulan_judul')
            ->leftJoin('mst_jenis_tugas_akhir', 'trt_usulan_judul.jenis_tugas_akhir_id', '=', 'mst_jenis_tugas_akhir.jenis_tugas_akhir_id')
            ->select('trt_usulan_judul.*', 'mst_jenis_tugas_akhir.kode_jenis_tugas_akhir')
            ->where('trt_usulan_judul.KODE_DOSEN', $kode_dosen)
            ->where('trt_usulan_judul.C_NPM', '<>', auth()->user()->name)
            ->get();
        return view('tugasakhir.mhs.detail_usulan_judul_calon_pembimbing', compact('data'));
    }
    // Akhir Halaman Tampilan Akhir Usulan

    // Halaman Tampilan Judul Usulan
    public function usulan_judul_semua_mahasiswa()
    {
        $data_riwayat_usulan = DB::table('trt_topik')
            ->join('t_mst_mahasiswa', 'trt_topik.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->select('t_mst_mahasiswa.C_NPM', 't_mst_mahasiswa.NAMA_MAHASISWA', 'trt_topik.topik', 'trt_topik.jenis_tugas_akhir_id', 'trt_topik.kerangka', 'trt_topik.status')
            ->get();
        return view('tugasakhir.mhs.usulan_judul_semua_mahasiswa', compact('data_riwayat_usulan'));
    }
    // Akhir Halaman Tampilan Akhir Usulan

    public function daftar_dosen()
    {
        $mahasiswa = DB::table('t_mst_mahasiswa')
            ->select('C_NPM', 'NAMA_MAHASISWA', 'C_KODE_PRODI')
            ->where('C_NPM', auth()->user()->name)
            ->first();

        if (!$mahasiswa) {
            return response('Data mahasiswa tidak ditemukan.', 404);
        }

        $dosenMap = [];

        if (Schema::hasTable('t_mst_dosen')) {
            $rowsUtama = DB::table('t_mst_dosen')
                ->select('C_KODE_DOSEN', 'NAMA_DOSEN', 'NO_HP', 'NO_TELP')
                ->orderBy('NAMA_DOSEN', 'asc')
                ->get();

            foreach ($rowsUtama as $row) {
                $kodeDosen = trim((string) ($row->C_KODE_DOSEN ?? ''));

                if ($kodeDosen === '') {
                    continue;
                }

                $nomorTelpon = $this->normalizeNomorTelponDosen($row->NO_HP ?? null, $row->NO_TELP ?? null);
                $nomorWhatsapp = $this->normalizeNomorWhatsappDosen($row->NO_HP ?? null, $row->NO_TELP ?? null);

                $dosenMap[$kodeDosen] = (object) [
                    'nidn' => $kodeDosen,
                    'nama_dosen' => trim((string) ($row->NAMA_DOSEN ?? '')),
                    'nomor_telpon' => $nomorWhatsapp ?: $nomorTelpon,
                    'nomor_whatsapp' => $nomorWhatsapp,
                ];
            }
        }

        if (Schema::hasTable('mig_t_mst_dosen')) {
            $rowsMigrasi = DB::table('mig_t_mst_dosen')
                ->select('C_KODE_DOSEN', 'NAMA_DOSEN', 'NO_HP', 'NO_TELP')
                ->orderBy('NAMA_DOSEN', 'asc')
                ->get();

            foreach ($rowsMigrasi as $row) {
                $kodeDosen = trim((string) ($row->C_KODE_DOSEN ?? ''));

                if ($kodeDosen === '') {
                    continue;
                }

                $nomorTelpon = $this->normalizeNomorTelponDosen($row->NO_HP ?? null, $row->NO_TELP ?? null);
                $nomorWhatsapp = $this->normalizeNomorWhatsappDosen($row->NO_HP ?? null, $row->NO_TELP ?? null);

                if (!isset($dosenMap[$kodeDosen])) {
                    $dosenMap[$kodeDosen] = (object) [
                        'nidn' => $kodeDosen,
                        'nama_dosen' => trim((string) ($row->NAMA_DOSEN ?? '')),
                        'nomor_telpon' => $nomorWhatsapp ?: $nomorTelpon,
                        'nomor_whatsapp' => $nomorWhatsapp,
                    ];
                    continue;
                }

                if ($dosenMap[$kodeDosen]->nama_dosen === '' && !empty($row->NAMA_DOSEN)) {
                    $dosenMap[$kodeDosen]->nama_dosen = trim((string) $row->NAMA_DOSEN);
                }

                if ($dosenMap[$kodeDosen]->nomor_telpon === '-' && $nomorTelpon !== '-') {
                    $dosenMap[$kodeDosen]->nomor_telpon = $nomorWhatsapp ?: $nomorTelpon;
                }

                if (empty($dosenMap[$kodeDosen]->nomor_whatsapp) && $nomorWhatsapp !== null) {
                    $dosenMap[$kodeDosen]->nomor_whatsapp = $nomorWhatsapp;
                    $dosenMap[$kodeDosen]->nomor_telpon = $nomorWhatsapp;
                }
            }
        }

        $data = collect(array_values($dosenMap))
            ->sortBy('nama_dosen', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        return view('tugasakhir.mhs.daftar_dosen', compact('data', 'mahasiswa'));
    }

    public function kelengkapan_kontak_post(Request $request)
    {
        $redirectPath = $request->input('return_to') === 'profil' ? '/mhs/profil' : '/home';

        if (!Schema::hasTable('trt_kontak_mahasiswa')) {
            return redirect()->to($redirectPath)->with('mhs_contact_error', 'Tabel kontak mahasiswa belum tersedia.');
        }

        $nim = trim((string) auth()->user()->name);

        if ($nim === '') {
            return redirect()->to($redirectPath)->with('mhs_contact_error', 'NIM akun login tidak ditemukan.');
        }

        $mahasiswa = DB::table('t_mst_mahasiswa')
            ->select('C_NPM', 'D_FOTO_MAHASISWA')
            ->where('C_NPM', $nim)
            ->first();

        if (!$mahasiswa) {
            return redirect()->to($redirectPath)->with('mhs_contact_error', 'Data mahasiswa tidak ditemukan.');
        }

        $this->validate($request, [
            'no_wa' => 'required|max:20',
            'id_telegram' => 'nullable|max:100',
            'foto' => 'nullable|file|image|mimes:jpeg,jpg,png,webp|max:5120',
        ], [
            'no_wa.required' => 'Nomor WhatsApp wajib diisi.',
            'foto.image' => 'Foto harus berupa gambar.',
            'foto.uploaded' => 'Upload foto gagal diproses server. Coba gunakan file yang lebih kecil lalu unggah kembali.',
            'foto.mimes' => 'Foto harus berformat JPEG, JPG, PNG, atau WebP.',
            'foto.max' => 'Ukuran foto maksimal 5 MB.',
        ]);

        $nomorWa = $this->normalizeNomorWaMahasiswa($request->no_wa);
        if ($nomorWa === null) {
            return redirect()->to($redirectPath)->withInput()->with('mhs_contact_error', 'Format nomor WhatsApp tidak valid. Gunakan format seperti 6281234567890 atau 081234567890.');
        }

        $idTelegram = $this->normalizeTelegramMahasiswa($request->id_telegram);
        if ($idTelegram === false) {
            return redirect()->to($redirectPath)->withInput()->with('mhs_contact_error', 'Format ID Telegram tidak valid. Gunakan format seperti @username_telegram.');
        }

        $fotoBaru = null;
        try {
            if ($request->hasFile('foto')) {
                $fotoBaru = $request->file('foto')->store('mahasiswa', 'public');
            }

            $existing = DB::table('trt_kontak_mahasiswa')
                ->where('C_NPM', $nim)
                ->first();

            $payload = [
                'C_NPM' => $nim,
                'no_wa' => $nomorWa,
                'id_telegram' => $idTelegram,
                'updated_at' => Carbon::now(),
            ];

            if ($existing) {
                DB::table('trt_kontak_mahasiswa')
                    ->where('C_NPM', $nim)
                    ->update($payload);
            } else {
                $payload['created_at'] = Carbon::now();
                DB::table('trt_kontak_mahasiswa')->insert($payload);
            }

            if ($fotoBaru !== null) {
                DB::table('t_mst_mahasiswa')
                    ->where('C_NPM', $nim)
                    ->update(['D_FOTO_MAHASISWA' => $fotoBaru]);

                $fotoLama = trim((string) $mahasiswa->D_FOTO_MAHASISWA);
                if (preg_match('/\Amahasiswa\/[a-zA-Z0-9._-]+\.(?:jpe?g|png)\z/i', $fotoLama)) {
                    Storage::disk('public')->delete($fotoLama);
                }
            }

            return redirect()->to($redirectPath)->with('mhs_contact_success', 'Data mahasiswa berhasil disimpan.');
        } catch (Exception $e) {
            if ($fotoBaru !== null) {
                Storage::disk('public')->delete($fotoBaru);
            }

            \Log::error('kelengkapan_kontak_mahasiswa_post error', [
                'nim' => $nim,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return redirect()->to($redirectPath)->withInput()->with('mhs_contact_error', 'Kontak mahasiswa gagal disimpan.');
        }
    }

    public function profil()
    {
        $profil = Helper::getCurrentMahasiswaContactByAuthUser();

        if (!empty($profil->C_KODE_PRODI) && Schema::hasTable('trt_prodi')) {
            $prodi = DB::table('trt_prodi')
                ->select('nama')
                ->where('kode_prodi', $profil->C_KODE_PRODI)
                ->first();
            $profil->nama_prodi = $prodi->nama ?? $profil->C_KODE_PRODI;
        } else {
            $profil->nama_prodi = $profil->C_KODE_PRODI ?? '-';
        }

        return view('tugasakhir.mhs.profil', compact('profil'));
    }

    public function draft_final()
    {
        $nim = (string) auth()->user()->name;
        $draft = null;

        if (Schema::hasTable('trt_draft_final_mahasiswa')) {
            $draft = DB::table('trt_draft_final_mahasiswa')
                ->where('C_NPM', $nim)
                ->first();
        }

        return view('tugasakhir.mhs.draft_final', compact('draft'));
    }

    public function draft_final_post(Request $request)
    {
        $nim = (string) auth()->user()->name;
        $proposalUrl = trim((string) $request->input('draft_proposal_url'));
        $tugasAkhirUrl = trim((string) $request->input('draft_tugas_akhir_url'));
        $errors = [];

        $proposalInfo = $this->parseGoogleDriveFileLink($proposalUrl);
        $tugasAkhirInfo = $this->parseGoogleDriveFileLink($tugasAkhirUrl);

        if ($proposalUrl !== '' && !$proposalInfo['valid']) {
            $errors['draft_proposal_url'] = $proposalInfo['message'];
        }

        if ($tugasAkhirUrl !== '' && !$tugasAkhirInfo['valid']) {
            $errors['draft_tugas_akhir_url'] = $tugasAkhirInfo['message'];
        }

        if ($proposalUrl === '' && $tugasAkhirUrl === '') {
            $errors['draft_proposal_url'] = 'Isi minimal satu link draft final Proposal atau Tugas Akhir.';
        }

        if (!empty($errors)) {
            return redirect('mhs/draft_final')->withInput()->withErrors($errors);
        }

        if (!Schema::hasTable('trt_draft_final_mahasiswa')) {
            return redirect('mhs/draft_final')->withInput()->with('draft_final_error', 'Tabel draft final belum tersedia. Jalankan migration terlebih dahulu.');
        }

        $existing = DB::table('trt_draft_final_mahasiswa')
            ->where('C_NPM', $nim)
            ->first();

        $payload = [
            'draft_proposal_url' => $proposalUrl !== '' ? $proposalUrl : null,
            'draft_proposal_file_id' => $proposalInfo['file_id'] ?? null,
            'draft_tugas_akhir_url' => $tugasAkhirUrl !== '' ? $tugasAkhirUrl : null,
            'draft_tugas_akhir_file_id' => $tugasAkhirInfo['file_id'] ?? null,
            'updated_at' => Carbon::now(),
        ];

        if ($existing) {
            DB::table('trt_draft_final_mahasiswa')
                ->where('C_NPM', $nim)
                ->update($payload);
        } else {
            $payload['C_NPM'] = $nim;
            $payload['created_at'] = Carbon::now();
            DB::table('trt_draft_final_mahasiswa')->insert($payload);
        }

        return redirect('mhs/draft_final')->with('draft_final_success', 'Link draft final berhasil disimpan.');
    }

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

    public function cetak_beritaacara_ujian($pendaftaran_id, $nim)
    {
        $trtjadwalujian = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("trt_jadwal_ujian.pendaftaran_id", $pendaftaran_id)
            ->first();

        if (!$trtjadwalujian) {
            return response('Data jadwal ujian meja tidak ditemukan.', 404);
        }

        $trtjadwalujianpermhs = TrtJadwalUjianPerMhs::join("mst_ruangan", "mst_ruangan.id", "trt_jadwal_ujian_per_mhs.ruangan")
            ->where([
                "C_NPM" => $nim,
                "jadwal_ujian" => $trtjadwalujian->id
            ])->first();
        $trt_bimbingan = trt_bimbingan::where("C_NPM", $nim)->first();
        $mst_pendaftaran = mst_pendaftaran::find($pendaftaran_id);

        if (!$trtjadwalujianpermhs || !$trt_bimbingan || !$mst_pendaftaran) {
            return response('Data berita acara ujian meja belum lengkap.', 404);
        }

        $trt_penguji = TrtPenguji::where([
            "C_NPM" => $nim,
            "tipe_ujian" => $mst_pendaftaran->tipe_ujian
        ])->first();

        if (!$trt_penguji) {
            return response('Data tim penguji ujian meja belum lengkap.', 404);
        }

        $ruanganModel = MstRuangan::find($trtjadwalujianpermhs->ruangan);
        $ruangan = $ruanganModel ? $ruanganModel->nama_ruangan : '-';
        $tgl_ujian = Carbon::parse($trtjadwalujian->tgl_ujian)->formatLocalized("%A, %d %B %Y");

        switch ($mst_pendaftaran->tipe_ujian) {
            case "0":
                $tipe_ujian = "Proposal";
                break;
            case "2":
                $tipe_ujian = "Meja";
                break;
            default:
                $tipe_ujian = "Ujian";
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
        $jenisTugasAkhir = $this->jenisTugasAkhirMahasiswaQuery($data[0]->jenis_tugas_akhir_id ?? null)
            ->orderBy('kode_jenis_tugas_akhir')
            ->get();
        return view("tugasakhir.mhs.ubah_judul", compact('data', 'jenisTugasAkhir'));
    }

    public function judul_update(Request $request, $id)
    {
        $topik = trt_topik::where('topik_id', $id)
            ->where('C_NPM', auth()->user()->name)
            ->first();

        if (!$topik) {
            return redirect::to('mhs/pengajuan_topik')->with('error', 'Data topik tidak ditemukan.');
        }

        $request->merge([
            'topik' => $this->judulTanpaKodeJenisTugasAkhir($request->topik),
        ]);
        $this->validate($request, [
            'topik' => 'required|max:1000',
            'jenis_tugas_akhir_id' => 'required|integer',
            'kerangka' => 'nullable|string|max:255',
        ]);

        $kerangkaUrl = trim((string) $request->input('kerangka'));
        $kerangkaInfo = $this->parseGoogleDriveFileLink($kerangkaUrl);
        if ($kerangkaUrl !== '' && !$kerangkaInfo['valid']) {
            return redirect()->back()->withInput()->withErrors([
                'kerangka' => $kerangkaInfo['message'],
            ]);
        }

        if (!$this->jenisTugasAkhirMahasiswaDapatDipilih($request->jenis_tugas_akhir_id, $topik->jenis_tugas_akhir_id)) {
            return redirect()->back()->withInput()->withErrors([
                'jenis_tugas_akhir_id' => 'Jenis tugas akhir tidak tersedia untuk mahasiswa.',
            ]);
        }

        DB::transaction(function () use ($request, $id, $kerangkaUrl) {
            $topikUpdate = [
                'topik' => trim((string) $request->topik),
                'jenis_tugas_akhir_id' => $request->jenis_tugas_akhir_id,
            ];

            if ($kerangkaUrl !== '') {
                $topikUpdate['kerangka'] = $kerangkaUrl;
            }

            trt_topik::where("topik_id", $id)
                ->where('C_NPM', auth()->user()->name)
                ->update($topikUpdate);

            if (Schema::hasColumn('trt_bimbingan', 'topik_id')) {
                DB::table('trt_bimbingan')
                    ->where('topik_id', $id)
                    ->update([
                        'judul' => trim((string) $request->topik),
                        'jenis_tugas_akhir_id' => $request->jenis_tugas_akhir_id,
                    ]);
            }
        });
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
        $queryBidangIlmu = DB::table('mst_bidangilmu')
            ->select('*');

        if (Schema::hasColumn('mst_bidangilmu', 'status_aktif')) {
            $queryBidangIlmu->where('status_aktif', 1);
        }

        $data = $queryBidangIlmu->get();

        $listdosen = DB::table('t_mst_dosen')
            ->leftJoin("trt_level_pembimbing", "trt_level_pembimbing.C_KODE_DOSEN", "=", "t_mst_dosen.C_KODE_DOSEN")
            ->select('t_mst_dosen.*', 'trt_level_pembimbing.level')
            ->get();

        $datatopik = DB::table('trt_topik')
            ->select('*')
            ->where('C_NPM', $id)
            ->get();

        $topik = DB::table('trt_topik')
            ->leftJoin('mst_jenis_tugas_akhir', 'trt_topik.jenis_tugas_akhir_id', '=', 'mst_jenis_tugas_akhir.jenis_tugas_akhir_id')
            ->select('trt_topik.*', 'mst_jenis_tugas_akhir.kode_jenis_tugas_akhir')
            ->where('trt_topik.C_NPM', $id)
            ->where('trt_topik.status', 1)
            ->first();
        $jenisTugasAkhir = $this->jenisTugasAkhirMahasiswaQuery()
            ->orderBy('kode_jenis_tugas_akhir')
            ->get();
        $bidangilmuid = $topik
            ? RequestPembimbing::where([
                'C_NPM' => $id,
                'topik' => $topik->topik_id,
            ])->get()
            : collect();

        return view('tugasakhir.mhs.pengajuan_topik', compact(
            'data',
            'datatopik',
            'listdosen',
            'cek',
            'topik',
            'bidangilmuid',
            'jenisTugasAkhir'
        ));
    }
    public function pengajuan_topikdel($id)
    {
        $topik = trt_topik::where('topik_id', $id)
            ->where('C_NPM', auth()->user()->name)
            ->first();

        if (!$topik) {
            return redirect::to('mhs/pengajuan_topik')->with('error', 'Data topik tidak ditemukan.');
        }

        $kerangka = trim((string) $topik->kerangka);
        if ($kerangka !== '' && !helper::isGoogleDriveUrl($kerangka)) {
            $legacyPath = public_path('dokumen/' . basename($kerangka));
            if (is_file($legacyPath)) {
                unlink($legacyPath);
            }
        }

        $topik->delete();
        return redirect::to('mhs/pengajuan_topik');
    }


    public function pengajuan_topikpost(Request $request)
    {
        $request->merge([
            'topik' => $this->judulTanpaKodeJenisTugasAkhir($request->topik),
            'C_NPM' => auth()->user()->name,
        ]);
        $this->validate($request, [
            'topik' => 'required|max:1000',
            'jenis_tugas_akhir_id' => 'required|integer',
            'bidang_ilmu' => 'required|array|min:1',
            'kerangka' => 'nullable|string|max:255',
        ]);

        $kerangkaUrl = trim((string) $request->input('kerangka'));
        $kerangkaInfo = $this->parseGoogleDriveFileLink($kerangkaUrl);
        if ($kerangkaUrl !== '' && !$kerangkaInfo['valid']) {
            return redirect()->back()->withInput()->withErrors([
                'kerangka' => $kerangkaInfo['message'],
            ]);
        }

        if (!$this->jenisTugasAkhirMahasiswaDapatDipilih($request->jenis_tugas_akhir_id)) {
            return redirect()->back()->withInput()->withErrors([
                'jenis_tugas_akhir_id' => 'Jenis tugas akhir tidak tersedia untuk mahasiswa.',
            ]);
        }

        $datapost = $request->except(["bidang_ilmu"]);
        $datapost['status'] = 0;
        $datapost['user_id'] = $datapost['C_NPM'];
        $datapost['bidang_ilmu_peminatan'] = $datapost['bidang_ilmu_peminatan'];
        $datapost['kerangka'] = $kerangkaUrl;

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

    private function judulTanpaKodeJenisTugasAkhir($judul)
    {
        return trim((string) preg_replace(
            '/^(?:\(\s*[A-Za-z]{2}\s*(?:-|_|\s|\/)\s*[A-Za-z0-9]{2,}\s*\)\s*)+/',
            '',
            trim((string) $judul)
        ));
    }

    private function jenisTugasAkhirMahasiswaQuery($selectedJenisTugasAkhirId = null)
    {
        $query = DB::table('mst_jenis_tugas_akhir');

        if (Schema::hasColumn('mst_jenis_tugas_akhir', 'tersedia_untuk_mahasiswa')) {
            $query->where(function ($subQuery) use ($selectedJenisTugasAkhirId) {
                $subQuery->where('tersedia_untuk_mahasiswa', 1);

                if ($selectedJenisTugasAkhirId !== null) {
                    $subQuery->orWhere('jenis_tugas_akhir_id', $selectedJenisTugasAkhirId);
                }
            });
        }

        return $query;
    }

    private function jenisTugasAkhirMahasiswaDapatDipilih($jenisTugasAkhirId, $selectedJenisTugasAkhirId = null)
    {
        return $this->jenisTugasAkhirMahasiswaQuery($selectedJenisTugasAkhirId)
            ->where('jenis_tugas_akhir_id', (int) $jenisTugasAkhirId)
            ->exists();
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

        $draftFinal = $this->getDraftFinalMahasiswa();
        $draftDefault = request()->query('draft', '');
        $perihalDefault = request()->query('perihal', '');

        return view('tugasakhir.mhs.mail_new', compact('data', 'draftFinal', 'draftDefault', 'perihalDefault'));
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

            $isiPesan = $request->isi_pesan;
            $draftLinks = $this->buildDraftFinalMessageLinks($request->input('draft_final_links', []));

            if (!empty($draftLinks)) {
                $isiPesan .= '<hr><p><strong>Lampiran Link Draft Final:</strong></p><ul>';
                foreach ($draftLinks as $label => $url) {
                    $isiPesan .= '<li>' . e($label) . ': <a href="' . e($url) . '" target="_blank">' . e($url) . '</a></li>';
                }
                $isiPesan .= '</ul>';
            }

            $mstpesan = mst_pesan::create([
                "perihal_pesan" => $request->perihal_pesan,
                "isi_pesan" => $isiPesan,
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
        $nim = auth()->user()->name;
        $ujianMejaTypes = [2, 3];

        $data = DB::table('mst_pendaftaran')
            ->select('*')
            ->where(function ($query) use ($ujianMejaTypes) {
                $query->whereIn('tipe_ujian', $ujianMejaTypes);

                if (Schema::hasColumn('mst_pendaftaran', 'status_ujian')) {
                    $query->where('status_ujian', 0);
                }
            })
            ->get();
        $syarat = DB::table('mst_syarat_ujian')
            ->select('*')
            ->where('tipe_ujian', 2)
            ->get();

        $registeredPeriodIds = mst_pendaftaran::whereIn('tipe_ujian', $ujianMejaTypes)->select('pendaftaran_id');
        $registeredBimbinganIds = trt_bimbingan::where('C_NPM', $nim)->select('bimbingan_id');
        $currentRegistration = trt_reg::where('C_NPM', $nim)
            ->where('status', 2)
            ->whereIn('bimbingan_id', $registeredBimbinganIds)
            ->whereIn('pendaftaran_id', $registeredPeriodIds)
            ->orderBy('reg_id', 'desc')
            ->first();

        $currentRegistrationScheduled = false;
        if (!empty($currentRegistration)) {
            $currentRegistrationScheduled = TrtJadwalUjianPerMhs::join('trt_jadwal_ujian', 'trt_jadwal_ujian.id', '=', 'trt_jadwal_ujian_per_mhs.jadwal_ujian')
                ->where('trt_jadwal_ujian_per_mhs.C_NPM', $nim)
                ->where('trt_jadwal_ujian.pendaftaran_id', $currentRegistration->pendaftaran_id)
                ->exists();
        }

        return view('tugasakhir.mhs.signup_ujianmeja', compact('data', 'syarat', 'currentRegistration', 'currentRegistrationScheduled'));
    }

    public function batalkan_registrasi_ujianmeja($pendaftaran_id)
    {
        try {
            $nim = auth()->user()->name;
            $pendaftaran = mst_pendaftaran::where('pendaftaran_id', $pendaftaran_id)
                ->whereIn('tipe_ujian', [2, 3])
                ->first();

            if (empty($pendaftaran)) {
                return redirect('mhs/signup_ujianmeja')->with('registration_status', 'cancel_not_found');
            }

            $scheduled = TrtJadwalUjianPerMhs::join('trt_jadwal_ujian', 'trt_jadwal_ujian.id', '=', 'trt_jadwal_ujian_per_mhs.jadwal_ujian')
                ->where('trt_jadwal_ujian_per_mhs.C_NPM', $nim)
                ->where('trt_jadwal_ujian.pendaftaran_id', $pendaftaran_id)
                ->exists();

            if ($scheduled) {
                return redirect('mhs/signup_ujianmeja')->with('registration_status', 'cancel_scheduled');
            }

            $registration = trt_reg::where('C_NPM', $nim)
                ->where('pendaftaran_id', $pendaftaran_id)
                ->where('status', 2)
                ->first();

            if (empty($registration)) {
                return redirect('mhs/signup_ujianmeja')->with('registration_status', 'cancel_not_found');
            }

            DB::transaction(function () use ($registration, $pendaftaran, $nim, $pendaftaran_id) {
                $registration->delete();

                mst_pendaftaran::where('pendaftaran_id', $pendaftaran_id)->update([
                    'jml_peserta' => max(0, ((int) $pendaftaran->jml_peserta) - 1),
                ]);

                TrtPenguji::where('C_NPM', $nim)
                    ->where('tipe_ujian', 2)
                    ->delete();
            });

            return redirect('mhs/signup_ujianmeja')->with('registration_status', 'cancel_success');
        } catch (Exception $e) {
            return redirect('mhs/signup_ujianmeja')->with('registration_status', 'cancel_error');
        }
    }

    public function registrasi(Request $request)
    {
        try {
            $datapost = $request->all();
            $mstsyaratujian = \App\Model\mst_syarat_ujian::where(["tipe_ujian" => $request->tipe_ujian])->count();
            $trtsyaratujian = \App\TrtSyaratUjian::where(["C_NPM" => auth()->user()->name, "status" => 1])->whereIn("syarat_ujian_id", \App\Model\mst_syarat_ujian::where(["tipe_ujian" => $request->tipe_ujian])->select("syarat_ujian_id"))->count();
            $registrationPeriodTypes = ((int) $request->tipe_ujian === 2) ? [2, 3] : [(int) $request->tipe_ujian];
            $trtreg = \App\Model\trt_reg::whereIn("bimbingan_id", \App\Model\trt_bimbingan::where("C_NPM", auth()->user()->name)->select("bimbingan_id"))->whereIn("pendaftaran_id", \App\Model\mst_pendaftaran::whereIn("tipe_ujian", $registrationPeriodTypes)->select("pendaftaran_id"))->count();


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
        return redirect('sk_pembimbing/' . $nomor);
    }

    public function surat_sk_pembimbing_pdf($nomor)
    {
        return redirect('sk_pembimbing_pdf/' . $nomor);
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

        if ($status !== 'ada' || $id_bimbingan === '') {
            return response('Data surat SK ujian meja tidak ditemukan.', 404);
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

        if ($data_sk->isEmpty()) {
            return response('Data surat SK ujian meja belum lengkap.', 404);
        }

        $safeNomorSk = preg_replace('/[^A-Za-z0-9._-]+/', '-', (string) $data_sk[0]->nomor_sk);

        return view('tugasakhir.fakultas.cetakskpenugasan', compact('data_sk'));
    }

    public function surat_sk_ujian_meja_pdf($nomor)
    {
        $data = DB::table('mst_sk_penugasan')
            ->join('trt_bimbingan', 'mst_sk_penugasan.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->select('*')
            ->get();

        $id_bimbingan = '';

        foreach ($data as $value) {
            if (str_replace('/', '', $value->nomor_sk) == $nomor) {
                $id_bimbingan = $value->bimbingan_id;
                break;
            }
        }

        if ($id_bimbingan === '') {
            return response('Data surat SK ujian meja tidak ditemukan.', 404);
        }

        $data_sk = DB::table('mst_sk_penugasan')
            ->join('trt_bimbingan', 'mst_sk_penugasan.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->join('trt_penguji', 'trt_penguji.C_NPM', '=', 'trt_bimbingan.C_NPM')
            ->join('trt_jadwal_ujian_per_mhs', 'trt_jadwal_ujian_per_mhs.C_NPM', '=', 'trt_bimbingan.C_NPM')
            ->join('trt_jadwal_ujian', 'trt_jadwal_ujian.id', '=', 'trt_jadwal_ujian_per_mhs.jadwal_ujian')
            ->join('mst_ruangan', 'mst_ruangan.id', '=', 'trt_jadwal_ujian_per_mhs.ruangan')
            ->select(['mst_sk_penugasan.created_at', 'mst_sk_penugasan.sk_penugasan_id', 'mst_sk_penugasan.nomor_sk', 'trt_bimbingan.pembimbing_I_id', 'trt_bimbingan.pembimbing_II_id', 'trt_penguji.ketua_sidang_id', 'trt_penguji.penguji_I_id', 'trt_penguji.penguji_II_id', 'trt_penguji.penguji_III_id', 'trt_penguji.C_NPM', 'trt_jadwal_ujian.tgl_ujian', 'trt_jadwal_ujian_per_mhs.jam_ujian', 'mst_ruangan.nama_ruangan', 'trt_jadwal_ujian.pendaftaran_id'])
            ->where('trt_bimbingan.bimbingan_id', $id_bimbingan)
            ->where('trt_penguji.tipe_ujian', 2)
            ->where('trt_jadwal_ujian.status', 2)
            ->get();

        if ($data_sk->isEmpty()) {
            return response('Data surat SK ujian meja belum lengkap.', 404);
        }

        $safeNomorSk = preg_replace('/[^A-Za-z0-9._-]+/', '-', (string) $data_sk[0]->nomor_sk);

        return PDF::loadView('tugasakhir.fakultas.cetakskpenugasan_pdf', compact('data_sk'))
            ->setPaper('a4', 'portrait')
            ->stream('SK-Ujian-Meja-' . trim($safeNomorSk, '-') . '.pdf');
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

    public function surat_sk_proposal_pdf($pendaftaran_id)
    {
        $nim = (string) auth()->user()->name;
        $surat = DB::table('trt_jadwal_ujian as jadwal')
            ->join('mst_pendaftaran as pendaftaran', 'pendaftaran.pendaftaran_id', '=', 'jadwal.pendaftaran_id')
            ->join('trt_jadwal_ujian_per_mhs as peserta', 'peserta.jadwal_ujian', '=', 'jadwal.id')
            ->join('mst_ruangan as ruangan', 'ruangan.id', '=', 'peserta.ruangan')
            ->join('trt_penguji as penguji', function ($join) {
                $join->on('penguji.C_NPM', '=', 'peserta.C_NPM')
                    ->where('penguji.tipe_ujian', '=', 0);
            })
            ->join('trt_bimbingan as bimbingan', 'bimbingan.C_NPM', '=', 'peserta.C_NPM')
            ->select([
                'jadwal.id as jadwal_id',
                'jadwal.pendaftaran_id',
                'jadwal.tgl_ujian',
                'peserta.C_NPM',
                'peserta.jam_ujian',
                'ruangan.nama_ruangan',
                'penguji.nomor_sk',
                'penguji.created_at as penguji_created_at',
                'penguji.penguji_I_id',
                'penguji.penguji_II_id',
                'penguji.penguji_III_id',
                'penguji.ketua_sidang_id',
                'bimbingan.pembimbing_I_id',
                'bimbingan.pembimbing_II_id',
                'bimbingan.judul',
                'bimbingan.jenis_tugas_akhir_id',
            ])
            ->where('jadwal.pendaftaran_id', $pendaftaran_id)
            ->where('pendaftaran.tipe_ujian', 0)
            ->where('peserta.C_NPM', $nim)
            ->first();

        if (!$surat) {
            return response('Data surat SK ujian proposal tidak ditemukan.', 404);
        }

        $safeNomorSk = preg_replace('/[^A-Za-z0-9._-]+/', '-', (string) $surat->nomor_sk);

        return PDF::loadView('tugasakhir.prodi.cetakskpenguji_pdf', compact('surat'))
            ->setPaper('a4', 'portrait')
            ->stream('SK-Ujian-Proposal-' . trim($safeNomorSk, '-') . '.pdf');
    }

    protected function normalizeNomorTelponDosen($nomorHp = null, $nomorTelp = null)
    {
        foreach ([$nomorHp, $nomorTelp] as $value) {
            $value = trim((string) $value);

            if ($value !== '' && $value !== '0' && $value !== '-') {
                return $value;
            }
        }

        return '-';
    }

    protected function normalizeNomorWhatsappDosen($nomorHp = null, $nomorTelp = null)
    {
        foreach ([$nomorHp, $nomorTelp] as $value) {
            $digits = preg_replace('/[^0-9]/', '', (string) $value);

            if ($digits === '' || $digits === '0') {
                continue;
            }

            if (strpos($digits, '0') === 0) {
                $digits = '62' . substr($digits, 1);
            } elseif (strpos($digits, '8') === 0) {
                $digits = '62' . $digits;
            }

            if (preg_match('/^628[0-9]{7,12}$/', $digits)) {
                return $digits;
            }
        }

        return null;
    }

    protected function getDraftFinalMahasiswa()
    {
        if (!Schema::hasTable('trt_draft_final_mahasiswa')) {
            return null;
        }

        return DB::table('trt_draft_final_mahasiswa')
            ->where('C_NPM', auth()->user()->name)
            ->first();
    }

    protected function buildDraftFinalMessageLinks($selectedTypes)
    {
        $selectedTypes = is_array($selectedTypes) ? $selectedTypes : [];
        $draft = $this->getDraftFinalMahasiswa();
        $links = [];

        if (!$draft) {
            return $links;
        }

        if (in_array('proposal', $selectedTypes) && !empty($draft->draft_proposal_url)) {
            $links['Draft Final Proposal'] = $draft->draft_proposal_url;
        }

        if (in_array('tugas_akhir', $selectedTypes) && !empty($draft->draft_tugas_akhir_url)) {
            $links['Draft Final Tugas Akhir'] = $draft->draft_tugas_akhir_url;
        }

        return $links;
    }

    protected function parseGoogleDriveFileLink($url)
    {
        $url = trim((string) $url);

        if ($url === '') {
            return ['valid' => true, 'file_id' => null, 'message' => null];
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['valid' => false, 'file_id' => null, 'message' => 'Link harus berupa URL lengkap Google Drive atau Google Docs.'];
        }

        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';
        $query = $parts['query'] ?? '';

        if ($host === 'www.drive.google.com') {
            $host = 'drive.google.com';
        }

        if ($host === 'www.docs.google.com') {
            $host = 'docs.google.com';
        }

        if ($scheme !== 'https') {
            return ['valid' => false, 'file_id' => null, 'message' => 'Link harus menggunakan HTTPS. Salin link langsung dari menu Share Google Drive.'];
        }

        if (!in_array($host, ['drive.google.com', 'docs.google.com'], true)) {
            return ['valid' => false, 'file_id' => null, 'message' => 'Link harus berasal dari Google Drive atau Google Docs.'];
        }

        if (strpos($path, '/folders/') !== false || strpos($path, '/drive/folders/') !== false) {
            return ['valid' => false, 'file_id' => null, 'message' => 'Link folder Google Drive tidak diterima. Gunakan link file dokumen.'];
        }

        if ($host === 'drive.google.com' && preg_match('#/file/d/([^/]+)#', $path, $matches)) {
            return ['valid' => true, 'file_id' => $matches[1], 'message' => null];
        }

        if ($host === 'docs.google.com' && preg_match('#^/(document|spreadsheets|presentation)/d/([^/]+)#', $path, $matches)) {
            return ['valid' => true, 'file_id' => $matches[2], 'message' => null];
        }

        parse_str($query, $queryParams);
        if ($host === 'drive.google.com' && !empty($queryParams['id'])) {
            return ['valid' => true, 'file_id' => $queryParams['id'], 'message' => null];
        }

        return ['valid' => false, 'file_id' => null, 'message' => 'Link belum dikenali sebagai link file. Buka file di Google Drive, klik Share, lalu salin link file tersebut.'];
    }

    protected function normalizeNomorWaMahasiswa($value)
    {
        $value = preg_replace('/[^0-9]/', '', (string) $value);

        if ($value === '') {
            return null;
        }

        if (strpos($value, '62') === 0) {
            $normalized = $value;
        } elseif (strpos($value, '0') === 0) {
            $normalized = '62' . substr($value, 1);
        } elseif (strpos($value, '8') === 0) {
            $normalized = '62' . $value;
        } else {
            return null;
        }

        if (!preg_match('/^62[0-9]{8,15}$/', $normalized)) {
            return null;
        }

        return $normalized;
    }

    protected function normalizeTelegramMahasiswa($value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $value = str_replace(' ', '', $value);

        if ($value[0] !== '@') {
            $value = '@' . $value;
        }

        if (!preg_match('/^@[A-Za-z0-9_]{5,32}$/', $value)) {
            return false;
        }

        return strtolower($value);
    }
}
