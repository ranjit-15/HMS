<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Techspire College - Library & Hive Management System">
    <title>@yield('title', 'Student') - Techspire College</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/hms-theme-overrides.css') }}">
    @include('components.alpine-head')
</head>

<body class="min-h-screen bg-[#f3f4f6]">
    <!-- Skip to main content link for accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Top Bar - Deep Red -->
    <div class="bg-[#8b0000] text-white text-sm py-2">
        <div class="max-w-6xl mx-auto px-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="opacity-80 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    New Baneshwor-10, Kathmandu
                </span>
            </div>
            <div class="flex items-center gap-4">
                <span class="opacity-80">Welcome, {{ auth()->user()->name ?? 'Student' }}</span>
            </div>
        </div>
    </div>

    <!-- Main Header - White with Blue accent -->
    <header class="bg-white shadow-sm border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <!-- Logo / Title -->
                <a href="{{ route('student.dashboard') }}"
                    class="flex items-center gap-3 hover:opacity-90 transition-opacity flex-shrink-0">
                    <img src="{{ asset('images/techspire-logo.png') }}" alt="Techspire College"
                        class="h-12 md:h-14 w-auto" loading="lazy" decoding="async" />
                </a>
                <!-- Centered Menu (always horizontal) -->
                <nav class="flex flex-1 justify-center items-center gap-1 text-sm whitespace-nowrap overflow-x-auto no-scrollbar"
                    role="navigation" aria-label="Main navigation">
                    <a href="{{ route('student.dashboard') }}"
                        class="px-3 py-2 rounded-lg transition-all font-medium @if(request()->routeIs('student.dashboard')) bg-[#8b0000] text-white shadow-sm @else text-slate-600 hover:text-[#8b0000] hover:bg-[#8b0000]/5 @endif"
                        @if(request()->routeIs('student.dashboard')) aria-current="page" @endif>Dashboard</a>
                    <a href="{{ route('student.hive') }}"
                        class="px-3 py-2 rounded-lg transition-all font-medium @if(request()->routeIs('student.hive*')) bg-[#f59e0b] text-white shadow-sm @else text-slate-600 hover:text-[#b45309] hover:bg-amber-50 @endif"
                        @if(request()->routeIs('student.hive*')) aria-current="page" @endif>Hive</a>
                    <a href="{{ route('student.library') }}"
                        class="px-3 py-2 rounded-lg transition-all font-medium @if(request()->routeIs('student.library*') || request()->routeIs('student.favorites')) bg-[#8b0000] text-white shadow-sm @else text-slate-600 hover:text-[#8b0000] hover:bg-[#8b0000]/5 @endif"
                        @if(request()->routeIs('student.library*') || request()->routeIs('student.favorites'))
                        aria-current="page" @endif>Library</a>
                    {{-- Calendar removed from main navbar; accessible via profile/dashboard pages --}}
                    <a href="{{ route('student.activity') }}"
                        class="px-3 py-2 rounded-lg transition-all font-medium @if(request()->routeIs('student.activity')) bg-[#8b0000] text-white shadow-sm @else text-slate-600 hover:text-[#8b0000] hover:bg-[#8b0000]/5 @endif"
                        @if(request()->routeIs('student.activity')) aria-current="page" @endif>My Borrowings</a>
                </nav>
                <!-- Right: Notifications + Profile -->
                <div class="flex items-center gap-3">
                    <a id="notification-link" href="{{ route('student.notifications') }}"
                        class="relative p-2 rounded-lg hover:bg-slate-50">
                        <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if(($unreadNotificationsCount ?? 0) > 0)
                            <span id="notification-badge"
                                class="absolute -top-1 -right-1 h-5 w-5 rounded-full bg-red-500 text-[10px] font-bold text-white flex items-center justify-center"
                                aria-label="{{ $unreadNotificationsCount }} unread">{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
                        @endif
                    </a>

                    <details class="relative">
                        <summary
                            class="cursor-pointer select-none rounded-lg border border-slate-200 px-3 py-2 text-slate-700 bg-white flex items-center gap-2 hover:border-[#8b0000] hover:shadow-sm transition-all">
                            @if(auth()->user()->avatar_path)
                                <img src="{{ Storage::url(auth()->user()->avatar_path) }}" alt=""
                                    class="h-7 w-7 rounded-full object-cover ring-2 ring-[#8b0000]/20" />
                            @else
                                <span
                                    class="h-7 w-7 rounded-full bg-gradient-to-br from-[#8b0000] to-[#bf4040] flex items-center justify-center text-white text-xs font-bold shadow-sm">{{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}</span>
                            @endif
                            <span
                                class="hidden sm:inline font-medium">{{ explode(' ', auth()->user()->name ?? 'User')[0] }}</span>
                            <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <div
                            class="absolute right-0 mt-2 w-56 rounded-xl border border-slate-200 bg-white shadow-xl z-50 overflow-hidden">
                            <div
                                class="px-4 py-3 bg-gradient-to-r from-[#8b0000]/5 to-transparent border-b border-slate-100">
                                <p class="font-medium text-slate-800 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <a class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 hover:bg-[#8b0000]/5 hover:text-[#8b0000] transition-colors"
                                href="{{ route('student.profile') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                My Profile
                            </a>
                            <a class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 hover:bg-[#8b0000]/5 hover:text-[#8b0000] transition-colors"
                                href="{{ route('student.favorites') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                My Favorites
                            </a>
                            <a class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 hover:bg-[#8b0000]/5 hover:text-[#8b0000] transition-colors"
                                href="{{ route('student.calendar') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Calendar
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100">
                                @csrf
                                <button type="submit"
                                    class="flex items-center gap-3 w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </details>
                </div>
            </div>
        </div>
    </header>

    <!-- Page Header - Blue Gradient -->
    <div class="bg-gradient-to-r from-[#8b0000]/10 via-[#bf4040]/5 to-transparent border-b border-slate-100">
        <div class="max-w-6xl mx-auto px-6 py-4">
            <h2 class="text-2xl font-bold text-[#8b0000]">@yield('header', 'Student Portal')</h2>
            <p class="text-sm text-slate-600 mt-1">@yield('subheader', '')</p>
        </div>
    </div>

    <!-- Main Content -->
    <main id="main-content" class="max-w-7xl mx-auto px-6 py-6 space-y-6" role="main">

        @if(session('status'))
            <div
                class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('status') }}
            </div>
        @endif
        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">Please fix the following errors:</span>
                </div>
                <ul class="list-disc pl-7 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-12 border-t border-slate-200 bg-white py-6">
        <div class="max-w-6xl mx-auto px-6 text-center text-sm text-slate-500">
            <p>© {{ date('Y') }} <span class="text-[#8b0000] font-medium">Techspire College</span>. All rights reserved.
            </p>
            <p class="text-xs mt-1 text-slate-400">New Baneshwor-10, Kathmandu, Nepal</p>
        </div>
    </footer>

    @stack('scripts')
    <script>
        // Centralized due-countdown updater for any element with class 'due-countdown' and bookings '[data-countdown]'
        (function () {
            function formatRemaining(diffMs) {
                if (diffMs <= 0) return { text: 'Overdue', cls: 'text-red-600' };
                const days = Math.floor(diffMs / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                if (days > 0) return { text: days + ' day' + (days > 1 ? 's' : '') + ' left' };
                if (hours > 0) return { text: hours + ' hour' + (hours > 1 ? 's' : '') + ' left' };
                const mins = Math.max(1, Math.ceil((diffMs % (1000 * 60 * 60)) / (1000 * 60)));
                return { text: mins + ' minute' + (mins > 1 ? 's' : '') + ' left' };
            }

            function updateAll() {
                const now = new Date();
                // due-countdown elements
                document.querySelectorAll('.due-countdown').forEach(el => {
                    const dueStr = el.dataset.due;
                    if (!dueStr) return;
                    const due = new Date(dueStr);
                    if (isNaN(due)) return;
                    const diffMs = due - now;
                    const out = formatRemaining(diffMs);
                    el.textContent = out.text;
                    if (diffMs <= 0) el.classList.add('text-red-600');
                });

                // booking countdowns (data-countdown elements)
                document.querySelectorAll('[data-countdown]').forEach(el => {
                    const end = new Date(el.dataset.end || el.dataset.due);
                    if (isNaN(end)) return;
                    const diffMs = end - now;
                    const out = formatRemaining(diffMs);
                    el.textContent = out.text;
                    if (diffMs <= 0) el.classList.add('text-red-600');
                });
            }

            document.addEventListener('DOMContentLoaded', function () {
                updateAll();
                setInterval(updateAll, 60 * 1000);

                // Notification badge click behavior
                const link = document.getElementById('notification-link');
                if (!link) return;
                link.addEventListener('click', function (e) {
                    const badge = document.getElementById('notification-badge');
                    if (!badge) return; // follow link normally
                    e.preventDefault();
                    fetch("{{ route('student.notifications.markAllRead') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({})
                    }).then(function (res) { return res.json(); })
                        .then(function (data) {
                            if (data && data.success) {
                                if (badge && badge.parentNode) badge.remove();
                            }
                            window.location = "{{ route('student.notifications') }}";
                        }).catch(function () {
                            window.location = "{{ route('student.notifications') }}";
                        });
                });
            });
        })();
    </script>
</body>

</html>