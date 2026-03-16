<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class mst_sk_pembimbing extends Model
{
  protected $table = 'mst_sk_pembimbing';
  protected $primaryKey = 'sk_pembimbing_id';
  protected $fillable = ['sk_pembimbing_id', 'nomor_sk', 'bimbingan_id'];
}
