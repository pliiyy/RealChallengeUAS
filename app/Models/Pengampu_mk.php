<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengampu_mk extends Model
{
    protected $table = 'pengampu_mk';
    protected $guarded = ['id'];

    public function Surat_tugas()
    {
        return $this->belongsTo(Surat_tugas::class,'surat_tugas_id');
    }
    public function Kelas()
    {
        return $this->belongsToMany(
            Kelas::class,
            'pengampu_mk_kelas',
            'pengampu_mk_id',
            'kelas_id'
        );
    }
    public function Matakuliah()
    {
        return $this->belongsTo(Matakuliah::class,'matakuliah_id');
    }
    public function Jadwal()
    {
        return $this->hasOne(Jadwal::class);
    }
}
