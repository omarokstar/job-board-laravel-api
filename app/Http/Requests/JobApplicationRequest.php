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
          'cover_letter' => 'required|string|min:100|max:2000',
           'resume_path' => 'required|file|mimes:pdf,doc,docx|max:2048'
        ];
    }
}
