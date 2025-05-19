<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;

class JobController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'category' => 'required|string',
            'location' => 'required|string',
            'salary_type' => 'required|string',
            'min_salary' => 'required|numeric',
            'max_salary' => 'required|numeric',
            'education_level' => 'required|string',
            'experience_level' => 'required|string',
            'job_level' => 'required|string',
            'description' => 'required|string',
        ]);

        $validated['user_id'] = Auth::id();

        $job = Job::create($validated);

        return response()->json($job, 201);
    }
}
