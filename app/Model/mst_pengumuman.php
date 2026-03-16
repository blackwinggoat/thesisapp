<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class mst_pengumuman extends Model
{
  protected $table = 'mst_pengumuman';
  protected $primaryKey = 'pengumuman_id';
  protected $fillable = ['pengumuman_id', 'judul', 'isi', 'gambar', 'last_update', 'user_id'];
}
