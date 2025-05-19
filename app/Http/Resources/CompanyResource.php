<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'company_name' => $this->company_name,
            'company_email' => $this->email,
            'company_phone' => $this->phone,
            'company_address' => $this->company_address,
            'city' => $this->city,
            'country' => $this->country,
            'about' => $this->about,
            'organization_type' => $this->organization_type,
            'establishment_year' => $this->establishment_year,
            'company_vision' => $this->company_vision,
            'industry_type' => $this->industry_type,
            'team_size' => $this->team_size,
            'company_website' => $this->company_website,
            'linkedIn' => $this->linkedIn,
            'facebook' => $this->facebook,
            'twitter' => $this->twitter,
            'github' => $this->github,
            'logo_url' => $this->logo ? asset('storage/images/'.$this->logo) : null,
            'banner_url' => $this->banner ? asset('storage/images/'.$this->banner) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        
    }
}
