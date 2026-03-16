<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrtSyaratUjian extends Model
{
    protected $primaryKey = "id";
    protected $table = "trt_syarat_ujian";
    protected $fillable = ["C_NPM", "syarat_ujian_id", "link", "status"];
}
