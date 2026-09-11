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
    /*
    |--------------------------------------------------------------------------
    | Posts List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $search = trim(
            (string) $request->input('search')
        );

        $sort = $request->input(
            'sort',
            'newest'
        );

        $posts = Post::with('user')
            ->search($search);

        switch ($sort) {

            case 'oldest':
                $posts->orderBy(
                    'created_at',
                    'asc'
                );
                break;

            case 'title_asc':
                $posts->orderBy(
                    'title',
                    'asc'
                );
                break;

            case 'title_desc':
                $posts->orderBy(
                    'title',
                    'desc'
                );
                break;

            default:
                $posts->latest();
                break;
        }

        $posts = $posts
            ->paginate(5)
            ->withQueryString();

        return view(
            'posts',
            compact(
                'posts',
                'search',
                'sort'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create Post
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        if (!auth()->check()) {
            abort(403);
        }

        $validated = $request->validate([
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

        $post = Post::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'body' => $validated['body'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Real-Time Post Event
        |--------------------------------------------------------------------------
        */

        event(
            new PostCreate($post)
        );

        /*
        |--------------------------------------------------------------------------
        | Create Notification For Admin Users
        |--------------------------------------------------------------------------
        */

        $admins = User::where(
            'is_admin',
            true
        )->get();

        foreach ($admins as $admin) {

            $notification = Notification::create([
                'user_id' => $admin->id,
                'post_id' => $post->id,
                'title' => 'New Post Created',
                'message' =>
                    "New post received: {$post->title}",
                'is_read' => false,
            ]);

            event(
                new NotificationCreated(
                    $notification
                )
            );
        }

        return back()->with(
            'success',
            'Post created successfully.'
        );
    }
}