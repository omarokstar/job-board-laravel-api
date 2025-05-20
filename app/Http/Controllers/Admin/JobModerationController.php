<?php

namespace App\Http\Controllers\Admin;

use App\Models\JobPost;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JobModerationController extends Controller
{
    public function index()
    {
        $pendingJobs = Job::where('status', 'pending')->get();
        $approvedJobs = Job::where('status', 'approved')->latest()->take(3)->get();
        $rejectedJobsCount = Job::where('status', 'rejected')->count();

        return response()->json([
            'pending_jobs' => $pendingJobs,
            'approved_jobs' => $approvedJobs,
            'pending_count' => $pendingJobs->count(),
            'approved_count' => Job::where('status', 'approved')->count(),
            'rejected_count' => $rejectedJobsCount,
        ], 200);
    }

    public function approve($id)
    {
        $job = Job::findOrFail($id);
        $job->status = 'approved';
        $job->save();

        return response()->json([
            'message' => 'Job approved successfully.',
            'job' => $job,
        ], 200);
    }

    public function reject($id)
    {
        $job = Job::findOrFail($id);
        $job->status = 'rejected';
        $job->save();

        return response()->json([
            'message' => 'Job rejected successfully.',
            'job' => $job,
        ], 200);
    }

    public function pending()
    {
        $pendingJobs = Job::where('status', 'pending')->get();

        return response()->json([
            'pending_jobs' => $pendingJobs,
        ], 200);
    }

    public function approved()
    {
        $approvedJobs = Job::where('status', 'approved')->get();

        return response()->json([
            'approved_jobs' => $approvedJobs,
        ], 200);
    }

    public function rejected()
    {
        $rejectedJobs = Job::where('status', 'rejected')->get();

        return response()->json([
            'rejected_jobs' => $rejectedJobs,
        ], 200);
    }

    public function show($id)
    {
        $job = Job::findOrFail($id);

        return response()->json([
            'job' => $job,
        ], 200);
    }
}