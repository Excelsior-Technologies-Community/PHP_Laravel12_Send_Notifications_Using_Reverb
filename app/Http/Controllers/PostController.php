<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreated;
use App\Events\PostCreate;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display posts.
     */
    public function index()
    {
        $posts = Post::with('user')
            ->latest()
            ->get();

        $unreadCount = 0;

        if (auth()->check()) {
            $unreadCount = Notification::where('user_id', auth()->id())
                ->where('is_read', false)
                ->count();
        }

        return view('posts', compact(
            'posts',
            'unreadCount'
        ));
    }

    /**
     * Store a new post.
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Validate
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'body' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Create Post
        |--------------------------------------------------------------------------
        */

        $post = Post::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'body' => $request->body,
        ]);

        $post->load('user');

        /*
        |--------------------------------------------------------------------------
        | Create Notification For Every Admin
        |--------------------------------------------------------------------------
        */

        $admins = User::where('is_admin', true)->get();

        foreach ($admins as $admin) {

            $notification = Notification::create([
                'user_id' => $admin->id,

                'post_id' => $post->id,

                'title' => 'New Post Created',

                'message' => auth()->user()->name .
                    " created a new post: {$post->title}",

                'is_read' => false,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Broadcast Notification
            |--------------------------------------------------------------------------
            */

            event(new NotificationCreated($notification));
        }

        /*
        |--------------------------------------------------------------------------
        | Broadcast New Post
        |--------------------------------------------------------------------------
        */

        event(new PostCreate($post));

        return back()->with(
            'success',
            'Post created successfully and real-time notification sent.'
        );
    }
}