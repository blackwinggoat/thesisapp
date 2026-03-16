<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class mst_pendaftaran extends Model
{
  protected $table = 'mst_pendaftaran';
  protected $primaryKey = 'pendaftaran_id';
  protected $fillable = ['pengumuman_id', 'nama_periode', 'tipe_ujian', 'status_prodi' ,'tgl_start', 'tgl_end', 'kuota', 'jml_peserta', 'user_id'];
}
