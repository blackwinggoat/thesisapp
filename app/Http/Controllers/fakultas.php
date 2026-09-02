<?php

namespace App\Http\Controllers;

use App\Model\mst_pendaftaran;
use Illuminate\Http\Request;
use helper;
use DB;
use Illuminate\Support\Facades\Redirect;
use App\Model\trt_topik;
use App\Model\trt_reg;
use App\Model\mst_sk_pembimbing;
use App\Model\mst_sk_penugasan;
use App\Model\trt_bimbingan;
use App\Model\trt_hasil;
use App\MstRuangan;
use App\TrtJadwalUjian;
use App\TrtJadwalUjianPerMhs;
use App\TrtPenguji;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Services\IndonesianHijriDateService;
use App\Services\SiakadIpkService;
use Exception;

class fakultas extends Controller
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

    // Halaman Approve Hasil Ujian TA
    public function rekap_nilai_proposal()
    {
        $data = DB::select("SELECT DISTINCT mst_pendaftaran.pendaftaran_id, mst_pendaftaran.nama_periode, mst_pendaftaran.kuota, mst_pendaftaran.jml_peserta, trt_jadwal_ujian.tgl_ujian FROM mst_pendaftaran, trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa, trt_jadwal_ujian, trt_jadwal_ujian_per_mhs , mst_ruangan WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND mst_ruangan.id =  trt_jadwal_ujian_per_mhs.ruangan AND trt_bimbingan.C_NPM = trt_jadwal_ujian_per_mhs.C_NPM AND trt_jadwal_ujian.id = trt_jadwal_ujian_per_mhs.jadwal_ujian AND trt_jadwal_ujian.pendaftaran_id = mst_pendaftaran.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND mst_pendaftaran.tipe_ujian = ? AND  trt_penguji.tipe_ujian = ? AND trt_reg.status = ? ORDER BY mst_pendaftaran.pendaftaran_id", [0, 0, 0]);
        return view('tugasakhir.fakultas.rekap_nilai_proposal', compact('data'));
    }
    // Akhir Approve Hasil Ujian TA

    // Halaman Approve Hasil Ujian TA
    public function detail_rekap_nilai_proposal($id)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $id)->first();
        if (!$info || !isset($info->tipe_ujian)) {
            return response('Data rekap nilai proposal tidak ditemukan.', 404);
        }
        $data = DB::select("SELECT * FROM mst_pendaftaran,trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_reg.pendaftaran_id = ? AND trt_reg.status = ?", [$id, $info->tipe_ujian]);
        return view('tugasakhir.fakultas.detail_rekap_nilai_proposal', array_merge(
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

        return view("tugasakhir.fakultas.lembaran_hasilujian_proposal", compact(
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
        $data = DB::select("SELECT DISTINCT mst_pendaftaran.pendaftaran_id, mst_pendaftaran.nama_periode, mst_pendaftaran.kuota, mst_pendaftaran.jml_peserta, trt_jadwal_ujian.tgl_ujian FROM mst_pendaftaran, trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa, trt_jadwal_ujian, trt_jadwal_ujian_per_mhs , mst_ruangan WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND mst_ruangan.id =  trt_jadwal_ujian_per_mhs.ruangan AND trt_bimbingan.C_NPM = trt_jadwal_ujian_per_mhs.C_NPM AND trt_jadwal_ujian.id = trt_jadwal_ujian_per_mhs.jadwal_ujian AND trt_jadwal_ujian.pendaftaran_id = mst_pendaftaran.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM  AND mst_pendaftaran.tipe_ujian = ? AND  trt_penguji.tipe_ujian = ? AND trt_reg.status = ? ORDER BY mst_pendaftaran.pendaftaran_id", [2, 2, 2]);
        return view('tugasakhir.fakultas.rekap_nilai_ujian_ta', compact('data'));
    }
    // Akhir Approve Hasil Ujian TA

    // Halaman Approve Hasil Ujian TA
    public function detail_rekap_nilai_ujian_ta($id)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $id)->first();
        if (!$info || !isset($info->tipe_ujian)) {
            return response('Data rekap nilai ujian TA tidak ditemukan.', 404);
        }
        $data = DB::select("SELECT * FROM mst_pendaftaran,trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa WHERE mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_reg.pendaftaran_id = ? AND trt_reg.status = ? ", [$id, $info->tipe_ujian]);
        return view('tugasakhir.fakultas.detail_rekap_nilai_ujian_ta', array_merge(
            compact('data', 'info'),
            $this->prepareRekapNilaiViewData($data)
        ));
    }
    // Akhir Approve Hasil Ujian TA

    protected function rekapUjianTugasAkhirSelesaiQuery()
    {
        return DB::table('trt_jadwal_ujian as jadwal')
            ->join('trt_jadwal_ujian_per_mhs as peserta', 'peserta.jadwal_ujian', '=', 'jadwal.id')
            ->join('mst_pendaftaran as pendaftaran', 'pendaftaran.pendaftaran_id', '=', 'jadwal.pendaftaran_id')
            ->join('trt_reg as registrasi', function ($join) {
                $join->on('registrasi.C_NPM', '=', 'peserta.C_NPM')
                    ->on('registrasi.pendaftaran_id', '=', 'jadwal.pendaftaran_id')
                    ->where('registrasi.status', '=', 2);
            })
            ->leftJoin('t_mst_mahasiswa as mahasiswa', 'mahasiswa.C_NPM', '=', 'peserta.C_NPM')
            ->where('pendaftaran.tipe_ujian', 2)
            ->whereNotNull('jadwal.tgl_ujian')
            ->whereRaw("CAST(jadwal.tgl_ujian AS CHAR) <> '0000-00-00'")
            ->whereDate('jadwal.tgl_ujian', '<', Carbon::today()->toDateString());
    }

    protected function tabelRekapUjianSelesaiTersedia()
    {
        return Schema::hasTable('trt_rekap_ujian_selesai');
    }

    protected function mahasiswaEksekutifTersedia()
    {
        return Schema::hasTable('trt_mahasiswa_eksekutif');
    }

    protected function dokumenYudisiumTersedia()
    {
        return Schema::hasTable('trt_sk_yudisium');
    }

    protected function dataMahasiswaYudisiumTersedia()
    {
        return Schema::hasTable('trt_yudisium_mahasiswa');
    }

    protected function nomorAlumniTerakhir()
    {
        if (!$this->dataMahasiswaYudisiumTersedia()) {
            return null;
        }

        return DB::table('trt_yudisium_mahasiswa')
            ->whereNotNull('nomor_alumni')
            ->where('nomor_alumni', '!=', '')
            ->pluck('nomor_alumni')
            ->reduce(function ($terakhir, $nomorAlumni) {
                $nomorAlumni = trim((string) $nomorAlumni);
                if (!preg_match('/^[1-9][0-9]{0,8}$/', $nomorAlumni)) {
                    return $terakhir;
                }

                $nomorAlumni = (int) $nomorAlumni;

                return $terakhir === null || $nomorAlumni > $terakhir
                    ? $nomorAlumni
                    : $terakhir;
            });
    }

    protected function nomorSuratYudisiumTerakhir()
    {
        if (!$this->dokumenYudisiumTersedia()) {
            return null;
        }

        return DB::table('trt_sk_yudisium')
            ->whereNotNull('nomor_surat')
            ->where('nomor_surat', '!=', '')
            ->pluck('nomor_surat')
            ->reduce(function ($terakhir, $nomorSurat) {
                if (!preg_match('/^\s*([1-9][0-9]{0,8})(?:\D|$)/', (string) $nomorSurat, $matches)) {
                    return $terakhir;
                }

                $nomorUrut = (int) $matches[1];

                return $terakhir === null || $nomorUrut > $terakhir
                    ? $nomorUrut
                    : $terakhir;
            });
    }

    protected function bulanRomawiYudisium($bulan)
    {
        $bulanRomawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        return $bulanRomawi[(int) $bulan] ?? '';
    }

    protected function metadataIpkSiakadTersedia()
    {
        return $this->dataMahasiswaYudisiumTersedia()
            && Schema::hasColumn('trt_yudisium_mahasiswa', 'ipk_sumber')
            && Schema::hasColumn('trt_yudisium_mahasiswa', 'ipk_disinkronkan_pada');
    }

    protected function kodeProdiYudisium($kodeProdi)
    {
        $kodeProdi = trim((string) $kodeProdi);

        return in_array($kodeProdi, ['130', '131'], true) ? $kodeProdi : null;
    }

    protected function namaProdiYudisium($kodeProdi)
    {
        return $kodeProdi === '130' ? 'Teknik Informatika' : 'Sistem Informasi';
    }

    protected function emailProdiYudisium($kodeProdi)
    {
        return $kodeProdi === '130'
            ? 's1.teknik.informatika@umi.ac.id'
            : 's1.sistem.informasi@umi.ac.id';
    }

    protected function dokumenYudisium($date, $kodeProdi)
    {
        if (!$this->dokumenYudisiumTersedia()) {
            return null;
        }

        return DB::table('trt_sk_yudisium')
            ->where('tanggal_ujian', $date)
            ->where('tipe_ujian', 2)
            ->where('kode_prodi', $kodeProdi)
            ->first();
    }

    protected function simpanDokumenYudisium($date, $kodeProdi, $nomorSurat)
    {
        $dokumen = $this->dokumenYudisium($date, $kodeProdi);
        $payload = [
            'nomor_surat' => trim((string) $nomorSurat),
            'updated_by' => auth()->id(),
            'updated_at' => now(),
        ];

        if (!$dokumen || empty($dokumen->verification_token)) {
            $payload['verification_token'] = Str::random(48);
        }

        if ($dokumen) {
            DB::table('trt_sk_yudisium')->where('id', $dokumen->id)->update($payload);
        } else {
            DB::table('trt_sk_yudisium')->insert(array_merge($payload, [
                'tanggal_ujian' => $date,
                'tipe_ujian' => 2,
                'kode_prodi' => $kodeProdi,
                'created_by' => auth()->id(),
                'created_at' => now(),
            ]));
        }

        return $this->dokumenYudisium($date, $kodeProdi);
    }

    protected function nilaiHurufYudisium($nilai)
    {
        if ($nilai === null) {
            return null;
        }

        if ($nilai > 85) {
            return 'A';
        }
        if ($nilai >= 81) {
            return 'A-';
        }
        if ($nilai >= 76) {
            return 'B+';
        }
        if ($nilai >= 71) {
            return 'B';
        }
        if ($nilai >= 66) {
            return 'B-';
        }
        if ($nilai >= 61) {
            return 'C+';
        }
        if ($nilai >= 56) {
            return 'C';
        }
        if ($nilai >= 51) {
            return 'C-';
        }
        if ($nilai >= 46) {
            return 'D';
        }

        return 'E';
    }

    protected function kategoriYudisium($ipk)
    {
        if ($ipk === null || $ipk === '') {
            return null;
        }

        $ipk = (float) $ipk;
        if ($ipk >= 3.51) {
            return 'I';
        }
        if ($ipk >= 3.01) {
            return 'II';
        }
        if ($ipk >= 2.76) {
            return 'III';
        }
        if ($ipk >= 2.00) {
            return 'IV';
        }

        return null;
    }

    protected function pesertaYudisium($date, $kodeProdi)
    {
        $peserta = $this->rekapUjianTugasAkhirSelesaiQuery()
            ->leftJoin('trt_bimbingan as bimbingan', 'bimbingan.bimbingan_id', '=', 'registrasi.bimbingan_id')
            ->leftJoin('trt_penguji as penguji', function ($join) {
                $join->on('penguji.C_NPM', '=', 'peserta.C_NPM')
                    ->where('penguji.tipe_ujian', '=', 2);
            })
            ->whereDate('jadwal.tgl_ujian', $date)
            ->where('peserta.C_NPM', 'like', $kodeProdi . '%')
            ->select(
                'peserta.C_NPM as nim',
                'mahasiswa.NAMA_MAHASISWA as nama',
                'registrasi.reg_id',
                'bimbingan.pembimbing_I_id',
                'bimbingan.pembimbing_II_id',
                'penguji.ketua_sidang_id',
                'penguji.penguji_I_id',
                'penguji.penguji_II_id',
                'penguji.penguji_III_id'
            )
            ->distinct()
            ->orderBy('peserta.C_NPM')
            ->get();

        $hasilByRegId = $peserta->isEmpty()
            ? collect()
            : DB::table('trt_hasil')
                ->whereIn('reg_id', $peserta->pluck('reg_id')->filter()->unique()->all())
                ->get(['reg_id', 'nidn', 'nilai_1', 'nilai_2', 'nilai_3', 'nilai_4', 'nilai_5'])
                ->groupBy('reg_id');

        $metadataByNim = !$this->dataMahasiswaYudisiumTersedia() || $peserta->isEmpty()
            ? collect()
            : DB::table('trt_yudisium_mahasiswa')
                ->where('tanggal_ujian', $date)
                ->where('tipe_ujian', 2)
                ->whereIn('C_NPM', $peserta->pluck('nim')->all())
                ->get()
                ->keyBy('C_NPM');

        return $peserta->map(function ($mahasiswa) use ($hasilByRegId, $metadataByNim) {
            $penilaiWajib = collect([
                $mahasiswa->pembimbing_I_id,
                $mahasiswa->pembimbing_II_id,
                $mahasiswa->ketua_sidang_id,
                $mahasiswa->penguji_I_id,
                $mahasiswa->penguji_II_id,
                $mahasiswa->penguji_III_id,
            ])->map(function ($kodeDosen) {
                return trim((string) $kodeDosen);
            })->filter(function ($kodeDosen) {
                return $kodeDosen !== '' && $kodeDosen !== '--';
            })->unique()->values();

            $hasilPerPenilai = collect($hasilByRegId->get($mahasiswa->reg_id, collect()))
                ->keyBy('nidn');
            $nilaiPenilai = $penilaiWajib->map(function ($kodeDosen) use ($hasilPerPenilai) {
                $hasil = $hasilPerPenilai->get($kodeDosen);
                if (!$hasil) {
                    return null;
                }

                $nilai = [$hasil->nilai_1, $hasil->nilai_2, $hasil->nilai_3, $hasil->nilai_4, $hasil->nilai_5];
                foreach ($nilai as $item) {
                    if (!is_numeric($item) || (float) $item <= 0) {
                        return null;
                    }
                }

                return array_sum($nilai);
            });

            $nilaiUjianTa = $penilaiWajib->isNotEmpty() && !$nilaiPenilai->contains(null)
                ? round($nilaiPenilai->sum() / $penilaiWajib->count(), 2)
                : null;
            $metadata = $metadataByNim->get($mahasiswa->nim);
            $ipk = $metadata && $metadata->ipk !== null && $metadata->ipk !== ''
                ? (float) $metadata->ipk
                : null;
            $ipkSumber = $metadata && $this->metadataIpkSiakadTersedia()
                ? trim((string) ($metadata->ipk_sumber ?? ''))
                : '';
            $ipkDisinkronkanPada = $metadata && $this->metadataIpkSiakadTersedia()
                ? ($metadata->ipk_disinkronkan_pada ?? null)
                : null;

            return (object) [
                'nim' => (string) $mahasiswa->nim,
                'nama' => $mahasiswa->nama ?: '-',
                'nilai_ujian_ta' => $nilaiUjianTa,
                'nilai_huruf' => $this->nilaiHurufYudisium($nilaiUjianTa),
                'nomor_alumni' => $metadata && trim((string) $metadata->nomor_alumni) !== ''
                    ? trim((string) $metadata->nomor_alumni)
                    : null,
                'ipk' => $ipk,
                'ipk_sumber' => $ipkSumber !== '' ? $ipkSumber : null,
                'ipk_disinkronkan_pada' => $ipkDisinkronkanPada,
                'kategori_yudisium' => $this->kategoriYudisium($ipk),
            ];
        })->values();
    }

    protected function kekuranganDokumenYudisium($dokumen, $peserta)
    {
        $kekurangan = [];

        if (!$dokumen || trim((string) $dokumen->nomor_surat) === '') {
            $kekurangan[] = 'Nomor surat belum diisi.';
        }
        if ($peserta->isEmpty()) {
            $kekurangan[] = 'Peserta ujian tidak ditemukan.';
        }

        $nilaiBelumLengkap = $peserta->filter(function ($mahasiswa) {
            return $mahasiswa->nilai_ujian_ta === null;
        })->count();
        if ($nilaiBelumLengkap > 0) {
            $kekurangan[] = $nilaiBelumLengkap . ' nilai ujian TA belum lengkap.';
        }

        $alumniBelumLengkap = $peserta->filter(function ($mahasiswa) {
            return trim((string) $mahasiswa->nomor_alumni) === '';
        })->count();
        if ($alumniBelumLengkap > 0) {
            $kekurangan[] = $alumniBelumLengkap . ' nomor alumni belum diisi.';
        }

        $ipkBelumLengkap = $peserta->filter(function ($mahasiswa) {
            return $mahasiswa->ipk === null || $mahasiswa->kategori_yudisium === null;
        })->count();
        if ($ipkBelumLengkap > 0) {
            $kekurangan[] = $ipkBelumLengkap . ' IPK belum valid untuk yudisium.';
        }

        return $kekurangan;
    }

    public function rekap_ujian_selesai()
    {
        $query = $this->rekapUjianTugasAkhirSelesaiQuery();
        $selects = [
            'jadwal.tgl_ujian as tanggal_ujian',
            DB::raw('COUNT(DISTINCT peserta.C_NPM) as jumlah_mahasiswa'),
            DB::raw("COUNT(DISTINCT CASE WHEN peserta.C_NPM LIKE '130%' THEN peserta.C_NPM END) as jumlah_teknik_informatika"),
            DB::raw("COUNT(DISTINCT CASE WHEN peserta.C_NPM LIKE '131%' THEN peserta.C_NPM END) as jumlah_sistem_informasi"),
            DB::raw('NULL as nomor_surat_ti'),
            DB::raw('NULL as nomor_surat_si'),
        ];

        if ($this->mahasiswaEksekutifTersedia()) {
            $query->leftJoin('trt_mahasiswa_eksekutif as kelas_eksekutif', 'kelas_eksekutif.C_NPM', '=', 'peserta.C_NPM');
            $selects[] = DB::raw('COUNT(DISTINCT CASE WHEN kelas_eksekutif.C_NPM IS NULL THEN peserta.C_NPM END) as jumlah_reguler');
            $selects[] = DB::raw('COUNT(DISTINCT CASE WHEN kelas_eksekutif.C_NPM IS NOT NULL THEN peserta.C_NPM END) as jumlah_eksekutif');
        } else {
            $selects[] = DB::raw('COUNT(DISTINCT peserta.C_NPM) as jumlah_reguler');
            $selects[] = DB::raw('0 as jumlah_eksekutif');
        }

        if ($this->dokumenYudisiumTersedia()) {
            $query->leftJoin('trt_sk_yudisium as yudisium_ti', function ($join) {
                $join->on('yudisium_ti.tanggal_ujian', '=', 'jadwal.tgl_ujian')
                    ->where('yudisium_ti.tipe_ujian', '=', 2)
                    ->where('yudisium_ti.kode_prodi', '=', '130');
            });
            $query->leftJoin('trt_sk_yudisium as yudisium_si', function ($join) {
                $join->on('yudisium_si.tanggal_ujian', '=', 'jadwal.tgl_ujian')
                    ->where('yudisium_si.tipe_ujian', '=', 2)
                    ->where('yudisium_si.kode_prodi', '=', '131');
            });
            $selects[4] = DB::raw('MAX(yudisium_ti.nomor_surat) as nomor_surat_ti');
            $selects[5] = DB::raw('MAX(yudisium_si.nomor_surat) as nomor_surat_si');
        }

        $data = $query
            ->select($selects)
            ->groupBy('jadwal.tgl_ujian')
            ->orderBy('jadwal.tgl_ujian', 'desc')
            ->get();

        return view('tugasakhir.fakultas.rekap_ujian_selesai', compact('data'));
    }

    public function rekap_ujian_selesai_peserta($date)
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date)) {
            abort(404);
        }

        $payload = $this->pesertaYudisium($date, '130')
            ->merge($this->pesertaYudisium($date, '131'))
            ->sortBy('nim')
            ->values()
            ->map(function ($mahasiswa) {
                return [
                    'nim' => $mahasiswa->nim,
                    'nama' => $mahasiswa->nama,
                    'nilai_ujian_ta' => $mahasiswa->nilai_ujian_ta === null
                        ? 'Belum lengkap'
                        : number_format($mahasiswa->nilai_ujian_ta, 2, ',', '.'),
                    'ipk' => $mahasiswa->ipk === null
                        ? 'Belum tersedia'
                        : number_format($mahasiswa->ipk, 2, ',', '.'),
                ];
            });

        return response()->json([
            'tanggal_ujian' => $date,
            'data' => $payload,
        ]);
    }

    public function sk_yudisium_data($date, $kodeProdi)
    {
        $kodeProdi = $this->kodeProdiYudisium($kodeProdi);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date) || !$kodeProdi) {
            abort(404);
        }
        if (!$this->dokumenYudisiumTersedia() || !$this->dataMahasiswaYudisiumTersedia()) {
            return redirect()->route('fakultas.rekap_ujian_selesai')->with([
                'status' => 'danger',
                'message' => 'Penyimpanan SK Yudisium belum siap. Jalankan migrasi sistem terlebih dahulu.',
            ]);
        }

        $peserta = $this->pesertaYudisium($date, $kodeProdi);
        if ($peserta->isEmpty()) {
            abort(404);
        }

        $dokumen = $this->dokumenYudisium($date, $kodeProdi);
        $kekurangan = $this->kekuranganDokumenYudisium($dokumen, $peserta);
        $programStudi = $this->namaProdiYudisium($kodeProdi);
        $nomorAlumniTerakhir = $this->nomorAlumniTerakhir();
        $nomorAlumniBerikutnya = ($nomorAlumniTerakhir ?: 0) + 1;
        $nomorSuratYudisiumTerakhir = $this->nomorSuratYudisiumTerakhir();
        $tanggalUjian = Carbon::parse($date);
        $contohNomorSuratYudisium = (($nomorSuratYudisiumTerakhir ?: 0) + 1)
            . '/A.10/SI-FIK/UMI/' . $this->bulanRomawiYudisium($tanggalUjian->month)
            . '/' . $tanggalUjian->year;

        return view('tugasakhir.fakultas.sk_yudisium_data', compact(
            'date',
            'kodeProdi',
            'programStudi',
            'peserta',
            'dokumen',
            'kekurangan',
            'nomorAlumniTerakhir',
            'nomorAlumniBerikutnya',
            'nomorSuratYudisiumTerakhir',
            'contohNomorSuratYudisium'
        ));
    }

    public function simpan_data_sk_yudisium(Request $request)
    {
        $validated = $request->validate([
            'tanggal_ujian' => 'required|date_format:Y-m-d',
            'kode_prodi' => 'required|in:130,131',
            'nomor_surat' => 'nullable|string|max:150',
            'mahasiswa' => 'required|array',
            'mahasiswa.*.nim' => 'required|string|max:15',
            'mahasiswa.*.nomor_alumni' => ['nullable', 'regex:/^[1-9][0-9]{0,8}$/'],
        ], [
            'mahasiswa.*.nomor_alumni.regex' => 'Nomor alumni harus berupa angka positif, maksimal 9 digit, dan tidak diawali angka nol.',
        ]);

        $date = $validated['tanggal_ujian'];
        $kodeProdi = $validated['kode_prodi'];
        if (!$this->dokumenYudisiumTersedia() || !$this->dataMahasiswaYudisiumTersedia()) {
            return redirect()->route('fakultas.rekap_ujian_selesai')->with([
                'status' => 'danger',
                'message' => 'Penyimpanan SK Yudisium belum siap. Jalankan migrasi sistem terlebih dahulu.',
            ]);
        }

        $peserta = $this->pesertaYudisium($date, $kodeProdi);
        $nimTerdaftar = $peserta->pluck('nim')->map(function ($nim) {
            return (string) $nim;
        })->sort()->values();
        $inputMahasiswa = collect($validated['mahasiswa'])->keyBy(function ($mahasiswa) {
            return (string) $mahasiswa['nim'];
        });
        $nimInput = $inputMahasiswa->keys()->sort()->values();

        if ($nimTerdaftar->isEmpty() || $nimTerdaftar->diff($nimInput)->isNotEmpty() || $nimInput->diff($nimTerdaftar)->isNotEmpty()) {
            return redirect()->back()->withInput()->withErrors([
                'mahasiswa' => 'Daftar mahasiswa berubah. Muat ulang halaman sebelum menyimpan data yudisium.',
            ]);
        }

        $nomorAlumniByNim = $inputMahasiswa->mapWithKeys(function ($mahasiswa) {
            $nomorAlumni = trim((string) ($mahasiswa['nomor_alumni'] ?? ''));

            return [(string) $mahasiswa['nim'] => $nomorAlumni === '' ? null : $nomorAlumni];
        });
        $nomorAlumniTerisi = $nomorAlumniByNim->filter(function ($nomorAlumni) {
            return $nomorAlumni !== null;
        });
        $duplikatDalamForm = $nomorAlumniTerisi
            ->groupBy(function ($nomorAlumni) {
                return $nomorAlumni;
            })
            ->filter(function ($nomorSama) {
                return $nomorSama->count() > 1;
            });

        if ($duplikatDalamForm->isNotEmpty()) {
            return redirect()->back()->withInput()->withErrors([
                'mahasiswa' => 'Nomor alumni tidak boleh digunakan oleh lebih dari satu mahasiswa.',
            ]);
        }

        if ($nomorAlumniTerisi->isNotEmpty()) {
            $nomorAlumniBentrok = DB::table('trt_yudisium_mahasiswa')
                ->whereIn('nomor_alumni', $nomorAlumniTerisi->values()->all())
                ->get(['C_NPM', 'nomor_alumni'])
                ->first(function ($record) use ($nomorAlumniByNim) {
                    $nomorUntukMahasiswa = $nomorAlumniByNim->get((string) $record->C_NPM);

                    return $nomorUntukMahasiswa === null
                        || $nomorUntukMahasiswa !== trim((string) $record->nomor_alumni);
                });

            if ($nomorAlumniBentrok) {
                return redirect()->back()->withInput()->withErrors([
                    'mahasiswa' => 'Salah satu nomor alumni sudah digunakan mahasiswa lain. Periksa nomor terakhir sebelum menyimpan.',
                ]);
            }
        }

        DB::transaction(function () use ($date, $kodeProdi, $validated, $peserta, $inputMahasiswa) {
            $this->simpanDokumenYudisium($date, $kodeProdi, $validated['nomor_surat'] ?? '');

            foreach ($peserta as $mahasiswa) {
                $input = $inputMahasiswa->get($mahasiswa->nim);
                $nomorAlumni = trim((string) ($input['nomor_alumni'] ?? ''));
                $payload = [
                    'nomor_alumni' => $nomorAlumni === '' ? null : $nomorAlumni,
                    'updated_by' => auth()->id(),
                    'updated_at' => now(),
                ];
                $record = DB::table('trt_yudisium_mahasiswa')
                    ->where('tanggal_ujian', $date)
                    ->where('tipe_ujian', 2)
                    ->where('C_NPM', $mahasiswa->nim);

                if ($record->exists()) {
                    $record->update($payload);
                } else {
                    DB::table('trt_yudisium_mahasiswa')->insert(array_merge($payload, [
                        'tanggal_ujian' => $date,
                        'tipe_ujian' => 2,
                        'C_NPM' => $mahasiswa->nim,
                        'created_by' => auth()->id(),
                        'created_at' => now(),
                    ]));
                }
            }
        });

        return redirect()->route('fakultas.sk_yudisium_data', [$date, $kodeProdi])->with([
            'status' => 'success',
            'message' => 'Data SK Yudisium berhasil disimpan.',
        ]);
    }

    public function sinkronkan_ipk_sk_yudisium(Request $request)
    {
        $validated = $request->validate([
            'tanggal_ujian' => 'required|date_format:Y-m-d',
            'kode_prodi' => 'required|in:130,131',
            'nim' => 'required|string|max:15',
        ]);

        if (!$this->metadataIpkSiakadTersedia()) {
            return response()->json([
                'ok' => false,
                'message' => 'Penyimpanan IPK SIAKAD belum siap. Jalankan migrasi sistem terlebih dahulu.',
            ], 409);
        }

        $date = $validated['tanggal_ujian'];
        $kodeProdi = $validated['kode_prodi'];
        $nim = trim((string) $validated['nim']);
        $mahasiswa = $this->pesertaYudisium($date, $kodeProdi)->first(function ($item) use ($nim) {
            return (string) $item->nim === $nim;
        });

        if (!$mahasiswa) {
            return response()->json([
                'ok' => false,
                'message' => 'Mahasiswa tidak terdaftar pada SK Yudisium ini.',
            ], 404);
        }

        $result = app(SiakadIpkService::class)->syncByNim($nim);
        if (empty($result['ok'])) {
            return response()->json([
                'ok' => false,
                'message' => $result['message'] ?? 'IPK dari SIAKAD tidak dapat ditarik.',
            ], 422);
        }

        $now = now();
        $payload = [
            'ipk' => (float) $result['ipk'],
            'ipk_sumber' => (string) $result['source'],
            'ipk_disinkronkan_pada' => $now,
            'updated_by' => auth()->id(),
            'updated_at' => $now,
        ];
        $record = DB::table('trt_yudisium_mahasiswa')
            ->where('tanggal_ujian', $date)
            ->where('tipe_ujian', 2)
            ->where('C_NPM', $nim);

        if ($record->exists()) {
            $record->update($payload);
        } else {
            DB::table('trt_yudisium_mahasiswa')->insert(array_merge($payload, [
                'tanggal_ujian' => $date,
                'tipe_ujian' => 2,
                'C_NPM' => $nim,
                'created_by' => auth()->id(),
                'created_at' => $now,
            ]));
        }

        return response()->json([
            'ok' => true,
            'ipk' => number_format((float) $result['ipk'], 2, '.', ''),
            'kategori_yudisium' => $this->kategoriYudisium($result['ipk']),
            'total_sks' => (int) $result['total_sks'],
            'course_count' => (int) $result['course_count'],
            'source' => (string) $result['source'],
            'synced_at' => $now->format('d-m-Y H:i'),
            'message' => 'IPK berhasil ditarik dari SIAKAD.',
        ]);
    }

    public function reset_data_sk_yudisium(Request $request)
    {
        $validated = $request->validate([
            'tanggal_ujian' => 'required|date_format:Y-m-d',
            'kode_prodi' => 'required|in:130,131',
            'konfirmasi' => 'required|in:RESET',
        ]);

        if (!$this->dokumenYudisiumTersedia() || !$this->dataMahasiswaYudisiumTersedia()) {
            return redirect()->route('fakultas.rekap_ujian_selesai')->with([
                'status' => 'danger',
                'message' => 'Penyimpanan SK Yudisium belum siap. Jalankan migrasi sistem terlebih dahulu.',
            ]);
        }

        $date = $validated['tanggal_ujian'];
        $kodeProdi = $validated['kode_prodi'];
        $peserta = $this->pesertaYudisium($date, $kodeProdi);
        if ($peserta->isEmpty()) {
            return redirect()->route('fakultas.rekap_ujian_selesai')->with([
                'status' => 'danger',
                'message' => 'Peserta SK Yudisium tidak ditemukan. Tidak ada data yang direset.',
            ]);
        }

        DB::transaction(function () use ($date, $kodeProdi, $peserta) {
            DB::table('trt_yudisium_mahasiswa')
                ->where('tanggal_ujian', $date)
                ->where('tipe_ujian', 2)
                ->whereIn('C_NPM', $peserta->pluck('nim')->all())
                ->delete();

            DB::table('trt_sk_yudisium')
                ->where('tanggal_ujian', $date)
                ->where('tipe_ujian', 2)
                ->where('kode_prodi', $kodeProdi)
                ->delete();
        });

        return redirect()->route('fakultas.rekap_ujian_selesai')->with([
            'status' => 'success',
            'message' => 'Data SK Yudisium ' . $this->namaProdiYudisium($kodeProdi)
                . ' tanggal ' . helper::tgl_indo_lengkap($date) . ' berhasil direset. Nilai ujian dan jadwal tetap tersimpan.',
        ]);
    }

    public function cetak_sk_yudisium($date, $kodeProdi)
    {
        $kodeProdi = $this->kodeProdiYudisium($kodeProdi);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date) || !$kodeProdi) {
            abort(404);
        }

        $peserta = $this->pesertaYudisium($date, $kodeProdi);
        $dokumen = $this->dokumenYudisium($date, $kodeProdi);
        $kekurangan = $this->kekuranganDokumenYudisium($dokumen, $peserta);
        if (!empty($kekurangan)) {
            return redirect()->route('fakultas.sk_yudisium_data', [$date, $kodeProdi])->with([
                'status' => 'danger',
                'message' => 'PDF belum dapat dibuat. ' . implode(' ', $kekurangan),
            ]);
        }

        if (empty($dokumen->verification_token)) {
            $dokumen = $this->simpanDokumenYudisium($date, $kodeProdi, $dokumen->nomor_surat);
        }

        $tanggalSurat = Carbon::parse($date);
        $tanggalHijriah = app(IndonesianHijriDateService::class)->format($tanggalSurat);
        $programStudi = $this->namaProdiYudisium($kodeProdi);
        $emailProgramStudi = $this->emailProdiYudisium($kodeProdi);
        $dekan = helper::getDekanByTanggal($date);
        $namaDekan = $dekan && !empty($dekan->nama) ? $dekan->nama : '-';
        $verificationUrl = route('verifikasi_sk_yudisium', ['token' => $dokumen->verification_token]);
        $safeNomorSurat = trim(preg_replace('/[^A-Za-z0-9._-]+/', '-', (string) $dokumen->nomor_surat), '-');

        return PDF::loadView('tugasakhir.fakultas.sk_yudisium_pdf', compact(
            'dokumen',
            'peserta',
            'tanggalSurat',
            'tanggalHijriah',
            'programStudi',
            'emailProgramStudi',
            'namaDekan',
            'verificationUrl'
        ))
            ->setPaper('a4', 'portrait')
            ->stream('SK-Yudisium-' . ($safeNomorSurat ?: $kodeProdi . '-' . $date) . '.pdf');
    }

    public function verifikasi_sk_yudisium($token)
    {
        if (!preg_match('/^[A-Za-z0-9]{32,64}$/', (string) $token) || !$this->dokumenYudisiumTersedia()) {
            abort(404);
        }

        $dokumen = DB::table('trt_sk_yudisium')
            ->where('verification_token', $token)
            ->where('tipe_ujian', 2)
            ->first();
        if (!$dokumen || !$this->kodeProdiYudisium($dokumen->kode_prodi)) {
            abort(404);
        }

        $programStudi = $this->namaProdiYudisium($dokumen->kode_prodi);
        $jumlahMahasiswa = $this->pesertaYudisium($dokumen->tanggal_ujian, $dokumen->kode_prodi)->count();

        return view('tugasakhir.fakultas.verifikasi_sk_yudisium', compact(
            'dokumen',
            'programStudi',
            'jumlahMahasiswa'
        ));
    }

    public function rekap_ujian_selesai_nomor_surat(Request $request)
    {
        $validated = $request->validate([
            'tanggal_ujian' => 'required|date_format:Y-m-d',
            'nomor_surat' => 'nullable|string|max:150',
        ]);

        $date = $validated['tanggal_ujian'];
        if (!$this->tabelRekapUjianSelesaiTersedia()) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Penyimpanan nomor surat belum siap. Jalankan migrasi sistem terlebih dahulu.',
            ]);
        }

        if (!$this->rekapUjianTugasAkhirSelesaiQuery()->whereDate('jadwal.tgl_ujian', $date)->exists()) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Tanggal ujian tidak ditemukan pada rekap ujian TA selesai.',
            ]);
        }

        $nomorSurat = trim((string) ($validated['nomor_surat'] ?? ''));
        if ($nomorSurat === '') {
            DB::table('trt_rekap_ujian_selesai')
                ->where('tanggal_ujian', $date)
                ->where('tipe_ujian', 2)
                ->delete();

            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Nomor surat untuk tanggal ujian tersebut dihapus.',
            ]);
        }

        $rekapSurat = DB::table('trt_rekap_ujian_selesai')
            ->where('tanggal_ujian', $date)
            ->where('tipe_ujian', 2);
        $payload = [
            'nomor_surat' => $nomorSurat,
            'updated_by' => auth()->id(),
            'updated_at' => now(),
        ];

        if ($rekapSurat->exists()) {
            $rekapSurat->update($payload);
        } else {
            DB::table('trt_rekap_ujian_selesai')->insert(array_merge($payload, [
                'tanggal_ujian' => $date,
                'tipe_ujian' => 2,
                'created_at' => now(),
            ]));
        }

        return redirect()->back()->with([
            'status' => 'success',
            'message' => 'Nomor surat berhasil disimpan untuk seluruh mahasiswa ujian pada tanggal tersebut.',
        ]);
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

        return view("tugasakhir.fakultas.lembaran_hasilujian_ujian_ta", compact(
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

    // Halaman Ubah Password
    public function ubah_password()
    {
        return view('tugasakhir.fakultas.ubah_password');
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


    // Cetak SK TIM Ujian TA Oleh Fakultas

    public function usulan_timujianta($id)
    {
        $datax = DB::table('mst_pendaftaran')
            ->select('*')
            ->whereIn('mst_pendaftaran.pendaftaran_id', $id)
            ->get();
        $a = 0;
        foreach ($datax as $key => $value) {
            $simpan['pendaftaran_id'] = $datax[$a]->pendaftaran_id;
            $simpan['nomor'] = $nomor;
            $simpan['perihal'] = $perihal;
            $simpan['tgl_surat'] = $tgl;
            DB::table('mst_pendaftaran')
                ->where('pendaftaran_id', $datax[$a]->pendaftaran_id)
                ->update(['status_sk' => '1']);
            $a++;
        }
        return view('tugasakhir.prodi.surat_usulantimujian', compact('datax'));
    }

    // Menampilkan Daftar atau List SK Penugasan TIM Ujian TA

    public function surat_penugasan_ujian_ta()
    {

        $data_sk_penugasan = DB::table('mst_sk_penugasan')
            ->select('*')
            ->join('trt_bimbingan', 'trt_bimbingan.bimbingan_id', '=', 'mst_sk_penugasan.bimbingan_id')
            ->orderBy('mst_sk_penugasan.sk_penugasan_id', 'DESC')
            ->get();

        $daftar_surat_usulan = DB::table('trt_sk_ujian_ta')
            ->select('*')
            ->join('mst_pendaftaran', 'mst_pendaftaran.pendaftaran_id', '=', 'trt_sk_ujian_ta.pendaftaran_id')
            ->orderBy('trt_sk_ujian_ta.sk_id', 'DESC')
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
        try {
            mst_sk_penugasan::updateOrcreate(
                [
                    "bimbingan_id" => $datapost['bimbingan_id'],
                ],
                [
                    "nomor_sk" => $datapost['nomor_sk'],
                ]
            );
            return redirect::to('fakultas/surat_penugasan_ujian_ta')->with('status', 'success');
        } catch (Exception $exception) {
            return redirect::to('fakultas/surat_penugasan_ujian_ta')->with('status', 'error');
        }
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
            ->select(['mst_sk_penugasan.created_at','mst_sk_penugasan.sk_penugasan_id', 'mst_sk_penugasan.nomor_sk', 'trt_bimbingan.pembimbing_I_id', "trt_bimbingan.pembimbing_II_id", "trt_penguji.ketua_sidang_id", "trt_penguji.penguji_I_id", "trt_penguji.penguji_II_id", "trt_penguji.penguji_III_id", "trt_penguji.C_NPM", "trt_jadwal_ujian.tgl_ujian", "trt_jadwal_ujian_per_mhs.jam_ujian", "mst_ruangan.nama_ruangan", "trt_jadwal_ujian.pendaftaran_id"])
            ->where('trt_bimbingan.bimbingan_id', $datapost['bimbingan_id'])
            ->where('trt_penguji.tipe_ujian', 2)
            ->where('trt_jadwal_ujian.status', 2)
            ->get();





        if ($data_sk->isEmpty()) {
            return response('Data surat penugasan belum lengkap atau tidak ditemukan.', 404);
        }

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
        return redirect('sk_pembimbing/' . str_replace('/', '', (string) $request->input('nomor')));
    }

    public function cetakskpembimbing_pdf(Request $request)
    {
        return redirect('sk_pembimbing_pdf/' . str_replace('/', '', (string) $request->input('nomor')));
    }

    private function getSkPembimbingForPdf($nomor)
    {
        return DB::table('mst_sk_pembimbing as sk')
            ->join('trt_bimbingan as bimbingan', 'sk.bimbingan_id', '=', 'bimbingan.bimbingan_id')
            ->select([
                'sk.sk_pembimbing_id',
                'sk.nomor_sk',
                'sk.status as status_sk',
                'sk.created_at as sk_created_at',
                'bimbingan.bimbingan_id',
                'bimbingan.C_NPM',
                'bimbingan.pembimbing_I_id',
                'bimbingan.pembimbing_II_id',
                'bimbingan.judul',
                'bimbingan.jenis_tugas_akhir_id',
            ])
            ->where('sk.nomor_sk', $nomor)
            ->first();
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
            ->orderBy('tgl_surat', 'DESC')
            ->get();

        $data_sk = DB::table('mst_sk_pembimbing')
            ->join('trt_bimbingan', 'mst_sk_pembimbing.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->select('*')
            ->orderBy('mst_sk_pembimbing.sk_pembimbing_id', 'DESC')
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
        try {
            $status_ = mst_sk_pembimbing::updateOrcreate(
                [
                    "bimbingan_id" => $datapost['bimbingan_id'],
                ],
                [
                    "nomor_sk" => $datapost['nomor_sk'],
                ]
            );
            return redirect::to('fakultas/sk_pembimbing')->with('status', 'success');
        } catch (Exception $exception) {
            return redirect::to('fakultas/sk_pembimbing')->with('status', 'error');
        }
    }

    // Surat Keputusan
    // Menampilkan Surat Ujian Berlaku Untuk Proposal, Ujian Meja dan Umum
    public function sk_ujian()
    {
        $pendaftaran = mst_pendaftaran::get();
        $jadwalujian = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where('mst_pendaftaran.tipe_ujian', '=', 0)
            ->orderBy('mst_pendaftaran.created_at', 'desc')
            ->get();
        return view('tugasakhir.prodi.sk_ujian', compact('pendaftaran', "jadwalujian"));
    }
}
