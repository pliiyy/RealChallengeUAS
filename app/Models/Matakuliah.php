<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matakuliah extends Model
{
    protected $table = 'matakuliah';
    protected $guarded = ['id'];

    public function Prodi()
    {
        return $this->belongsTo(Prodi::class,'prodi_id');
    }
}
