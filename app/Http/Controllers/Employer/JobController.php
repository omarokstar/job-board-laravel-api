<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use App\Models\Job;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    // List jobs with filters
    public function index(Request $request)
    {
        $query = Job::with(['category', 'tags'])
            ->where('status', 'published');

        $this->applyFilters($query, $request);

        return response()->json($query->latest()->paginate(10));
    }

    // Show a single job
    public function show($id)
    {
        $job = Job::with(['category', 'tags', 'skills'])->find($id);

        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        return response()->json($job);
    }

    // Create a new job (no user_id)
    public function store(Request $request)
    {
        $validated = $this->validateJobRequest($request);

        $job = null;
        DB::transaction(function () use (&$job, $validated, $request) {
            $job = Job::create($validated + [
                'status' => 'draft'
            ]);

            // Sync tags
            if ($request->filled('tags')) {
                $tagIds = Tag::firstOrCreateMany($request->tags);
                $job->tags()->sync($tagIds);
            }

            // Sync skills
            if ($request->filled('skills')) {
                $job->skills()->createMany(
                    collect($request->skills)->map(fn($skill) => ['name' => $skill])
                );
            }
        });

        return response()->json($job, 201);
    }

    // Update a job
    public function update(Request $request, $id)
    {
        $job = Job::findOrFail($id);

        $validated = $this->validateJobRequest($request);

        DB::transaction(function () use ($job, $validated, $request) {
            $job->update($validated);

            // Sync tags
            if ($request->filled('tags')) {
                $tagIds = Tag::firstOrCreateMany($request->tags);
                $job->tags()->sync($tagIds);
            }

            // Sync skills
            if ($request->filled('skills')) {
                $job->skills()->delete();
                $job->skills()->createMany(
                    collect($request->skills)->map(fn($skill) => ['name' => $skill])
                );
            }
        });

        return response()->json($job);
    }

    // Delete a job
    public function destroy($id)
    {
        $job = Job::findOrFail($id);
        $job->delete();
        return response()->json(['message' => 'Job deleted successfully']);
    }

    // --- Helper methods ---

    protected function applyFilters($query, $request)
    {
        $filters = [
            'search' => fn($term) => $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%$term%")
                  ->orWhere('description', 'like', "%$term%")
                  ->orWhere('company', 'like', "%$term%")
                  ->orWhere('location', 'like', "%$term%");
            }),
            'title' => fn($term) => $query->where('title', 'like', "%$term%"),
            'job_type' => fn($type) => $query->where('job_type', $type),
            'category_id' => fn($id) => $query->where('category_id', $id),
            'location' => fn($loc) => $query->where('location', 'like', "%$loc%"),
            'company' => fn($comp) => $query->where('company', 'like', "%$comp%"),
            'status' => fn($status) => $query->where('status', $status),
            'skills' => fn($skills) => $query->whereHas('skills', function($q) use ($skills) {
                $q->whereIn('name', (array)$skills);
            }),
            'tags' => fn($tags) => $query->whereHas('tags', function($q) use ($tags) {
                $q->whereIn('name', (array)$tags);
            })
        ];

        foreach ($filters as $key => $filter) {
            if ($request->filled($key)) {
                $filter($request->input($key));
            }
        }
    }

    protected function validateJobRequest(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'job_type' => ['required', Rule::in(['full-time', 'part-time', 'contract', 'freelance', 'internship'])],
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:255',
            'salary_type' => ['required', Rule::in(['range', 'fixed'])],
            'min_salary' => 'required_if:salary_type,range|numeric|min:0',
            'max_salary' => 'required_if:salary_type,range|numeric|gt:min_salary',
            'salary' => 'required_if:salary_type,fixed|numeric|min:0',
            'education_level' => ['required', Rule::in(['high_school', 'bachelor', 'master', 'phd'])],
            'experience_level' => ['required', Rule::in(['entry', 'mid', 'senior'])],
            'job_level' => ['required', Rule::in(['junior', 'mid', 'senior'])],
            'description' => 'required|string|min:100',
            'responsibilities' => 'required|string|min:50',
            'company' => 'required|string|max:255',
            'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])],
=======
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
>>>>>>> d2dfc3eb8fcf1dd1cfc5cd2938b9f11555759f29
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:50',
            'skills' => 'required|array|min:3',
            'skills.*' => 'string|max:50'
        ]);
<<<<<<< HEAD
=======

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
>>>>>>> d2dfc3eb8fcf1dd1cfc5cd2938b9f11555759f29
    }
}
