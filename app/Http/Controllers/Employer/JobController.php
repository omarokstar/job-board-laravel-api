<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Job; // Ensure this is present
use App\Models\Skill;
use App\Models\Tag;
use Illuminate\Validation\Rule;
// use Illuminate\Support\Facades\DB; // Uncomment for debugging if needed

class JobController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => [
                'required',
                Rule::in(Job::JOB_TYPES)
            ],
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:255',
            'salary_type' => ['required', Rule::in(['range', 'fixed'])],
            'min_salary' => 'nullable|numeric|required_if:salary_type,range',
            'max_salary' => 'nullable|numeric|gte:min_salary|required_if:salary_type,range',
            'education_level' => [
                'required',
                Rule::in(Job::EDUCATION_LEVELS)
            ],
            'experience_level' => [
                'required',
                Rule::in(Job::EXPERIENCE_LEVELS)
            ],
            'job_level' => [
                'required',
                Rule::in(Job::JOB_LEVELS)
            ],
            'description' => 'required|string|min:100',
            'responsibilities' => 'required|string|min:50',
            'company' => 'required|string|max:255',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:50',
            'skills' => 'required|array|min:3',
            'skills.*' => 'string|max:50'
        ]);

        $validated['user_id'] = Auth::id();


        $validated['job_type'] = $validated['type'];
        unset($validated['type']);

        if ($validated['salary_type'] === 'fixed') {
            $validated['min_salary'] = null;
            $validated['max_salary'] = null;
        } else {
            $validated['fixed_salary'] = null;
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
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'type' => [
                'sometimes',
                Rule::in(Job::JOB_TYPES) // Using constant from Job model
            ],
            'description' => 'sometimes|string',
            'responsibilities' => 'sometimes|string',
            'education_level' => [
                'sometimes',
                Rule::in(Job::EDUCATION_LEVELS) // Using constant from Job model
            ],
            'experience_level' => [
                'sometimes',
                Rule::in(Job::EXPERIENCE_LEVELS) // Using constant from Job model
            ],
            'job_level' => [
                'sometimes',
                Rule::in(Job::JOB_LEVELS) // Using constant from Job model
            ],
            'location' => 'sometimes|string',
            'keywords' => 'nullable|string',
            'status' => [
                'sometimes',
                Rule::in([Job::STATUS_PENDING, Job::STATUS_ACTIVE, Job::STATUS_CLOSED]), // Using constants
            ],
            'skills' => 'sometimes|array',
            'skills.*' => 'string|max:50',
            'salary_type' => 'sometimes|in:fixed,range',
            'min_salary' => 'nullable|numeric',
            'max_salary' => 'nullable|numeric|gte:min_salary', // Keep gte for range if you want
            'category_id' => 'sometimes|exists:categories,id'
        ]);

        $job = Job::findOrFail($id);

        if ($job->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (isset($validated['type'])) {
            $validated['job_type'] = $validated['type'];
            unset($validated['type']);
        }
        
        $job->update($validated);

        if ($request->has('skills')) {
            $skillIds = [];
            foreach ($request->skills as $skillName) {
                $skill = Skill::firstOrCreate(['name' => $skillName]);
                $skillIds[] = $skill->id;
            }
            $job->skills()->sync($skillIds);
        }

        return response()->json([
            'success' => true,
            'data' => $job->load('skills'),
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
            'status' => Job::STATUS_EXPIRED, // Using the model constant directly
            'expiry_date' => now() // Fixed to use 'expiry_date' as per your Job model
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
            ->with(['skills'])
            ->latest()
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'job_type' => $job->job_type,
                    'status' => $job->status, // This uses the accessor (expired -> closed)
                    'applications_count' => $job->applications_count,
                    'end_date' => $job->expiry_date, // Map expiry_date to end_date for frontend
                    'created_at' => $job->created_at,
                    'location' => $job->location,
                    'education_level' => $job->education_level,
                    'experience_level' => $job->experience_level,
                    'job_level' => $job->job_level,
                    'description' => $job->description,
                    'responsibilities' => $job->responsibilities,
                    'keywords' => $job->keywords,
                    'skills' => $job->skills,
                    'salary_type' => $job->salary_type,
                    'fixed_salary' => $job->fixed_salary,
                    'min_salary' => $job->min_salary,
                    'max_salary' => $job->max_salary
                ];
            });

        return response()->json($jobs);
    }

    public function employerJobs()
    {
        try {
            \Log::info('Fetching jobs for user: ' . auth()->id());
            $jobs = auth()->user()->jobs()
                ->withCount('applications')
                ->get();
            \Log::info('Jobs found:', $jobs->toArray());
            return response()->json($jobs);
        } catch (\Exception $e) {
            \Log::error('Error in employerJobs: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }
}
