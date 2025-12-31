<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Events\PostCreate;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->get();
        return view('posts', compact('posts'));
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
        ]);

        $post = Post::create([
            'user_id' => auth()->id(),
            'title'   => $request->title,
            'body'    => $request->body,
        ]);

        event(new PostCreate($post));

        return back()->with('success', 'Post created successfully.');
    }
}
