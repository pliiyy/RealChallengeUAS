<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fakultas extends Model
{
    protected $table = 'fakultas';
    protected $guarded = ['id'];

    public function Dekan()
    {
        return $this->hasMany(Dekan::class);
    }
    public function Prodi()
    {
        return $this->hasMany(Prodi::class);
    }
}
