<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestPembimbing extends Model
{
    protected $primaryKey = "request_pembimbing_id";
    protected $table = "request_pembimbing";
    protected $fillable = ["C_NPM", "bidang_ilmu", "topik"];
}
