<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class trt_reg extends Model
{
  protected $table = 'trt_reg';
  protected $primaryKey = 'reg_id';
  protected $fillable = ['reg_id', 'bimbingan_id', 'pendaftaran_id', 'status', 'tgl_reg', 'C_NPM'];
}
