<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;

class BlogController extends Controller
{
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
        $blog = Blog::findOrFail($id);
        return response()->json($this->formatBlog($blog));
    }

    protected function formatBlog(Blog $blog)
    {
        return [
            'id' => $blog->id,
            'title' => $blog->title,
            'date' => $blog->date ?? $blog->created_at->format('F d, Y'),
            'author' => $blog->author,
            'comments' => method_exists($blog, 'comments') ? $blog->comments()->count() : 0,
            'image' => $blog->image,
            'category' => $blog->category,
            'tags' => $blog->tags ?? [],
        ];
    }
} 