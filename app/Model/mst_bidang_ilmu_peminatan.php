<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class mst_bidang_ilmu_peminatan extends Model
{
    protected $table = 'mst_bidang_ilmu_peminatan';
    protected $primaryKey = 'bidang_ilmu_peminatan_id';
    protected $fillable = [
        'kode_prodi',
        'nama_peminatan',
        'status_aktif',
    ];
}
