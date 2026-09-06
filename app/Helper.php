<?php

namespace App;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

class Helper
{

    const ANNOUNCEMENT_IMAGE_DIRECTORY = 'uploads/announcements';

    const MANAGED_OFFICIAL_IMAGE_PREFIX = 'ttd_kaprodi_';

    // get tanggal 1-31
    // public static function fortgl() {
    //     for($i=1; $i<=31; $i++){
    //         $data[] = $i;
    //     }
    //     return $data;
    // }
    // get tahun 1945-sekarang
    // public static function forthn() {
    //     for($i=date('Y'); $i>='1935'; $i--){
    //         $data[] = $i;
    //     }
    //     return $data;
    // }
    // 01- januari- 2018

    public static function tgl_indo($tgl)
    {
        $tanggal = substr($tgl, 8, 2);
        $bulan = Helper::getBulan((int) substr($tgl, 5, 2));
        $tahun = substr($tgl, 0, 4);
        $tgl = $tanggal . " " . $bulan . " " . $tahun;
        if ($tgl != "--") {
            return $tanggal . " " . $bulan . " " . $tahun;
        }
    }

    public static function getHariNew($hari) {}

    // tahun - bulan -tanggal

    public static function getBulan($bln)
    {
        switch ($bln) {
            case 1:
                return "Januari";
                break;
            case 2:
                return "Februari";
                break;
            case 3:
                return "Maret";
                break;
            case 4:
                return "April";
                break;
            case 5:
                return "Mei";
                break;
            case 6:
                return "Juni";
                break;
            case 7:
                return "Juli";
                break;
            case 8:
                return "Agustus";
                break;
            case 9:
                return "September";
                break;
            case 10:
                return "Oktober";
                break;
            case 11:
                return "November";
                break;
            case 12:
                return "Desember";
                break;
        }
    }

    // tanggal -bulan -tahun

    public static function tgl($tgl)
    {
        $tanggal = substr($tgl, 3, 2);
        $bulan = substr($tgl, 0, 2);
        $tahun = substr($tgl, 6, 2);
        if ($tgl == '') {
            return null;
        } else {
            return "20" . $tahun . "-" . $bulan . "-" . $tanggal;
        }
    }

    public static function tgl1($tgl)
    {
        $tanggal = substr($tgl, 8, 2);
        $bulan = substr($tgl, 5, 2);
        $tahun = substr($tgl, 0, 4);
        if ($tgl == '') {
            return null;
        } else {
            return $tanggal . "-" . $bulan . "-" . $tahun;
        }
    }

    public static function tgl1_new($tgl)
    {
        $tanggal = substr($tgl, 8, 2);
        $bulan = substr($tgl, 5, 2);
        $tahun = substr($tgl, 0, 2);
        if ($tgl == '') {
            return null;
        } else {
            return $tanggal . "-" . $bulan . "-" . $tahun;
        }
    }

    public static function bln($tgl)
    {
        $bulan = Helper::getBulan(substr($tgl, 5, 2));
        return $bulan;
    }

    public static function thn($tgl)
    {
        $tahun = substr($tgl, 0, 4);
        return $tahun;
    }

    // fungsi upload gambar produk

    public static function uploadImage($image, $folder, $fileold)
    {
        $tgl = date('Y-m-d');
        $file = ['file' => $image];
        $rules = ['file' => 'mimes:jpeg,jpg,gif,png'];
        $validator = Validator::make($file, $rules);

        if ($validator->fails() or $image == null) {
            $fileName = $fileold == '' ? '' : $fileold;
        } else {
            $extension = strstr($image->getClientOriginalName(), '.');
            $uniq = uniqid();
            $fileName = $tgl . "-" . $uniq . $extension;
            $fileName = str_replace('-', '', $fileName);

            $image->move($folder, $fileName);
            // list($w, $h) = getimagesize($folder.$fileName);
            // $w = $w / 2;
            // $h = $h / 2;
            //  // open an image file
            // $img_medium = Image::make($folder.$fileName);
            // // resize image instance
            // $img_medium->resize($w, $h);
            // // save image in desired format
            // $img_medium->save($folder."medium/".$fileName);

            // $w = $w / 2;
            // $h = $h / 2;
            //  // open an image file
            // $img_small = Image::make($folder.$fileName);
            // // resize image instance
            // $img_small->resize($w, $h);
            // // save image in desired format
            // $img_small->save($folder."small/".$fileName);

            Helper::DeleteImage($fileold, $folder);
        }
        return $fileName;
    }

    public static function storeAnnouncementImage($image)
    {
        $extension = strtolower($image->getClientOriginalExtension());

        if (!in_array($extension, ['jpeg', 'jpg', 'gif', 'png'], true)) {
            throw new RuntimeException('Format gambar pengumuman tidak didukung.');
        }

        $fileName = date('Ymd') . uniqid() . '.' . $extension;
        $path = $image->storeAs(self::ANNOUNCEMENT_IMAGE_DIRECTORY, $fileName, 'public');

        if ($path === false) {
            throw new RuntimeException('Gambar pengumuman gagal disimpan.');
        }

        return $path;
    }

    public static function deleteAnnouncementImage($path)
    {
        if (!self::isManagedAnnouncementImage($path)) {
            return false;
        }

        return Storage::disk('public')->delete($path);
    }

    public static function announcementImageUrl($path)
    {
        if (empty($path)) {
            return asset('gambar/no_image.jpg');
        }

        if (self::isManagedAnnouncementImage($path)) {
            return asset('storage/' . ltrim($path, '/'));
        }

        return asset('gambar/' . basename($path));
    }

    public static function isManagedAnnouncementImage($path)
    {
        if (!is_string($path)) {
            return false;
        }

        $prefix = self::ANNOUNCEMENT_IMAGE_DIRECTORY . '/';

        if (strpos($path, $prefix) !== 0) {
            return false;
        }

        $fileName = substr($path, strlen($prefix));

        return $fileName !== '' && basename($fileName) === $fileName;
    }

    public static function storeOfficialImage($image, $prefix)
    {
        $extension = strtolower($image->getClientOriginalExtension());

        if (!in_array($extension, ['jpeg', 'jpg', 'png'], true)) {
            throw new RuntimeException('Format gambar resmi tidak didukung.');
        }

        $prefix = preg_replace('/[^a-z0-9_-]+/i', '_', (string) $prefix);
        $fileName = trim($prefix, '_') . '_' . uniqid() . '.' . $extension;
        $path = $image->storeAs('', $fileName, 'official');

        if ($path === false) {
            throw new RuntimeException('Gambar resmi gagal disimpan.');
        }

        return $fileName;
    }

    public static function officialImageDataUri($fileName)
    {
        if (!self::isSafeOfficialImageName($fileName)) {
            return asset('gambar/no_image.jpg');
        }

        $contents = null;

        if (Storage::disk('official')->exists($fileName)) {
            $contents = Storage::disk('official')->get($fileName);
        } else {
            $legacyPath = public_path('gambar/' . $fileName);

            if (is_file($legacyPath)) {
                $contents = file_get_contents($legacyPath);
            }
        }

        if ($contents === null || $contents === false) {
            return asset('gambar/no_image.jpg');
        }

        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $mime = $extension === 'png' ? 'image/png' : 'image/jpeg';

        return 'data:' . $mime . ';base64,' . base64_encode($contents);
    }

    /**
     * Return a validated local image data URI for PDF rendering.
     * Dompdf must not receive a broken or remote fallback image.
     */
    public static function pdfOfficialImageDataUri($fileName)
    {
        $dataUri = self::officialImageDataUri($fileName);

        if (strpos($dataUri, 'data:') !== 0) {
            return self::publicImageDataUri('master/assets/img/logo-login.png');
        }

        $parts = explode(',', $dataUri, 2);
        $imageData = isset($parts[1]) ? base64_decode($parts[1], true) : false;

        if ($imageData === false || $imageData === '') {
            return self::publicImageDataUri('master/assets/img/logo-login.png');
        }

        $mime = strtolower(substr($parts[0], 5));
        $isValid = true;

        if ($mime === 'image/png') {
            $isValid = strlen($imageData) >= 12
                && substr($imageData, 0, 8) === "\x89PNG\r\n\x1a\n"
                && substr($imageData, -12, 4) === 'IEND';
        } elseif ($mime === 'image/jpeg' || $mime === 'image/jpg') {
            $isValid = strlen($imageData) >= 4
                && substr($imageData, 0, 2) === "\xff\xd8"
                && substr($imageData, -2) === "\xff\xd9";
        }

        return $isValid ? $dataUri : self::publicImageDataUri('master/assets/img/logo-login.png');
    }

    public static function qrCodeDataUri($value, $size = 130)
    {
        $size = max(80, min(240, (int) $size));
        $renderer = new ImageRenderer(
            new RendererStyle($size),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $svg = $writer->writeString((string) $value);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public static function publicImageDataUri($fileName)
    {
        $fileName = ltrim((string) $fileName, '/');
        if (strpos($fileName, '..') !== false
            || !preg_match('/\A[a-zA-Z0-9._\/-]+\.(?:gif|jpe?g|png)\z/i', $fileName)) {
            $fileName = 'master/assets/img/logo-login.png';
        }

        $path = public_path($fileName);
        $imageData = is_file($path) && is_readable($path) ? @file_get_contents($path) : false;
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $imageIsValid = $imageData !== false;

        if ($imageIsValid && $extension === 'png') {
            $imageIsValid = strlen($imageData) >= 12
                && substr($imageData, 0, 8) === "\x89PNG\r\n\x1a\n"
                && substr($imageData, -12, 4) === 'IEND';
        }

        if (!$imageIsValid) {
            $fallback = public_path('master/assets/img/logo-login.png');
            $fileName = 'master/assets/img/logo-login.png';
            $path = $fallback;
            $imageData = file_get_contents($path);
            $extension = 'png';
        }

        $mime = $extension === 'png' ? 'image/png' : ($extension === 'gif' ? 'image/gif' : 'image/jpeg');

        return 'data:' . $mime . ';base64,' . base64_encode($imageData);
    }

    public static function deleteManagedOfficialImage($fileName)
    {
        if (!self::isManagedOfficialImage($fileName)) {
            return false;
        }

        return Storage::disk('official')->delete($fileName);
    }

    public static function isManagedOfficialImage($fileName)
    {
        if (!self::isSafeOfficialImageName($fileName)) {
            return false;
        }

        return (bool) preg_match('/\Attd_kaprodi_\d+_[a-f0-9]+\.(?:jpe?g|png)\z/i', $fileName);
    }

    public static function isSafeOfficialImageName($fileName)
    {
        if (!is_string($fileName) || $fileName === '' || basename($fileName) !== $fileName) {
            return false;
        }

        return (bool) preg_match('/\A[a-zA-Z0-9._-]+\.(?:jpe?g|png)\z/i', $fileName);
    }

    // delete foto produk
    public static function deleteImage($image, $folder)
    {
        File::delete($folder . $image);
        // File::delete($folder."medium/".$image);
        // File::delete($folder."small/".$image);
    }

    public static function uploadFile($image, $path, $file_old)
    {
        $tgl = date('Y-m-d');
        $file = ['file' => $image];
        $rules = ['file' => 'mimes:pdf,xls,doc,docx,pptx,pps,jpeg,bmp,png,xlsx,zip,rar'];
        $validator = Validator::make($file, $rules);

        if ($validator->fails() or $image == null) {
            $fileName = $file_old == '' ? '' : $file_old;
        } else {
            $extension = strstr($image->getClientOriginalName(), '.');
            $uniq = uniqid();
            $fileName = $tgl . "_" . $uniq . $extension;
            $image->move($path, $fileName);
            Helper::deleteFile($file_old, $path);
        }
        return $fileName;
    }

    public static function deleteFile($file, $path)
    {
        File::delete($path . $file);
    }

    public static function Option($kode)
    {
        $Model = DB::table('option')->select('option', 'deskripsi')->where('kode', '=', $kode)->orderBy('urutan', 'asc')->get();
        return isset($Model) ? $Model : "";
    }

    public static function noRegister($tabel, $key)
    {
        $v = DB::table($tabel)->select('no_reg', 'created_at')->where('no_reg', '<>', '')->orderBy($key, 'desc')->first();
        $no = isset($v->no_reg) ? $v->no_reg : '';
        $tgl = isset($v->created_at) ? $v->created_at : '';
        if (substr($tgl, 0, 4) == date('Y')) {
            $no = (int) substr($no, -4);
            $no++;
        } else {
            $no = "0001";
        }
        return sprintf("%04s", $no);
    }

    public static function nomor($tabel, $no_reg, $kode, $kd)
    {
        $v = DB::table($tabel)->select($no_reg, 'created_at')->where($no_reg, '<>', '')->orderBy('id', 'desc')->first();
        $no = isset($v->$no_reg) ? $v->$no_reg : '';
        $tgl = isset($v->created_at) ? $v->created_at : '';
        if (substr($tgl, 0, 7) == date('Y-m')) {
            $jml = 1 + (strlen($kode));
            $no = (int) substr($no, $jml, 3);
            $no++;
        } else {
            $no = "001";
        }
        return $kode . '-' . sprintf("%03s", $no) . $kd . date('m/Y');
    }

    public static function nomorKep($kode)
    {
        $v = DB::table('surat_keputusan_wja')->select('no_surat', 'tipe_surat', 'created_at')->where('no_surat', '<>', '')->where('tipe_surat', $kode)->orderBy('id', 'desc')->first();
        $no = isset($v->no_surat) ? $v->no_surat : '';
        $tgl = isset($v->created_at) ? $v->created_at : '';
        if (substr($tgl, 0, 7) == date('Y-m')) {
            $no = (int) substr($no, -17, 3);
            $no++;
        } else {
            $no = "001";
        }
        return 'KEP-' . $kode . '-' . sprintf("%03s", $no) . '/B/WJA/' . date('m/Y');
    }

    public static function nomorReg($tabel, $kode, $id)
    {
        $v = DB::table($tabel)->select('no_reg', 'created_at')->where('no_reg', '<>', '')->where('no_reg', 'like', '%' . $kode . '%')->orderBy($id, 'desc')->first();
        $no = isset($v->no_reg) ? $v->no_reg : '';
        $tgl = isset($v->created_at) ? $v->created_at : '';
        if (substr($tgl, 0, 7) == date('Y-m')) {
            $no = (int) substr($no, -4);
            $no++;
            // dd($no);
        } else {
            $no = "0001";
        }
        return $kode . '-' . sprintf("%04s", $no);
    }

    public static function getDeskripsi($id)
    {
        return self::getNamaDosenByKode($id);
    }

    public static function getDosenRecordByKode($kodeDosen)
    {
        $kodeDosen = trim((string) $kodeDosen);

        if ($kodeDosen === '') {
            return null;
        }

        $dosen = null;

        if (Schema::hasTable('t_mst_dosen')) {
            $dosen = DB::table('t_mst_dosen')
                ->select('*')
                ->where('C_KODE_DOSEN', $kodeDosen)
                ->first();
        }

        if (!$dosen && Schema::hasTable('mig_t_mst_dosen')) {
            $dosen = DB::table('mig_t_mst_dosen')
                ->select('*')
                ->where('C_KODE_DOSEN', $kodeDosen)
                ->first();
        }

        return $dosen;
    }

    public static function getNamaDosenByKode($kodeDosen)
    {
        $dosen = self::getDosenRecordByKode($kodeDosen);
        return isset($dosen->NAMA_DOSEN) && $dosen->NAMA_DOSEN !== '' ? $dosen->NAMA_DOSEN : '--';
    }

    public static function getNamaMhs($id)
    {
        $v = DB::table('t_mst_mahasiswa')
            ->select('*')
            ->Where('C_NPM', $id)
            ->first();
        return isset($v) ? $v->NAMA_MAHASISWA : '';
    }

    public static function getKodeDosenFromUser($user = null)
    {
        $user = $user ?: auth()->user();

        if (!$user) {
            return '';
        }

        $candidates = array_filter([
            trim((string) ($user->name ?? '')),
            trim((string) ($user->email ?? '')),
        ]);

        foreach ($candidates as $candidate) {
            $dosen = self::getDosenRecordByKode($candidate);
            if ($dosen && !empty($dosen->C_KODE_DOSEN)) {
                return $dosen->C_KODE_DOSEN;
            }
        }

        foreach ($candidates as $candidate) {
            $dosen = null;

            if (Schema::hasTable('t_mst_dosen')) {
                $dosen = DB::table('t_mst_dosen')
                    ->select('C_KODE_DOSEN')
                    ->whereRaw('LOWER(TRIM(EMAIL)) = ?', [strtolower($candidate)])
                    ->first();
            }

            if (!$dosen && Schema::hasTable('mig_t_mst_dosen')) {
                $dosen = DB::table('mig_t_mst_dosen')
                    ->select('C_KODE_DOSEN')
                    ->whereRaw('LOWER(TRIM(EMAIL)) = ?', [strtolower($candidate)])
                    ->first();
            }

            if ($dosen && !empty($dosen->C_KODE_DOSEN)) {
                return $dosen->C_KODE_DOSEN;
            }
        }

        foreach ($candidates as $candidate) {
            $dosen = null;

            if (Schema::hasTable('t_mst_dosen')) {
                $dosen = DB::table('t_mst_dosen')
                    ->select('C_KODE_DOSEN')
                    ->whereRaw('LOWER(TRIM(NAMA_DOSEN)) = ?', [strtolower($candidate)])
                    ->first();
            }

            if (!$dosen && Schema::hasTable('mig_t_mst_dosen')) {
                $dosen = DB::table('mig_t_mst_dosen')
                    ->select('C_KODE_DOSEN')
                    ->whereRaw('LOWER(TRIM(NAMA_DOSEN)) = ?', [strtolower($candidate)])
                    ->first();
            }

            if ($dosen && !empty($dosen->C_KODE_DOSEN)) {
                return $dosen->C_KODE_DOSEN;
            }
        }

        $normalizedCandidates = array_filter(array_unique(array_map(function ($candidate) {
            return self::normalizeNamaOrang($candidate);
        }, $candidates)));

        foreach ($normalizedCandidates as $normalizedCandidate) {
            $rows = collect();

            if (Schema::hasTable('t_mst_dosen')) {
                $rows = $rows->merge(
                    DB::table('t_mst_dosen')
                        ->select('C_KODE_DOSEN', 'NAMA_DOSEN')
                        ->get()
                );
            }

            if (Schema::hasTable('mig_t_mst_dosen')) {
                $rows = $rows->merge(
                    DB::table('mig_t_mst_dosen')
                        ->select('C_KODE_DOSEN', 'NAMA_DOSEN')
                        ->get()
                );
            }

            $match = $rows->first(function ($row) use ($normalizedCandidate) {
                return self::normalizeNamaOrang($row->NAMA_DOSEN ?? '') === $normalizedCandidate;
            });

            if ($match && !empty($match->C_KODE_DOSEN)) {
                return $match->C_KODE_DOSEN;
            }
        }

        return trim((string) ($user->name ?? ''));
    }

    public static function getKodeDosenForTrtHasil($user = null)
    {
        $user = $user ?: auth()->user();

        if (!$user) {
            return '';
        }

        $candidates = array_filter([
            trim((string) ($user->name ?? '')),
            trim((string) ($user->email ?? '')),
        ]);

        if (Schema::hasTable('mig_t_mst_dosen')) {
            foreach ($candidates as $candidate) {
                $dosen = DB::table('mig_t_mst_dosen')
                    ->select('C_KODE_DOSEN')
                    ->where('C_KODE_DOSEN', $candidate)
                    ->first();

                if ($dosen && !empty($dosen->C_KODE_DOSEN)) {
                    return $dosen->C_KODE_DOSEN;
                }
            }

            foreach ($candidates as $candidate) {
                $dosen = DB::table('mig_t_mst_dosen')
                    ->select('C_KODE_DOSEN')
                    ->whereRaw('LOWER(TRIM(EMAIL)) = ?', [strtolower($candidate)])
                    ->first();

                if ($dosen && !empty($dosen->C_KODE_DOSEN)) {
                    return $dosen->C_KODE_DOSEN;
                }
            }

            $normalizedCandidates = array_filter(array_unique(array_map(function ($candidate) {
                return self::normalizeNamaOrang($candidate);
            }, $candidates)));

            if (!empty($normalizedCandidates)) {
                $rows = DB::table('mig_t_mst_dosen')
                    ->select('C_KODE_DOSEN', 'NAMA_DOSEN')
                    ->get();

                foreach ($normalizedCandidates as $normalizedCandidate) {
                    $match = $rows->first(function ($row) use ($normalizedCandidate) {
                        return self::normalizeNamaOrang($row->NAMA_DOSEN ?? '') === $normalizedCandidate;
                    });

                    if ($match && !empty($match->C_KODE_DOSEN)) {
                        return $match->C_KODE_DOSEN;
                    }
                }
            }
        }

        return '';
    }

    public static function getCurrentDosenProfileByAuthUser($user = null)
    {
        $user = $user ?: auth()->user();

        if (!$user) {
            return (object) [];
        }

        $kodeDosen = self::getKodeDosenFromUser($user);
        $utama = null;
        $migrasi = null;

        if ($kodeDosen !== '' && Schema::hasTable('t_mst_dosen')) {
            $utama = DB::table('t_mst_dosen')
                ->select('*')
                ->where('C_KODE_DOSEN', $kodeDosen)
                ->first();
        }

        if ($kodeDosen !== '' && Schema::hasTable('mig_t_mst_dosen')) {
            $migrasi = DB::table('mig_t_mst_dosen')
                ->select('*')
                ->where('C_KODE_DOSEN', $kodeDosen)
                ->first();
        }

        if (!$utama || !$migrasi) {
            $candidateEmail = strtolower(trim((string) ($user->email ?? '')));
            $candidateName = trim((string) ($user->name ?? ''));

            if (!$utama && Schema::hasTable('t_mst_dosen')) {
                if ($candidateEmail !== '') {
                    $utama = DB::table('t_mst_dosen')
                        ->select('*')
                        ->whereRaw('LOWER(TRIM(EMAIL)) = ?', [$candidateEmail])
                        ->first();
                }

                if (!$utama && $candidateName !== '') {
                    $utama = DB::table('t_mst_dosen')
                        ->select('*')
                        ->whereRaw('LOWER(TRIM(NAMA_DOSEN)) = ?', [strtolower($candidateName)])
                        ->first();
                }
            }

            if (!$migrasi && Schema::hasTable('mig_t_mst_dosen')) {
                if ($candidateEmail !== '') {
                    $migrasi = DB::table('mig_t_mst_dosen')
                        ->select('*')
                        ->whereRaw('LOWER(TRIM(EMAIL)) = ?', [$candidateEmail])
                        ->first();
                }

                if (!$migrasi && $candidateName !== '') {
                    $migrasi = DB::table('mig_t_mst_dosen')
                        ->select('*')
                        ->whereRaw('LOWER(TRIM(NAMA_DOSEN)) = ?', [strtolower($candidateName)])
                        ->first();
                }
            }

            if ($kodeDosen === '') {
                $kodeDosen = trim((string) ($utama->C_KODE_DOSEN ?? $migrasi->C_KODE_DOSEN ?? ''));
            }
        }

        $profile = (object) array_merge((array) $migrasi, (array) $utama);
        $profile->C_KODE_DOSEN = $profile->C_KODE_DOSEN ?? $kodeDosen;
        $profile->exists_t_mst_dosen = $utama ? 1 : 0;
        $profile->exists_mig_t_mst_dosen = $migrasi ? 1 : 0;

        if (!empty($profile->C_KODE_PRODI) && Schema::hasTable('trt_prodi')) {
            $prodi = DB::table('trt_prodi')
                ->select('nama')
                ->where('kode_prodi', $profile->C_KODE_PRODI)
                ->first();
            $profile->nama_prodi = $prodi->nama ?? $profile->C_KODE_PRODI;
        } else {
            $profile->nama_prodi = $profile->C_KODE_PRODI ?? '';
        }

        return $profile;
    }

    public static function getCurrentDosenProfileMissingFields($user = null)
    {
        $profile = self::getCurrentDosenProfileByAuthUser($user);

        $requiredFields = [
            'C_KODE_PRODI' => 'Program Studi',
            'JENIS_KELAMIN' => 'Jenis Kelamin',
            'NO_HP' => 'No. HP',
            'EMAIL' => 'Email',
            'website' => 'Pangkat',
            'jabatan_fungsional' => 'Jabatan Fungsional',
        ];

        $missing = [];

        foreach ($requiredFields as $field => $label) {
            $value = trim((string) ($profile->$field ?? ''));
            if ($value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
                $missing[] = $label;
            }
        }

        return $missing;
    }

    public static function shouldShowCurrentDosenProfilePopup($user = null)
    {
        return count(self::getCurrentDosenProfileMissingFields($user)) > 0;
    }

    public static function getCurrentMahasiswaContactByAuthUser($user = null)
    {
        $user = $user ?: auth()->user();

        if (!$user) {
            return (object) [];
        }

        $nim = trim((string) ($user->name ?? ''));
        $mahasiswa = null;
        $kontak = null;

        if ($nim !== '' && Schema::hasTable('t_mst_mahasiswa')) {
            $mahasiswa = DB::table('t_mst_mahasiswa')
                ->select('C_NPM', 'NAMA_MAHASISWA', 'C_KODE_PRODI', 'D_FOTO_MAHASISWA')
                ->where('C_NPM', $nim)
                ->first();
        }

        if ($nim !== '' && Schema::hasTable('trt_kontak_mahasiswa')) {
            $kontak = DB::table('trt_kontak_mahasiswa')
                ->select('*')
                ->where('C_NPM', $nim)
                ->first();
        }

        $data = (object) array_merge((array) $mahasiswa, (array) $kontak);
        $data->C_NPM = $data->C_NPM ?? $nim;
        $data->NAMA_MAHASISWA = $data->NAMA_MAHASISWA ?? self::getNamaMhs($nim);

        return $data;
    }

    public static function getCurrentMahasiswaContactMissingFields($user = null)
    {
        $kontak = self::getCurrentMahasiswaContactByAuthUser($user);
        $missing = [];
        $noWa = trim((string) ($kontak->no_wa ?? ''));

        if ($noWa === '') {
            $missing[] = 'Nomor WhatsApp';
        }

        return $missing;
    }

    public static function shouldShowCurrentMahasiswaContactPopup($user = null)
    {
        if ((int) session('login_as_source_user_level') === 5 && !empty(session('login_as_source_user_id'))) {
            return false;
        }

        return count(self::getCurrentMahasiswaContactMissingFields($user)) > 0;
    }

    public static function mahasiswaPhotoUrl($photo, $jenisKelamin = null)
    {
        $photo = trim((string) $photo);

        if (preg_match('/\Amahasiswa\/[a-zA-Z0-9._-]+\.(?:jpe?g|png|webp)\z/i', $photo)
            && Storage::disk('public')->exists($photo)) {
            return asset('storage/' . $photo);
        }

        if (self::isSafeOfficialImageName($photo) && is_file(public_path('gambar/' . $photo))) {
            return asset('gambar/' . $photo);
        }

        $jenisKelamin = strtoupper(trim((string) $jenisKelamin));

        if (in_array($jenisKelamin, ['P', 'WANITA'], true)) {
            return asset('images/defaults/student-female.png');
        }

        return asset('images/defaults/student-male.png');
    }

    public static function dosenPhotoUrl($photo)
    {
        $photo = trim((string) $photo);

        if (preg_match('/\Adosen\/[a-zA-Z0-9._-]+\.(?:jpe?g|png|webp)\z/i', $photo)
            && Storage::disk('public')->exists($photo)) {
            return asset('storage/' . $photo);
        }

        if (self::isSafeOfficialImageName($photo) && is_file(public_path('gambar/' . $photo))) {
            return asset('gambar/' . $photo);
        }

        return asset('gambar/no_image.jpg');
    }

    private static function normalizeNamaOrang($nama)
    {
        $nama = strtolower(trim((string) $nama));

        if ($nama === '') {
            return '';
        }

        if (strpos($nama, ',') !== false) {
            $nama = trim(strstr($nama, ',', true));
        }

        $prefixes = [
            'prof.',
            'prof ',
            'dr.',
            'dr ',
            'drs.',
            'drs ',
            'dra.',
            'dra ',
            'ir.',
            'ir ',
            'h.',
            'h ',
            'hj.',
            'hj ',
        ];

        $trimmed = true;
        while ($trimmed) {
            $trimmed = false;
            foreach ($prefixes as $prefix) {
                if (strpos($nama, $prefix) === 0) {
                    $nama = trim(substr($nama, strlen($prefix)));
                    $trimmed = true;
                }
            }
        }

        $nama = preg_replace('/[^a-z0-9\s]/', ' ', $nama);
        $nama = preg_replace('/\s+/', ' ', $nama);

        return trim($nama);
    }

    // fungsi implode
    public static function gabungBidang($id)
    {
        $kepada = '';
        if ($id != '') {
            foreach ($id as $v) {
                if ($v != '') {
                    $option = $v;
                    if ($kepada == null) {
                        $kepada = $option;
                    } else {
                        $kepada = $kepada . ',' . $option;
                    }
                }
            }
        }
        return isset($kepada) ? $kepada : "";
    }

    // fungsi implode
    public static function getHari($tgl)
    {
        $daftar_hari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        $tgl = str_replace('-', '/', $tgl);
        $namahari = date('l', strtotime($tgl));

        return $daftar_hari[$namahari];
    }

    public static function rekap($tglm, $tgls, $asal, $tujuan, $sifat)
    {
        if ($asal == '1') {
            if ($tujuan == 'all') {
                $Model = DB::table('surat_masuk')->select('id')->where('wilayah', '<>', '4')->where('tgl_masuk', '>=', $tglm)->where('tgl_masuk', '<=', $tgls)->get();
            } else {
                $Model = DB::table('surat_masuk')->select('id')->where('wilayah', '<>', '4')->where('sifat_surat', $sifat)->where('tgl_masuk', '>=', $tglm)->where('tgl_masuk', '<=', $tgls)->get();
            }
        } elseif ($asal == '2') {
            if ($sifat == '') {
                $Model = DB::table('surat_masuk')->select('id')->where('wilayah', '<>', '4')->where('tujuan', $tujuan)->where('tgl_masuk', '>=', $tglm)->where('tgl_masuk', '<=', $tgls)->get();
            } else {
                $Model = DB::table('surat_masuk')->select('id')->where('wilayah', '<>', '4')->where('tujuan', $tujuan)->where('sifat_surat', $sifat)->where('tgl_masuk', '>=', $tglm)->where('tgl_masuk', '<=', $tgls)->get();
            }
        } elseif ($asal == '3') {
            if ($sifat == '') {
                $Model = DB::table('surat_masuk')->select('id')->where('wilayah', '=', '4')->where('tujuan', $tujuan)->where('tgl_masuk', '>=', $tglm)->where('tgl_masuk', '<=', $tgls)->get();
            } else {
                $Model = DB::table('surat_masuk')->select('id')->where('wilayah', '=', '4')->where('tujuan', $tujuan)->where('sifat_surat', $sifat)->where('tgl_masuk', '>=', $tglm)->where('tgl_masuk', '<=', $tgls)->get();
            }
        } elseif ($asal == '4') {
            if ($sifat != '') {
                $data1 = DB::table('surat_masuk')->select('id')->where('wilayah', '<>', '4')->where('sifat_surat', $sifat)->where('tgl_masuk', '>=', $tglm)->where('tgl_masuk', '<=', $tgls)->get()->count();
                $data2 = DB::table('surat_masuk')->select('id')->where('wilayah', '<>', '4')->where('sifat_surat', $sifat)->where('tujuan', 'ja')->where('tgl_masuk', '>=', $tglm)->where('tgl_masuk', '<=', $tgls)->get()->count();
                $data3 = DB::table('surat_masuk')->select('id')->where('wilayah', '<>', '4')->where('sifat_surat', $sifat)->where('tujuan', 'wja')->where('tgl_masuk', '>=', $tglm)->where('tgl_masuk', '<=', $tgls)->get()->count();
                $data4 = DB::table('surat_masuk')->select('id')->where('wilayah', '=', '4')->where('sifat_surat', $sifat)->where('tgl_masuk', '>=', $tglm)->where('tgl_masuk', '<=', $tgls)->get()->count();
            } else {
                $data1 = DB::table('surat_masuk')->select('id')->where('wilayah', '<>', '4')->where('tgl_masuk', '>=', $tglm)->where('tgl_masuk', '<=', $tgls)->get()->count();
                $data2 = DB::table('surat_masuk')->select('id')->where('wilayah', '<>', '4')->where('tujuan', 'ja')->where('tgl_masuk', '>=', $tglm)->where('tgl_masuk', '<=', $tgls)->get()->count();
                $data3 = DB::table('surat_masuk')->select('id')->where('wilayah', '<>', '4')->where('tujuan', 'wja')->where('tgl_masuk', '>=', $tglm)->where('tgl_masuk', '<=', $tgls)->get()->count();
                $data4 = DB::table('surat_masuk')->select('id')->where('wilayah', '=', '4')->where('tgl_masuk', '>=', $tglm)->where('tgl_masuk', '<=', $tgls)->get()->count();
            }
            $data = $data1 + $data2 + $data3 + $data4;
            return $data;
        }
        $data = $Model->count();
        return ($data != 0) ? $data : '';
    }

    public static function cekNosurat($table, $no_surat)
    {
        if ($no_surat != '') {
            $Model = DB::table($table)->select('no_surat')->where('no_surat', '=', $no_surat)->first();
        }
        return isset($Model) ? $Model : "";
    }

    public static function getJabatanFungsionalByNIDN($nidn)
    {
        $v = self::getDosenRecordByKode($nidn);
        if (!isset($v)) {
            return '';
        }

        $jabatan = trim((string) ($v->jabatan_fungsional ?? ''));
        $website = trim((string) ($v->website ?? ''));

        if ($jabatan !== '' && $website !== '') {
            return $jabatan . " / " . $website;
        }

        return $jabatan !== '' ? $jabatan : $website;
    }

    public static function getNomorSkPerMhs($bimbingan_id)
    {
        $v = DB::table('mst_sk_pembimbing')
            ->select('*')
            ->Where('bimbingan_id', $bimbingan_id)
            ->first();
        return isset($v) ? $v->nomor_sk : '';
    }

    public static function getStatusSuratUsulan($nomor)
    {
        $data = DB::table('trt_bimbingan')
            ->join('trt_sk', 'trt_sk.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->select('*')
            ->where('nomor', $nomor)
            ->get();

        $data_sk_selesai = DB::table('trt_bimbingan')
            ->join('trt_sk', 'trt_sk.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->join('mst_sk_pembimbing', 'mst_sk_pembimbing.bimbingan_id', '=', 'trt_sk.bimbingan_id')
            ->select('*')
            ->where('nomor', $nomor)
            ->get();

        $status = "";
        if (count($data) == count($data_sk_selesai)) {
            $status = '<i class="fa fa-check-circle text-success"></i>';
        } else {
            $status = '<i class="fa fa-times-circle text-danger"></i>';
        }

        return $status;
    }

    public static function getStatusSuratUsulanTIMUjianTa($id, $nomor)
    {
        $data = DB::select('SELECT * FROM trt_sk_ujian_ta, trt_reg, trt_bimbingan WHERE trt_sk_ujian_ta.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND  trt_sk_ujian_ta.pendaftaran_id = ? AND trt_sk_ujian_ta.nomor = ?', [$id, $nomor]);

        $data_sk_selesai = DB::select('SELECT * FROM trt_sk_ujian_ta, trt_reg, trt_bimbingan, mst_sk_penugasan WHERE trt_sk_ujian_ta.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND mst_sk_penugasan.bimbingan_id = trt_bimbingan.bimbingan_id AND  trt_sk_ujian_ta.pendaftaran_id = ? AND trt_sk_ujian_ta.nomor = ?', [$id, $nomor]);




        $status = "";
        if (count($data) == count($data_sk_selesai)) {
            $status = '<i class="fa fa-check-circle text-success"></i>';
        } else {
            $status = '<i class="fa fa-times-circle text-danger"></i>';
        }

        return $status;
    }

    public static function getProgramStudiByNim($nim)
    {
        $v = DB::table('t_mst_mahasiswa', 'trt_prodi')
            ->select('*')
            ->join('trt_prodi', 'trt_prodi.kode_prodi', '=', 't_mst_mahasiswa.C_KODE_PRODI')
            ->where('t_mst_mahasiswa.C_NPM', $nim)
            ->first();
        return isset($v) ? $v->nama : '';
    }

    /**
     * Resolve a student's program for documents using the authoritative NIM prefix.
     */
    public static function getProgramStudiByStambuk($nim)
    {
        $stambuk = preg_replace('/\D+/', '', (string) $nim);
        $prefix = substr($stambuk, 0, 3);

        if ($prefix === '130') {
            return 'Teknik Informatika';
        }

        if ($prefix === '131') {
            return 'Sistem Informasi';
        }

        return self::getProgramStudiByNim($nim);
    }

    public static function getProgramStudiByAuthUser($username = null)
    {
        $username = strtolower(trim($username ?? auth()->user()->name ?? ''));

        $map = [
            'proditi' => 'Teknik Informatika',
            'prodisi' => 'Sistem Informasi',
        ];

        return $map[$username] ?? '';
    }

    public static function normalizeProgramStudiName($prodi)
    {
        $prodi = strtolower(trim((string) $prodi));

        $map = [
            'teknik informatika' => 'Teknik Informatika',
            'sistem informasi' => 'Sistem Informasi',
            'proditi' => 'Teknik Informatika',
            'prodisi' => 'Sistem Informasi',
            'ti' => 'Teknik Informatika',
            'si' => 'Sistem Informasi',
        ];

        return $map[$prodi] ?? trim((string) $prodi);
    }

    public static function getKaprodiByProdiAndTanggal($prodi, $tanggalAcuan = null)
    {
        $prodi = self::normalizeProgramStudiName($prodi);
        $tanggalAcuan = self::parseTanggalAcuan($tanggalAcuan) ?? Carbon::today();

        $defaultKaprodi = (object) [
            'nama' => $prodi === 'Teknik Informatika' ? 'Tasrif Hasanuddin, S.T., M.Cs' : 'Herman, S.Kom.,M.Cs., MTA.',
            'nidn' => $prodi === 'Teknik Informatika' ? '0910126901' : '0913038506',
            'prodi' => $prodi,
            'ttd' => $prodi === 'Teknik Informatika' ? 'ttd_kaprodi.png' : 'ttd_kaprodi_si.png',
            'tanggal_menjabat' => null,
            'tanggal_berakhir' => null,
            'email' => null,
            'no_telepon' => null,
        ];

        if ($prodi === '') {
            return $defaultKaprodi;
        }

        if (!Schema::hasTable('mst_periode_jabatan')) {
            return $defaultKaprodi;
        }

        $dataPeriode = DB::table('mst_periode_jabatan')
            ->whereRaw('LOWER(TRIM(prodi)) = ?', [strtolower($prodi)])
            ->get()
            ->sortByDesc(function ($item) {
                $tanggalMulai = self::parseTanggalAcuan($item->tanggal_menjabat);
                return $tanggalMulai ? $tanggalMulai->timestamp : 0;
            })
            ->values();

        $periodeAktif = $dataPeriode->first(function ($item) use ($tanggalAcuan) {
            $tanggalMulai = self::parseTanggalAcuan($item->tanggal_menjabat);
            $tanggalBerakhir = self::parseTanggalAcuan($item->tanggal_berakhir);

            if ($tanggalMulai && $tanggalAcuan->lt($tanggalMulai)) {
                return false;
            }

            if ($tanggalBerakhir && $tanggalAcuan->gt($tanggalBerakhir)) {
                return false;
            }

            return $tanggalMulai || $tanggalBerakhir;
        });

        if (!$periodeAktif) {
            $periodeAktif = $dataPeriode->first(function ($item) use ($tanggalAcuan) {
                $tanggalMulai = self::parseTanggalAcuan($item->tanggal_menjabat);
                return $tanggalMulai && $tanggalMulai->lte($tanggalAcuan);
            }) ?? $dataPeriode->first();
        }

        if (!$periodeAktif) {
            return $defaultKaprodi;
        }

        $dosen = null;

        if (Schema::hasTable('t_mst_dosen')) {
            $dosen = DB::table('t_mst_dosen')
                ->select('C_KODE_DOSEN', 'NAMA_DOSEN')
                ->whereRaw('LOWER(TRIM(NAMA_DOSEN)) = ?', [strtolower(trim($periodeAktif->nama))])
                ->first();
        }

        if (!$dosen && Schema::hasTable('mig_t_mst_dosen')) {
            $dosen = DB::table('mig_t_mst_dosen')
                ->select('C_KODE_DOSEN', 'NAMA_DOSEN')
                ->whereRaw('LOWER(TRIM(NAMA_DOSEN)) = ?', [strtolower(trim($periodeAktif->nama))])
                ->first();
        }

        $periodeAktif->nama = $periodeAktif->nama ?: $defaultKaprodi->nama;
        $periodeAktif->nidn = $dosen->C_KODE_DOSEN ?? $defaultKaprodi->nidn;
        $periodeAktif->ttd = $periodeAktif->ttd ?: $defaultKaprodi->ttd;
        $periodeAktif->prodi = $prodi;

        return $periodeAktif;
    }

    public static function getKaprodiByNimAndTanggal($nim, $tanggalAcuan = null)
    {
        $prodi = self::getProgramStudiByNim($nim);
        return self::getKaprodiByProdiAndTanggal($prodi, $tanggalAcuan);
    }

    public static function getPejabatFakultasByTanggal($jabatan, $tanggalAcuan = null, $kodeFakultas = 'FIKOM')
    {
        $jabatan = trim((string) $jabatan);
        $kodeFakultas = strtoupper(trim((string) $kodeFakultas)) ?: 'FIKOM';
        $tanggalAcuan = self::parseTanggalAcuan($tanggalAcuan) ?? Carbon::today();

        $defaultPejabat = (object) [
            'nama' => '',
            'nip_nidn' => '',
            'email' => '',
            'no_telepon' => '',
            'ttd' => '',
            'jabatan' => $jabatan,
            'kode_fakultas' => $kodeFakultas,
            'nama_fakultas' => '',
            'tanggal_menjabat' => null,
            'tanggal_berakhir' => null,
            'status_aktif' => 0,
        ];

        if ($jabatan === '') {
            return $defaultPejabat;
        }

        if (!Schema::hasTable('mst_periode_jabatan_fakultas')) {
            return $defaultPejabat;
        }

        $dataPeriode = DB::table('mst_periode_jabatan_fakultas')
            ->whereRaw('UPPER(TRIM(kode_fakultas)) = ?', [$kodeFakultas])
            ->whereRaw('LOWER(TRIM(jabatan)) = ?', [strtolower($jabatan)])
            ->get()
            ->sortByDesc(function ($item) {
                $tanggalMulai = self::parseTanggalAcuan($item->tanggal_menjabat);
                return $tanggalMulai ? $tanggalMulai->timestamp : 0;
            })
            ->values();

        $periodeAktif = $dataPeriode->first(function ($item) use ($tanggalAcuan) {
            $tanggalMulai = self::parseTanggalAcuan($item->tanggal_menjabat);
            $tanggalBerakhir = self::parseTanggalAcuan($item->tanggal_berakhir);

            if ($tanggalMulai && $tanggalAcuan->lt($tanggalMulai)) {
                return false;
            }

            if ($tanggalBerakhir && $tanggalAcuan->gt($tanggalBerakhir)) {
                return false;
            }

            return $tanggalMulai || $tanggalBerakhir;
        });

        if (!$periodeAktif) {
            $periodeAktif = $dataPeriode->first(function ($item) use ($tanggalAcuan) {
                $tanggalMulai = self::parseTanggalAcuan($item->tanggal_menjabat);
                return $tanggalMulai && $tanggalMulai->lte($tanggalAcuan);
            }) ?? $dataPeriode->first();
        }

        return $periodeAktif ?: $defaultPejabat;
    }

    public static function getDekanByTanggal($tanggalAcuan = null, $kodeFakultas = 'FIKOM')
    {
        return self::getPejabatFakultasByTanggal('Dekan', $tanggalAcuan, $kodeFakultas);
    }

    private static function parseTanggalAcuan($tanggal)
    {
        if ($tanggal instanceof \DateTimeInterface) {
            return Carbon::instance($tanggal)->startOfDay();
        }

        $tanggal = trim((string) $tanggal);

        if ($tanggal === '') {
            return null;
        }

        if (strpos($tanggal, ',') !== false) {
            $tanggal = trim(substr($tanggal, strrpos($tanggal, ',') + 1));
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $tanggal)) {
            return Carbon::parse(substr($tanggal, 0, 10))->startOfDay();
        }

        $tanggalIndonesia = str_ireplace(
            ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            $tanggal
        );

        foreach (['d-m-Y', 'd/m/Y', 'd-m-y', 'd/m/y', 'd M Y', 'd F Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $tanggalIndonesia)->startOfDay();
            } catch (\Exception $e) {
            }
        }

        try {
            return Carbon::parse($tanggalIndonesia)->startOfDay();
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getNomorSkPenugasanPerMhs($bimbingan_id)
    {
        $v = DB::table('mst_sk_penugasan')
            ->select('*')
            ->Where('bimbingan_id', $bimbingan_id)
            ->first();
        return isset($v) ? $v->nomor_sk : '';
    }

    public static function tgl_indo_lengkap($tanggal)
    {
        $bulan = array(
            1 =>   'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );
        $pecahkan = explode('-', $tanggal);

        // variabel pecahkan 0 = tanggal
        // variabel pecahkan 1 = bulan
        // variabel pecahkan 2 = tahun
        return $pecahkan[2] . ' ' . $bulan[(int) $pecahkan[1]] . ' ' . $pecahkan[0];
    }

    public static function getJudulTugasAkhirByNim($nim)
    {
        $v = DB::table('trt_bimbingan')
            ->select('*')
            ->Where('C_NPM', $nim)
            ->first();
        return isset($v) ? $v->judul : '';
    }

    public static function getSaranByNidnAndRegId($nidn, $reg_id)
    {
        $v = DB::table('trt_hasil')
            ->select('*')
            ->Where('nidn', $nidn)
            ->Where('reg_id', $reg_id)
            ->first();
        return isset($v) ? $v->saran : '-';
    }

    public static function getTotalNilaByNidnAndRegId($nidn, $reg_id)
    {
        $v = DB::table('trt_hasil')
            ->select('*')
            ->Where('nidn', $nidn)
            ->Where('reg_id', $reg_id)
            ->first();


        return isset($v) ? ($v->nilai_1 + $v->nilai_2  + $v->nilai_3  + $v->nilai_4 + $v->nilai_5) : 0;
    }

    public static function getStatusBimbinganByNim($nim)
    {
        $v = DB::table('trt_bimbingan')
            ->select('*')
            ->Where('C_NPM', $nim)
            ->first();
        return isset($v) ? $v->status_bimbingan : '999';
    }

    public static function getJumlahTrtHasil($reg_id)
    {
        $v = DB::table('trt_hasil')
            ->select('*')
            ->Where('reg_id', $reg_id)
            ->get();

        return isset($v) ? count($v) : '';
    }

    public static function getPenilaiWajibByRegId($reg_id)
    {
        $reg = DB::table('trt_reg as rg')
            ->join('trt_bimbingan as tb', 'tb.bimbingan_id', '=', 'rg.bimbingan_id')
            ->leftJoin('trt_penguji as tp', function ($join) {
                $join->on('tp.C_NPM', '=', 'tb.C_NPM')
                    ->on('tp.tipe_ujian', '=', 'rg.status');
            })
            ->select(
                'rg.status',
                'tb.pembimbing_I_id',
                'tb.pembimbing_II_id',
                'tp.penguji_I_id',
                'tp.penguji_II_id',
                'tp.penguji_III_id',
                'tp.ketua_sidang_id'
            )
            ->where('rg.reg_id', $reg_id)
            ->first();

        if (!$reg) {
            return [];
        }

        $penilai = [
            trim((string) ($reg->pembimbing_I_id ?? '')),
            trim((string) ($reg->pembimbing_II_id ?? '')),
            trim((string) ($reg->penguji_I_id ?? '')),
            trim((string) ($reg->penguji_II_id ?? '')),
            trim((string) ($reg->penguji_III_id ?? '')),
            trim((string) ($reg->ketua_sidang_id ?? '')),
        ];

        $penilai = array_filter($penilai, function ($value) {
            return $value !== '' && $value !== '--';
        });

        return array_values(array_unique($penilai));
    }

    public static function getJumlahPenilaiSudahIsiByRegId($reg_id)
    {
        $penilaiWajib = self::getPenilaiWajibByRegId($reg_id);

        if (empty($penilaiWajib)) {
            return 0;
        }

        return (int) DB::table('trt_hasil')
            ->where('reg_id', $reg_id)
            ->whereIn('nidn', $penilaiWajib)
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
            ->count('nidn');
    }

    public static function isPenilaianLengkapByRegId($reg_id)
    {
        $penilaiWajib = self::getPenilaiWajibByRegId($reg_id);
        $jumlahWajib = count($penilaiWajib);

        if ($jumlahWajib === 0) {
            return false;
        }

        $jumlahSudahIsi = self::getJumlahPenilaiSudahIsiByRegId($reg_id);
        return $jumlahSudahIsi >= $jumlahWajib;
    }

    public static function getNomorSkByNIM($nim)
    {
        $v = DB::table('trt_bimbingan')
            ->select("*")
            ->where('trt_bimbingan.bimbingan_id', 'mst_sk_pembimbing.bimbingan_id')
            ->where('trt_bimbingan.C_NPM', $nim)
            ->get();
        return isset($v) ? $v->nomor_sk : '-';
    }

    public static function getRuanganByNim($nim)
    {
        $v = DB::table('trt_jadwal_ujian_per_mhs')
            ->join('trt_jadwal_ujian', 'trt_jadwal_ujian.id', '=', 'trt_jadwal_ujian_per_mhs.jadwal_ujian')
            ->select("*")
            ->where('trt_jadwal_ujian_per_mhs.C_NPM', $nim)
            ->where('trt_jadwal_ujian.status', 0)
            ->first();
        return $v;

        if ($v == null) {
            return '-';
        } else {
            return $v->ruangan;
        }
    }

    public static function getRuanganUjianTAByNim($nim)
    {
        $v = DB::table('trt_jadwal_ujian_per_mhs')
            ->join('trt_jadwal_ujian', 'trt_jadwal_ujian.id', '=', 'trt_jadwal_ujian_per_mhs.jadwal_ujian')
            ->select("*")
            ->where('trt_jadwal_ujian_per_mhs.C_NPM', $nim)
            ->where('trt_jadwal_ujian.status', 2)
            ->first();
        return $v;

        if ($v == null) {
            return '-';
        } else {
            return $v->ruangan;
        }
    }

    public static function getTotalUsulanJudulFromDosen($kode_dosen)
    {
        $v = DB::table('trt_usulan_judul')
            ->select("*")
            ->where('trt_usulan_judul.KODE_DOSEN', $kode_dosen)
            ->get();
        return isset($v) ? $v : '0';
    }

    public static function get5Pengumuman()
    {
        $v = DB::table('mst_pengumuman')
            ->select("*")
            ->limit(5)
            ->orderBy('last_update', 'desc')
            ->get();
        return isset($v) ? $v : 'Tidak Ada Pengumuman';
    }

    public static function getSemesterAkademik($tanggal = null)
    {
        $date = $tanggal === null ? Carbon::now()->startOfDay() : self::parseTanggalAcuan($tanggal);
        $date = $date ?: Carbon::now()->startOfDay();
        $month = (int) $date->month;

        if ($month >= 9) {
            $semester = 'Ganjil';
            $tahunMulai = (int) $date->year;
            $startDate = Carbon::create($tahunMulai, 9, 1, 0, 0, 0);
            $endDate = Carbon::create($tahunMulai + 1, 2, 1, 0, 0, 0)->endOfMonth()->endOfDay();
        } elseif ($month <= 2) {
            $semester = 'Ganjil';
            $tahunMulai = (int) $date->year - 1;
            $startDate = Carbon::create($tahunMulai, 9, 1, 0, 0, 0);
            $endDate = Carbon::create($tahunMulai + 1, 2, 1, 0, 0, 0)->endOfMonth()->endOfDay();
        } else {
            $semester = 'Genap';
            $tahunMulai = (int) $date->year - 1;
            $startDate = Carbon::create($date->year, 3, 1, 0, 0, 0);
            $endDate = Carbon::create($date->year, 8, 31, 23, 59, 59);
        }

        $tahunAkademik = $tahunMulai . '/' . ($tahunMulai + 1);

        return (object) [
            'semester' => $semester,
            'tahun_akademik' => $tahunAkademik,
            'label' => 'Periode Semester ' . $tahunAkademik . ' ' . $semester,
            'start' => $startDate,
            'end' => $endDate,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ];
    }

    public static function getCurrentSemesterDateRange($tanggal = null)
    {
        return self::getSemesterAkademik($tanggal);
    }

    public static function getStatusBimbinganByStatus($status, $isPeriode = false)
    {
        $query = DB::table('trt_bimbingan')
            ->select("*")
            ->where('trt_bimbingan.status_bimbingan', $status);

        if ($isPeriode) {
            $semesterRange = self::getCurrentSemesterDateRange();
            $query->whereBetween('trt_bimbingan.updated_at', [$semesterRange->start, $semesterRange->end]);
        }

        $v = $query->get();

        return isset($v) ? $v : '-';
    }


    public static function getStatusBimbinganByStatusTi($status, $isPeriode = false)
    {
        $query = DB::table('trt_bimbingan')
            ->select("*")
            ->where('trt_bimbingan.status_bimbingan', $status)
            ->where('trt_bimbingan.C_NPM', 'LIKE', '130%');

        if ($isPeriode) {
            $semesterRange = self::getCurrentSemesterDateRange();
            $query->whereBetween('trt_bimbingan.updated_at', [$semesterRange->start, $semesterRange->end]);
        }

        $v = $query->get();

        return isset($v) ? $v : '-';
    }

    public static function getStatusBimbinganByStatusSi($status, $isPeriode = false)
    {
        $query = DB::table('trt_bimbingan')
            ->select("*")
            ->where('trt_bimbingan.status_bimbingan', $status)
            ->where('trt_bimbingan.C_NPM', 'LIKE', '131%');

        if ($isPeriode) {
            $semesterRange = self::getCurrentSemesterDateRange();
            $query->whereBetween('trt_bimbingan.updated_at', [$semesterRange->start, $semesterRange->end]);
        }

        $v = $query->get();

        return isset($v) ? $v : '-';
    }

    public static function getStatusBimbinganSummaryByDosen($kode_dosen)
    {
        $baseQuery = DB::table('trt_bimbingan')
            ->where(function ($query) use ($kode_dosen) {
                $query->where('trt_bimbingan.pembimbing_I_id', $kode_dosen)
                    ->orWhere('trt_bimbingan.pembimbing_II_id', $kode_dosen);
            });
        $aktifQuery = clone $baseQuery;

        $data = (object) [
            "y" => "",
            "PP" => (clone $aktifQuery)->where('trt_bimbingan.status_bimbingan', 0)->count(),
            "PUM" => (clone $aktifQuery)->where('trt_bimbingan.status_bimbingan', 2)->count(),
            "L" => (clone $baseQuery)->where('trt_bimbingan.status_bimbingan', 3)->count(),
        ];

        return $data;
    }

    public static function getRingkasanPeranPengujiAktifByDosen($kodeDosen)
    {
        $kodeDosen = trim((string) $kodeDosen);

        if ($kodeDosen === '') {
            return [
                'proposal' => 0,
                'ujian_ta' => 0,
                'ketua_sidang_proposal' => 0,
                'ketua_sidang_ujian_ta' => 0,
                'ketua_sidang' => 0,
            ];
        }

        $base = DB::table('trt_penguji as tp')
            ->join('trt_reg as rg', function ($join) {
                $join->on('rg.C_NPM', '=', 'tp.C_NPM')
                    ->on('rg.status', '=', 'tp.tipe_ujian');
            })
            ->join('trt_bimbingan as tb', 'tb.C_NPM', '=', 'tp.C_NPM')
            ->whereIn('tp.tipe_ujian', [0, 2]);

        $proposal = (clone $base)
            ->where('tp.tipe_ujian', 0)
            ->where('tb.status_bimbingan', 0)
            ->where(function ($query) use ($kodeDosen) {
                $query->where('tp.penguji_I_id', $kodeDosen)
                    ->orWhere('tp.penguji_II_id', $kodeDosen)
                    ->orWhere('tp.penguji_III_id', $kodeDosen);
            })
            ->distinct()
            ->count('tp.C_NPM');

        $ujianTa = (clone $base)
            ->where('tp.tipe_ujian', 2)
            ->where('tb.status_bimbingan', 2)
            ->where(function ($query) use ($kodeDosen) {
                $query->where('tp.penguji_I_id', $kodeDosen)
                    ->orWhere('tp.penguji_II_id', $kodeDosen)
                    ->orWhere('tp.penguji_III_id', $kodeDosen);
            })
            ->distinct()
            ->count('tp.C_NPM');

        $ketuaSidang = (clone $base)
            ->whereIn('tp.tipe_ujian', [0, 2])
            ->whereIn('tb.status_bimbingan', [0, 2])
            ->where('tp.ketua_sidang_id', $kodeDosen)
            ->distinct()
            ->count('tp.C_NPM');

        $ketuaSidangProposal = (clone $base)
            ->where('tp.tipe_ujian', 0)
            ->where('tb.status_bimbingan', 0)
            ->where('tp.ketua_sidang_id', $kodeDosen)
            ->distinct()
            ->count('tp.C_NPM');

        $ketuaSidangUjianTa = (clone $base)
            ->where('tp.tipe_ujian', 2)
            ->where('tb.status_bimbingan', 2)
            ->where('tp.ketua_sidang_id', $kodeDosen)
            ->distinct()
            ->count('tp.C_NPM');

        return [
            'proposal' => (int) $proposal,
            'ujian_ta' => (int) $ujianTa,
            'ketua_sidang_proposal' => (int) $ketuaSidangProposal,
            'ketua_sidang_ujian_ta' => (int) $ketuaSidangUjianTa,
            'ketua_sidang' => (int) $ketuaSidang,
        ];
    }

    public static function getKomposisiPeranPembimbingAktifByDosen($kodeDosen)
    {
        $kodeDosen = trim((string) $kodeDosen);

        if ($kodeDosen === '') {
            return [
                'proposal_utama' => 0,
                'proposal_pendamping' => 0,
                'ujian_utama' => 0,
                'ujian_pendamping' => 0,
            ];
        }

        $base = DB::table('trt_bimbingan')
            ->whereIn('trt_bimbingan.status_bimbingan', [0, 2]);

        return [
            'proposal_utama' => (int) (clone $base)
                ->where('trt_bimbingan.status_bimbingan', 0)
                ->where('trt_bimbingan.pembimbing_I_id', $kodeDosen)
                ->count(),
            'proposal_pendamping' => (int) (clone $base)
                ->where('trt_bimbingan.status_bimbingan', 0)
                ->where('trt_bimbingan.pembimbing_II_id', $kodeDosen)
                ->count(),
            'ujian_utama' => (int) (clone $base)
                ->where('trt_bimbingan.status_bimbingan', 2)
                ->where('trt_bimbingan.pembimbing_I_id', $kodeDosen)
                ->count(),
            'ujian_pendamping' => (int) (clone $base)
                ->where('trt_bimbingan.status_bimbingan', 2)
                ->where('trt_bimbingan.pembimbing_II_id', $kodeDosen)
                ->count(),
        ];
    }

    public static function getStatusBimbinganByDosenAndStatus($kode_dosen, $status)
    {
        return DB::table('trt_bimbingan')
            ->join('t_mst_mahasiswa', 't_mst_mahasiswa.C_NPM', '=', 'trt_bimbingan.C_NPM')
            ->leftJoin('t_mst_dosen as dosen_utama', 'dosen_utama.C_KODE_DOSEN', '=', 'trt_bimbingan.pembimbing_I_id')
            ->leftJoin('t_mst_dosen as dosen_pendamping', 'dosen_pendamping.C_KODE_DOSEN', '=', 'trt_bimbingan.pembimbing_II_id')
            ->leftJoin(DB::raw('(SELECT bimbingan_id, MAX(created_at) AS tgl_sk_penetapan FROM mst_sk_pembimbing GROUP BY bimbingan_id) sk_pembimbing'), 'sk_pembimbing.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->select(
                't_mst_mahasiswa.C_NPM',
                't_mst_mahasiswa.NAMA_MAHASISWA',
                'sk_pembimbing.tgl_sk_penetapan',
                'trt_bimbingan.pembimbing_I_id',
                'trt_bimbingan.pembimbing_II_id',
                'dosen_utama.NAMA_DOSEN as pembimbing_utama',
                'dosen_pendamping.NAMA_DOSEN as pembimbing_pendamping',
                'trt_bimbingan.judul',
                'trt_bimbingan.status_bimbingan'
            )
            ->where('trt_bimbingan.status_bimbingan', $status)
            ->where(function ($query) use ($kode_dosen) {
                $query->where('trt_bimbingan.pembimbing_I_id', $kode_dosen)
                    ->orWhere('trt_bimbingan.pembimbing_II_id', $kode_dosen);
            })
            ->orderBy('t_mst_mahasiswa.C_NPM', 'asc')
            ->distinct()
            ->get();
    }

    public static function getLamaBimbinganSejakTanggal($tanggal)
    {
        if (empty($tanggal)) {
            return '-';
        }

        $mulai = Carbon::parse($tanggal);
        $sekarang = Carbon::now();

        if ($mulai->greaterThan($sekarang)) {
            return '0 hari';
        }

        $selisih = $mulai->diff($sekarang);
        $bulan = ($selisih->y * 12) + $selisih->m;
        $hari = $selisih->d;

        $hasil = [];
        if ($bulan > 0) {
            $hasil[] = $bulan . ' bulan';
        }
        if ($hari > 0) {
            $hasil[] = $hari . ' hari';
        }

        return !empty($hasil) ? implode(' ', $hasil) : '0 hari';
    }

    public static function getRataLamaProsesBimbinganDosenPerAngkatan($kodeDosen)
    {
        $kodeDosen = trim((string) $kodeDosen);
        if ($kodeDosen === '') {
            return [];
        }

        $rows = DB::select(
            "
            SELECT
                src.angkatan_tahun,
                ROUND(AVG(CASE WHEN src.kode_prodi_nim = '130' THEN src.lama_hari END) / 30, 1) AS ti_bulan,
                ROUND(AVG(CASE WHEN src.kode_prodi_nim = '131' THEN src.lama_hari END) / 30, 1) AS si_bulan,
                SUM(CASE WHEN src.kode_prodi_nim = '130' THEN 1 ELSE 0 END) AS total_ti,
                SUM(CASE WHEN src.kode_prodi_nim = '131' THEN 1 ELSE 0 END) AS total_si
            FROM (
                SELECT
                    tb.C_NPM,
                    LEFT(tb.C_NPM, 3) AS kode_prodi_nim,
                    SUBSTRING(tb.C_NPM, 4, 4) AS angkatan_tahun,
                    DATEDIFF(
                        CASE
                            WHEN tb.status_bimbingan = 3 THEN COALESCE(DATE(tb.last_update), DATE(tb.updated_at), CURDATE())
                            ELSE CURDATE()
                        END,
                        COALESCE(sk.tgl_sk_penetapan, DATE(tb.created_at), DATE(tb.updated_at), CURDATE())
                    ) AS lama_hari
                FROM trt_bimbingan tb
                LEFT JOIN (
                    SELECT bimbingan_id, DATE(MIN(created_at)) AS tgl_sk_penetapan
                    FROM mst_sk_pembimbing
                    GROUP BY bimbingan_id
                ) sk ON sk.bimbingan_id = tb.bimbingan_id
                WHERE (tb.pembimbing_I_id = ? OR tb.pembimbing_II_id = ?)
                  AND LEFT(tb.C_NPM, 3) IN ('130', '131')
            ) src
            WHERE src.angkatan_tahun REGEXP '^[0-9]{4}$'
              AND src.lama_hari >= 0
            GROUP BY src.angkatan_tahun
            ORDER BY src.angkatan_tahun ASC
            ",
            [$kodeDosen, $kodeDosen]
        );

        return collect($rows)->map(function ($row) {
            $tiBulan = (float) ($row->ti_bulan ?? 0);
            $siBulan = (float) ($row->si_bulan ?? 0);

            return [
                'y' => (string) ($row->angkatan_tahun ?? '-'),
                'ti_bulan' => $tiBulan,
                'si_bulan' => $siBulan,
                'ti_label' => number_format($tiBulan, 1) . ' bulan',
                'si_label' => number_format($siBulan, 1) . ' bulan',
                'total_ti' => (int) ($row->total_ti ?? 0),
                'total_si' => (int) ($row->total_si ?? 0),
            ];
        })->values()->all();
    }

    public static function getStatusBimbinganSummaryProdiByUsername($username = null)
    {
        try {
            $nimLike = self::getScopeTaNimLikeByUsername($username);

            $baseQuery = DB::table('trt_bimbingan');
            if ($nimLike !== '%') {
                $baseQuery->where('trt_bimbingan.C_NPM', 'LIKE', $nimLike);
            }
            $aktifQuery = clone $baseQuery;

            return (object) [
                'y' => '',
                'PP' => (clone $aktifQuery)->where('trt_bimbingan.status_bimbingan', 0)->count(),
                'PUM' => (clone $aktifQuery)->where('trt_bimbingan.status_bimbingan', 2)->count(),
                'L' => (clone $baseQuery)->where('trt_bimbingan.status_bimbingan', 3)->count(),
            ];
        } catch (\Throwable $e) {
            \Log::warning('getStatusBimbinganSummaryProdiByUsername error', [
                'username' => $username,
                'message' => $e->getMessage(),
            ]);

            return (object) [
                'y' => '',
                'PP' => 0,
                'PUM' => 0,
                'L' => 0,
            ];
        }
    }

    public static function getRataLamaProsesBimbinganProdiPerAngkatanByUsername($username = null)
    {
        try {
            $rows = DB::select(
                "
            SELECT
                src.angkatan_tahun,
                ROUND(AVG(CASE WHEN src.kode_prodi_nim = '130' THEN src.lama_hari END) / 30, 1) AS ti_bulan,
                ROUND(AVG(CASE WHEN src.kode_prodi_nim = '131' THEN src.lama_hari END) / 30, 1) AS si_bulan,
                SUM(CASE WHEN src.kode_prodi_nim = '130' THEN 1 ELSE 0 END) AS total_ti,
                SUM(CASE WHEN src.kode_prodi_nim = '131' THEN 1 ELSE 0 END) AS total_si
            FROM (
                SELECT
                    tb.C_NPM,
                    LEFT(tb.C_NPM, 3) AS kode_prodi_nim,
                    SUBSTRING(tb.C_NPM, 4, 4) AS angkatan_tahun,
                    DATEDIFF(
                        CASE
                            WHEN tb.status_bimbingan = 3 THEN COALESCE(DATE(tb.last_update), DATE(tb.updated_at), CURDATE())
                            ELSE CURDATE()
                        END,
                        COALESCE(sk.tgl_sk_penetapan, DATE(tb.created_at), DATE(tb.updated_at), CURDATE())
                    ) AS lama_hari
                FROM trt_bimbingan tb
                LEFT JOIN (
                    SELECT bimbingan_id, DATE(MIN(created_at)) AS tgl_sk_penetapan
                    FROM mst_sk_pembimbing
                    GROUP BY bimbingan_id
                ) sk ON sk.bimbingan_id = tb.bimbingan_id
                WHERE LEFT(tb.C_NPM, 3) IN ('130', '131')
                  AND tb.C_NPM LIKE ?
            ) src
            WHERE src.angkatan_tahun REGEXP '^[0-9]{4}$'
              AND src.lama_hari >= 0
            GROUP BY src.angkatan_tahun
            ORDER BY src.angkatan_tahun ASC
            ",
                [self::getScopeTaNimLikeByUsername($username)]
            );

            return collect($rows)->map(function ($row) {
                $tiBulan = (float) ($row->ti_bulan ?? 0);
                $siBulan = (float) ($row->si_bulan ?? 0);

                return [
                    'y' => (string) ($row->angkatan_tahun ?? '-'),
                    'ti_bulan' => $tiBulan,
                    'si_bulan' => $siBulan,
                    'ti_label' => number_format($tiBulan, 1) . ' bulan',
                    'si_label' => number_format($siBulan, 1) . ' bulan',
                    'total_ti' => (int) ($row->total_ti ?? 0),
                    'total_si' => (int) ($row->total_si ?? 0),
                ];
            })->values()->all();
        } catch (\Throwable $e) {
            \Log::warning('getRataLamaProsesBimbinganProdiPerAngkatanByUsername error', [
                'username' => $username,
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    public static function getScopeTaNimLikeByUsername($username = null)
    {
        $username = strtolower(trim((string) ($username ?: auth()->user()->name ?? '')));

        if (in_array($username, ['proditi', 'akademikproditi', 'teknik informatika', 'ti'], true)) {
            return '130%';
        }

        if (in_array($username, ['prodisi', 'prodinyalilis', 'akademikprodisi', 'sistem informasi', 'si'], true)) {
            return '131%';
        }

        return '%';
    }

    public static function getScopeTaLulusanPeriodeChartByAuthUser()
    {
        return self::getScopeTaLulusanPeriodeChartByUsername();
    }

    public static function getScopeTaLulusanPeriodeChartByUsername($username = null)
    {
        $nimLike = self::getScopeTaNimLikeByUsername($username);
        $chart = [];

        try {
            $rows = DB::table('trt_bimbingan')
                ->select('C_NPM', 'last_update', 'updated_at', 'created_at')
                ->where('status_bimbingan', 3)
                ->where('C_NPM', 'LIKE', $nimLike)
                ->orderBy('bimbingan_id', 'desc')
                ->get();

            $lulusanNims = [];
            $periodeCounts = [];

            foreach ($rows as $row) {
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
                        $label = self::getSemesterAkademik($tanggalAcuan)->tahun_akademik;
                    } catch (\Exception $e) {
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
                $chart[] = [
                    'y' => $label,
                    'total' => (int) $total,
                ];
            }
        } catch (\Exception $e) {
            return [];
        }

        return $chart;
    }

    public static function getScopeTaLulusanBidangChartByAuthUser()
    {
        return self::getScopeTaLulusanBidangChartByUsername();
    }

    public static function getScopeTaLulusanBidangChartByUsername($username = null)
    {
        $nimLike = self::getScopeTaNimLikeByUsername($username);
        $chart = [];

        try {
            $lulusanRows = DB::table('trt_bimbingan')
                ->select('C_NPM')
                ->where('status_bimbingan', 3)
                ->where('C_NPM', 'LIKE', $nimLike)
                ->orderBy('bimbingan_id', 'desc')
                ->get();

            $lulusanNims = [];
            foreach ($lulusanRows as $row) {
                if (!in_array($row->C_NPM, $lulusanNims)) {
                    $lulusanNims[] = $row->C_NPM;
                }
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
                $chart[] = [
                    'y' => $label,
                    'total' => (int) $total,
                ];
            }
        } catch (\Exception $e) {
            return [];
        }

        return $chart;
    }


    public static function getStatusAkunPerMahasiswa($nim)
    {
        $data = DB::table('users')
            ->select('*')
            ->where('users.name', $nim)
            ->first();
        $status = "";
        if ($data == null || $data == '') {
            $status = '<i class="fa fa-times-circle text-danger"></i>';
        } else {
            $status = '<i class="fa fa-check-circle text-success"></i>';
        }
        return $status;
    }

    public static function getPengujiByNim($nim)
    {
        $v = DB::table('trt_penguji')
            ->select("*")
            ->where('trt_penguji.C_NPM', $nim)
            ->get();
        return isset($v) ? $v : '-';
    }

    public static function getMahasiswaByPenguji($kode_dosen)
    {
        $v = DB::table('trt_penguji')
            ->select("trt_penguji.C_NPM")
            ->where('trt_penguji.penguji_I_id', $kode_dosen)
            ->orWhere('trt_penguji.penguji_II_id', $kode_dosen)
            ->orWhere('trt_penguji.penguji_III_id', $kode_dosen)
            ->distinct()
            ->get();
        return isset($v) ? $v : '-';
    }

    public static function getNomorSkPerMhsFromTrtPenguji($nim)
    {
        $v = DB::table('trt_penguji')
            ->select("trt_penguji.nomor_sk")
            ->where('trt_penguji.C_NPM', $nim)
            ->where('trt_penguji.tipe_ujian', 0)
            ->first();
        return isset($v) ? $v->nomor_sk : '';
    }

    public static function getStatusPenilaianPerDosen($nim, $reg_id)
    {
        $data = DB::table('trt_hasil')
            ->select('*')
            ->where('trt_hasil.nidn', $nim)
            ->where('trt_hasil.reg_id', $reg_id)
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
            ->first();
        $status = "";
        if ($data == null || $data == '') {
            $status = '<i class="fa fa-times-circle text-danger"></i>';
        } else {
            $status = '<i class="fa fa-check-circle text-success"></i>';
        }
        return $status;
    }

    public static function getDataSuratUsulanTa($id)
    {
        $info = TrtJadwalUjian::join("mst_pendaftaran", "mst_pendaftaran.pendaftaran_id", "=", "trt_jadwal_ujian.pendaftaran_id")
            ->where("mst_pendaftaran.pendaftaran_id", $id)->first();

        if (!isset($info) || !isset($info->tipe_ujian)) {
            return collect();
        }

        $data = DB::select("SELECT * FROM mst_pendaftaran,trt_reg, trt_bimbingan, trt_penguji, t_mst_mahasiswa , trt_jadwal_ujian, trt_jadwal_ujian_per_mhs , mst_ruangan WHERE mst_ruangan.id =  trt_jadwal_ujian_per_mhs.ruangan AND trt_bimbingan.C_NPM = trt_jadwal_ujian_per_mhs.C_NPM AND trt_jadwal_ujian.id = trt_jadwal_ujian_per_mhs.jadwal_ujian AND trt_jadwal_ujian.pendaftaran_id = trt_reg.pendaftaran_id AND mst_pendaftaran.pendaftaran_id = trt_reg.pendaftaran_id AND trt_reg.bimbingan_id = trt_bimbingan.bimbingan_id AND trt_bimbingan.C_NPM = t_mst_mahasiswa.C_NPM AND trt_penguji.tipe_ujian = trt_reg.status AND  trt_penguji.C_NPM = trt_bimbingan.C_NPM AND trt_reg.pendaftaran_id = ? AND trt_reg.status = ?", [$id, $info->tipe_ujian]);

        return $data;
    }

    public static function getPendaftaranIdForMahasiswa()
    {
        $v = DB::table('trt_bimbingan')
            ->select('trt_reg.pendaftaran_id')
            ->join("trt_reg", 'trt_reg.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->where('trt_bimbingan.C_NPM', auth()->user()->name)
            ->first();
        return isset($v) ? $v->pendaftaran_id : '';
    }

    public static function getPendaftaranIdForDosen($nim)
    {
        $v = DB::table('trt_bimbingan')
            ->select('trt_reg.pendaftaran_id')
            ->join("trt_reg", 'trt_reg.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->where('trt_bimbingan.C_NPM', $nim)
            ->first();
        return isset($v) ? $v->pendaftaran_id : '';
    }

    public static function getStatusSKPembimbingForMahasiswa($nim)
    {
        $v = DB::table('mst_sk_pembimbing')
            ->select(["mst_sk_pembimbing.status", "mst_sk_pembimbing.nomor_sk"])
            ->join("trt_bimbingan", 'trt_bimbingan.bimbingan_id', '=', 'mst_sk_pembimbing.bimbingan_id')
            ->where('trt_bimbingan.C_NPM', $nim)
            ->first();
        return isset($v) ? $v : '';
    }

    public static function getStatusSKUjianProposalForMahasiswa($nim)
    {
        $v = DB::table('trt_penguji')
            ->select("trt_penguji.nomor_sk")
            ->where('trt_penguji.C_NPM', $nim)
            ->where('trt_penguji.tipe_ujian', 0)
            ->first();
        return isset($v) ? $v->nomor_sk : '';
    }

    public static function getStatusSKUjianMejaForMahasiswa($nim)
    {
        $v = DB::table('mst_sk_penugasan')
            ->select(["mst_sk_penugasan.status", "mst_sk_penugasan.nomor_sk"])
            ->join("trt_bimbingan", 'trt_bimbingan.bimbingan_id', '=', 'mst_sk_penugasan.bimbingan_id')
            ->where('trt_bimbingan.C_NPM', $nim)
            ->first();
        return isset($v) ? $v : '';
    }

    public static function getStatusApproveWakilDekan($id)
    {
        $v = DB::table('mst_sk_pembimbing')
            ->select("mst_sk_pembimbing.status")
            ->where('mst_sk_pembimbing.sk_pembimbing_id', $id)
            ->first();
        return isset($v) ? $v->status : '';
    }

    public static function getStatusFromSkPenugasan($id)
    {
        $v = DB::table('mst_sk_penugasan')
            ->select("mst_sk_penugasan.status")
            ->where('mst_sk_penugasan.sk_penugasan_id', $id)
            ->first();
        return isset($v) ? $v->status : '';
    }

    public static function getNomorSkProdi($nim)
    {
        $v = DB::table('trt_penguji')
            ->select("trt_penguji.nomor_sk")
            ->where('trt_penguji.C_NPM', $nim)
            ->first();
        return isset($v) ? $v->nomor_sk : 'Belum Ada';
    }

    public static function getNomorSKWithBimbinganId($id)
    {
        $v = DB::table('trt_sk')
            ->select("trt_sk.nomor")
            ->where('trt_sk.bimbingan_id', $id)
            ->first();
        return isset($v) ? $v->nomor : '';
    }

    public static function getNomorSKPenugasanWithBimbinganId($id)
    {
        $v = DB::table('trt_sk_ujian_ta')
            ->select("trt_sk_ujian_ta.nomor")
            ->where('trt_sk_ujian_ta.pendaftaran_id', $id)
            ->first();
        return isset($v) ? $v->nomor : '';
    }

    public static function getStatusTolakBimbinganProposalByNim($nim)
    {
        $v = DB::table('trt_bimbingan')
            ->select("trt_bimbingan.status_tolak_proposal")
            ->where('trt_bimbingan.C_NPM', $nim)
            ->first();
        return isset($v) ? $v->status_tolak_proposal : '';
    }

    public static function getStatusTolakBimbinganMejaByNim($nim)
    {
        $v = DB::table('trt_bimbingan')
            ->select("trt_bimbingan.status_tolak_meja")
            ->where('trt_bimbingan.C_NPM', $nim)
            ->first();
        return isset($v) ? $v->status_tolak_meja : '';
    }

    public static function getDataPesanMasuk($nim)
    {
        $v = DB::table('trt_konsultasi')
            ->select("*")
            ->where('trt_konsultasi.penerima_id', $nim)
            ->where('trt_konsultasi.status_baca', 0)
            ->get();
        return isset($v) ? count($v) : '0';
    }

    public static function getDataPesanKeluar($nim)
    {
        $v = DB::table('trt_konsultasi')
            ->select("*")
            ->where('trt_konsultasi.pengirim_id', $nim)
            ->where('trt_konsultasi.status_baca', 0)
            ->get();
        return isset($v) ? count($v) : '0';
    }

    public static function getNilaiKetuaSidangByDosen($dosen, $reg_id)
    {
        static $cache = [];

        $dosen = trim((string) $dosen);
        $reg_id = trim((string) $reg_id);
        $cacheKey = $dosen . '|' . $reg_id;

        if (array_key_exists($cacheKey, $cache)) {
            return $cache[$cacheKey];
        }

        $defaultNilai = (object) [
            'nidn' => $dosen,
            'reg_id' => $reg_id,
            'nilai_1' => 0,
            'nilai_2' => 0,
            'nilai_3' => 0,
            'nilai_4' => 0,
            'nilai_5' => 0,
        ];

        if ($dosen === '' || $reg_id === '') {
            $cache[$cacheKey] = $defaultNilai;
            return $cache[$cacheKey];
        }

        $v = DB::table('trt_hasil')
            ->select("*")
            ->where('trt_hasil.nidn', $dosen)
            ->where('trt_hasil.reg_id', $reg_id)
            ->first();

        $cache[$cacheKey] = isset($v) ? $v : $defaultNilai;
        return $cache[$cacheKey];
    }

    public static function getPeriodePendaftaranByStatusUjian($status_ujian, $tipe_ujian)
    {
        $v = DB::table('mst_pendaftaran')
            ->select("*")
            ->where('status_ujian', $status_ujian)
            ->where('tipe_ujian', $tipe_ujian)
            ->get();

        return $v;
    }

    //  Mengecek Status Konfirmasi Pada Usulan Judul Berdasarkan NIM
    public static function getStatusKonfirmasiTopikPenelitian($nim)
    {
        $v = DB::table('trt_topik')
            ->select('status')
            ->where('C_NPM', $nim)
            ->where('status', 1)
            ->first();
        return isset($v) ? $v->status : '0';
    }


    public static function getPeriode($tgl_ujian)
    {
        return self::getSemesterAkademik($tgl_ujian)->tahun_akademik;
    }

    public static function getNamaSemester($tanggal = null)
    {
        return self::getSemesterAkademik($tanggal)->semester;
    }

    public static function convertToIndonesianDate($date)
    {
        // Parsing tanggal menggunakan Carbon
        $carbonDate = Carbon::parse($date);
        // Format tanggal menggunakan formatLocalized
        $formattedDate = $carbonDate->formatLocalized('%A, %d %B %Y');

        // Array konversi nama hari dan bulan
        $hariInggris = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $hariIndonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $bulanInggris = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];
        $bulanIndonesia = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        // Konversi nama hari dan bulan
        $formattedDate = str_replace($hariInggris, $hariIndonesia, $formattedDate);
        $formattedDate = str_replace($bulanInggris, $bulanIndonesia, $formattedDate);

        return $formattedDate;
    }

    public static function getPeriodeSemester($tanggal = null)
    {
        return self::getSemesterAkademik($tanggal)->label;
    }

    public static function formatRupiah($angka)
    {
        $hasil_rupiah = "Rp " . number_format($angka, 0, ',', '.');
        return $hasil_rupiah;
    }

    public static function formatRupiahWithoutRp($angka)
    {
        $hasil_rupiah = number_format($angka, 0, ',', '.');
        return $hasil_rupiah;
    }

    public static function getTandaTanganByKodeDosen($kode_dosen)
    {
        $tandaTangan = DB::table('mst_tanda_tangan')
            ->where('C_KODE_DOSEN', $kode_dosen)
            ->first();

        return $tandaTangan->tanda_tangan ?? '';
    }

    public static function getDateNow()
    {
        $date = Carbon::now();
        $date = \Carbon\Carbon::parse($date)->format('d F Y');

        $bulanInggris = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        $bulanIndonesia = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        $date = str_replace($bulanInggris, $bulanIndonesia, $date);

        return $date;
    }

    public static function getInformationCetakHonorarium($C_NPM)
    {
        return DB::table('trt_topik')
            ->join('t_mst_mahasiswa', 'trt_topik.C_NPM', '=', 't_mst_mahasiswa.C_NPM')
            ->join('trt_prodi', 't_mst_mahasiswa.C_KODE_PRODI', '=', 'trt_prodi.kode_prodi')
            ->join('trt_jadwal_ujian_per_mhs', 'trt_topik.C_NPM', '=', 'trt_jadwal_ujian_per_mhs.C_NPM')
            ->join('mst_ruangan', 'trt_jadwal_ujian_per_mhs.ruangan', '=', 'mst_ruangan.id')
            ->where('trt_topik.C_NPM', $C_NPM)
            ->select(
                'trt_topik.topik',
                't_mst_mahasiswa.NAMA_MAHASISWA',
                'trt_prodi.nama as nama_prodi',
                'trt_jadwal_ujian_per_mhs.jam_ujian',
                'trt_jadwal_ujian_per_mhs.ruangan',
                'mst_ruangan.nama_ruangan'
            )
            ->first();
    }

    public static function getDateNowWithParam($inputDate = null)
    {
        $date = $inputDate ? Carbon::parse($inputDate) : Carbon::now();

        $formattedDate = $date->format('d F Y');

        $namaHari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];

        $bulanInggris = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        $bulanIndonesia = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        $formattedDate = str_replace($bulanInggris, $bulanIndonesia, $formattedDate);

        $hari = $namaHari[$date->format('l')];

        return $hari . ', ' . $formattedDate;
    }

    public static function jenisTugasAkhirKode($jenisTugasAkhirId)
    {
        static $types = null;

        if ($jenisTugasAkhirId === null) {
            return '';
        }

        if ($types === null) {
            $types = DB::table('mst_jenis_tugas_akhir')
                ->pluck('kode_jenis_tugas_akhir', 'jenis_tugas_akhir_id');
        }

        return $types->get($jenisTugasAkhirId, '');
    }

    public static function judulDenganKodeJenisTugasAkhir($jenisTugasAkhirId, $judul)
    {
        $judul = trim((string) $judul);
        $code = self::jenisTugasAkhirKode($jenisTugasAkhirId);

        return $code ? '[' . $code . '] ' . $judul : $judul;
    }

    public static function jenisTugasAkhirBadge($jenisTugasAkhirId)
    {
        $code = self::jenisTugasAkhirKode($jenisTugasAkhirId);
        if (!$code) {
            return '';
        }

        $classes = [
            'NS-AI' => 'label-info',
            'NS-AR' => 'label-primary',
            'NS-KK' => 'label-danger',
            'NS-KP' => 'label-success',
            'NS-KT' => 'label-warning',
            'TA-SK' => 'label-info',
            'TA-SM' => 'label-default',
        ];
        $class = $classes[$code] ?? 'label-default';

        return '<span class="label ' . $class . '" style="display: inline-block; margin: 0 6px 4px 0;">'
            . htmlspecialchars($code, ENT_QUOTES, 'UTF-8')
            . '</span>';
    }
}
