@extends('layouts.app')

@section('content')

<div class="container">

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-warning text-dark">

            <i class="fa fa-edit"></i>

            Edit Profile

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
                action="{{ route('profile.update') }}"
                enctype="multipart/form-data"
            >

                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-4 text-center mb-4">

                        @if($user->avatar)
                            <img
                                src="{{ asset('storage/' . $user->avatar) }}"
                                alt="{{ $user->name }}"
                                class="rounded-circle img-thumbnail preview-img"
                                style="width: 150px; height: 150px; object-fit: cover;"
                                id="avatar-preview"
                            >
                        @else
                            <div
                                class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center img-thumbnail mx-auto"
                                style="width: 150px; height: 150px;"
                                id="avatar-placeholder"
                            >

                                <i class="fa fa-user fa-3x"></i>

                            </div>
                        @endif

                        <div class="mt-3">

                            <label
                                class="btn btn-outline-primary btn-sm"
                            >

                                <i class="fa fa-upload"></i>
                                Choose Avatar

                                <input
                                    type="file"
                                    name="avatar"
                                    class="d-none"
                                    accept="image/*"
                                    onchange="previewAvatar(event)"
                                >

                            </label>

                            @if($user->avatar)
                                <div class="form-check mt-2">

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

                                        Remove current avatar

                                    </label>

                                </div>
                            @endif

                        </div>

                    </div>

                    <div class="col-md-8">

                        <div class="mb-3">

                            <label class="form-label">
                                <i class="fa fa-user"></i> Name
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="{{ old('name', $user->name) }}"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                <i class="fa fa-envelope"></i> Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email', $user->email) }}"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                <i class="fa fa-lock"></i> New Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                autocomplete="new-password"
                            >

                            <small class="text-muted">
                                Leave blank to keep current password.
                            </small>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                <i class="fa fa-lock"></i> Confirm Password
                            </label>

                            <input
                                type="password"
                                name="password_confirmation"
                                class="form-control"
                                autocomplete="new-password"
                            >

                        </div>

                        <button
                            type="submit"
                            class="btn btn-warning"
                        >

                            <i class="fa fa-save"></i>
                            Update Profile

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

<script>
function previewAvatar(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const placeholder = document.getElementById('avatar-placeholder');
        if (placeholder) {
            placeholder.style.display = 'none';
        }

        let preview = document.getElementById('avatar-preview');
        if (preview) {
            preview.src = e.target.result;
        } else {
            const img = document.createElement('img');
            img.id = 'avatar-preview';
            img.src = e.target.result;
            img.alt = 'Preview';
            img.className = 'rounded-circle img-thumbnail preview-img';
            img.style.width = '150px';
            img.style.height = '150px';
            img.style.objectFit = 'cover';
            if (placeholder) {
                placeholder.parentNode.insertBefore(img, placeholder.nextSibling);
            }
        }
    };
    reader.readAsDataURL(file);
}
</script>

@endsection
