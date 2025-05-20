<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSocialLinks extends Model
{
    protected $fillable = [
        'user_id',
        'linkedin',
        'twitter',
        'github',
        'facebook',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
