<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;  // لازم تضيفي هذه

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'job_type',
        'company',
        'location',
        'salary_type',
        'min_salary',
        'max_salary',
        'salary',
        'description',
        'responsibilities',
        'education_level',
        'experience_level',
        'job_level',
        'status',
        'published_at',
        'category_id',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($job) {
            if (empty($job->slug)) {
                $job->slug = Str::slug($job->title);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
