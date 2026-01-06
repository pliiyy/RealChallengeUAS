<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';
    protected $guarded = ['id']; 

    public function Pengampu_mk()
    {
        return $this->belongsTo(Pengampu_mk::class,'pengampu_mk_id');
    }
    public function Ruangan()
    {
        return $this->belongsTo(Ruangan::class,'ruangan_id');
    }
    public function Shift()
    {
        return $this->belongsTo(Shift::class,'shift_id');
    }
    public function Pindah_jadwal()
    {
        return $this->hasMany(Pindah_jadwal::class);
    }
}
