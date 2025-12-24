<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dekan extends Model
{
    protected $table = 'dekan';
    protected $guarded = ['id'];

     protected $casts = [
        'periode_mulai' => 'datetime',
        'periode_selesai' => 'datetime',
    ];

    public function User()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function Fakultas()
    {
        return $this->belongsTo(Fakultas::class,'fakultas_id');
    }
    public function photoUrl()
    {
        return $this->foto_profile
            ? asset('storage/'.$this->foto_profil)
            : asset('images/default-avatar.png');
    }
}
