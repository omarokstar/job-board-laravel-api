<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobApplicationRequest;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class JobApplicationController extends Controller
{
  
public function apply(JobApplicationRequest $request, $jobId)
{
        $user = $request->user(); 
      if (!$user->subscribed('default')) {
        if ($user->appliedJobs()->count() >= 3) {
            return response()->json(['message' => 'Upgrade to premium to apply to more jobs'], 403);
        }
    }

    $job = Job::findOrFail($jobId);

    $alreadyApplied = JobApplication::where('user_id', $user->id)
        ->where('job_id', $jobId)
        ->exists();

    if ($alreadyApplied) {
        return response()->json(['message' => 'You have already applied for this job.'], 409);
    }

    $application = JobApplication::create([
        'user_id' => $user->id,
        'job_id' => $jobId,
        'cover_letter' => $request->cover_letter,
        'resume_path' => $request->resume_path,
    ]);

    return response()->json([
        'message' => 'Application submitted successfully.',
        'application' => $application,
    ], 201);
}





    public function getJobApplications($jobId)
    {
        $user = Auth::user();
        
        if ($user->role !== 'employer') {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }
        $job = Job::where('id', $jobId)
                  ->where('user_id', $user->id)
                  ->firstOrFail();

        $applications = JobApplication::where('job_id', $job->id)
                        ->with(['user' => function($query) {
                            $query->select('id', 'name', 'email');
                        }])
                        ->get();

        return response()->json($applications);
    }

    public function updateStatus(Request $request, $id) 
    {
        try {
            $application = JobApplication::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Application not found.'], 404);
        }
    
        $user = Auth::user();
    
        if ($user->id !== $application->job->user_id) {
            return response()->json(['message' => 'Unauthorized to update this application.'], 403);
        }
    
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['pending', 'reviewed', 'accepted', 'rejected'])],
        ]);
    
        try {
            $application->status = $validated['status'];
            $application->save();
    
            return response()->json([
                'message' => 'Application status updated successfully.',
                'application' => $application->fresh()
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to update application status: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update application status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    protected function storeResume($file)
    {
        if (!$file) {
            return null;
        }

        return $file->store('resumes', 'public');
    }
}
