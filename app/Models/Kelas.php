<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $guarded = ['id'];

    public function Kosma()
    {
        return $this->hasMany(Kosma::class);
    }
}
