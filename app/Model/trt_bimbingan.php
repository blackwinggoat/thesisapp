<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class trt_bimbingan extends Model
{
  protected $table = 'trt_bimbingan';
  protected $primaryKey = 'bimbingan_id';
  protected $fillable = ['bimbingan_id', 'C_NPM', 'pembimbing_I_id', 'pembimbing_II_id', 'judul', 'status_I', 'status_II', 'status_bimbingan', 'status_sk', 'user_id'];
}
