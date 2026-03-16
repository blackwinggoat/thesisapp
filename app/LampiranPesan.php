<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LampiranPesan extends Model
{
    protected $primaryKey = "id";
    protected $table = "trt_lampiran_pesan";
    protected $fillable = ["pesan_id", "lampiran"];
}
