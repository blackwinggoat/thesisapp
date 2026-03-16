<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class mst_tmp_usulan extends Model
{
    protected $table = 'mst_tmp_usulan';
    protected $primaryKey = 'tmp_usulan_id';
    protected $fillable = ['tmp_usulan_id', 'C_NPM', 'pembimbing_I_id', 'pembimbing_II_id'];
}
