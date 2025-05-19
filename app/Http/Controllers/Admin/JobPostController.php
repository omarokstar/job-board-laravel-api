<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Support\Facades\Log; // Optional for basic logging


class JobPostController extends Controller
{
    /**
     * List job posts with optional status filter
     */
    public function index(Request $request)
    {
        $request->validate([
            'status' => 'sometimes|in:pending,approved,rejected'
        ]);

        return Job::with('employer')
            ->when($request->status, fn($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(15);
    }

  
    public function show($id)
    {
        return Job::with(['employer'])->findOrFail($id);
    }

    /**
     * Approve a pending job post
     */
    public function approve($id)
    {
        $jobPost = Job::findOrFail($id);

        $this->validateJobPostStatus($jobPost);

        $jobPost->update([
            'status' => 'approved',
            'rejection_reason' => null, // Clear any previous rejection
        ]);

        Log::info("Job post {$id} approved by user " . auth()->id()); // Optional
        return response()->json(['message' => 'Job post approved successfully']);
    }

    /**
     * Reject a pending job post with a reason
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500'
        ]);

        $jobPost = Job::findOrFail($id);

        $this->validateJobPostStatus($jobPost);

        $jobPost->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        Log::info("Job post {$id} rejected. Reason: " . $request->rejection_reason); // Optional
        return response()->json(['message' => 'Job post rejected successfully']);
    }

    /**
     * Validate that the job post is pending (reusable method)
     */
    protected function validateJobPostStatus(Job $jobPost)
    {
        if (!$jobPost->isPending()) {
            abort(400, 'This job post has already been processed');
        }
    }
}