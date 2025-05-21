<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'job_tag')
                    ->withTimestamps();
    }

    public static function findOrCreate($name)
    {
        $tag = self::where('name', $name)->first();
        
        if (!$tag) {
            $tag = self::create(['name' => $name]);
        }

        return $tag;
    }
}