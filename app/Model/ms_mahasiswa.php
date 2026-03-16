<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ms_mahasiswa extends Model
{
  protected $table = 'ms_mahasiswa';
  protected $primaryKey = 'mahasiswa_id';
  protected $fillable = ['mahasiswa_id', 'stambuk', 'nama_mahasiswa', 'jk', 'alamat', 'status'];

  public function pembimbing1()
  {
    return $this->hasOne('App\Model\trt_bimbingan', 'mahasiswa_id', 'mahasiswa_id');
  }
}
