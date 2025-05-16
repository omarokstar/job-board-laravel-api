<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return false;
        return Auth::user() !== null ;

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'company_address' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'country' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:255',
            'about' => 'sometimes|string|max:255',
            'logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'organization_type' => 'sometimes|string|max:255',
            'establishment_year' => 'sometimes|date_format:Y-m-d',
            'company_vision' => 'sometimes|string|max:255',
            'industry_type' => 'sometimes|string|max:255',
            'team_size' => 'sometimes|integer',
            'company_website' => 'sometimes|url|max:255',
            'banner' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'linkedIn' => 'sometimes|url|max:255',
            'facebook' => 'sometimes|url|max:255',
            'twitter' => 'sometimes|url|max:255',
            'github' => 'sometimes|url|max:255',
        ];
    }
}
