<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'image', 'category', 'author', 'date', 'tags'
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
