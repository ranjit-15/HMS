<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Techspire College</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/hms-theme-overrides.css') }}">
</head>

<body class="min-h-screen bg-[#f3f4f6] font-sans">
    <!-- Skip to main content link for accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Top Bar - Deep Red (match student) -->
    <div class="bg-[#8b0000] text-white text-sm py-2">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="opacity-80 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Techspire College Admin
                </span>
            </div>
            <div class="flex items-center gap-4">
                <span class="opacity-80">Welcome, {{ auth('admin')->user()->name ?? 'Administrator' }}</span>
            </div>
        </div>
    </div>

    <!-- Main Header - Modern Accent -->
    <header class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 hover:opacity-90 transition-opacity">
                    <img src="{{ asset('images/techspire-logo.png') }}" alt="Techspire College" class="h-10 w-auto" />
                </a>
                <!-- Desktop Navigation (wrap to avoid horizontal scroll) -->
                <nav class="flex flex-1 justify-center items-center gap-1 text-sm font-medium flex-wrap" role="navigation" aria-label="Admin navigation">
                    <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-lg transition-all @if(request()->routeIs('admin.dashboard')) bg-[#8b0000] text-white shadow-sm @else text-slate-600 hover:text-[#8b0000] hover:bg-[#8b0000]/5 @endif">Dashboard</a>
                        <div class="relative group">
                            <a href="{{ route('admin.tables.index') }}" class="px-3 py-2 rounded-lg transition-all @if(request()->routeIs('admin.tables.*') || request()->routeIs('admin.bookings.*')) bg-[#8b0000] text-white shadow-sm @else text-slate-600 hover:text-[#8b0000] hover:bg-[#8b0000]/5 @endif">Manage Tables</a>
                            <div class="absolute left-0 mt-2 w-48 rounded-xl border border-slate-200 bg-white shadow-xl py-2 z-50 hidden group-hover:block">
                                <a href="{{ route('admin.tables.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-[#8b0000]/5">Tables List</a>
                                <a href="{{ route('admin.bookings.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-[#8b0000]/5">Table Bookings</a>
                            </div>
                        </div>
                    <div class="relative group">
                        <a href="{{ route('admin.books.index') }}" class="px-3 py-2 rounded-lg transition-all @if(request()->routeIs('admin.books.*') || request()->routeIs('admin.reviews.*') || request()->routeIs('admin.borrows.*') || request()->routeIs('admin.bookings.*')) bg-[#8b0000] text-white shadow-sm @else text-slate-600 hover:text-[#8b0000] hover:bg-[#8b0000]/5 @endif">Book Management</a>
                        <div class="absolute left-0 mt-2 w-48 rounded-xl border border-slate-200 bg-white shadow-xl py-2 z-50 hidden group-hover:block">
                            <a href="{{ route('admin.books.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-[#8b0000]/5">Books List</a>
                                <a href="{{ route('admin.borrows.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-[#8b0000]/5">Borrow Records</a>
                                <a href="{{ route('admin.reviews.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-[#8b0000]/5">Book Reviews</a>
                        </div>
                    </div>
                    {{-- Calendar removed from navbar; available in other UI areas like Dashboard/Profile --}}
                    @php
                        $adminId = auth('admin')->id();
                        $adminNotifications = \App\Models\AdminNotification::forUser($adminId)->active()->latest()->limit(5)->get();
                        $adminUnreadCount = \App\Models\AdminNotification::forUser($adminId)->active()
                            ->whereDoesntHave('readByUsers', fn($q) => $q->where('user_id', $adminId))->count();
                    @endphp
                    <div class="relative group">
                        <button aria-haspopup="true" class="px-3 py-2 rounded-lg transition-all text-slate-600 hover:text-[#8b0000] hover:bg-[#8b0000]/5 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            @if($adminUnreadCount > 0)
                                <span class="inline-flex items-center justify-center text-xs font-semibold bg-rose-500 text-white rounded-full w-5 h-5">{{ $adminUnreadCount }}</span>
                            @endif
                        </button>
                        <div class="absolute right-0 mt-2 w-80 rounded-xl border border-slate-200 bg-white shadow-xl py-2 z-50 hidden group-hover:block">
                            <div class="px-4 py-2 text-sm font-semibold">Notifications</div>
                            <div class="divide-y divide-slate-100 max-h-64 overflow-auto">
                                @forelse($adminNotifications as $n)
                                    <a href="{{ route('admin.notifications.index') }}" class="block px-4 py-3 hover:bg-slate-50 text-sm">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-slate-800">{{ $n->title }}</div>
                                                <div class="text-xs text-slate-500 mt-1">{{ Str::limit($n->message, 120) }}</div>
                                            </div>
                                            <div class="text-xs text-slate-400 ml-3">{{ $n->created_at->diffForHumans() }}</div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="px-4 py-3 text-sm text-slate-500">No notifications</div>
                                @endforelse
                            </div>
                            <div class="text-center py-2">
                                <a href="{{ route('admin.notifications.index') }}" class="text-sm text-[#8b0000] hover:underline">View all</a>
                            </div>
                        </div>
                    </div>
                    <div class="relative group">
                        <a href="{{ route('admin.settings.edit') }}" class="px-3 py-2 rounded-lg transition-all text-slate-600 hover:text-[#8b0000] hover:bg-[#8b0000]/5 @if(request()->routeIs('admin.settings.*') || request()->routeIs('admin.reports.*') || request()->routeIs('admin.users.*')) bg-[#8b0000] text-white shadow-sm @endif">Settings</a>
                        <div class="absolute left-0 mt-2 w-44 rounded-xl border border-slate-200 bg-white shadow-xl py-2 z-50 hidden group-hover:block">
                            <a href="{{ route('admin.settings.edit') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-[#8b0000]/5">General Settings</a>
                            <a href="{{ route('admin.reports.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-[#8b0000]/5">Reports</a>
                            <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-[#8b0000]/5">Users</a>
                        </div>
                    </div>
                </nav>

                <!-- Mobile hamburger removed to keep navbar horizontal on all sizes -->
            </div>
        </div>
    </header>

    <!-- Page Header - Subtle Gradient -->
    <div class="bg-gradient-to-b from-white to-[#f3f4f6] border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">@yield('header', 'Admin Panel')</h1>
        </div>
    </div>

    <!-- Main Content -->
    <main id="main-content" class="max-w-7xl mx-auto px-6 py-8 space-y-6" role="main">
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">Please fix the following errors:</span>
                </div>
                <ul class="list-disc pl-7 space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-12 border-t border-slate-200 bg-white py-4">
        <div class="max-w-7xl mx-auto px-6 text-center text-sm text-slate-500">
            <p>© {{ date('Y') }} <span class="text-[#8b0000] font-medium">Techspire College</span> • Admin Panel</p>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>