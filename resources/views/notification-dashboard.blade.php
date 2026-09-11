@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row mb-4">

        <div class="col-12">

            <div class="card shadow-sm">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h3 class="mb-1">

                                <i class="fa fa-chart-line"></i>

                                Real-Time Broadcasting Dashboard

                            </h3>

                            <p class="text-muted mb-0">

                                Live Laravel Reverb + Redis + Queue statistics

                            </p>

                        </div>

                        <div>

                            <span class="badge bg-success">

                                <i class="fa fa-bolt"></i>

                                LIVE

                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="row g-4">

        <!-- Total Posts -->

        <div class="col-md-6 col-lg-3">

            <div class="card shadow-sm border-primary h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <h6 class="text-muted">
                                Total Posts
                            </h6>

                            <h2 id="total-posts">
                                0
                            </h2>

                        </div>

                        <i class="fa fa-file fa-2x text-primary"></i>

                    </div>

                </div>

            </div>

        </div>


        <!-- Total Notifications -->

        <div class="col-md-6 col-lg-3">

            <div class="card shadow-sm border-info h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <h6 class="text-muted">
                                Total Notifications
                            </h6>

                            <h2 id="total-notifications">
                                0
                            </h2>

                        </div>

                        <i class="fa fa-bell fa-2x text-info"></i>

                    </div>

                </div>

            </div>

        </div>


        <!-- Unread -->

        <div class="col-md-6 col-lg-3">

            <div class="card shadow-sm border-warning h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <h6 class="text-muted">
                                Unread Notifications
                            </h6>

                            <h2 id="unread-notifications">
                                0
                            </h2>

                        </div>

                        <i class="fa fa-envelope fa-2x text-warning"></i>

                    </div>

                </div>

            </div>

        </div>


        <!-- Today's Posts -->

        <div class="col-md-6 col-lg-3">

            <div class="card shadow-sm border-success h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <h6 class="text-muted">
                                Today's Posts
                            </h6>

                            <h2 id="today-posts">
                                0
                            </h2>

                        </div>

                        <i class="fa fa-calendar-day fa-2x text-success"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- Reverb Connection -->

    <div class="row mt-4">

        <div class="col-lg-6">

            <div class="card shadow-sm">

                <div class="card-header">

                    <i class="fa fa-server"></i>

                    Reverb Connection

                </div>

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <span
                            id="dashboard-status-dot"
                            class="status-dot"
                        ></span>

                        <strong
                            id="dashboard-status-text"
                            class="ms-2"
                        >
                            Connecting...
                        </strong>

                    </div>

                    <hr>

                    <p class="mb-1">

                        <strong>Broadcast Driver:</strong>

                        Reverb

                    </p>

                    <p class="mb-1">

                        <strong>Queue:</strong>

                        Redis

                    </p>

                    <p class="mb-0">

                        <strong>Transport:</strong>

                        WebSocket

                    </p>

                </div>

            </div>

        </div>


        <!-- Activity -->

        <div class="col-lg-6">

            <div class="card shadow-sm">

                <div class="card-header">

                    <i class="fa fa-bolt"></i>

                    Live Activity

                </div>

                <div class="card-body">

                    <div id="live-activity">

                        <div class="text-muted">

                            Waiting for Reverb events...

                        </div>

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

    /*
    |--------------------------------------------------------------------------
    | Elements
    |--------------------------------------------------------------------------
    */

    const totalPosts =
        document.getElementById(
            'total-posts'
        );

    const totalNotifications =
        document.getElementById(
            'total-notifications'
        );

    const unreadNotifications =
        document.getElementById(
            'unread-notifications'
        );

    const todayPosts =
        document.getElementById(
            'today-posts'
        );

    const activity =
        document.getElementById(
            'live-activity'
        );

    /*
    |--------------------------------------------------------------------------
    | Load Statistics
    |--------------------------------------------------------------------------
    */

    function loadStats() {

        fetch(
            '{{ route("notification.dashboard.stats") }}',
            {
                headers: {
                    'Accept': 'application/json'
                }
            }
        )
        .then(response => response.json())
        .then(data => {

            totalPosts.textContent =
                data.total_posts;

            totalNotifications.textContent =
                data.total_notifications;

            unreadNotifications.textContent =
                data.unread_notifications;

            todayPosts.textContent =
                data.today_posts;

        })
        .catch(error => {

            console.error(
                'Unable to load dashboard statistics.',
                error
            );

        });

    }

    /*
    |--------------------------------------------------------------------------
    | Initial Statistics
    |--------------------------------------------------------------------------
    */

    loadStats();

    /*
    |--------------------------------------------------------------------------
    | Reverb Connection Status
    |--------------------------------------------------------------------------
    */

    if (window.Echo) {

        try {

            window.Echo
                .connector
                .pusher
                .connection
                .bind(
                    'connected',
                    function () {

                        const dot =
                            document.getElementById(
                                'dashboard-status-dot'
                            );

                        const status =
                            document.getElementById(
                                'dashboard-status-text'
                            );

                        dot.classList.add(
                            'connected'
                        );

                        status.textContent =
                            'Reverb Connected';

                        status.className =
                            'ms-2 text-success';

                    }
                );

            window.Echo
                .connector
                .pusher
                .connection
                .bind(
                    'disconnected',
                    function () {

                        const dot =
                            document.getElementById(
                                'dashboard-status-dot'
                            );

                        const status =
                            document.getElementById(
                                'dashboard-status-text'
                            );

                        dot.classList.remove(
                            'connected'
                        );

                        status.textContent =
                            'Reverb Disconnected';

                        status.className =
                            'ms-2 text-danger';

                    }
                );

        } catch (error) {

            console.error(
                'Unable to monitor Reverb.',
                error
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Real-Time Post Event
        |--------------------------------------------------------------------------
        */

        window.Echo
            .channel('posts')
            .listen(
                '.create',
                function (data) {

                    loadStats();

                    const item =
                        document.createElement(
                            'div'
                        );

                    item.className =
                        'alert alert-success mb-2';

                    item.innerHTML = `

                        <i class="fa fa-bolt"></i>

                        <strong>New Post:</strong>

                        ${escapeHtml(data.title)}

                        <small class="d-block text-muted">

                            Created by
                            ${escapeHtml(data.user_name)}

                        </small>

                    `;

                    activity.prepend(item);

                }
            );


        /*
        |--------------------------------------------------------------------------
        | Real-Time Notification Event
        |--------------------------------------------------------------------------
        */

        window.Echo
            .channel('posts')
            .listen(
                '.notification.created',
                function (data) {

                    loadStats();

                }
            );

    }


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

});

</script>

@endsection