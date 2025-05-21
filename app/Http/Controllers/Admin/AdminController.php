<?php

namespace App\Http\Controllers\Admin;

use App\Models\JobPost;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index()
{
    $pendingJobs = JobPost::where('status', 'pending')->get();
    $approvedJobs = JobPost::where('status', 'approved')->latest()->take(3)->get();
    $rejectedJobs = JobPost::where('status', 'rejected')->get(); // ✅ Define it here

    return response()->json([
        'pending_jobs' => $pendingJobs,
        'approved_jobs' => $approvedJobs,
        'rejected_jobs' => $rejectedJobs, // ✅ Now you can include it
        'pending_count' => $pendingJobs->count(),
        'approved_count' => JobPost::where('status', 'approved')->count(),
        'rejected_count' => $rejectedJobs->count(),
    ], 200);
}

    public function approve($id)
    {
        $job = JobPost::findOrFail($id);
        $job->status = 'approved';
        $job->save();

        return response()->json([
            'message' => 'Job approved successfully.',
            'job' => $job,
        ], 200);
    }

    public function reject($id)
    {
        $job = JobPost::findOrFail($id);
        $job->status = 'rejected';
        $job->save();

        return response()->json([
            'message' => 'Job rejected successfully.',
            'job' => $job,
        ], 200);
    }

    public function pending()
    {
        $pendingJobs = JobPost::where('status', 'pending')->get();

        return response()->json([
            'pending_jobs' => $pendingJobs,
        ], 200);
    }

    public function approved()
    {
        $approvedJobs = JobPost::where('status', 'approved')->get();

        return response()->json([
            'approved_jobs' => $approvedJobs,
        ], 200);
    }

   public function rejected()
{
    $rejectedJobs = JobPost::whereRaw('LOWER(status) = ?', ['rejected'])->get();

    return response()->json([
        'rejected_jobs' => $rejectedJobs,
        'count' => $rejectedJobs->count(),
    ], 200);
}

    public function show($id)
    {
        $job = JobPost::findOrFail($id);

        return response()->json([
            'job' => $job,
        ], 200);
    }
}