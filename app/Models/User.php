<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'user';
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function Roles() {
        return $this->belongsToMany(Role::class);
    }
    public function Dekan()
    {
        return $this->hasOne(Dekan::class,'user_id');
    }
    public function Dosen()
    {
        return $this->hasOne(Dosen::class,'user_id');
    }
    public function Mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class,'user_id');
    }
    public function Biodata()
    {
        return $this->hasOne(Biodata::class,'user_id');
    }
}
