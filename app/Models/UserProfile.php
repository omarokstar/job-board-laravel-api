<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{ protected $fillable = [
        'user_id',
        'nationality',
        'date_of_birth',
        'gender',
        'marital_status',
        'education',
        'experience',
        'biography',
    ];

    protected $dates = ['date_of_birth'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    //
}
