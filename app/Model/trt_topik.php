<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class trt_topik extends Model
{
  protected $table = 'trt_topik';
  protected $primaryKey = 'topik_id';
  protected $fillable = ['topik_id', 'C_NPM', 'bidang_ilmu', 'topik', 'jenis_tugas_akhir_id', 'kerangka', 'status', 'last_update', 'user_id', 'bidang_ilmu_peminatan', 'bidang_ilmu_peminatan_id', "note"];

  public function jenisTugasAkhir()
  {
    return $this->belongsTo('App\\Model\\mst_jenis_tugas_akhir', 'jenis_tugas_akhir_id', 'jenis_tugas_akhir_id');
  }

  public function bimbingan()
  {
    return $this->hasOne('App\\Model\\trt_bimbingan', 'topik_id', 'topik_id');
  }
}
