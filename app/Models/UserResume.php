<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserResume extends Model
{
     protected $fillable = [
        'user_id',
        'name',
        'path',
        'size',
        'extension',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
