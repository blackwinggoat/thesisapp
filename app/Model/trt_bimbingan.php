<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class trt_bimbingan extends Model
{
  protected $table = 'trt_bimbingan';
  protected $primaryKey = 'bimbingan_id';
  protected $fillable = ['bimbingan_id', 'topik_id', 'C_NPM', 'pembimbing_I_id', 'pembimbing_II_id', 'judul', 'jenis_tugas_akhir_id', 'status_I', 'status_II', 'status_bimbingan', 'status_sk', 'user_id'];

  public function jenisTugasAkhir()
  {
    return $this->belongsTo('App\\Model\\mst_jenis_tugas_akhir', 'jenis_tugas_akhir_id', 'jenis_tugas_akhir_id');
  }

  public function topik()
  {
    return $this->belongsTo('App\\Model\\trt_topik', 'topik_id', 'topik_id');
  }
}
