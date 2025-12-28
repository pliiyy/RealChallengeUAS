<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $table = 'shift';
    protected $guarded = ['id'];

    public function Jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }
}
