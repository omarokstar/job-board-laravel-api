<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
       
      //i should delete user id from the table
        'title',
        'job_type',
        'company',
        'location',
        'salary',
        'fixed_salary', // Added 'fixed_salary' to fillable
        'description',
        'salary_type',
        'min_salary',
        'max_salary',
        'education_level',
        'experience_level',
        'job_level',
        'responsibilities',
        'status',
        'expiry_date'
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'fixed_salary' => 'decimal:2', // Added cast for fixed_salary
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'expiry_date' => 'datetime'
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active'; // Changed from APPROVED
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CLOSED = 'closed';

    public const JOB_TYPES = [
        'full-time',
        'part-time',
        'contract',
        'freelance',
        'internship',
        'temporary' // Added 'temporary'
    ];

    public const EDUCATION_LEVELS = [
        'high_school',
        'bachelor',
        'master',
        'phd',
        'diploma' // Added 'diploma'
    ];

    public const EXPERIENCE_LEVELS = [
        'entry',
        'mid',
        'senior'
    ];

    public const JOB_LEVELS = [
        'junior',
        'mid',
        'senior',
        'lead' // Added 'lead' if used
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($job) {
            // Check if expiry_date has passed AND status is not already 'closed'
            if ($job->expiry_date && now()->gt($job->expiry_date) && $job->getRawOriginal('status') !== self::STATUS_CLOSED) {
                $job->status = self::STATUS_EXPIRED;
            }
        });
    }

    public function getStatusAttribute($value)
    {
        return $value === self::STATUS_EXPIRED ? self::STATUS_CLOSED : $value;
    }

    // Accessor to get the actual database status value
    public function getActualStatusAttribute(): string
    {
        return $this->getRawOriginal('status');
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_skill')
            ->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'job_tag')
            ->withTimestamps();
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function applicationsCount()
    {
        return $this->hasMany(JobApplication::class)->count();
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeActive($query) // Renamed from scopeApproved
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    public function isPending(): bool
    {
        return $this->getRawOriginal('status') === self::STATUS_PENDING;
    }

    public function isActive(): bool
    {
        return $this->getRawOriginal('status') === self::STATUS_ACTIVE;
    }

    public function isRejected(): bool
    {
        return $this->getRawOriginal('status') === self::STATUS_REJECTED;
    }

    public function isExpired(): bool
    {
        return $this->getRawOriginal('status') === self::STATUS_EXPIRED;
    }

    public function isClosed(): bool
    {
        return $this->getRawOriginal('status') === self::STATUS_CLOSED;
    }

    public function syncSkills(array $skillNames): void
    {
        $skills = collect($skillNames)->map(function ($name) {
            return Skill::firstOrCreate(['name' => $name])->id;
        });

        $this->skills()->sync($skills);
    }

    public function syncTags(?array $tagNames): void
    {
        if (empty($tagNames)) {
            $this->tags()->detach();
            return;
        }

        $tags = collect($tagNames)->map(function ($name) {
            return Tag::firstOrCreate(['name' => $name])->id;
        });

        $this->tags()->sync($tags);
    }
}