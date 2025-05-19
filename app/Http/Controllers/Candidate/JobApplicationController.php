<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\JobApplicationRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;
use App\Models\JobApplication; 
use Illuminate\Support\Facades\Storage;

class JobApplicationController extends Controller
{
    public function apply(JobApplicationRequest $request, $jobId)
    {
        $user = Auth::user();
        
        if (!$user->role=='candidate') {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $job = Job::findOrFail($jobId);

        if ($user->applications()->where('job_id', $jobId)->exists()) {
            return response()->json(['message' => 'You have already applied for this job.'], 409);
        }

        $resumePath = $this->storeResume($request->file('resume'));

        $application = JobApplication::create([
            'user_id' => $user->id,
            'job_id' => $jobId,
            'cover_letter' => $request->cover_letter,
            'resume_path' => $resumePath,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Application submitted successfully.',
            'application' => $application->load('job')
        ], 201);
    }

    protected function storeResume($file)
    {
        if (!$file) {
            return null;
        }

        return $file->store('resumes', 'public');
    }
}