<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrtUsulanJudul extends Model
{
    protected $primaryKey = "usulan_judul_id";
    protected $table = "trt_usulan_judul";
    protected $fillable = ["judul", "C_NPM", "KODE_DOSEN"];
}
