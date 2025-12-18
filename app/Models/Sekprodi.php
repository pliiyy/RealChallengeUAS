<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekprodi extends Model
{
    protected $table = 'sekprodi';
    protected $guarded = ['id'];

    public function User()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
