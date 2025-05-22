<?php

namespace App\Http\Controllers;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
public function latestJobs()
{
    $latestJobs = Job::where('status', 'approved')
                    ->orderBy('created_at', 'desc')
                    ->take(10) 
                    ->get();

    return response()->json([
        'jobs' => $latestJobs
    ]);
}










    public function index(Request $request)
    {
        $query =Job::where('status', 'approved');

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('company', 'like', '%' . $searchTerm . '%')
                  ->orWhere('location', 'like', '%' . $searchTerm . '%');
            });
        }


        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }


        if ($request->filled('job_type')) {
            $query->where('job_type', $request->job_type);
        }


        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }


        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }


        if ($request->filled('company')) {
            $query->where('company', 'like', '%' . $request->company . '%');
        }


        $jobs = $query->latest()->paginate(10);

        return response()->json($jobs);
    }

    public function show($id)
    {
        $job = Job::with('category')->find($id);

        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        return response()->json($job);
    }
        public function jobTypes()
        {
            $types = Job::select('job_type')
                ->distinct()
                ->pluck('job_type');

            return response()->json($types);
        }





    
}


