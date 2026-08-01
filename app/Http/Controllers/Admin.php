<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Model\mst_periode_jabatan;

class Admin extends Controller
{
    public function periode_jabatan(){
        $data = mst_periode_jabatan::all();
        return view('tugasakhir.admin.periode_jabatan', compact('data'));
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
