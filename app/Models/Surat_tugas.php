<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Surat_tugas extends Model
{
    protected $table = 'surat_tugas';
    protected $guarded = ['id'];
    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function Dekan()
    {
        return $this->belongsTo(Dekan::class,'dekan_id');
    }
    public function Dosen()
    {
        return $this->belongsTo(Dosen::class,'dosen_id');
    }
    public function Semester()
    {
        return $this->belongsTo(Semester::class,'semester_id');
    }
    public function Pengampu_mk()
    {
        return $this->hasMany(Pengampu_mk::class);
    }
    public function photoUrl()
    {
        return $this->file
            ? asset('storage/'.$this->file)
            : null;
    }
}
