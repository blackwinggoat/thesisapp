<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrtJadwalUjian extends Model
{
    protected $primaryKey = "id";
    protected $table = "trt_jadwal_ujian";
    protected $fillable = ["pendaftaran_id", "tgl_ujian", "jml_ruangan", "status"];
}
