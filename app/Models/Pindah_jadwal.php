<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pindah_jadwal extends Model
{
    protected $table = 'pindah_jadwal';
    protected $guarded = ['id'];

    public function Jadwal_asal()
    {
        return $this->belongsTo(Jadwal::class,'jadwal_asal_id');
    }
    public function Jadwal_tujuan()
    {
        return $this->belongsTo(Jadwal::class,'jadwal_tujuan_id');
    }
    public function Ruangan()
    {
        return $this->belongsTo(Ruangan::class,'ruangan_id');
    }
    public function Shift()
    {
        return $this->belongsTo(Shift::class,'shift_id');
    }
    public function Kosma()
    {
        return $this->belongsTo(Kosma::class,'kosma_id');
    }
}
