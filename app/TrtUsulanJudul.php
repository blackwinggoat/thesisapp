<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrtUsulanJudul extends Model
{
    protected $primaryKey = "usulan_judul_id";
    protected $table = "trt_usulan_judul";
    protected $fillable = ["judul", "jenis_tugas_akhir_id", "C_NPM", "KODE_DOSEN"];

    public function jenisTugasAkhir()
    {
        return $this->belongsTo('App\\Model\\mst_jenis_tugas_akhir', 'jenis_tugas_akhir_id', 'jenis_tugas_akhir_id');
    }
}
