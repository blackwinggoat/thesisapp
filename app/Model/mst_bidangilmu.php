<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class mst_bidangilmu extends Model
{
  protected $table = 'mst_bidangilmu';
  protected $primaryKey = 'bidangilmu_id';
  protected $fillable = ['bidangilmu_id', 'bidang_ilmu'];
}
