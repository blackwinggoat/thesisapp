<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class trt_hasil extends Model
{
    protected $table = 'trt_hasil';
    protected $primaryKey = 'nilai_id';
    protected $fillable = ['reg_id', 'nidn', 'nilai_1', 'nilai_2', 'nilai_3', 'nilai_4','nilai_5', 'saran'];
}
