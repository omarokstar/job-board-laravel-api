<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;
    protected $fillable = [
        'company_name',
        'user_id',
        'email',
        'about',
        'organization_type',
        'establishment_year',
        'company_vision',
        'industry_type',
        'team_size',
        'company_website',
        'company_address',
        'city',
        'country',
        'phone',
        'email',
        'linkedIn',
        'facebook',
        'twitter',
        'github',
        'logo',
        'banner',
    ];
    public function User(){
        return $this->belongsTo(User::class);
    }
}