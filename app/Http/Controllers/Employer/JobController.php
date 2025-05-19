<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:50',
            'skills' => 'required|array|min:3',
            'skills.*' => 'string|max:50'
        ]);
    }
}