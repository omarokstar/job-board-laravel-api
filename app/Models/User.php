<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;


use App\Models\JobApplication;
class User extends Authenticatable  implements MustVerifyEmail
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasApiTokens;
    use Billable;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'website',
        'profile_photo_path',
        'professional_title',
    ];


    public function jobs()
{
    return $this->hasMany(Job::class);
}
public function applications()
{
    return $this->hasMany(JobApplication::class);
}

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }






 public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function socialLinks()
    {
        return $this->hasOne(UserSocialLinks::class);
    }

    public function resumes()
    {
        return $this->hasMany(UserResume::class);
    }


    public function appliedJobs()
    {
        return $this->belongsToMany(Job::class, 'job_applications', 'user_id', 'job_id')
            ->withPivot(['cover_letter', 'resume_path', 'status', 'created_at'])
            ->withTimestamps();
    }




}
