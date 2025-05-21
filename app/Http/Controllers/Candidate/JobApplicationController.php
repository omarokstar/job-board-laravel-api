<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobApplicationRequest;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JobApplicationController extends Controller
{
    public function apply(JobApplicationRequest $request, $jobId)
    {
        $user = Auth::user();
        
        if ($user->role !== 'candidate') {
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

    public function getJobApplications($jobId)
    {
        $user = Auth::user();
        
        if ($user->role !== 'employer') {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }
        $job = Job::where('id', $jobId)
                  ->where('employer_id', $user->id)
                  ->firstOrFail();

        $applications = JobApplication::where('job_id', $job->id)
                        ->with(['user' => function($query) {
                            $query->select('id', 'name', 'email', 'position', 'experience', 'education', 'avatar');
                        }])
                        ->get();

        return response()->json($applications);
    }

    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        
        if ($user->role !== 'employer') {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $application = JobApplication::whereHas('job', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->findOrFail($id);

        $validStatuses = ['pending', 'shortlisted', 'accepted', 'rejected'];
        
        if (!in_array($request->status, $validStatuses)) {
            return response()->json(['message' => 'Invalid status.'], 422);
        }

        $application->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Application status updated successfully.',
            'application' => $application
        ]);
    }

    public function downloadResume($id)
    {
        $user = Auth::user();
        $application = JobApplication::with('job')->findOrFail($id);

        // Check if user is either the applicant or the employer
        if ($user->id !== $application->user_id && $user->id !== $application->job->employer_id) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        if (!$application->resume_path || !Storage::disk('public')->exists($application->resume_path)) {
            return response()->json(['message' => 'Resume not found.'], 404);
        }

        return Storage::disk('public')->download($application->resume_path);
    }

    protected function storeResume($file)
    {
        if (!$file) {
            return null;
        }

        return $file->store('resumes', 'public');
    }
}