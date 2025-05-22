<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;

class EmployerDashboardController extends Controller
{
    public function overview(Request $request)
    {
        $user = $request->user();

        // Statistics
        $stats = [
            'totalJobs' => Job::where('user_id', $user->id)->count(),
            'activeJobs' => Job::where('user_id', $user->id)
                            ->where('status', 'approved')
                            ->count(),
            'totalApplications' => JobApplication::whereHas('job', function($q) use ($user) {
                                    $q->where('user_id', $user->id);
                                })->count(),
            'newCandidates' => User::whereHas('appliedJobs', function($q) use ($user) {
                                    $q->where('jobs.user_id', $user->id);
                                })->where('users.created_at', '>=', now()->subDays(30))
                                  ->count()
        ];

        // Recent Jobs
        $recentJobs = Job::where('user_id', $user->id)
                        ->withCount('applications')
                        ->latest()
                        ->take(5)
                        ->get();

        // Recent Applications
        $recentApplications = JobApplication::with(['job', 'user'])
                                ->whereHas('job', fn($q) => $q->where('user_id', $user->id))
                                ->latest()
                                ->take(5)
                                ->get();

        return response()->json([
            'stats' => $stats,
            'recent_jobs' => $recentJobs,
            'recent_applications' => $recentApplications
        ]);
    }
}