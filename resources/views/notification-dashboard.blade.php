@extends('layouts.app')

@section('content')

<div class="container">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h3 class="mb-1">

                        <i class="fa fa-chart-line"></i>

                        Real-Time Notification Dashboard

                    </h3>

                    <p class="text-muted mb-0">

                        Laravel Reverb + Redis + Queue

                    </p>

                </div>


                <span
                    id="live-badge"
                    class="badge bg-success"
                >

                    <i class="fa fa-bolt"></i>

                    LIVE

                </span>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- STATISTICS --}}
    {{-- ========================================================= --}}

    <div class="row g-4">

        <div class="col-md-6 col-lg-3">

            <div class="card shadow-sm border-primary">

                <div class="card-body">

                    <small class="text-muted">
                        Total Posts
                    </small>

                    <h2 id="total-posts">
                        0
                    </h2>

                    <i class="fa fa-file text-primary fa-2x"></i>

                </div>

            </div>

        </div>


        <div class="col-md-6 col-lg-3">

            <div class="card shadow-sm border-info">

                <div class="card-body">

                    <small class="text-muted">
                        Notifications
                    </small>

                    <h2 id="total-notifications">
                        0
                    </h2>

                    <i class="fa fa-bell text-info fa-2x"></i>

                </div>

            </div>

        </div>


        <div class="col-md-6 col-lg-3">

            <div class="card shadow-sm border-warning">

                <div class="card-body">

                    <small class="text-muted">
                        Unread
                    </small>

                    <h2 id="unread-notifications">
                        0
                    </h2>

                    <i class="fa fa-envelope text-warning fa-2x"></i>

                </div>

            </div>

        </div>


        <div class="col-md-6 col-lg-3">

            <div class="card shadow-sm border-success">

                <div class="card-body">

                    <small class="text-muted">
                        Read %
                    </small>

                    <h2 id="read-percentage">
                        0%
                    </h2>

                    <i class="fa fa-check text-success fa-2x"></i>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- SECONDARY STATS --}}
    {{-- ========================================================= --}}

    <div class="row g-4 mt-1">

        <div class="col-md-3">

            <div class="card shadow-sm">

                <div class="card-body">

                    <small class="text-muted">
                        Today's Posts
                    </small>

                    <h3 id="today-posts">
                        0
                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card shadow-sm">

                <div class="card-body">

                    <small class="text-muted">
                        Today's Notifications
                    </small>

                    <h3 id="today-notifications">
                        0
                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card shadow-sm">

                <div class="card-body">

                    <small class="text-muted">
                        Last 24 Hours
                    </small>

                    <h3 id="last-24-hours">
                        0
                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card shadow-sm">

                <div class="card-body">

                    <small class="text-muted">
                        Last 7 Days
                    </small>

                    <h3 id="last-7-days">
                        0
                    </h3>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- CONNECTION + ACTIVITY --}}
    {{-- ========================================================= --}}

    <div class="row g-4 mt-2">

        <div class="col-lg-4">

            <div class="card shadow-sm">

                <div class="card-header">

                    <i class="fa fa-server"></i>

                    Reverb Connection

                </div>

                <div class="card-body">

                    <h5
                        id="connection-status"
                        class="text-warning"
                    >
                        Connecting...
                    </h5>

                    <hr>

                    <p>
                        <strong>
                            Broadcast:
                        </strong>

                        Reverb
                    </p>

                    <p>
                        <strong>
                            Queue:
                        </strong>

                        Redis
                    </p>

                    <p class="mb-0">
                        <strong>
                            Transport:
                        </strong>

                        WebSocket
                    </p>

                </div>

            </div>

        </div>


        <div class="col-lg-8">

            <div class="card shadow-sm">

                <div class="card-header">

                    <i class="fa fa-bolt"></i>

                    Live Activity

                </div>

                <div
                    id="live-activity"
                    class="card-body"
                >

            <div class="text-muted">

                Waiting for real-time events...

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ONLINE ADMINS --}}
    {{-- ========================================================= --}}

    <div class="card shadow-sm mt-4">

        <div class="card-header">

            <i class="fa fa-users"></i>

            Online Admins

            <span class="badge bg-success float-end" id="online-count">
                0 online
            </span>

        </div>

        <div class="card-body" id="online-admins-list">

            <div class="text-muted">
                Loading...
            </div>

        </div>

    </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- RECENT NOTIFICATIONS --}}
    {{-- ========================================================= --}}

    <div class="card shadow-sm mt-4">

        <div class="card-header">

            <i class="fa fa-history"></i>

            Recent Notifications

        </div>

        <div class="card-body">

            <div
                id="recent-notifications"
            >

                Loading...

            </div>

        </div>

    </div>

</div>

@endsection


@section('script')

<script type="module">

document.addEventListener(
    'DOMContentLoaded',
    function () {

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

        const readPercentage =
            document.getElementById(
                'read-percentage'
            );

        const todayPosts =
            document.getElementById(
                'today-posts'
            );

        const todayNotifications =
            document.getElementById(
                'today-notifications'
            );

        const last24 =
            document.getElementById(
                'last-24-hours'
            );

        const last7 =
            document.getElementById(
                'last-7-days'
            );

        const activity =
            document.getElementById(
                'live-activity'
            );

        const recent =
            document.getElementById(
                'recent-notifications'
            );

        const onlineCountEl =
            document.getElementById(
                'online-count'
            );

        const onlineAdminsList =
            document.getElementById(
                'online-admins-list'
            );


        /*
        |--------------------------------------------------------------------------
        | Load Dashboard Statistics
        |--------------------------------------------------------------------------
        */

        function loadStats() {

            fetch(
                '{{ route("notification.dashboard.stats") }}',
                {
                    headers: {
                        'Accept':
                            'application/json'
                    }
                }
            )
            .then(
                response => response.json()
            )
             .then(
                data => {

                    totalPosts.textContent =
                        data.total_posts;

                    totalNotifications.textContent =
                        data.total_notifications;

                    unreadNotifications.textContent =
                        data.unread_notifications;

                    readPercentage.textContent =
                        `${data.read_percentage}%`;

                    todayPosts.textContent =
                        data.today_posts;

                    todayNotifications.textContent =
                        data.today_notifications;

                    last24.textContent =
                        data.last_24_hours;

                    last7.textContent =
                        data.last_7_days;


                    /*
                    |--------------------------------------------------------------------------
                    | Online Admins
                    |--------------------------------------------------------------------------
                    */

                    const onlineAdmins =
                        data.online_admins
                            ?? [];

                    if (onlineCountEl) {
                        onlineCountEl.textContent =
                            `${onlineAdmins.length} online`;
                    }

                    if (onlineAdminsList) {

                        if (onlineAdmins.length === 0) {

                            onlineAdminsList.innerHTML = `
                                <div class="text-muted">
                                    <i class="fa fa-user-slash"></i>
                                    No admins online.
                                </div>
                            `;

                        } else {

                            onlineAdminsList.innerHTML =
                                onlineAdmins.map(admin => {

                                    return `
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-success me-2">
                                                <i class="fa fa-circle"></i>
                                            </span>
                                            <div>
                                                <strong>
                                                    ${escapeHtml(admin.name)}
                                                </strong>
                                                <small class="text-muted d-block">
                                                    ${escapeHtml(admin.email)}
                                                </small>
                                            </div>
                                        </div>
                                    `;

                                }).join('');

                        }

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Recent Notifications
                    |--------------------------------------------------------------------------
                    */

                    recent.innerHTML = '';

                    if (
                        data.recent_notifications.length === 0
                    ) {

                        recent.innerHTML = `
                            <div class="text-muted">
                                No notifications yet.
                            </div>
                        `;

                    } else {

                        data.recent_notifications.forEach(
                            notification => {

                                const row =
                                    document.createElement(
                                        'div'
                                    );

                                row.className =
                                    'alert ' +
                                    (
                                        notification.is_read
                                            ? 'alert-light'
                                            : 'alert-warning'
                                    ) +
                                    ' border';

                                row.innerHTML = `

                                    <div class="d-flex justify-content-between">

                                        <div>

                                            <strong>
                                                ${escapeHtml(
                                                    notification.title
                                                )}
                                            </strong>

                                            <div>
                                                ${escapeHtml(
                                                    notification.message
                                                )}
                                            </div>

                                            <small class="text-muted">

                                                By
                                                ${escapeHtml(
                                                    notification.user
                                                )}

                                                ·

                                                ${escapeHtml(
                                                    notification.created_at
                                                )}

                                            </small>

                                        </div>

                                        <span class="badge ${
                                            notification.is_read
                                                ? 'bg-success'
                                                : 'bg-warning text-dark'
                                        }">

                                            ${
                                                notification.is_read
                                                    ? 'READ'
                                                    : 'UNREAD'
                                            }

                                        </span>

                                    </div>

                                `;

                                recent.appendChild(
                                    row
                                );

                            }
                        );

                    }

                }
            )
            .catch(
                error => {

                    console.error(
                        'Dashboard error:',
                        error
                    );

                }
            );

        }


        loadStats();


        /*
        |------------------------------------------------------------------
        | Presence Channel for Live Online Admin Status
        |------------------------------------------------------------------
        */

        if (window.Echo) {

            window.Echo.join('presence.online')
                .here(function (admins) {
                    updateOnlineAdmins(admins);
                })
                .joining(function (admin) {
                    loadStats();
                })
                .leaving(function (admin) {
                    loadStats();
                });

        }


        function updateOnlineAdmins(admins) {

            if (onlineCountEl) {
                onlineCountEl.textContent =
                    `${admins.length} online`;
            }

            if (onlineAdminsList) {

                if (admins.length === 0) {
                    onlineAdminsList.innerHTML = `
                        <div class="text-muted">
                            <i class="fa fa-user-slash"></i>
                            No admins online.
                        </div>
                    `;
                } else {
                    onlineAdminsList.innerHTML =
                        admins.map(admin => {
                            return `
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-success me-2">
                                        <i class="fa fa-circle"></i>
                                    </span>
                                    <div>
                                        <strong>
                                            ${escapeHtml(admin.name)}
                                        </strong>
                                    </div>
                                </div>
                            `;
                        }).join('');
                }
            }

        }


        function loadOnlineAdmins() {

            fetch('{{ route('notification.dashboard.online-admins') }}', {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                updateOnlineAdmins(data.online_admins);
            });

        }


        loadOnlineAdmins();

        if (window.Echo) {

            try {

                window.Echo
                    .connector
                    .pusher
                    .connection
                    .bind(
                        'connected',
                        function () {

                            const status =
                                document.getElementById(
                                    'connection-status'
                                );

                            status.textContent =
                                'Reverb Connected';

                            status.className =
                                'text-success';

                        }
                    );


                window.Echo
                    .connector
                    .pusher
                    .connection
                    .bind(
                        'disconnected',
                        function () {

                            const status =
                                document.getElementById(
                                    'connection-status'
                                );

                            status.textContent =
                                'Reverb Disconnected';

                            status.className =
                                'text-danger';

                        }
                    );

            } catch (error) {

                console.error(
                    'Connection monitoring error:',
                    error
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Post Event
            |--------------------------------------------------------------------------
            */

            window.Echo
                .channel('posts')
                .listen(
                    '.create',
                    function (data) {

                        loadStats();

                        const item =
                            document.createElement('div');

                        item.className =
                            'alert alert-success mb-2';

                        item.innerHTML = `
                            <i class="fa fa-bolt"></i>
                            <strong>New Post:</strong>
                            ${escapeHtml(data.title)}
                            <small class="d-block text-muted">
                                Created by ${escapeHtml(data.user_name)}
                            </small>
                        `;

                        activity.prepend(item);

                    }
                );


            window.Echo
                .channel('posts')
                .listen(
                    '.update',
                    function (data) {

                        loadStats();

                        const item =
                            document.createElement('div');

                        item.className =
                            'alert alert-info mb-2';

                        item.innerHTML = `
                            <i class="fa fa-edit"></i>
                            <strong>Post Updated:</strong>
                            ${escapeHtml(data.title)}
                            <small class="d-block text-muted">
                                Updated by ${escapeHtml(data.user_name ?? 'Unknown')}
                            </small>
                        `;

                        activity.prepend(item);

                    }
                );


            window.Echo
                .channel('posts')
                .listen(
                    '.delete',
                    function (data) {

                        loadStats();

                        const item =
                            document.createElement('div');

                        item.className =
                            'alert alert-warning mb-2';

                        item.innerHTML = `
                            <i class="fa fa-trash"></i>
                            <strong>Post Deleted:</strong>
                            ${escapeHtml(data.title)}
                        `;

                        activity.prepend(item);

                    }
                );


            /*
            |------------------------------------------------------------------
            | Real-Time Stats Updates (private channel)
            |------------------------------------------------------------------
            */

            const userId = {{ auth()->id() }};

            window.Echo
                .private(`notification.${userId}`)
                .listen('.created', function (data) {

                    loadStats();

                });


            /*
            |------------------------------------------------------------------
            | Stats Updated Event
            |------------------------------------------------------------------
            */

            window.Echo
                .channel('dashboard.stats')
                .listen('.updated', function (data) {

                    totalPosts.textContent = data.total_posts;
                    totalNotifications.textContent = data.total_notifications;
                    unreadNotifications.textContent = data.unread_notifications;
                    readPercentage.textContent = `${data.read_percentage}%`;
                    todayPosts.textContent = data.today_posts;
                    todayNotifications.textContent = data.today_notifications;
                    last24.textContent = data.last_24_hours;
                    last7.textContent = data.last_7_days;

                    const item =
                        document.createElement('div');

                    item.className =
                        'alert alert-warning mb-2';

                    item.innerHTML = `
                        <i class="fa fa-sync"></i>
                        <strong>Stats Updated:</strong>
                        ${data.total_notifications} total, ${data.unread_notifications} unread
                    `;

                    activity.prepend(item);

                });

            }


        /*
        |--------------------------------------------------------------------------
        | Escape HTML
        |--------------------------------------------------------------------------
        */

        function escapeHtml(value) {

            const div =
                document.createElement(
                    'div'
                );

            div.textContent =
                value ?? '';

            return div.innerHTML;
        }


        /*
        |--------------------------------------------------------------------------
        | Auto Refresh Statistics
        |--------------------------------------------------------------------------
        */

        setInterval(
            loadStats,
            10000
        );

    }
);

</script>

@endsection