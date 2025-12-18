<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kaprodi extends Model
{
    protected $table = 'kaprodi';
    protected $guarded = ['id'];

    public function User()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
