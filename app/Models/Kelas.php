<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $guarded = ['id'];

    public function Mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class);
    }
    public function Kosma()
    {
        return $this->hasMany(Kosma::class);
    }
    public function Prodi()
    {
        return $this->belongsTo(Prodi::class,"prodi_id");
    }
    public function Semester()
    {
        return $this->belongsTo(Semester::class,"semester_id");
    }
    public function Angkatan()
    {
        return $this->belongsTo(Angkatan::class,"angkatan_id");
    }
    public function Pengampu_mk()
    {
        return $this->belongsToMany(
            Pengampu_mk::class,
            'pengampu_mk_kelas',
            'kelas_id',
            'pengampu_mk_id'
        );
    }
}
