<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class t_mst_mahasiswa extends Model
{
  protected $table = 't_mst_mahasiswa';
  protected $primaryKey = 'C_NPM';
  protected $keyType = 'string';
  public $incrementing = false;
  protected $fillable = ['C_NPM', 'NAMA_MAHASISWA', 'JENIS_KELAMIN', 'ALAMAT', 'JML_SKS_DIAKUI', 'C_KODE_STATUS_AKTIF_MHS'];

  public function pembimbing1()
  {
    return $this->hasOne('App\Model\trt_bimbingan', 'C_NPM', 'C_NPM')->latest('bimbingan_id');
  }
}
