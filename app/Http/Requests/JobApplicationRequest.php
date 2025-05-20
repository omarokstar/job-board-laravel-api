<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'cover_letter' => 'nullable|string|max:5000',
            'resume_path' => 'nullable|string|max:255',
        ];
    }
}
