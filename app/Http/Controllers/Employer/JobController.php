<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;
use App\Models\Skill;
use App\Models\Tag;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => [
                'required', 
                Rule::in(['full-time', 'part-time', 'contract', 'freelance', 'internship'])
            ],
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:255',
            'salary_type' => [
                'required', 
                Rule::in(['range', 'fixed'])
            ],
            'min_salary' => 'required_if:salary_type,range|numeric|min:0',
            'max_salary' => 'required_if:salary_type,range|numeric|gt:min_salary',
            'salary' => 'required_if:salary_type,fixed|numeric|min:0',
            'education_level' => [
                'required', 
                Rule::in(['high_school', 'bachelor', 'master', 'phd'])
            ],
            'experience_level' => [
                'required', 
                Rule::in(['entry', 'mid', 'senior'])
            ],
            'job_level' => [
                'required', 
                Rule::in(['junior', 'mid', 'senior'])
            ],
            'description' => 'required|string|min:100',
            'responsibilities' => 'required|string|min:50',
            'company' => 'required|string|max:255',
            'status' => [
                'sometimes', 
                Rule::in(['draft', 'published', 'archived'])
            ],
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:50',
            'skills' => 'required|array|min:3',
            'skills.*' => 'string|max:50'
        ]);

        $validated['user_id'] = Auth::id();
        
        // Ensure description meets minimum length
        if (strlen($validated['description']) < 100) {
            return response()->json([
                'message' => 'Description must be at least 100 characters',
                'errors' => [
                    'description' => ['The description field must be at least 100 characters.']
                ]
            ], 422);
        }

        if (strlen($validated['responsibilities']) < 50) {
            return response()->json([
                'message' => 'Responsibilities must be at least 50 characters',
                'errors' => [
                    'responsibilities' => ['The responsibilities field must be at least 50 characters.']
                ]
            ], 422);
        }

        $job = Job::create($validated);

        $this->syncSkills($job, $request->skills);

        if ($request->has('tags')) {
            $this->syncTags($job, $request->tags);
        }

        return response()->json([
            'message' => 'Job created successfully',
            'job' => $job->load(['skills', 'tags'])
        ], 201);
    }


    protected function syncSkills(Job $job, array $skills)
    {
        $skillIds = [];
        
        foreach ($skills as $skillName) {
            $skill = Skill::firstOrCreate(['name' => $skillName]);
            $skillIds[] = $skill->id;
        }

        $job->skills()->sync($skillIds);
    }

    protected function syncTags(Job $job, array $tags)
    {
        $tagIds = [];
        
        foreach ($tags as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $tagIds[] = $tag->id;
        }

        $job->tags()->sync($tagIds);
    }

    public function show(Job $job)
    {
        return response()->json([
            'job' => $job->load(['skills', 'tags'])
        ]);
    }

public function update(Request $request, $id)
{
    // Validate the request
    $validatedData = $request->validate([
        'title' => 'string|max:255',
        'job_type' => 'string|in:Fulltime,Parttime,Contract,Temporary,Internship',
        'description' => 'string',
        'responsibilities' => 'string',
        'education_level' => 'string',
        'experience_level' => 'string',
        'job_level' => 'string',
        'location' => 'string',
        'keywords' => 'nullable|string',
        'status' => 'in:Active,Expired',
        'skills' => 'array',
        'salary_type' => 'in:fixed,range',
        'fixed_salary' => 'if:salary_type,fixed|numeric',
        'min_salary' => 'if:salary_type,range|numeric',
        'max_salary' => 'if:salary_type,range|numeric|gt:min_salary',
        'category_id' => 'exists:categories,id'
    ]);

    // Find the job
    $job = Job::findOrFail($id);
    
    // Authorization check
    if ($job->user_id !== auth()->id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $job->update($validated);

    return response()->json([
        'success' => true,
        'data' => $job,
        'message' => 'Job updated successfully'
    ]);
}

public function expire(Request $request, $id)
{
    $job = Job::findOrFail($id);
    
    if ($job->user_id !== auth()->id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $job->update([
        'status' => 'Expired',
        'end_date' => now()
    ]);

    return response()->json([
        'success' => true,
        'data' => $job,
        'message' => 'Job expired successfully'
    ]);
}

    public function destroy(Job $job)
    {
        if ($job->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $job->delete();

        return response()->json(['message' => 'Job deleted successfully']);
    }
    public function index()
    {
        $jobs = Job::where('user_id', Auth::id())
            ->withCount('applications')
            ->latest()
            ->get()
            ->map(function ($job) {
                $job->status = $job->isExpired() ? 'closed' : $job->status;
                return $job;
            });

        return response()->json($jobs);
    }
    public function employerJobs()
{
    try {
        \Log::info('Fetching jobs for user: '.auth()->id());
        
        $jobs = auth()->user()->jobs()
                   ->withCount('applications')
                   ->get();
        
        \Log::info('Jobs found:', $jobs->toArray());
        
        return response()->json($jobs);
    } catch (\Exception $e) {
        \Log::error('Error in employerJobs: '.$e->getMessage());
        return response()->json([], 500);
    }
}
}