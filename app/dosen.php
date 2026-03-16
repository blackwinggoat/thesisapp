<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    protected $primaryKey = "id";
    protected $table = 't_mst_dosen';
    protected $fillable = ["C_KODE_DOSEN", "NAMA_DOSEN"];
}
