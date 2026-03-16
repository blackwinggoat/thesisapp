<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class mst_periode_jabatan extends Model
{
    protected $table = 'mst_periode_jabatan';
    protected $primaryKey = 'id_jabatan';
    protected $fillable = ['nama', 'prodi', 'tanggal_menjabat', 'tanggal_berakhir', 'email', 'no_telepon', 'ttd'];
}