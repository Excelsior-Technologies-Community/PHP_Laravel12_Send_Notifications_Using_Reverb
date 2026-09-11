@extends('layouts.app')

@section('content')

<div class="container">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-primary text-white">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <i class="fa fa-bolt"></i>

                    <strong>
                        Reverb Posts & Notifications
                    </strong>
                </div>

                @auth

                    <div>

                        <button
                            type="button"
                            class="btn btn-light position-relative"
                            id="notification-button"
                        >

                            <i class="fa fa-bell"></i>

                            Notifications

                            <span
                                id="notification-count"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                style="display:none;"
                            >
                                0
                            </span>

                        </button>

                    </div>

                @endauth

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- NOTIFICATION PANEL --}}
    {{-- ========================================================= --}}

    @auth

    <div
        id="notification-panel"
        class="card shadow-sm mb-4"
        style="display:none;"
    >

        <div class="card-header">

            <div class="d-flex justify-content-between align-items-center">

                <strong>
                    <i class="fa fa-bell"></i>
                    Notification Center
                </strong>

                <div>

                    <button
                        id="mark-all-read"
                        class="btn btn-sm btn-success"
                    >

                        <i class="fa fa-check"></i>
                        Mark All Read

                    </button>

                    <button
                        id="clear-notifications"
                        class="btn btn-sm btn-danger"
                    >

                        <i class="fa fa-trash"></i>
                        Clear All

                    </button>

                </div>

            </div>

        </div>


        <div class="card-body">

            <div class="row mb-3">

                <div class="col-md-6">

                    <input
                        type="text"
                        id="notification-search"
                        class="form-control"
                        placeholder="Search notifications..."
                    >

                </div>


                <div class="col-md-4">

                    <select
                        id="notification-status"
                        class="form-select"
                    >

                        <option value="">
                            All Notifications
                        </option>

                        <option value="unread">
                            Unread
                        </option>

                        <option value="read">
                            Read
                        </option>

                    </select>

                </div>


                <div class="col-md-2">

                    <button
                        id="load-notifications"
                        class="btn btn-primary w-100"
                    >

                        <i class="fa fa-filter"></i>
                        Filter

                    </button>

                </div>

            </div>


            <div id="notification-list">

                <div class="text-center text-muted py-3">

                    Loading notifications...

                </div>

            </div>

        </div>

    </div>

    @endauth


    {{-- ========================================================= --}}
    {{-- SEARCH + SORT --}}
    {{-- ========================================================= --}}

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('posts.index') }}"
            >

                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search title or body..."
                            value="{{ $search }}"
                        >

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Sort By
                        </label>

                        <select
                            name="sort"
                            class="form-select"
                        >

                            <option
                                value="newest"
                                {{ $sort == 'newest' ? 'selected' : '' }}
                            >
                                Newest First
                            </option>

                            <option
                                value="oldest"
                                {{ $sort == 'oldest' ? 'selected' : '' }}
                            >
                                Oldest First
                            </option>

                            <option
                                value="title_asc"
                                {{ $sort == 'title_asc' ? 'selected' : '' }}
                            >
                                Title A-Z
                            </option>

                            <option
                                value="title_desc"
                                {{ $sort == 'title_desc' ? 'selected' : '' }}
                            >
                                Title Z-A
                            </option>

                        </select>

                    </div>


                    <div class="col-md-2 d-flex align-items-end">

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >

                            <i class="fa fa-search"></i>
                            Search

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- CREATE POST --}}
    {{-- ========================================================= --}}

    @auth

        @if(!auth()->user()->is_admin)

            <div class="card shadow-sm border-success mb-4">

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

                            <label class="form-label">
                                Title
                            </label>

                            <input
                                type="text"
                                name="title"
                                class="form-control"
                                placeholder="Enter post title"
                                value="{{ old('title') }}"
                                required
                            >

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Body
                            </label>

                            <textarea
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

                Administrator mode:

                new posts will arrive through Laravel Reverb.

            </div>

        @endif

    @endauth


    {{-- ========================================================= --}}
    {{-- REAL-TIME STATUS --}}
    {{-- ========================================================= --}}

    <div
        id="realtime-event"
        class="alert alert-light border"
    >

        <i class="fa fa-bolt text-warning"></i>

        Waiting for real-time Reverb events...

    </div>


    {{-- ========================================================= --}}
    {{-- POSTS --}}
    {{-- ========================================================= --}}

    <div class="card shadow-sm">

        <div class="card-header bg-dark text-white">

            <i class="fa fa-list"></i>

            Posts List

            <span class="badge bg-primary float-end">

                {{ $posts->total() }}

                Total

            </span>

        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table
                    class="table table-bordered table-hover align-middle"
                    id="posts-table"
                >

                    <thead class="table-dark">

                        <tr>

                            <th>
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

                            <th>
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


            {{-- ========================================================= --}}
            {{-- NUMBER ONLY PAGINATION --}}
            {{-- ========================================================= --}}

            @if ($posts->hasPages())

                <div class="d-flex justify-content-center mt-4">

                    <nav aria-label="Posts pagination">

                        <ul class="pagination mb-0">

                            @foreach ($posts->getUrlRange(1, $posts->lastPage()) as $page => $url)

                                <li
                                    class="page-item {{ $page == $posts->currentPage() ? 'active' : '' }}"
                                >

                                    <a
                                        class="page-link"
                                        href="{{ $url }}"
                                    >

                                        {{ $page }}

                                    </a>

                                </li>

                            @endforeach

                        </ul>

                    </nav>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection


@section('script')

<script type="module">

document.addEventListener(
    'DOMContentLoaded',
    function () {

        @auth

        /*
        |--------------------------------------------------------------------------
        | Global Variables
        |--------------------------------------------------------------------------
        */

        let notificationPanelOpen = false;


        /*
        |--------------------------------------------------------------------------
        | Elements
        |--------------------------------------------------------------------------
        */

        const notificationButton =
            document.getElementById(
                'notification-button'
            );

        const notificationPanel =
            document.getElementById(
                'notification-panel'
            );

        const notificationList =
            document.getElementById(
                'notification-list'
            );

        const notificationCount =
            document.getElementById(
                'notification-count'
            );


        /*
        |--------------------------------------------------------------------------
        | Notification Count
        |--------------------------------------------------------------------------
        */

        function updateNotificationCount(count) {

            notificationCount.textContent =
                count;

            notificationCount.style.display =
                count > 0
                    ? 'inline-block'
                    : 'none';

        }


        /*
        |--------------------------------------------------------------------------
        | Load Notifications
        |--------------------------------------------------------------------------
        */

        function loadNotifications() {

            const search =
                document.getElementById(
                    'notification-search'
                ).value;

            const status =
                document.getElementById(
                    'notification-status'
                ).value;


            const params =
                new URLSearchParams();


            if (search) {

                params.append(
                    'search',
                    search
                );

            }


            if (status) {

                params.append(
                    'status',
                    status
                );

            }


            fetch(
                `{{ route('notifications.index') }}?${params.toString()}`,
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

                    updateNotificationCount(
                        data.unread_count
                    );


                    notificationList.innerHTML =
                        '';


                    const notifications =
                        data.notifications.data;


                    if (
                        notifications.length === 0
                    ) {

                        notificationList.innerHTML = `

                            <div class="alert alert-light text-center">

                                No notifications found.

                            </div>

                        `;

                        return;

                    }


                    notifications.forEach(
                        notification => {

                            const item =
                                document.createElement(
                                    'div'
                                );


                            item.className =
                                notification.is_read
                                    ? 'alert alert-light border mb-2'
                                    : 'alert alert-warning border mb-2';


                            item.innerHTML = `

                                <div class="d-flex justify-content-between">

                                    <div>

                                        <strong>
                                            ${escapeHtml(notification.title)}
                                        </strong>

                                        <p class="mb-1">
                                            ${escapeHtml(notification.message)}
                                        </p>

                                        <small class="text-muted">

                                            ${escapeHtml(
                                                notification.created_at ?? ''
                                            )}

                                        </small>

                                    </div>


                                    <div>

                                        ${
                                            notification.is_read

                                            ?

                                            `<button
                                                class="btn btn-sm btn-outline-warning toggle-read"
                                                data-id="${notification.id}"
                                                data-action="unread"
                                            >
                                                Mark Unread
                                            </button>`

                                            :

                                            `<button
                                                class="btn btn-sm btn-outline-success toggle-read"
                                                data-id="${notification.id}"
                                                data-action="read"
                                            >
                                                Mark Read
                                            </button>`
                                        }


                                        <button
                                            class="btn btn-sm btn-outline-danger delete-notification"
                                            data-id="${notification.id}"
                                        >

                                            Delete

                                        </button>

                                    </div>

                                </div>

                            `;


                            notificationList.appendChild(
                                item
                            );

                        }
                    );

                }
            )
            .catch(
                error => {

                    console.error(
                        'Notification error:',
                        error
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Open / Close Notification Center
        |--------------------------------------------------------------------------
        */

        notificationButton.addEventListener(
            'click',
            function () {

                notificationPanelOpen =
                    !notificationPanelOpen;


                notificationPanel.style.display =
                    notificationPanelOpen
                        ? 'block'
                        : 'none';


                if (
                    notificationPanelOpen
                ) {

                    loadNotifications();

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Notification Filter
        |--------------------------------------------------------------------------
        */

        document
            .getElementById(
                'load-notifications'
            )
            .addEventListener(
                'click',
                loadNotifications
            );


        /*
        |--------------------------------------------------------------------------
        | Mark All Read
        |--------------------------------------------------------------------------
        */

        document
            .getElementById(
                'mark-all-read'
            )
            .addEventListener(
                'click',
                function () {

                    fetch(
                        '{{ route("notifications.read-all") }}',
                        {
                            method: 'POST',

                            headers: {

                                'X-CSRF-TOKEN':
                                    '{{ csrf_token() }}',

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

                            updateNotificationCount(
                                data.unread_count
                            );

                            loadNotifications();

                        }
                    );

                }
            );


        /*
        |--------------------------------------------------------------------------
        | Clear All Notifications
        |--------------------------------------------------------------------------
        */

        document
            .getElementById(
                'clear-notifications'
            )
            .addEventListener(
                'click',
                function () {

                    if (
                        !confirm(
                            'Delete all notifications?'
                        )
                    ) {

                        return;

                    }


                    fetch(
                        '{{ route("notifications.clear") }}',
                        {
                            method: 'DELETE',

                            headers: {

                                'X-CSRF-TOKEN':
                                    '{{ csrf_token() }}',

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

                            updateNotificationCount(
                                0
                            );

                            loadNotifications();

                        }
                    );

                }
            );


        /*
        |--------------------------------------------------------------------------
        | Notification Buttons
        |--------------------------------------------------------------------------
        */

        notificationList.addEventListener(
            'click',
            function (event) {

                const readButton =
                    event.target.closest(
                        '.toggle-read'
                    );


                const deleteButton =
                    event.target.closest(
                        '.delete-notification'
                    );


                /*
                |--------------------------------------------------------------------------
                | Mark Read / Unread
                |--------------------------------------------------------------------------
                */

                if (readButton) {

                    const id =
                        readButton.dataset.id;


                    const action =
                        readButton.dataset.action;


                    const url =
                        action === 'read'
                            ? `/notifications/${id}/read`
                            : `/notifications/${id}/unread`;


                    fetch(
                        url,
                        {
                            method: 'POST',

                            headers: {

                                'X-CSRF-TOKEN':
                                    '{{ csrf_token() }}',

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

                            updateNotificationCount(
                                data.unread_count
                            );

                            loadNotifications();

                        }
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Delete Notification
                |--------------------------------------------------------------------------
                */

                if (deleteButton) {

                    const id =
                        deleteButton.dataset.id;


                    if (
                        !confirm(
                            'Delete this notification?'
                        )
                    ) {

                        return;

                    }


                    fetch(
                        `/notifications/${id}`,
                        {
                            method: 'DELETE',

                            headers: {

                                'X-CSRF-TOKEN':
                                    '{{ csrf_token() }}',

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

                            loadNotifications();

                        }
                    );

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Laravel Echo / Reverb
        |--------------------------------------------------------------------------
        */

        if (!window.Echo) {

            console.error(
                'Laravel Echo is unavailable.'
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | New Post Event
        |--------------------------------------------------------------------------
        */

        window.Echo
            .channel('posts')
            .listen(
                '.create',
                function (data) {

                    const tbody =
                        document.getElementById(
                            'posts-table-body'
                        );


                    const noPosts =
                        document.getElementById(
                            'no-posts-row'
                        );


                    if (noPosts) {

                        noPosts.remove();

                    }


                    if (
                        document.querySelector(
                            `[data-post-id="${data.id}"]`
                        )
                    ) {

                        return;

                    }


                    const row =
                        document.createElement(
                            'tr'
                        );


                    row.setAttribute(
                        'data-post-id',
                        data.id
                    );


                    row.className =
                        'table-success';


                    row.innerHTML = `

                        <td>
                            #${data.id}
                        </td>

                        <td>

                            <strong>
                                ${escapeHtml(data.title)}
                            </strong>

                            <span class="badge bg-success">
                                NEW
                            </span>

                        </td>

                        <td>
                            ${escapeHtml(data.body)}
                        </td>

                        <td>

                            <span class="badge bg-secondary">

                                ${escapeHtml(
                                    data.user_name
                                )}

                            </span>

                        </td>

                        <td>
                            ${escapeHtml(data.created_at)}
                        </td>

                    `;


                    tbody.prepend(row);


                    const eventBox =
                        document.getElementById(
                            'realtime-event'
                        );


                    eventBox.className =
                        'alert alert-success';


                    eventBox.innerHTML = `

                        <i class="fa fa-bolt"></i>

                        <strong>
                            Real-time update:
                        </strong>

                        New post

                        "<strong>
                            ${escapeHtml(data.title)}
                        </strong>"

                        received through Reverb.

                    `;


                    setTimeout(
                        function () {

                            row.classList.remove(
                                'table-success'
                            );

                        },
                        5000
                    );

                }
            );


        /*
        |--------------------------------------------------------------------------
        | New Notification Event
        |--------------------------------------------------------------------------
        */

        window.Echo
            .channel('posts')
            .listen(
                '.notification.created',
                function (data) {

                    updateNotificationCount(
                        data.unread_count
                    );


                    const eventBox =
                        document.getElementById(
                            'realtime-event'
                        );


                    eventBox.className =
                        'alert alert-warning';


                    eventBox.innerHTML = `

                        <i class="fa fa-bell"></i>

                        <strong>
                            New notification:
                        </strong>

                        ${escapeHtml(
                            data.message
                        )}

                    `;


                    if (
                        notificationPanelOpen
                    ) {

                        loadNotifications();

                    }

                }
            );


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
        | Initial Notification Count
        |--------------------------------------------------------------------------
        */

        loadNotifications();


        @endauth

    }

);

</script>

@endsection

