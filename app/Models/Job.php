<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
 
    const JOB_TYPES = ['full-time', 'part-time', 'contract', 'temporary', 'internship', 'remote'];
    const EDUCATION_LEVELS = ['high_school', 'bachelor', 'master', 'phd']; 
    const EXPERIENCE_LEVELS = ['entry', 'mid', 'senior', 'executive']; 
    const JOB_LEVELS = ['intern', 'junior', 'mid', 'senior', 'lead'];

    const STATUS_PENDING = 'pending';  
    const STATUS_ACTIVE = 'active';    
    const STATUS_CLOSED = 'closed';    
    const STATUS_EXPIRED = 'expired';  // System-set if expiry date passed

    protected $fillable = [
        'title',
        'user_id', // The employer who posted the job
        'category_id',
        'location',
        'salary_type',
        'min_salary',
        'max_salary',
        'education_level',
        'experience_level',
        'job_level',
        'description',
        'responsibilities',
        'company', // Added 'company' field which is in your frontend
        'job_type',
        'status', // This needs to be fillable to be set by admin or system
        'expiry_date', // Important for automatic closing
        'keywords',
    ];

    // Set default status for new jobs
    protected $attributes = [
        'status' => self::STATUS_PENDING, // New jobs start as 'pending'
    ];

    // Accessor for 'status' (if you want to map 'expired' to 'closed' for frontend display)
    // Your current JobController has this. Keep it if you want that behavior.
    public function getStatusAttribute($value)
    {
        if ($value === self::STATUS_EXPIRED) {
            return self::STATUS_CLOSED;
        }
        return $value;
    }

    // Relationships (ensure these are correctly defined)
    public function user() // The employer who posted the job
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'job_skill'); // Ensure pivot table name if not default
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'job_tag'); // Ensure pivot table name if not default
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }
}