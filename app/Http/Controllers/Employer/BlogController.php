<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::withCount('comments')->get()->map(fn($blog) => $this->formatBlog($blog));
        return response()->json($blogs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|string',
            'category' => 'required|string|max:100',
            'author' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        $validated['author'] = $validated['author'] ?? 'Admin';
        $validated['date'] = now()->format('F d, Y');

        $blog = Blog::create($validated);

        return response()->json($this->formatBlog($blog), 201);
    }

    public function show($id)
    {
        $blog = Blog::withCount('comments')->findOrFail($id);
        return response()->json($this->formatBlog($blog));
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'image' => 'nullable|string',
            'category' => 'sometimes|string|max:100',
            'author' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);
        $blog->update($validated);
        return response()->json($this->formatBlog($blog));
    }

    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        $blog->delete();
        return response()->json(['message' => 'Blog deleted successfully']);
    }

    protected function formatBlog(Blog $blog)
    {
        return [
            'id' => $blog->id,
            'title' => $blog->title,
            'date' => $blog->date ?? $blog->created_at->format('F d, Y'),
            'author' => $blog->author,
            'comments' => $blog->comments_count ?? $blog->comments()->count(),
            'image' => $blog->image,
            'category' => $blog->category,
            'tags' => $blog->tags ?? [],
        ];
    }
} 