<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    protected $table = 'dosen';
    protected $guarded = ['id'];

    public function User()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
