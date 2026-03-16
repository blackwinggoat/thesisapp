<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class trt_sk extends Model
{
  protected $table = 'trt_sk';
  protected $primaryKey = 'sk_id';
  protected $fillable = ['sk_id', 'bimbingan_id', 'tipe', 'nomor', 'perihal', 'tgl_surat', 'user_id'];
}
