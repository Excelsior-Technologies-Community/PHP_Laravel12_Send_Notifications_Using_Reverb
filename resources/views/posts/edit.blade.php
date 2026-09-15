@extends('layouts.app')

@section('content')

<div class="container">

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-warning text-dark">

            <i class="fa fa-edit"></i>

            Edit Post

        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('posts.update', $post->id) }}"
                enctype="multipart/form-data"
            >

                @csrf
                @method('PUT')

                <div class="mb-3">

                    <label class="form-label">
                        <i class="fa fa-heading"></i> Title
                    </label>

                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        value="{{ old('title', $post->title) }}"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        <i class="fa fa-tag"></i> Category
                    </label>

                    <select
                        name="category"
                        class="form-select"
                    >

                        <option value="">
                            -- Select category --
                        </option>

                        @foreach($categories as $cat)
                            <option
                                value="{{ $cat }}"
                                {{ old('category', $post->category) == $cat ? 'selected' : '' }}
                            >
                                {{ $cat }}
                            </option>
                        @endforeach

                        <option value="Technology" {{ old('category', $post->category) == 'Technology' ? 'selected' : '' }}>
                            Technology
                        </option>

                        <option value="News" {{ old('category', $post->category) == 'News' ? 'selected' : '' }}>
                            News
                        </option>

                        <option value="General" {{ old('category', $post->category) == 'General' ? 'selected' : '' }}>
                            General
                        </option>

                        <option value="Announcement" {{ old('category', $post->category) == 'Announcement' ? 'selected' : '' }}>
                            Announcement
                        </option>

                    </select>

                    <input
                        type="text"
                        name="category_new"
                        class="form-control mt-2"
                        placeholder="Or type a new category..."
                        value="{{ old('category_new') }}"
                    >

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        <i class="fa fa-align-left"></i> Body
                    </label>

                    <textarea
                        name="body"
                        class="form-control"
                        rows="5"
                        required
                    >{{ old('body', $post->body) }}</textarea>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        <i class="fa fa-image"></i> Featured Image
                    </label>

                    @if($post->image)

                        <div class="mb-2">

                            <img
                                src="{{ $post->image }}"
                                alt="Current post image"
                                class="img-thumbnail"
                                style="width: 150px; height: 150px; object-fit: cover;"
                            >

                        </div>

                        <div class="form-check mb-2">

                            <input
                                type="checkbox"
                                name="remove_image"
                                id="remove_image"
                                class="form-check-input"
                                value="1"
                            >

                            <label
                                class="form-check-label"
                                for="remove_image"
                            >

                                Remove current image

                            </label>

                        </div>

                    @endif

                    <input
                        type="file"
                        name="image"
                        class="form-control"
                        accept="image/*"
                    >

                    <small class="text-muted">
                        Leave empty to keep current image.
                    </small>

                </div>

                <div class="d-flex justify-content-between">

                    <a
                        href="{{ route('posts.index') }}"
                        class="btn btn-outline-secondary"
                    >

                        <i class="fa fa-arrow-left"></i>
                        Back to Posts

                    </a>

                    <button
                        type="submit"
                        class="btn btn-warning text-dark"
                    >

                        <i class="fa fa-save"></i>
                        Update Post

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection
