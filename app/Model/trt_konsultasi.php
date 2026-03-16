<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class trt_konsultasi extends Model
{
  protected $table = 'trt_konsultasi';
  protected $primaryKey = 'konsultasi_id';
  protected $fillable = ['konsultasi_id', 'pesan_id', 'pengirim_id', 'penerima_id', 'status_baca'];
}
