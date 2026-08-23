<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Model\mst_periode_jabatan;
use App\Services\SystemMailSettings;

class Admin extends Controller
{
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
