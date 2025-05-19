<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'type', 'category', 'location',
        'salary_type', 'min_salary', 'max_salary',
        'education_level', 'experience_level', 'job_level', 'description'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
