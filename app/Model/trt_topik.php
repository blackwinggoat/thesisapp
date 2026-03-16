<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class trt_topik extends Model
{
  protected $table = 'trt_topik';
  protected $primaryKey = 'topik_id';
  protected $fillable = ['topik_id', 'C_NPM', 'bidang_ilmu', 'topik', 'kerangka', 'status', 'last_update', 'user_id', 'bidang_ilmu_peminatan', "note"];
}
