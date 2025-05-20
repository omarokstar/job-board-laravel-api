<?php
namespace App\Http\Controllers\Candidate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\JobApplicationRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;
use App\Models\JobApplication;
class JobApplicationController extends Controller
{

public function apply(JobApplicationRequest $request, $jobId)
{
    $user = Auth::user(); 

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




public function getApplications()
{
    $user = Auth::user();

   
    $applications = JobApplication::with('job')
        ->where('user_id', $user->id)
        ->get();

    return response()->json(['applications' => $applications], 200);
}



    }
     


