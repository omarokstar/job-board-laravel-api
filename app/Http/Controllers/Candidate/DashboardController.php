<?php

namespace App\Http\Controllers\Candidate;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => $user,
            'applied_jobs_count' => $user->appliedJobs()->count(),
            'recent_applied_jobs' => $user->appliedJobs()
                ->latest('pivot_created_at')
                ->take(5)
                ->get()
                ->map(function($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'location' => $job->location,
                        'salary' => $job->salary,
                        'applied_at' => $job->pivot->created_at,
                        'status' => $job->pivot->status,
                    ];
                }),
        ]);
    }
}