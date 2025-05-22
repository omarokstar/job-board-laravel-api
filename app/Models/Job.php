<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'job_type',
        'company',
        'location',
        'salary',
        'description',
        'status',
    ];





       public function employer()
    {
        return $this->belongsTo(User::class, 'user_id');
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





    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function applications()
{
    return $this->hasMany(JobApplication::class);
}
    

}
