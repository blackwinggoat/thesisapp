<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrtLevelPembimbing extends Model
{
    protected $primaryKey = "trt_level_pembimbing_id";
    protected $table = "trt_level_pembimbing";
    protected $fillable = ["C_KODE_DOSEN", "level"];
}
