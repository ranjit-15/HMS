@extends('admin.layout')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
    <div class="space-y-6">
        {{-- Stats Row (single horizontal sequence) --}}
        <div class="flex gap-4 overflow-x-auto no-scrollbar py-1">
            {{-- Active Bookings --}}
            <div class="min-w-[220px] rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center">
                        <span class="text-2xl">🐝</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase">Active Bookings</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $activeBookingsCount ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- Books Borrowed --}}
            <div class="min-w-[220px] rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-2xl">📚</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase">Books Borrowed</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $borrowedCount ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- Overdue Items --}}
            <div class="min-w-[220px] rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <span class="text-2xl">⚠️</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase">Overdue</p>
                        <p class="text-2xl font-bold {{ ($overdueCount ?? 0) > 0 ? 'text-red-600' : 'text-slate-800' }}">
                            {{ $overdueCount ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Popular Book --}}
            <div class="min-w-[220px] rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                        <span class="text-2xl">⭐</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase">Most Popular</p>
                        <p class="text-sm font-bold text-slate-800 truncate max-w-[120px]"
                            title="{{ $popularBookTitle ?? 'N/A' }}">{{ $popularBookTitle ?? 'N/A' }}</p>
                        <p class="text-xs text-slate-500">{{ $popularBookBorrows ?? 0 }} borrows</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                <h2 class="font-semibold text-slate-800">Quick Actions</h2>
            </div>
            <div class="p-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('admin.tables.create') }}"
                    class="flex items-center gap-3 p-4 rounded-lg border border-slate-200 hover:border-amber-300 hover:bg-amber-50 transition-colors group">
                    <div
                        class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center group-hover:bg-amber-200 transition-colors">
                        <span class="text-lg">➕</span>
                    </div>
                    <div>
                        <p class="font-medium text-slate-800 group-hover:text-amber-700">Add Table</p>
                        <p class="text-xs text-slate-500">Create new Hive table</p>
                    </div>
                </a>

                <a href="{{ route('admin.books.create') }}"
                    class="flex items-center gap-3 p-4 rounded-lg border border-slate-200 hover:border-blue-300 hover:bg-blue-50 transition-colors group">
                    <div
                        class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                        <span class="text-lg">📖</span>
                    </div>
                    <div>
                        <p class="font-medium text-slate-800 group-hover:text-blue-700">Add Book</p>
                        <p class="text-xs text-slate-500">Add to library catalog</p>
                    </div>
                </a>

                <a href="{{ route('admin.notifications.index') }}"
                    class="flex items-center gap-3 p-4 rounded-lg border border-slate-200 hover:border-purple-300 hover:bg-purple-50 transition-colors group">
                    <div
                        class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                        <span class="text-lg">📢</span>
                    </div>
                    <div>
                        <p class="font-medium text-slate-800 group-hover:text-purple-700">Push Notification</p>
                        <p class="text-xs text-slate-500">Send announcement</p>
                    </div>
                </a>

                <a href="{{ route('admin.borrows.index') }}"
                    class="flex items-center gap-3 p-4 rounded-lg border border-slate-200 hover:border-cyan-300 hover:bg-cyan-50 transition-colors group">
                    <div
                        class="w-10 h-10 rounded-lg bg-cyan-100 flex items-center justify-center group-hover:bg-cyan-200 transition-colors">
                        <span class="text-lg">📋</span>
                    </div>
                    <div>
                        <p class="font-medium text-slate-800 group-hover:text-cyan-700">Manage Borrows</p>
                        <p class="text-xs text-slate-500">Review borrow requests</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Active Bookings & Borrowed Books --}}
        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Active Table Bookings --}}
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-amber-50 flex items-center justify-between">
                    <h2 class="font-semibold text-amber-800 flex items-center gap-2">
                        🐝 Active Table Bookings
                    </h2>
                    <a href="{{ route('admin.tables.index') }}" class="text-xs text-amber-600 hover:underline">View All
                        →</a>
                </div>
                <div class="divide-y divide-slate-100 max-h-[300px] overflow-y-auto">
                    @forelse($activeBookings ?? [] as $booking)
                        @php
                            $minutesLeft = now()->diffInMinutes($booking->end_at, false);
                            $hoursLeft = floor($minutesLeft / 60);
                            $minsLeft = $minutesLeft % 60;
                        @endphp
                        <div class="px-4 py-3 flex items-center justify-between text-sm hover:bg-slate-50">
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-slate-800">{{ $booking->table->name ?? 'Table' }}</div>
                                <div class="text-xs text-slate-500">{{ $booking->user->name ?? 'User' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold {{ $minutesLeft < 30 ? 'text-red-600' : 'text-amber-600' }}"
                                    data-countdown="{{ $booking->end_at->toIso8601String() }}">
                                    {{ $hoursLeft }}h {{ $minsLeft }}m left
                                </div>
                                <div class="text-xs text-slate-400">Ends {{ $booking->end_at->format('g:i A') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-6 text-center text-slate-500 text-sm">
                            <span class="text-2xl block mb-2">🟢</span>
                            All tables available
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Borrowed Books --}}
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-blue-50 flex items-center justify-between">
                    <h2 class="font-semibold text-blue-800 flex items-center gap-2">
                        📚 Borrowed Books
                    </h2>
                    <a href="{{ route('admin.borrows.index') }}" class="text-xs text-blue-600 hover:underline">View All
                        →</a>
                </div>
                <div class="divide-y divide-slate-100 max-h-[300px] overflow-y-auto">
                    @forelse($borrowedBooks ?? [] as $borrow)
                        @php
                            $isOverdue = $borrow->due_at && $borrow->due_at->isPast();
                            $daysLeft = $borrow->due_at ? now()->diffInDays($borrow->due_at, false) : null;
                        @endphp
                        <div
                            class="px-4 py-3 flex items-center justify-between text-sm hover:bg-slate-50 {{ $isOverdue ? 'bg-red-50' : '' }}">
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-slate-800 truncate max-w-[180px]">
                                    {{ $borrow->book->title ?? 'Book' }}</div>
                                <div class="text-xs text-slate-500">{{ $borrow->user->name ?? 'User' }}</div>
                            </div>
                            <div class="text-right">
                                @if($borrow->due_at)
                                    @if($isOverdue)
                                        <div class="font-semibold text-red-600">{{ abs($daysLeft) }}d overdue</div>
                                    @elseif($daysLeft <= 3)
                                        <div class="font-semibold text-amber-600">{{ $daysLeft }}d left</div>
                                    @else
                                        <div class="font-medium text-slate-600">{{ $daysLeft }}d left</div>
                                    @endif
                                    <div class="text-xs text-slate-400">Due {{ $borrow->due_at->format('M j') }}</div>
                                @else
                                    <div class="text-slate-400 text-xs">No due date</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-6 text-center text-slate-500 text-sm">
                            <span class="text-2xl block mb-2">📖</span>
                            No books currently borrowed
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- System Status & Notifications --}}
        <div class="grid gap-6 lg:grid-cols-2">
            {{-- System Status --}}
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h2 class="font-semibold text-slate-800">System Status</h2>
                    <span class="text-xs text-emerald-600 font-medium bg-emerald-100 px-2 py-1 rounded-full">● Online</span>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600">Library Hours</span>
                        <span class="font-medium text-slate-800">7:00 AM - 6:00 PM</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600">Hive Hours</span>
                        <span class="font-medium text-slate-800">8:00 AM - 8:00 PM</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600">Server Time</span>
                        <span class="font-medium text-slate-800"
                            id="server-time">{{ now()->format('M j, Y g:i:s A') }}</span>
                    </div>
                </div>
            </div>

            {{-- Notifications --}}
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 class="font-semibold text-slate-800">Notifications</h2>
                </div>
                <div class="p-5">
                    @if(($overdueCount ?? 0) > 0)
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-red-50 border border-red-200 mb-3">
                            <span class="text-lg">⚠️</span>
                            <div>
                                <p class="text-sm font-medium text-red-800">{{ $overdueCount }} overdue book(s)</p>
                                <p class="text-xs text-red-600">Please follow up with borrowers</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-emerald-50 border border-emerald-200">
                            <span class="text-lg">✅</span>
                            <div>
                                <p class="text-sm font-medium text-emerald-800">All clear!</p>
                                <p class="text-xs text-emerald-600">No pending notifications at this time</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Real-time clock
            setInterval(() => {
                const el = document.getElementById('server-time');
                if (el) {
                    el.textContent = new Date().toLocaleString('en-US', {
                        month: 'short', day: 'numeric', year: 'numeric',
                        hour: 'numeric', minute: '2-digit', second: '2-digit', hour12: true
                    });
                }
            }, 1000);
        </script>
    @endpush
@endsection