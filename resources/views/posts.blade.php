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
    {{-- SEARCH + SORT + CATEGORY --}}
    {{-- ========================================================= --}}

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('posts.index') }}"
            >

                <div class="row g-3">

                    <div class="col-md-3">

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

                    <div class="col-md-3">

                        <label class="form-label">
                            Category
                        </label>

                        <select
                            name="category"
                            class="form-select"
                        >

                            <option value="">
                                All Categories
                            </option>

                            @foreach($categories as $cat)
                                <option
                                    value="{{ $cat }}"
                                    {{ ($category ?? '') == $cat ? 'selected' : '' }}
                                >
                                    {{ $cat }}
                                </option>
                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-3">

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

                    <div class="col-md-3 d-flex align-items-end">

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

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('posts.store') }}"
                        enctype="multipart/form-data"
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
                                <i class="fa fa-tag"></i> Category
                            </label>

                            <select
                                name="category"
                                class="form-select"
                            >

                                <option value="">
                                    -- Select category --
                                </option>

                                @foreach($categories ?? [] as $cat)
                                    <option
                                        value="{{ $cat }}"
                                        {{ old('category') == $cat ? 'selected' : '' }}
                                    >
                                        {{ $cat }}
                                    </option>
                                @endforeach

                                <option value="Technology" {{ old('category') == 'Technology' ? 'selected' : '' }}>
                                    Technology
                                </option>

                                <option value="News" {{ old('category') == 'News' ? 'selected' : '' }}>
                                    News
                                </option>

                                <option value="General" {{ old('category') == 'General' ? 'selected' : '' }}>
                                    General
                                </option>

                                <option value="Announcement" {{ old('category') == 'Announcement' ? 'selected' : '' }}>
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

                        <div class="mb-3">

                            <label class="form-label">
                                <i class="fa fa-image"></i> Featured Image
                            </label>

                            <input
                                type="file"
                                name="image"
                                class="form-control"
                                accept="image/*"
                            >

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
                                Category
                            </th>

                            <th>
                                Image
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

                            <th
                                class="text-center"
                                style="width: 150px;"
                            >
                                Actions
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

                                    @if($post->category)
                                        <span class="badge bg-info text-dark">
                                            {{ $post->category }}
                                        </span>
                                    @else
                                        <span class="text-muted fst-italic">
                                            &mdash;
                                        </span>
                                    @endif

                                </td>

                                <td class="text-center">

                                    @if($post->image)
                                        <img
                                            src="{{ $post->image }}"
                                            alt="Post image"
                                            class="img-thumbnail"
                                            style="width: 60px; height: 60px; object-fit: cover;"
                                        >
                                    @else
                                        <span class="text-muted fst-italic">
                                            No image
                                        </span>
                                    @endif

                                </td>

                                <td>
                                    {{ \Illuminate\Support\Str::limit($post->body, 100) }}
                                </td>

                                <td>

                                    <span class="badge bg-secondary">

                                        {{ $post->user?->name ?? 'Unknown' }}

                                    </span>

                                </td>

                                <td>

                                    {{ $post->created_at->format('d M Y, h:i A') }}

                                </td>

                                <td class="text-center">

                                    @if($post->user_id === auth()->id() || auth()->user()?->is_admin)

                                        <a
                                            href="{{ route('posts.edit', $post->id) }}"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Edit"
                                        >
                                            <i class="fa fa-edit"></i>
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('posts.destroy', $post->id) }}"
                                            onsubmit="return confirmDelete(this, '{{ $post->title }}')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete"
                                            >
                                                <i class="fa fa-trash"></i>
                                            </button>

                                        </form>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr id="no-posts-row">

                                <td
                                    colspan="8"
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

        const userId = {{ auth()->id() }};

        /*
        |------------------------------------------------------------------
        | Elements
        |------------------------------------------------------------------
        */

        const notificationButton =
            document.getElementById('notification-button');

        const notificationPanel =
            document.getElementById('notification-panel');

        const realtimeEvent =
            document.getElementById('realtime-event');

        let notificationPanelOpen = false;


        /*
        |------------------------------------------------------------------
        | Load Notifications (for notification panel on posts page)
        |------------------------------------------------------------------
        */

        function loadNotifications() {

            const searchEl =
                document.getElementById('notification-search');

            const statusEl =
                document.getElementById('notification-status');

            const search =
                searchEl ? searchEl.value : '';

            const status =
                statusEl ? statusEl.value : '';

            const params = new URLSearchParams();

            if (search) {
                params.append('search', search);
            }

            if (status) {
                params.append('status', status);
            }

            fetch(
                `{{ route('notifications.index') }}?${params.toString()}`,
                {
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            )
            .then(response => response.json())
            .then(data => {

                updateNotificationCount(data.unread_count);

                const notificationList =
                    document.getElementById('notification-list');

                if (!notificationList) {
                    return;
                }

                notificationList.innerHTML = '';

                const notifications = data.notifications.data;

                if (notifications.length === 0) {
                    notificationList.innerHTML = `
                        <div class="alert alert-light text-center">
                            No notifications found.
                        </div>
                    `;
                    return;
                }

                notifications.forEach(notification => {

                    const item = document.createElement('div');

                    item.className =
                        notification.is_read
                            ? 'alert alert-light border mb-2'
                            : 'alert alert-warning border mb-2';

                    item.innerHTML = `
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>${escapeHtml(notification.title)}</strong>
                                <p class="mb-1">${escapeHtml(notification.message)}</p>
                                <small class="text-muted">
                                    ${escapeHtml(notification.created_at ?? '')}
                                </small>
                            </div>
                            <div>
                                ${notification.is_read
                                    ? `<button class="btn btn-sm btn-outline-warning toggle-read" data-id="${notification.id}" data-action="unread">Mark Unread</button>`
                                    : `<button class="btn btn-sm btn-outline-success toggle-read" data-id="${notification.id}" data-action="read">Mark Read</button>`
                                }
                                <button class="btn btn-sm btn-outline-danger delete-notification" data-id="${notification.id}">Delete</button>
                            </div>
                        </div>
                    `;

                    notificationList.appendChild(item);

                });

            })
            .catch(error => {
                console.error('Notification error:', error);
            });

        }


        /*
        |------------------------------------------------------------------
        | Notification Panel Toggle
        |------------------------------------------------------------------
        */

        if (notificationButton && notificationPanel) {
            notificationButton.addEventListener('click', function () {
                notificationPanelOpen = !notificationPanelOpen;
                notificationPanel.style.display =
                    notificationPanelOpen ? 'block' : 'none';
                if (notificationPanelOpen) {
                    loadNotifications();
                }
            });
        }


        /*
        |------------------------------------------------------------------
        | Notification Filter
        |------------------------------------------------------------------
        */

        const loadNotificationsBtn =
            document.getElementById('load-notifications');

        if (loadNotificationsBtn) {
            loadNotificationsBtn.addEventListener('click', loadNotifications);
        }


        /*
        |------------------------------------------------------------------
        | Mark All Read
        |------------------------------------------------------------------
        */

        const markAllReadBtn =
            document.getElementById('mark-all-read');

        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function () {
                fetch('{{ route("notifications.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    updateNotificationCount(data.unread_count);
                    loadNotifications();
                });
            });
        }


        /*
        |------------------------------------------------------------------
        | Clear Notifications
        |------------------------------------------------------------------
        */

        const clearBtn =
            document.getElementById('clear-notifications');

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                if (!confirm('Delete all notifications?')) return;
                fetch('{{ route("notifications.clear") }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    updateNotificationCount(0);
                    loadNotifications();
                });
            });
        }


        /*
        |------------------------------------------------------------------
        | Notification Item Buttons
        |------------------------------------------------------------------
        */

        const notificationListEl =
            document.getElementById('notification-list');

        if (notificationListEl) {
            notificationListEl.addEventListener('click', function (event) {

                const readBtn = event.target.closest('.toggle-read');
                const deleteBtn = event.target.closest('.delete-notification');

                if (readBtn) {
                    const id = readBtn.dataset.id;
                    const action = readBtn.dataset.action;
                    const url = action === 'read'
                        ? `/notifications/${id}/read`
                        : `/notifications/${id}/unread`;

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        updateNotificationCount(data.unread_count);
                        loadNotifications();
                    });
                }

                if (deleteBtn) {
                    const id = deleteBtn.dataset.id;
                    if (!confirm('Delete this notification?')) return;
                    fetch(`/notifications/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(() => loadNotifications());
                }
            });
        }


        /*
        |------------------------------------------------------------------
        | Escape HTML
        |------------------------------------------------------------------
        */

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value ?? '';
            return div.innerHTML;
        }


        /*
        |------------------------------------------------------------------
        | Confirm Delete Post
        |------------------------------------------------------------------
        */

        window.confirmDelete = function (form, title) {
            if (confirm(`Delete post "${title}"? This cannot be undone.`)) {
                return true;
            }
            return false;
        };


        /*
        |------------------------------------------------------------------
        | Laravel Echo / Reverb
        |------------------------------------------------------------------
        */

        if (!window.Echo) {
            console.error('Laravel Echo is unavailable.');
            return;
        }


        /*
        |------------------------------------------------------------------
        | New Post Event (public posts channel)
        |------------------------------------------------------------------
        */

        window.Echo
            .channel('posts')
            .listen('.create', function (data) {

                const tbody =
                    document.getElementById('posts-table-body');

                const noPosts =
                    document.getElementById('no-posts-row');

                if (noPosts) {
                    noPosts.remove();
                }

                if (document.querySelector(`[data-post-id="${data.id}"]`)) {
                    return;
                }

                const row = document.createElement('tr');
                row.setAttribute('data-post-id', data.id);
                row.className = 'table-success';

                const imageCell = data.image
                    ? `<td class="text-center"><img src="${escapeHtml(data.image)}" alt="Post image" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;"></td>`
                    : `<td><span class="text-muted fst-italic">No image</span></td>`;

                const categoryCell = data.category
                    ? `<td><span class="badge bg-info text-dark">${escapeHtml(data.category)}</span></td>`
                    : `<td><span class="text-muted fst-italic">&mdash;</span></td>`;

                row.innerHTML = `
                    <td>#${data.id}</td>
                    <td><strong>${escapeHtml(data.title)}</strong> <span class="badge bg-success">NEW</span></td>
                    ${categoryCell}
                    ${imageCell}
                    <td>${escapeHtml(data.body)}</td>
                    <td><span class="badge bg-secondary">${escapeHtml(data.user_name)}</span></td>
                    <td>${escapeHtml(data.created_at)}</td>
                    <td class="text-center">
                        <a href="/posts/${data.id}/edit" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa fa-edit"></i></a>
                    </td>
                `;

                tbody.prepend(row);

                realtimeEvent.className = 'alert alert-success';
                realtimeEvent.innerHTML = `
                    <i class="fa fa-bolt"></i>
                    <strong>Real-time update:</strong>
                    New post <strong>"${escapeHtml(data.title)}"</strong> received through Reverb.
                `;

                setTimeout(() => {
                    row.classList.remove('table-success');
                }, 5000);

            });


        /*
        |------------------------------------------------------------------
        | Post Update Event
        |------------------------------------------------------------------
        */

        window.Echo
            .channel('posts')
            .listen('.update', function (data) {

                const row = document.querySelector(
                    `[data-post-id="${data.id}"]`
                );

                if (!row) return;

                const cells = row.querySelectorAll('td');

                if (cells.length >= 2) {
                    cells[1].innerHTML = `<strong>${escapeHtml(data.title)}</strong>`;
                }

                if (cells.length >= 3) {
                    cells[2].innerHTML = data.category
                        ? `<span class="badge bg-info text-dark">${escapeHtml(data.category)}</span>`
                        : `<span class="text-muted fst-italic">&mdash;</span>`;
                }

                if (cells.length >= 5) {
                    cells[4].textContent = escapeHtml(data.body);
                }

                realtimeEvent.className = 'alert alert-info';
                realtimeEvent.innerHTML = `
                    <i class="fa fa-edit"></i>
                    <strong>Real-time update:</strong>
                    Post <strong>"${escapeHtml(data.title)}"</strong> updated.
                `;

            });


        /*
        |------------------------------------------------------------------
        | Post Delete Event
        |------------------------------------------------------------------
        */

        window.Echo
            .channel('posts')
            .listen('.delete', function (data) {

                const row = document.querySelector(
                    `[data-post-id="${data.id}"]`
                );

                if (row) {
                    row.remove();
                }

                realtimeEvent.className = 'alert alert-warning';
                realtimeEvent.innerHTML = `
                    <i class="fa fa-trash"></i>
                    <strong>Real-time update:</strong>
                    Post <strong>"${escapeHtml(data.title)}"</strong> deleted.
                `;

            });


        /*
        |------------------------------------------------------------------
        | Real-Time Notification (Private Channel per User)
        |------------------------------------------------------------------
        */

        window.Echo
            .private(`notification.${userId}`)
            .listen('.created', function (data) {

                updateNotificationCount(data.unread_count);

                realtimeEvent.className = 'alert alert-warning';
                realtimeEvent.innerHTML = `
                    <i class="fa fa-bell"></i>
                    <strong>New notification:</strong>
                    ${escapeHtml(data.message)}
                `;

                if (notificationPanelOpen) {
                    loadNotifications();
                }

            });


        /*
        |------------------------------------------------------------------
        | Stats Updated (real-time)
        |------------------------------------------------------------------
        */

        window.Echo
            .channel('dashboard.stats')
            .listen('.updated', function (data) {
                updateNotificationCount(data.unread_notifications);
            });


        /*
        |------------------------------------------------------------------
        | Initial Load
        |------------------------------------------------------------------
        */

        loadNotifications();

        @endauth

    }

);

</script>

@endsection
