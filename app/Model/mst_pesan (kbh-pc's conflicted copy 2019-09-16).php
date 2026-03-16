<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class mst_pesan extends Model
{
  protected $table = 'mst_pesan';
  protected $primaryKey = 'pesan_id';
  protected $fillable = ['pesan_id', 'perihal_pesan', 'isi_pesan', 'lampiran'];
}
