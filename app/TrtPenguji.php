<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrtPenguji extends Model
{
    protected $primaryKey = "id";
    protected $table = "trt_penguji";
    protected $fillable = ["C_NPM", "tipe_ujian", "penguji_I_id", "penguji_II_id", "penguji_III_id", "ketua_sidang_id", "sekretaris_id"];
}
