<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class mst_jenis_tugas_akhir extends Model
{
    protected $table = 'mst_jenis_tugas_akhir';
    protected $primaryKey = 'jenis_tugas_akhir_id';
    protected $fillable = ['jenis_tugas_akhir_id', 'kode_jenis_tugas_akhir', 'deskripsi'];
}
