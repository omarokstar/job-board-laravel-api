<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'description',
        'salary_type', 'min_salary', 'max_salary',
        'education_level',
         'experience_level', 
         'job_level'
   
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
