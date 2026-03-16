<?php

namespace App\Http\Controllers;

use App\Model\mst_periode_jabatan;

class Admin extends Controller
{
    public function periode_jabatan(){
        $data = mst_periode_jabatan::all();
        return view('tugasakhir.admin.periode_jabatan', compact('data'));
    }

    public function periode_jabatan_update(){
        try {
            $request = request()->all();
            $data = mst_periode_jabatan::find($request['id_jabatan']);
            $data->nama = $request['nama'];
            $data->prodi = $request['prodi'];
            $data->tanggal_menjabat = $request['tanggal_menjabat'];
            $data->tanggal_berakhir = $request['tanggal_berakhir'];
            $data->email = $request['email'];
            $data->no_telepon = $request['no_telepon'];
            if (request()->hasFile('ttd')) {
                $file = request()->file('ttd');
                $extension = $file->getClientOriginalExtension() ?: 'png';
                $filename = 'ttd_kaprodi_' . $data->id_jabatan . '_' . time() . '.' . $extension;
                $file->move('gambar', $filename);
                $data->ttd = $filename;
            }

            $data->save();

            return redirect()->back()->with(['status' => "berhasil"]);
        } catch (\Exception $th) {
            return redirect()->back()->with(['status' => "gagal"]);
        }
    }
}
