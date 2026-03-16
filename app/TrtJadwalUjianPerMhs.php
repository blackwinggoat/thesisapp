<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrtJadwalUjianPerMhs extends Model
{
    protected $primaryKey = "id";
    protected $table = "trt_jadwal_ujian_per_mhs";
    protected $fillable = ["C_NPM", "jadwal_ujian", "jam_ujian", "ruangan"];
}
