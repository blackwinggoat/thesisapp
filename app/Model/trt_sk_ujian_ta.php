<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class trt_sk_ujian_ta extends Model
{
    protected $table = 'trt_sk_ujian_ta';
    protected $primaryKey = 'sk_id';
    protected $fillable = ['sk_id', 'pendaftaran_id', 'nomor', 'perihal', 'tgl_surat'];
}
