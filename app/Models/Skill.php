<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = ['name'];


    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'job_skill')
                    ->withTimestamps();
    }

    public static function findOrCreate($name)
    {
        $skill = self::where('name', $name)->first();
        
        if (!$skill) {
            $skill = self::create(['name' => $name]);
        }

        return $skill;
    }
}
