<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrtPengajuanDokumen extends Model
{
    protected $primaryKey = "id";
    protected $table = "trt_pengajuan_dokumen";
    protected $fillable = ["C_NPM", "type"];
}
