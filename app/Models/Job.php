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
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
