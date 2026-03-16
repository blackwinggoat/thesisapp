<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class mst_syarat_ujian extends Model
{
  protected $table = 'mst_syarat_ujian';
  protected $primaryKey = 'syarat_ujian_id';
  protected $fillable = ['syarat_ujian_id', 'nama_syarat', 'tipe_ujian'];
}
