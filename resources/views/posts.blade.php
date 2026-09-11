@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row justify-content-center">

        <div class="col-lg-11">

            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white">

                    <div class="d-flex justify-content-between align-items-center">

                        <span>

                            <i class="fa fa-list"></i>

                            Posts List

                        </span>

                        <span>

                            <i class="fa fa-bolt"></i>

                            Live Reverb Feed

                        </span>

                    </div>

                </div>

                <div class="card-body">

                    @if(session('success'))

                        <div class="alert alert-success">

                            <i class="fa fa-check-circle"></i>

                            {{ session('success') }}

                        </div>

                    @endif

                    @if($errors->any())

                        <div class="alert alert-danger">

                            <strong>
                                Please fix the following errors:
                            </strong>

                            <ul class="mb-0 mt-2">

                                @foreach($errors->all() as $error)

                                    <li>
                                        {{ $error }}
                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endif

                    @auth

                        @if(!auth()->user()->is_admin)

                            <div class="card mb-4 border-success">

                                <div class="card-header bg-success text-white">

                                    <i class="fa fa-plus-circle"></i>

                                    Create New Post

                                </div>

                                <div class="card-body">

                                    <form
                                        method="POST"
                                        action="{{ route('posts.store') }}"
                                    >

                                        @csrf

                                        <div class="mb-3">

                                            <label
                                                for="title"
                                                class="form-label"
                                            >
                                                Title
                                            </label>

                                            <input
                                                id="title"
                                                type="text"
                                                name="title"
                                                class="form-control"
                                                placeholder="Enter post title"
                                                value="{{ old('title') }}"
                                                required
                                            >

                                        </div>

                                        <div class="mb-3">

                                            <label
                                                for="body"
                                                class="form-label"
                                            >
                                                Body
                                            </label>

                                            <textarea
                                                id="body"
                                                name="body"
                                                class="form-control"
                                                rows="4"
                                                placeholder="Enter post content"
                                                required
                                            >{{ old('body') }}</textarea>

                                        </div>

                                        <button
                                            type="submit"
                                            class="btn btn-success"
                                        >

                                            <i class="fa fa-paper-plane"></i>

                                            Create Post

                                        </button>

                                    </form>

                                </div>

                            </div>

                        @else

                            <div class="alert alert-info">

                                <i class="fa fa-user-shield"></i>

                                You are logged in as an administrator.
                                New posts will appear here automatically
                                through Laravel Reverb.

                            </div>

                        @endif

                    @endauth

                    @guest

                        <div class="alert alert-info">

                            <i class="fa fa-info-circle"></i>

                            Login to create a post.

                        </div>

                    @endguest

                    <!-- Real-Time Event Information -->

                    <div
                        id="realtime-event"
                        class="alert alert-light border"
                    >

                        <i class="fa fa-bolt text-warning"></i>

                        Waiting for real-time post events...

                    </div>

                    <!-- Posts Table -->

                    <div class="table-responsive">

                        <table
                            class="table table-bordered table-hover align-middle"
                            id="posts-table"
                        >

                            <thead class="table-dark">

                                <tr>

                                    <th width="80">
                                        ID
                                    </th>

                                    <th>
                                        Title
                                    </th>

                                    <th>
                                        Body
                                    </th>

                                    <th>
                                        Created By
                                    </th>

                                    <th width="180">
                                        Created At
                                    </th>

                                </tr>

                            </thead>

                            <tbody id="posts-table-body">

                                @forelse($posts as $post)

                                    <tr
                                        data-post-id="{{ $post->id }}"
                                    >

                                        <td>

                                            #{{ $post->id }}

                                        </td>

                                        <td>

                                            <strong>
                                                {{ $post->title }}
                                            </strong>

                                        </td>

                                        <td>

                                            {{ $post->body }}

                                        </td>

                                        <td>

                                            <span class="badge bg-secondary">

                                                {{ $post->user?->name ?? 'Unknown' }}

                                            </span>

                                        </td>

                                        <td>

                                            {{ $post->created_at->format('d M Y, h:i A') }}

                                        </td>

                                    </tr>

                                @empty

                                    <tr id="no-posts-row">

                                        <td
                                            colspan="5"
                                            class="text-center text-muted py-4"
                                        >

                                            No posts found.

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection


@section('script')

<script type="module">

document.addEventListener('DOMContentLoaded', function () {

    @auth

    /*
    |--------------------------------------------------------------------------
    | Listen For New Posts Through Reverb
    |--------------------------------------------------------------------------
    */

    if (!window.Echo) {

        console.error(
            'Laravel Echo is not available.'
        );

        return;

    }

    window.Echo
        .channel('posts')
        .listen(
            '.create',
            function (data) {

                console.log(
                    'New post received through Reverb:',
                    data
                );

                const tbody =
                    document.getElementById(
                        'posts-table-body'
                    );

                const noPosts =
                    document.getElementById(
                        'no-posts-row'
                    );

                /*
                |--------------------------------------------------------------------------
                | Remove Empty Row
                |--------------------------------------------------------------------------
                */

                if (noPosts) {
                    noPosts.remove();
                }

                /*
                |--------------------------------------------------------------------------
                | Prevent Duplicate Post
                |--------------------------------------------------------------------------
                */

                if (
                    document.querySelector(
                        `[data-post-id="${data.id}"]`
                    )
                ) {

                    return;

                }

                /*
                |--------------------------------------------------------------------------
                | Create New Table Row
                |--------------------------------------------------------------------------
                */

                const row =
                    document.createElement('tr');

                row.setAttribute(
                    'data-post-id',
                    data.id
                );

                row.classList.add(
                    'realtime-row'
                );

                row.innerHTML = `

                    <td>
                        <strong>#${data.id}</strong>
                    </td>

                    <td>

                        <strong>
                            ${escapeHtml(data.title)}
                        </strong>

                        <span class="badge bg-success ms-2">
                            NEW
                        </span>

                    </td>

                    <td>
                        ${escapeHtml(data.body)}
                    </td>

                    <td>

                        <span class="badge bg-secondary">

                            ${escapeHtml(data.user_name)}

                        </span>

                    </td>

                    <td>

                        ${escapeHtml(data.created_at)}

                    </td>

                `;

                /*
                |--------------------------------------------------------------------------
                | Add At Top
                |--------------------------------------------------------------------------
                */

                tbody.prepend(row);

                /*
                |--------------------------------------------------------------------------
                | Update Real-Time Message
                |--------------------------------------------------------------------------
                */

                const eventBox =
                    document.getElementById(
                        'realtime-event'
                    );

                if (eventBox) {

                    eventBox.className =
                        'alert alert-success';

                    eventBox.innerHTML = `

                        <i class="fa fa-bolt"></i>

                        <strong>Real-time update:</strong>

                        New post
                        "<strong>${escapeHtml(data.title)}</strong>"
                        received through Laravel Reverb.

                    `;

                }

                /*
                |--------------------------------------------------------------------------
                | Automatically Remove NEW Badge
                |--------------------------------------------------------------------------
                */

                setTimeout(
                    function () {

                        const badge =
                            row.querySelector(
                                '.badge.bg-success'
                            );

                        if (badge) {
                            badge.remove();
                        }

                    },
                    5000
                );

            }
        );

    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        const div =
            document.createElement('div');

        div.textContent =
            value ?? '';

        return div.innerHTML;

    }

    @endauth

});

</script>

@endsection