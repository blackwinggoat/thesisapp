<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class mst_sk_penugasan extends Model
{
    protected $table = 'mst_sk_penugasan';
    protected $primaryKey = 'sk_penugasan_id';
    protected $fillable = ['sk_penugasan_id', 'bimbingan_id', 'nomor_sk',];
}
