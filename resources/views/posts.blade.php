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

            @guest
                <div class="alert alert-info mt-3">
                    Login to create a post.
                </div>
            @endguest

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
