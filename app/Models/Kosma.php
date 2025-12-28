<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kosma extends Model
{
    protected $table = 'kosma';
    protected $guarded = ['id'];

    public function Mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class,'mahasiswa_id');
    }
    public function Kelas()
    {
        return $this->belongsTo(Kelas::class,'kelas_id');
    }
}
