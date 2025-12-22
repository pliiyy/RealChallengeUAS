<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Angkatan extends Model
{
    protected $table = 'angkatan';
    protected $guarded = ['id'];

    public function Kelas()
    {
        return $this->hasMany(Kelas::class);
    }
}
