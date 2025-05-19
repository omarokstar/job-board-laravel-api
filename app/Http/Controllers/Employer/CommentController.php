<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Blog;

class CommentController extends Controller
{
    public function store(Request $request, $blogId)
    {
        $blog = Blog::findOrFail($blogId);
        $validated = $request->validate([
            'author' => 'nullable|string|max:100',
            'content' => 'required|string|min:2',
        ]);
        $validated['author'] = $validated['author'] ?? 'Anonymous';
        $comment = $blog->comments()->create($validated);
        return response()->json($comment, 201);
    }
} 