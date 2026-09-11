<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ config('app.name', 'Laravel Realtime Notifications') }}
    </title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">

    <link
        href="https://fonts.bunny.net/css?family=Nunito"
        rel="stylesheet"
    >

    @vite([
        'resources/sass/app.scss',
        'resources/js/app.js'
    ])

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />

    <style>

        .notification-dropdown {
            width: 360px;
            max-height: 450px;
            overflow-y: auto;
        }

        .notification-item {
            border-bottom: 1px solid #eee;
            padding: 12px;
            cursor: pointer;
        }

        .notification-item:hover {
            background: #f8f9fa;
        }

        .notification-unread {
            background: #eef6ff;
        }

        .notification-badge {
            position: relative;
            top: -8px;
            left: -5px;
        }

        .realtime-row {
            animation: highlightRow 2s ease;
        }

        @keyframes highlightRow {

            0% {
                background-color: #d1e7dd;
            }

            100% {
                background-color: transparent;
            }

        }

        .status-dot {
            width: 10px;
            height: 10px;
            display: inline-block;
            border-radius: 50%;
            background: #dc3545;
        }

        .status-dot.connected {
            background: #198754;
        }

    </style>

    @yield('script')

</head>

<body>

<div id="app">

    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">

        <div class="container">

            <a
                class="navbar-brand"
                href="{{ url('/') }}"
            >
                Laravel Reverb Notifications
            </a>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div
                class="collapse navbar-collapse"
                id="navbarSupportedContent"
            >

                <ul class="navbar-nav me-auto">

                    @auth

                        <li class="nav-item">

                            <a
                                class="nav-link"
                                href="{{ route('posts.index') }}"
                            >
                                <i class="fa fa-file"></i>
                                Posts
                            </a>

                        </li>

                        @if(auth()->user()->is_admin)

                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="{{ route('notification.dashboard') }}"
                                >
                                    <i class="fa fa-chart-line"></i>
                                    Realtime Dashboard
                                </a>

                            </li>

                        @endif

                    @endauth

                </ul>

                <ul class="navbar-nav ms-auto">

                    @guest

                        @if(Route::has('login'))

                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="{{ route('login') }}"
                                >
                                    Login
                                </a>

                            </li>

                        @endif

                        @if(Route::has('register'))

                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="{{ route('register') }}"
                                >
                                    Register
                                </a>

                            </li>

                        @endif

                    @else

                        <!-- Notification Bell -->

                        <li class="nav-item dropdown">

                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                id="notificationDropdown"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >

                                <i class="fa fa-bell"></i>

                                <span
                                    id="notification-count"
                                    class="badge bg-danger notification-badge"
                                    style="{{ ($unreadCount ?? 0) > 0 ? '' : 'display:none;' }}"
                                >
                                    {{ $unreadCount ?? 0 }}
                                </span>

                            </a>

                            <div
                                class="dropdown-menu dropdown-menu-end notification-dropdown"
                                aria-labelledby="notificationDropdown"
                            >

                                <div class="d-flex justify-content-between align-items-center px-3 py-2">

                                    <strong>
                                        Notifications
                                    </strong>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-link"
                                        id="mark-all-read"
                                    >
                                        Mark all read
                                    </button>

                                </div>

                                <hr class="my-1">

                                <div id="notification-list">

                                    <div class="text-center text-muted p-3">

                                        Loading...

                                    </div>

                                </div>

                                <hr class="my-1">

                                <div class="text-center">

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-danger mb-2"
                                        id="clear-notifications"
                                    >
                                        Clear All
                                    </button>

                                </div>

                            </div>

                        </li>

                        <!-- Reverb Status -->

                        <li class="nav-item">

                            <span
                                class="nav-link"
                                title="Reverb connection status"
                            >

                                <span
                                    id="reverb-status-dot"
                                    class="status-dot"
                                ></span>

                                <small id="reverb-status-text">
                                    Connecting
                                </small>

                            </span>

                        </li>

                        <!-- User -->

                        <li class="nav-item dropdown">

                            <a
                                id="navbarDropdown"
                                class="nav-link dropdown-toggle"
                                href="#"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                            >

                                {{ Auth::user()->name }}

                            </a>

                            <div
                                class="dropdown-menu dropdown-menu-end"
                            >

                                <a
                                    class="dropdown-item"
                                    href="{{ route('logout') }}"
                                    onclick="
                                        event.preventDefault();
                                        document.getElementById('logout-form').submit();
                                    "
                                >
                                    Logout
                                </a>

                                <form
                                    id="logout-form"
                                    action="{{ route('logout') }}"
                                    method="POST"
                                    class="d-none"
                                >
                                    @csrf
                                </form>

                            </div>

                        </li>

                    @endguest

                </ul>

            </div>

        </div>

    </nav>

    <main class="py-4">

        @yield('content')

    </main>

</div>

<script>

document.addEventListener('DOMContentLoaded', function () {

    @auth

    /*
    |--------------------------------------------------------------------------
    | Notification Functions
    |--------------------------------------------------------------------------
    */

    const notificationList =
        document.getElementById('notification-list');

    const notificationCount =
        document.getElementById('notification-count');

    function updateNotificationCount(count) {

        if (!notificationCount) {
            return;
        }

        notificationCount.textContent = count;

        if (count > 0) {

            notificationCount.style.display = 'inline-block';

        } else {

            notificationCount.style.display = 'none';

        }

    }

    function renderNotifications(notifications) {

        if (!notificationList) {
            return;
        }

        if (!notifications.length) {

            notificationList.innerHTML = `
                <div class="text-center text-muted p-4">
                    <i class="fa fa-bell-slash fa-2x mb-2"></i>
                    <br>
                    No notifications
                </div>
            `;

            return;
        }

        notificationList.innerHTML =
            notifications.map(notification => {

                return `
                    <div
                        class="notification-item
                        ${notification.is_read ? '' : 'notification-unread'}"
                        data-id="${notification.id}"
                    >

                        <div class="d-flex">

                            <div class="me-2">

                                <i class="fa fa-circle-info text-primary"></i>

                            </div>

                            <div>

                                <strong>
                                    ${escapeHtml(notification.title)}
                                </strong>

                                <div class="small text-muted">
                                    ${escapeHtml(notification.message)}
                                </div>

                                <small class="text-secondary">
                                    ${escapeHtml(notification.created_at)}
                                </small>

                            </div>

                        </div>

                    </div>
                `;

            }).join('');

        bindNotificationClicks();

    }

    function bindNotificationClicks() {

        document
            .querySelectorAll('.notification-item')
            .forEach(item => {

                item.addEventListener('click', function () {

                    const id = this.dataset.id;

                    markNotificationAsRead(id, this);

                });

            });

    }

    function markNotificationAsRead(id, element) {

        fetch(`/notifications/${id}/read`, {

            method: 'POST',

            headers: {

                'X-CSRF-TOKEN':
                    document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),

                'Accept': 'application/json'

            }

        })
        .then(response => response.json())
        .then(data => {

            if (data.success) {

                element.classList.remove(
                    'notification-unread'
                );

                updateNotificationCount(
                    data.unread_count
                );

            }

        });

    }

    function loadNotifications() {

        fetch('{{ route("notifications.index") }}', {

            headers: {
                'Accept': 'application/json'
            }

        })
        .then(response => response.json())
        .then(data => {

            renderNotifications(
                data.notifications
            );

            updateNotificationCount(
                data.unread_count
            );

        })
        .catch(() => {

            if (notificationList) {

                notificationList.innerHTML = `
                    <div class="text-center text-danger p-3">
                        Unable to load notifications.
                    </div>
                `;

            }

        });

    }

    function escapeHtml(value) {

        const div = document.createElement('div');

        div.textContent = value ?? '';

        return div.innerHTML;

    }

    /*
    |--------------------------------------------------------------------------
    | Load Notifications
    |--------------------------------------------------------------------------
    */

    loadNotifications();

    /*
    |--------------------------------------------------------------------------
    | Mark All As Read
    |--------------------------------------------------------------------------
    */

    const markAllButton =
        document.getElementById('mark-all-read');

    if (markAllButton) {

        markAllButton.addEventListener(
            'click',
            function (event) {

                event.preventDefault();

                fetch('{{ route("notifications.read-all") }}', {

                    method: 'POST',

                    headers: {

                        'X-CSRF-TOKEN':
                            document
                                .querySelector(
                                    'meta[name="csrf-token"]'
                                )
                                .getAttribute('content'),

                        'Accept': 'application/json'

                    }

                })
                .then(response => response.json())
                .then(data => {

                    if (data.success) {

                        updateNotificationCount(0);

                        loadNotifications();

                    }

                });

            }
        );

    }

    /*
    |--------------------------------------------------------------------------
    | Clear Notifications
    |--------------------------------------------------------------------------
    */

    const clearButton =
        document.getElementById('clear-notifications');

    if (clearButton) {

        clearButton.addEventListener(
            'click',
            function (event) {

                event.preventDefault();

                fetch('{{ route("notifications.clear") }}', {

                    method: 'DELETE',

                    headers: {

                        'X-CSRF-TOKEN':
                            document
                                .querySelector(
                                    'meta[name="csrf-token"]'
                                )
                                .getAttribute('content'),

                        'Accept': 'application/json'

                    }

                })
                .then(response => response.json())
                .then(data => {

                    if (data.success) {

                        updateNotificationCount(0);

                        loadNotifications();

                    }

                });

            }
        );

    }

    /*
    |--------------------------------------------------------------------------
    | Reverb Connection
    |--------------------------------------------------------------------------
    */

    if (window.Echo) {

        try {

            window.Echo.connector.pusher.connection.bind(
                'connected',
                function () {

                    const dot =
                        document.getElementById(
                            'reverb-status-dot'
                        );

                    const text =
                        document.getElementById(
                            'reverb-status-text'
                        );

                    if (dot) {
                        dot.classList.add('connected');
                    }

                    if (text) {
                        text.textContent = 'Connected';
                    }

                }
            );

            window.Echo.connector.pusher.connection.bind(
                'disconnected',
                function () {

                    const dot =
                        document.getElementById(
                            'reverb-status-dot'
                        );

                    const text =
                        document.getElementById(
                            'reverb-status-text'
                        );

                    if (dot) {
                        dot.classList.remove('connected');
                    }

                    if (text) {
                        text.textContent = 'Disconnected';
                    }

                }
            );

        } catch (error) {

            console.error(
                'Unable to monitor Reverb connection.',
                error
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Real-Time Notification
        |--------------------------------------------------------------------------
        */

        window.Echo
            .channel('posts')
            .listen(
                '.notification.created',
                function (data) {

                    /*
                    | Only process notification for current user.
                    |
                    | The event contains unread_count calculated
                    | for the notification owner.
                    */

                    const notificationList =
                        document.getElementById(
                            'notification-list'
                        );

                    updateNotificationCount(
                        data.unread_count
                    );

                    if (notificationList) {

                        const emptyMessage =
                            notificationList.querySelector(
                                '.text-muted'
                            );

                        if (emptyMessage) {
                            emptyMessage.remove();
                        }

                        const html = `

                            <div
                                class="notification-item notification-unread"
                                data-id="${data.id}"
                            >

                                <div class="d-flex">

                                    <div class="me-2">
                                        <i class="fa fa-bell text-success"></i>
                                    </div>

                                    <div>

                                        <strong>
                                            ${escapeHtml(data.title)}
                                        </strong>

                                        <div class="small text-muted">
                                            ${escapeHtml(data.message)}
                                        </div>

                                        <small class="text-secondary">
                                            ${escapeHtml(data.created_at)}
                                        </small>

                                    </div>

                                </div>

                            </div>

                        `;

                        notificationList.insertAdjacentHTML(
                            'afterbegin',
                            html
                        );

                        bindNotificationClicks();

                    }

                }
            );

    }

    @endauth

});

</script>

</body>

</html>