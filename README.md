# PHP Laravel 12 – Send Real-Time Notifications Using Reverb

##  Overview

This project is a complete example of **real-time notifications in Laravel 12**
using **Reverb**, **Redis**, **Queues**, and **Laravel Echo**.

In this system:
- A **Normal User** can create posts
- An **Admin User** receives **real-time notifications instantly**
- No page refresh is required
- Communication happens through **WebSockets (Reverb)**

This project is ideal for learning:
- Laravel Broadcasting
- Real-time event handling
- Redis queues
- Admin notification systems

---

##  Features

- Laravel 12 Authentication (Login / Register)
- Admin & Normal User role separation
- Post creation module
- Real-time notifications using Laravel Reverb
- WebSocket-based broadcasting
- Redis used for Queue, Cache, and Session
- Queue-based event broadcasting (ShouldQueue)
- Laravel Echo frontend listener
- Clean MVC architecture
- Windows Redis support
- Production-ready structure

---

##  Folder Structure Overview

```text
app/
├── Events/
│   └── PostCreate.php          # Broadcast event for new post
│
├── Http/
│   └── Controllers/
│       └── PostController.php  # Handles post logic
│
├── Models/
│   ├── User.php                # User model with admin role
│   └── Post.php                # Post model
│
config/
├── broadcasting.php            # Reverb configuration
├── database.php                # Database configuration
│
database/
├── migrations/
│   ├── xxxx_add_is_admin_to_users_table.php
│   └── xxxx_create_posts_table.php
│
├── seeders/
│   └── CreateAdminUser.php     # Admin user seeder
│
resources/
├── js/
│   └── bootstrap.js            # Laravel Echo + Reverb setup
│
├── views/
│   └── posts.blade.php         # UI + notification listener
│
routes/
├── web.php                     # Web routes
└── channels.php                # Broadcast channels
```

---

## STEP 1: PROJECT CREATE

```bash
composer create-project laravel/laravel example-app
```

---

## STEP 2: AUTH (LOGIN / REGISTER)

```bash
composer require laravel/ui
php artisan ui bootstrap --auth
npm install
npm run dev
npm run build
```

---

## STEP 3: DATABASE CREATE

```sql
CREATE DATABASE notification;
```

---

## STEP 4: .env (FULL FILE)

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:8t4GcI1BSeruwfoUWqT9V5cYzbyGqC1IFeIOxGiomTM=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=notification
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null

BROADCAST_CONNECTION=reverb

REVERB_APP_ID=774534
REVERB_APP_KEY=zteyhtq4lkjb8iztgd01
REVERB_APP_SECRET=jxiwvjj85sdghqm6lmbp
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

```bash
php artisan optimize:clear
```

---

## STEP 5: USERS TABLE → ADMIN COLUMN

```bash
php artisan make:migration add_is_admin_to_users_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('is_admin')->default(0);
        });
    }

    public function down(): void
    {
        //
    }
};
```

### app/Models/User.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

---

## STEP 6: POST MODEL + MIGRATION

```bash
php artisan make:model Post -m
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
```

```bash
php artisan migrate
```

### app/Models/Post.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'body', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

---

## STEP 7: CONTROLLER

```bash
php artisan make:controller PostController
```

```php
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
```

---

## STEP 8: EVENT (REAL-TIME)

```bash
php artisan make:event PostCreate
```

```php
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostCreate implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, SerializesModels;

    public $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('posts');
    }

    public function broadcastAs(): string
    {
        return 'create';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => "[{$this->post->created_at}] New Post Received with title '{$this->post->title}'."
        ];
    }
}
```

---

## STEP 9: REVERB INSTALL

```bash
php artisan reverb:install
php artisan install:broadcasting
```
config/broadcasting.php
```
<?php

return [

    'default' => env('BROADCAST_CONNECTION', 'null'),

    'connections' => [

        'reverb' => [
            'driver' => 'reverb',
            'key' => env('REVERB_APP_KEY'),
            'secret' => env('REVERB_APP_SECRET'),
            'app_id' => env('REVERB_APP_ID'),
            'options' => [
                'host' => env('REVERB_HOST'),
                'port' => env('REVERB_PORT', 8080),
                'scheme' => env('REVERB_SCHEME', 'http'),
                'useTLS' => env('REVERB_SCHEME', 'http') === 'https',
            ],
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],
    ],
];
```

config/database.php (IMPORTANT PART)
```
'default' => env('DB_CONNECTION', 'mysql'),
```
## STEP 10: ROUTES

### routes/web.php

```php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
```

### routes/channels.php

```php
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('posts', function () {
    return true;
});
```

---

## STEP 11: FRONTEND (ECHO)

resources/js/bootstrap.js
```js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});
```

---

## STEP 12: BLADE VIEW

resources/views/posts.blade.php
```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <i class="fa fa-list"></i> Posts List
        </div>

        <div class="card-body">

            @session('success')
                <div class="alert alert-success">{{ $value }}</div>
            @endsession

            <div id="notification"></div>

            @auth
                @if(!auth()->user()->is_admin)
                    <p><strong>Create New Post</strong></p>

                    <form method="POST" action="{{ route('posts.store') }}">
                        @csrf

                        <div class="mb-2">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control">
                        </div>

                        <div class="mb-2">
                            <label>Body</label>
                            <textarea name="body" class="form-control"></textarea>
                        </div>

                        <button class="btn btn-success mt-2">Submit</button>
                    </form>
                @endif
            @endauth

            <hr>

            <table class="table table-bordered">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Body</th>
                </tr>

                @foreach($posts as $post)
                    <tr>
                        <td>{{ $post->id }}</td>
                        <td>{{ $post->title }}</td>
                        <td>{{ $post->body }}</td>
                    </tr>
                @endforeach
            </table>

        </div>
    </div>
</div>
@endsection

@section('script')
@auth
    @if(auth()->user()->is_admin)
        <script type="module">
            window.Echo.channel('posts')
                .listen('.create', (data) => {
                    document.getElementById('notification')
                        .insertAdjacentHTML(
                            'beforeend',
                            `<div class="alert alert-success">${data.message}</div>`
                        );
                });
        </script>
    @endif
@endauth
@endsection
```

---

## STEP 13: REDIS (WINDOWS)

```bash
redis-server redis.windows.conf
redis-cli ping
```

Output:

```
PONG
```
<img width="675" height="241" alt="image" src="https://github.com/user-attachments/assets/b6a37faf-99d5-4b97-b3ad-49e44a1a4b3e" />

---

## STEP 14: CREATE ADMIN USER

```bash
php artisan make:seeder CreateAdminUser
```

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class CreateAdminUser extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456'),
            'is_admin' => 1
        ]);
    }
}
```

```bash
php artisan db:seed --class=CreateAdminUser
```

---

## STEP 15: RUN ORDER (IMPORTANT)

```bash
redis-server redis.windows.conf
php artisan serve
npm run dev
php artisan reverb:start
php artisan queue:work
```

---

## DONE

- Register or log in as a normal user
- Create a new post
- Log in as an admin user in another browser
- Receive real-time notifications without refreshing the page


## OUTPUT:-

<img width="1915" height="1030" alt="Screenshot 2025-12-31 160200" src="https://github.com/user-attachments/assets/c4c543d1-93fe-4eb9-beec-81f6f6d22527" />


ADMIN USER:-

<img width="945" height="363" alt="Screenshot 2025-12-31 160214" src="https://github.com/user-attachments/assets/c67dd3c3-b3ff-4aae-8e42-d89a06b440f9" />

NORMAL USER:-

<img width="1665" height="617" alt="Screenshot 2025-12-31 165851" src="https://github.com/user-attachments/assets/242046ac-b731-4263-97b6-807eab84f8ef" />
