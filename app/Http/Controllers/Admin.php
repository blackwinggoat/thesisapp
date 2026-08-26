<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Model\mst_periode_jabatan;
use App\Services\SystemMailSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Admin extends Controller
{
    protected function roleLabels()
    {
        return [
            1 => 'Admin',
            2 => 'Dekan',
            3 => 'Wakil Dekan',
            4 => 'Akademik Fakultas',
            5 => 'Ketua Program Studi',
            6 => 'Akademik Prodi',
            7 => 'Dosen',
            8 => 'Mahasiswa',
            9 => 'Keuangan Fakultas',
        ];
    }

    public function users(Request $request)
    {
        $roleLabels = $this->roleLabels();
        $q = trim((string) $request->get('q', ''));
        $level = $request->get('level', 'semua');
        $perPage = (int) $request->get('per_page', 50);
        if (!in_array($perPage, [25, 50, 100, 200], true)) {
            $perPage = 50;
        }

        $usersQuery = DB::table('users')
            ->leftJoin('t_mst_dosen as dosen', 'dosen.C_KODE_DOSEN', '=', 'users.name')
            ->leftJoin('t_mst_mahasiswa as mahasiswa', 'mahasiswa.C_NPM', '=', 'users.name')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.level',
                'users.created_at',
                'users.updated_at',
                DB::raw('COALESCE(dosen.NAMA_DOSEN, mahasiswa.NAMA_MAHASISWA, users.email, users.name) as display_name')
            );

        if ($q !== '') {
            $usersQuery->where(function ($query) use ($q) {
                $query->where('users.name', 'LIKE', '%' . $q . '%')
                    ->orWhere('users.email', 'LIKE', '%' . $q . '%')
                    ->orWhere('dosen.NAMA_DOSEN', 'LIKE', '%' . $q . '%')
                    ->orWhere('mahasiswa.NAMA_MAHASISWA', 'LIKE', '%' . $q . '%');
            });
        }

        if ($level !== 'semua' && array_key_exists((int) $level, $roleLabels)) {
            $usersQuery->where('users.level', (int) $level);
        } else {
            $level = 'semua';
        }

        $roleCounts = DB::table('users')
            ->select('level', DB::raw('COUNT(*) as total'))
            ->groupBy('level')
            ->pluck('total', 'level');

        $data = $usersQuery
            ->orderBy('users.level')
            ->orderBy('display_name')
            ->paginate($perPage)
            ->appends($request->only(['q', 'level', 'per_page']));

        return view('tugasakhir.admin.users', compact('data', 'roleLabels', 'roleCounts', 'q', 'level', 'perPage'));
    }

    public function reset_user_password(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $targetUser = DB::table('users')->where('id', $id)->first();
        if (!$targetUser) {
            return redirect()->back()->with('status', 'user_not_found');
        }

        DB::table('users')
            ->where('id', $id)
            ->update([
                'password' => Hash::make($request->password),
                'remember_token' => null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->back()->with('status', 'password_reset')->with('target_user_name', $targetUser->name);
    }

    public function login_as_user(Request $request, $id)
    {
        $authUser = $request->user();
        if (!$authUser || (int) $authUser->level !== 1) {
            return redirect('/')->with('danger', 'Akses login as hanya untuk akun admin.');
        }

        $targetUser = DB::table('users')
            ->select('id', 'name', 'level')
            ->where('id', $id)
            ->first();

        if (!$targetUser) {
            return redirect()->back()->with('danger', 'User tujuan tidak ditemukan.');
        }

        if ((int) $targetUser->id === (int) $authUser->id || (int) $targetUser->level === 1) {
            return redirect()->back()->with('danger', 'Login As tidak tersedia untuk akun admin.');
        }

        $request->session()->put('login_as_source_user_id', $authUser->id);
        $request->session()->put('login_as_source_user_name', $authUser->name);
        $request->session()->put('login_as_source_user_level', (int) $authUser->level);

        Auth::loginUsingId($targetUser->id);
        $request->session()->regenerate();

        return redirect('/')->with('success', 'Berhasil login sebagai user terpilih.');
    }

    public function back_to_admin(Request $request)
    {
        $sourceUserId = $request->session()->get('login_as_source_user_id');
        $sourceUserLevel = (int) $request->session()->get('login_as_source_user_level', 0);

        if (empty($sourceUserId) || $sourceUserLevel !== 1) {
            return redirect('/')->with('danger', 'Session Login As admin tidak ditemukan.');
        }

        $sourceUser = DB::table('users')
            ->select('id', 'level')
            ->where('id', $sourceUserId)
            ->first();

        if (!$sourceUser || (int) $sourceUser->level !== 1) {
            $request->session()->forget([
                'login_as_source_user_id',
                'login_as_source_user_name',
                'login_as_source_user_level',
            ]);
            return redirect('/')->with('danger', 'Akun admin asal tidak valid.');
        }

        Auth::loginUsingId($sourceUserId);
        $request->session()->regenerate();
        $request->session()->forget([
            'login_as_source_user_id',
            'login_as_source_user_name',
            'login_as_source_user_level',
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Berhasil kembali ke akun admin.');
    }

    public function periode_jabatan(){
        $data = mst_periode_jabatan::all();
        return view('tugasakhir.admin.periode_jabatan', compact('data'));
    }

    public function mail_settings()
    {
        $settings = SystemMailSettings::all();
        $passwordMask = SystemMailSettings::maskedPassword();

        return view('tugasakhir.admin.mail_settings', compact('settings', 'passwordMask'));
    }

    public function mail_settings_update()
    {
        request()->validate([
            'driver' => 'required|in:smtp,sendmail,mail,log',
            'host' => 'required_if:enabled,1|nullable|string|max:191',
            'port' => 'required_if:enabled,1|nullable|integer|min:1|max:65535',
            'username' => 'nullable|string|max:191',
            'password' => 'nullable|string|max:191',
            'encryption' => 'nullable|in:tls,ssl',
            'from_address' => 'required_if:enabled,1|nullable|email|max:191',
            'from_name' => 'nullable|string|max:191',
        ]);

        try {
            SystemMailSettings::update(request()->all());

            return redirect()->back()->with(['status' => 'berhasil']);
        } catch (\Exception $exception) {
            return redirect()->back()->withInput()->with(['status' => 'gagal']);
        }
    }

    public function periode_jabatan_update(){
        request()->validate([
            'id_jabatan' => 'required|integer',
            'ttd' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
        ]);

        $newFileName = null;

        try {
            $request = request()->all();
            $data = mst_periode_jabatan::find($request['id_jabatan']);
            $oldFileName = $data->ttd;
            $data->nama = $request['nama'];
            $data->prodi = $request['prodi'];
            $data->tanggal_menjabat = $request['tanggal_menjabat'];
            $data->tanggal_berakhir = $request['tanggal_berakhir'];
            $data->email = $request['email'];
            $data->no_telepon = $request['no_telepon'];
            if (request()->hasFile('ttd')) {
                $file = request()->file('ttd');
                $newFileName = Helper::storeOfficialImage(
                    $file,
                    Helper::MANAGED_OFFICIAL_IMAGE_PREFIX . $data->id_jabatan
                );
                $data->ttd = $newFileName;
            }

            $data->save();

            if ($newFileName !== null) {
                Helper::deleteManagedOfficialImage($oldFileName);
            }

            return redirect()->back()->with(['status' => "berhasil"]);
        } catch (\Exception $th) {
            if ($newFileName !== null) {
                Helper::deleteManagedOfficialImage($newFileName);
            }

            return redirect()->back()->with(['status' => "gagal"]);
        }
    }
}
