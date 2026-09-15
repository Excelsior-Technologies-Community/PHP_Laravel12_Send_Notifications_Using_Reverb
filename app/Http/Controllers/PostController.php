<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreated;
use App\Events\PostCreate;
use App\Events\PostDelete;
use App\Events\PostUpdate;
use App\Events\StatsUpdated;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        $category = $request->input('category');

        $posts = Post::with('user')
            ->search($search)
            ->category($category);

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

        $categories = Post::categories();

        return view(
            'posts',
            compact(
                'posts',
                'search',
                'sort',
                'category',
                'categories'
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
        if (! auth()->check()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'category' => [
                'nullable',
                'string',
                'max:100',
            ],
            'category_new' => [
                'nullable',
                'string',
                'max:100',
            ],
            'body' => [
                'required',
                'string',
                'max:5000',
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048',
            ],
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $finalCategory = $validated['category_new']
            ? $validated['category_new']
            : $validated['category'];

        $post = Post::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'category' => $finalCategory,
            'body' => $validated['body'],
            'image' => $imagePath,
        ]);

        /*
        |----------------------------------------------------------------------
        | Real-Time Post Event
        |----------------------------------------------------------------------
        */

        event(
            new PostCreate($post)
        );

        /*
        |----------------------------------------------------------------------
        | Broadcast live stats update
        |----------------------------------------------------------------------
        */

        event(new StatsUpdated(
            $this->buildStats()
        ));

        /*
        |----------------------------------------------------------------------
        | Create Notification For Admin Users
        |----------------------------------------------------------------------
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
                'message' => "New post received: {$post->title}",
                'is_read' => false,
            ]);

            event(
                new NotificationCreated(
                    $notification
                )
            );

            event(new StatsUpdated(
                $this->buildStats()
            ));
        }

        return back()->with(
            'success',
            'Post created successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Post
    |--------------------------------------------------------------------------
    */

    public function edit(Post $post)
    {
        abort_unless(
            $post->user_id === auth()->id() || auth()->user()->is_admin,
            403
        );

        $categories = Post::categories();

        return view(
            'posts.edit',
            compact('post', 'categories')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Post
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Post $post)
    {
        abort_unless(
            $post->user_id === auth()->id() || auth()->user()->is_admin,
            403
        );

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'category' => [
                'nullable',
                'string',
                'max:100',
            ],
            'body' => [
                'required',
                'string',
                'max:5000',
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048',
            ],
            'remove_image' => [
                'sometimes',
                'boolean',
            ],
        ]);

        $imagePath = $post->image;

        if ($request->boolean('remove_image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $imagePath = null;
        }

        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $post->update([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'body' => $validated['body'],
            'image' => $imagePath,
        ]);

        event(new PostUpdate($post));

        event(new StatsUpdated(
            $this->buildStats()
        ));

        return back()->with(
            'success',
            'Post updated successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Post
    |--------------------------------------------------------------------------
    */

    public function destroy(Post $post)
    {
        abort_unless(
            $post->user_id === auth()->id() || auth()->user()->is_admin,
            403
        );

        $postData = $post->only(['id', 'title']);

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        event(new PostDelete($post));

        event(new StatsUpdated(
            $this->buildStats()
        ));

        return back()->with(
            'success',
            "Post '{$postData['title']}' deleted successfully."
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function buildStats(): array
    {
        return [
            'total_posts' => Post::count(),
            'total_notifications' => Notification::count(),
            'unread_notifications' => Notification::where('is_read', false)->count(),
            'read_notifications' => Notification::where('is_read', true)->count(),
            'today_posts' => Post::whereDate('created_at', today())->count(),
            'today_notifications' => Notification::whereDate('created_at', today())->count(),
            'last_24_hours' => Notification::where('created_at', '>=', now()->subDay())->count(),
            'last_7_days' => Notification::where('created_at', '>=', now()->subDays(7))->count(),
            'read_percentage' => Notification::where('is_read', true)->count() > 0
                ? round((Notification::where('is_read', true)->count() / max(Notification::count(), 1)) * 100, 1)
                : 0,
        ];
    }
}
