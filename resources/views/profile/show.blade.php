@extends('layouts.app')

@section('content')

<div class="container">

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-primary text-white">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <i class="fa fa-user"></i>

                    <strong>
                        Profile
                    </strong>

                </div>

                <a
                    href="{{ route('profile.edit') }}"
                    class="btn btn-sm btn-light"
                >

                    <i class="fa fa-edit"></i>
                    Edit Profile

                </a>

            </div>

        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row">

                <div class="col-md-3 text-center mb-4">

                    @if($user->avatar)
                        <img
                            src="{{ asset('storage/' . $user->avatar) }}"
                            alt="{{ $user->name }}"
                            class="rounded-circle img-thumbnail"
                            style="width: 150px; height: 150px; object-fit: cover;"
                        >
                    @else
                        <div
                            class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center img-thumbnail mx-auto"
                            style="width: 150px; height: 150px;"
                        >

                            <i class="fa fa-user fa-3x"></i>

                        </div>
                    @endif

                </div>

                <div class="col-md-9">

                    <table class="table table-bordered table-sm">

                        <tr>
                            <th>Name</th>
                            <td>{{ $user->name }}</td>
                        </tr>

                        <tr>
                            <th>Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>

                        <tr>
                            <th>Role</th>
                            <td>
                                @if($user->is_admin)
                                    <span class="badge bg-danger">
                                        <i class="fa fa-user-shield"></i> Administrator
                                    </span>
                                @else
                                    <span class="badge bg-info">
                                        Normal User
                                    </span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Email Verified</th>
                            <td>
                                {{ $user->email_verified_at ? $user->email_verified_at->format('d M Y, h:i A') : 'Not verified' }}
                            </td>
                        </tr>

                        <tr>
                            <th>Member Since</th>
                            <td>{{ $user->created_at->format('d M Y, h:i A') }}</td>
                        </tr>

                        <tr>
                            <th>Unread Notifications</th>
                            <td>
                                <span class="badge bg-warning text-dark">
                                    {{ $user->unread_notifications_count }}
                                </span>
                            </td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
