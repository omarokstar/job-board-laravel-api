<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    use HasFactory;
protected $table = 'jobs';


    protected $fillable = [
        'employer_id',
        'title',
        'description',
        'location',
        'salary',
        'status',
        'rejection_reason',
    ];

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}