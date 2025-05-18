<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
class StoreCompanyRequest extends FormRequest
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
        
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'company_address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'about' => 'required|string|max:255',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'organization_type' => 'required|string|max:255',
            'establishment_year' => 'required|date_format:Y-m-d',
            'company_vision' => 'required|string|max:255',
            'industry_type' => 'required|string|max:255',
            'team_size' => 'required|integer',
            'company_website' => 'required|url|max:255',
            'banner' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'linkedIn' => 'sometimes|url|max:255',
            'facebook' => 'sometimes|url|max:255',
            'twitter' => 'sometimes|url|max:255',
            'github' => 'sometimes|url|max:255',
        ];
    }
}
