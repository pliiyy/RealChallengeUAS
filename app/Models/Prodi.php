<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    protected $table = 'prodi';
    protected $guarded = ['id'];

    public function Fakultas()
    {
        return $this->belongsTo(Fakultas::class,'fakultas_id');
    }
    public function Kelas()
    {
        return $this->hasMany(Kelas::class);
    }
}
