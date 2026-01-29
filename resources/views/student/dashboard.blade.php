@extends('layouts.student')

@section('title', 'Student Dashboard')
@section('header', 'Welcome Back!')
@section('subheader', auth()->user()->name ?? 'Student')

@section('content')
    {{-- Overdue Alert --}}
    @if(($stats['overdueBooks'] ?? 0) > 0)
        <div
            class="rounded-xl border-l-4 border-red-500 bg-red-50 px-5 py-4 text-sm text-red-800 flex items-center gap-4 shadow-sm">
            <div class="rounded-full bg-red-100 p-3">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="flex-1">
                <p class="font-semibold">Attention Required!</p>
                <p>You have {{ $stats['overdueBooks'] }} overdue book(s). Please return them to avoid additional fines.</p>
            </div>
            <a href="{{ route('student.activity') }}"
                class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition-colors">View
                Activity</a>
        </div>
    @endif

    {{-- Library Notice --}}
    <div
        class="rounded-xl border border-amber-200 bg-gradient-to-r from-amber-50 to-yellow-50 px-5 py-4 text-sm text-amber-800 flex items-center gap-3">
        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Please maintain silence in the library and Hive study areas. Thank you for your cooperation!</span>
    </div>

    {{-- Quick Stats Row --}}
    <div class="flex gap-4 overflow-x-auto no-scrollbar py-1">
        {{-- Active Table Bookings --}}
        <div class="min-w-[240px] rounded-xl bg-gradient-to-br from-[#f59e0b] to-[#fbbf24] p-5 text-white shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wider text-white/80 font-medium">Active Bookings</p>
                    <p class="text-4xl font-bold mt-1">{{ $stats['activeBookings'] ?? 0 }}</p>
                </div>
                <div class="rounded-full bg-white/20 p-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            @if(($stats['pendingBookings'] ?? 0) > 0)
                <p class="text-xs text-white/80 mt-3 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                    {{ $stats['pendingBookings'] }} pending confirmation
                </p>
            @else
                <p class="text-xs text-white/60 mt-3">Hive study tables</p>
            @endif
        </div>

        {{-- Borrowed Books --}}
        <div class="min-w-[240px] rounded-xl bg-gradient-to-br from-[#8b0000] to-[#bf4040] p-5 text-white shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wider text-white/80 font-medium">Borrowed Books</p>
                    <p class="text-4xl font-bold mt-1">{{ $stats['borrowedBooks'] ?? 0 }}</p>
                </div>
                <div class="rounded-full bg-white/20 p-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
            </div>
            @if(($stats['pendingBorrows'] ?? 0) > 0)
                <p class="text-xs text-white/80 mt-3 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                    {{ $stats['pendingBorrows'] }} pending/approved
                </p>
            @else
                <p class="text-xs text-white/60 mt-3">From library</p>
            @endif
        </div>

        {{-- Favorites --}}
        <div class="min-w-[240px] rounded-xl bg-white border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wider text-slate-500 font-medium">Saved Books</p>
                    <p class="text-4xl font-bold text-slate-800 mt-1">{{ $stats['favorites'] ?? 0 }}</p>
                </div>
                <div class="rounded-full bg-rose-100 p-3">
                    <svg class="w-7 h-7 text-rose-500" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                    </svg>
                </div>
            </div>
            <a href="{{ route('student.favorites') }}"
                class="text-xs text-[#8b0000] mt-3 inline-flex items-center gap-1 hover:underline font-medium">
                View wishlist →
            </a>
        </div>

        {{-- Total Activity --}}
        <div class="min-w-[240px] rounded-xl bg-white border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wider text-slate-500 font-medium">Total Activity</p>
                    <p class="text-4xl font-bold text-slate-800 mt-1">
                        {{ ($stats['totalBookings'] ?? 0) + ($stats['totalBorrows'] ?? 0) }}
                    </p>
                </div>
                <div class="rounded-full bg-purple-100 p-3">
                    <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-3">
                {{ $stats['totalBookings'] ?? 0 }} bookings • {{ $stats['totalBorrows'] ?? 0 }} borrows
            </p>
        </div>
    </div>

    {{-- Quick Actions Row --}}
    <div class="flex gap-4 overflow-x-auto no-scrollbar py-1 mt-4">
        <a class="group rounded-xl border-2 border-amber-200 bg-gradient-to-br from-white to-amber-50 p-6 shadow-sm hover:shadow-lg hover:border-amber-400 transition-all"
            href="{{ route('student.hive') }}">
            <div class="flex items-center gap-4">
                <div
                    class="rounded-xl bg-gradient-to-br from-[#f59e0b] to-[#fbbf24] p-3 group-hover:scale-110 transition-transform">
                    <img src="{{ asset('images/hive.png') }}" alt="Hive" class="h-8 w-8 object-contain"
                        onerror="this.innerHTML='🐝'">
                </div>
                <div>
                    <div class="text-xs uppercase tracking-wide text-amber-600 font-semibold">Hive</div>
                    <div class="text-lg font-bold text-slate-800">Book a Table</div>
                </div>
            </div>
            <p class="text-sm text-slate-600 mt-3">Reserve your study space in the co-working area</p>
        </a>

        <a class="min-w-[260px] group rounded-xl border-2 border-blue-200 bg-gradient-to-br from-white to-blue-50 p-6 shadow-sm hover:shadow-lg hover:border-blue-400 transition-all"
            href="{{ route('student.library') }}">
            <div class="flex items-center gap-4">
                <div
                    class="rounded-xl bg-gradient-to-br from-[#8b0000] to-[#bf4040] p-3 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs uppercase tracking-wide text-blue-600 font-semibold">Library</div>
                    <div class="text-lg font-bold text-slate-800">Browse Books</div>
                </div>
            </div>
            <p class="text-sm text-slate-600 mt-3">Search and borrow from our collection</p>
        </a>

        <a class="min-w-[260px] group rounded-xl border-2 border-emerald-200 bg-gradient-to-br from-white to-emerald-50 p-6 shadow-sm hover:shadow-lg hover:border-emerald-400 transition-all"
            href="{{ route('student.calendar') }}">
            <div class="flex items-center gap-4">
                <div
                    class="rounded-xl bg-gradient-to-br from-emerald-600 to-emerald-500 p-3 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs uppercase tracking-wide text-emerald-600 font-semibold">Calendar</div>
                    <div class="text-lg font-bold text-slate-800">View Schedule</div>
                </div>
            </div>
            <p class="text-sm text-slate-600 mt-3">Check closures and upcoming events</p>
        </a>

        <a class="min-w-[260px] group rounded-xl border-2 border-purple-200 bg-gradient-to-br from-white to-purple-50 p-6 shadow-sm hover:shadow-lg hover:border-purple-400 transition-all"
            href="{{ route('student.activity') }}">
            <div class="flex items-center gap-4">
                <div
                    class="rounded-xl bg-gradient-to-br from-purple-600 to-purple-500 p-3 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs uppercase tracking-wide text-purple-600 font-semibold">Activity</div>
                    <div class="text-lg font-bold text-slate-800">Your History</div>
                </div>
            </div>
            <p class="text-sm text-slate-600 mt-3">View all bookings and borrow history</p>
        </a>
    </div>

    {{-- Recent Activity Section (single row sequence) --}}
    <div class="flex gap-6 overflow-x-auto no-scrollbar mt-6 py-1">
        {{-- Recent Table Bookings --}}
        <div class="min-w-[520px] rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div
                class="border-b border-slate-100 bg-gradient-to-r from-amber-50 to-white px-5 py-4 flex items-center justify-between">
                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                    <span class="text-xl">🐝</span> Recent Hive Bookings
                </h3>
                <a href="{{ route('student.activity') }}" class="text-xs text-[#8b0000] hover:underline font-medium">View
                    all →</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentBookings ?? [] as $booking)
                    <div class="px-5 py-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
                        <div>
                            <p class="font-medium text-slate-800">{{ $booking->table->name ?? 'Table' }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $booking->created_at->format('M j, Y g:i A') }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                                            @if($booking->status === 'confirmed') bg-emerald-100 text-emerald-700
                                                            @elseif($booking->status === 'pending') bg-amber-100 text-amber-700
                                                            @elseif($booking->status === 'cancelled') bg-slate-100 text-slate-600
                                                            @else bg-slate-100 text-slate-600
                                                            @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-slate-500 text-sm">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <p>No table bookings yet</p>
                        <a href="{{ route('student.hive') }}"
                            class="text-[#f59e0b] hover:underline font-medium mt-2 inline-block">Book a table now →</a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Book Borrows --}}
        <div class="min-w-[520px] rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div
                class="border-b border-slate-100 bg-gradient-to-r from-blue-50 to-white px-5 py-4 flex items-center justify-between">
                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                    <span class="text-xl">📚</span> Recent Book Borrows
                </h3>
                <a href="{{ route('student.activity') }}" class="text-xs text-[#8b0000] hover:underline font-medium">View
                    all →</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentBorrows ?? [] as $borrow)
                    @php
                        $isOverdue = $borrow->due_at && $borrow->due_at->isPast() && $borrow->status === 'borrowed';
                        $daysLeft = $borrow->due_at && $borrow->status === 'borrowed' ? now()->diffInDays($borrow->due_at, false) : null;
                    @endphp
                    <div
                        class="px-5 py-4 flex items-center justify-between hover:bg-slate-50 transition-colors {{ $isOverdue ? 'bg-red-50' : '' }}">
                        <div class="flex-1">
                            <p class="font-medium text-slate-800">{{ $borrow->book->title ?? 'Book' }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                @if($borrow->due_at && $borrow->status === 'borrowed')
                                    @if($isOverdue)
                                        <span class="text-red-600 font-semibold">⚠️ {{ abs($daysLeft) }} day(s) overdue</span>
                                    @elseif($daysLeft <= 3)
                                        <span class="text-amber-600 font-semibold">⏰ {{ $daysLeft }} day(s) left</span>
                                    @else
                                        <span class="text-slate-600">{{ $daysLeft }} day(s) left</span>
                                    @endif
                                    <span class="text-slate-400 ml-1">• Due {{ $borrow->due_at->format('M j') }}</span>
                                @else
                                    {{ $borrow->created_at->format('M j, Y') }}
                                @endif
                            </p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                                            @if($borrow->status === 'borrowed') {{ $isOverdue ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}
                                                            @elseif($borrow->status === 'returned') bg-emerald-100 text-emerald-700
                                                            @elseif($borrow->status === 'pending') bg-amber-100 text-amber-700
                                                            @elseif($borrow->status === 'approved') bg-cyan-100 text-cyan-700
                                                            @else bg-slate-100 text-slate-600
                                                            @endif">
                            {{ $isOverdue ? 'Overdue' : ucfirst($borrow->status) }}
                        </span>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-slate-500 text-sm">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <p>No book borrows yet</p>
                        <a href="{{ route('student.library') }}"
                            class="text-[#8b0000] hover:underline font-medium mt-2 inline-block">Browse library now →</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection