@extends('admin.layout')

@section('title', 'Hive Tables')
@section('header', 'Hive Tables')

@section('content')
    <div class="space-y-4">
        {{-- Header with Add Button --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-2xl">🐝</span>
                <div>
                    <p class="text-sm text-slate-600">Manage study tables and monitor bookings</p>
                </div>
            </div>
            <a href="{{ route('admin.tables.create') }}"
                class="rounded-lg bg-[#f59e0b] px-4 py-2 text-white text-sm font-semibold hover:bg-[#d97706] transition-colors shadow-sm">
                + Add Table
            </a>
        </div>

        {{-- Stats Row --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="rounded-lg bg-green-50 border border-green-200 p-4">
                <div class="text-2xl font-bold text-green-700">
                    {{ $tables->where('is_active', true)->count() - $tables->filter(fn($t) => $t->active_booking_id)->count() }}
                </div>
                <div class="text-xs text-green-600 font-medium">Available Now</div>
            </div>
            <div class="rounded-lg bg-red-50 border border-red-200 p-4">
                <div class="text-2xl font-bold text-red-700">{{ $tables->filter(fn($t) => $t->active_booking_id)->count() }}
                </div>
                <div class="text-xs text-red-600 font-medium">Currently Booked</div>
            </div>
            <div class="rounded-lg bg-slate-50 border border-slate-200 p-4">
                <div class="text-2xl font-bold text-slate-700">{{ $tables->count() }}</div>
                <div class="text-xs text-slate-600 font-medium">Total Tables</div>
            </div>
            <div class="rounded-lg bg-amber-50 border border-amber-200 p-4">
                <div class="text-2xl font-bold text-amber-700">{{ $tables->sum('capacity') }}</div>
                <div class="text-xs text-amber-600 font-medium">Total Capacity</div>
            </div>
        </div>

        {{-- Table Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @forelse($tables as $table)
                @php
                    $hasBooking = $table->active_booking_id !== null;
                    $activeBooking = $hasBooking ? $table->bookings()->where('id', $table->active_booking_id)->first() : null;
                    $cardColor = $hasBooking ? 'border-red-200 hover:border-red-300 bg-red-50' : 'border-emerald-200 hover:border-emerald-300 bg-emerald-50';
                    $iconBg = $hasBooking ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600';
                    $badgeBg = $hasBooking ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700';
                @endphp
                <div class="bg-white rounded-xl shadow-sm border-2 p-4 text-center transition-all hover:shadow-md {{ $cardColor }}">
                    <div class="mx-auto w-12 h-12 rounded-full flex items-center justify-center mb-3 {{ $iconBg }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>

                    <h3 class="font-semibold text-slate-800">{{ $table->name }}</h3>
                    <p class="text-xs text-slate-500 mt-1">{{ $table->capacity }} seats</p>

                    <div class="mt-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $badgeBg }}">
                            {{ $hasBooking ? 'Occupied' : 'Available' }}
                        </span>
                    </div>

                    @if($hasBooking && $activeBooking)
                        <div class="mt-2 text-xs text-slate-500 space-y-1">
                            <p>Booked by <span class="font-semibold text-slate-700">{{ $activeBooking->user->name ?? 'Unknown' }}</span></p>
                            <p>Ends at <span class="font-semibold text-red-600">{{ $activeBooking->end_at?->format('g:i A') ?? '—' }}</span></p>
                        </div>
                    @else
                        <div class="mt-2 text-xs text-emerald-600">Ready for booking</div>
                    @endif

                    <div class="mt-4 flex flex-col gap-2 text-xs">
                        <a href="{{ route('admin.tables.edit', $table) }}" class="w-full px-3 py-2 rounded-lg bg-blue-50 text-blue-700 font-medium hover:bg-blue-100">Edit</a>
                        @if($hasBooking)
                            <form action="{{ route('admin.tables.endBooking', $table) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-3 py-2 rounded-lg bg-amber-50 text-amber-700 font-medium hover:bg-amber-100">End Booking</button>
                            </form>
                        @endif
                        @if($table->deleted_at)
                            <form action="{{ route('admin.tables.restore', $table->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-3 py-2 rounded-lg bg-green-50 text-green-700 font-medium hover:bg-green-100">Restore</button>
                            </form>
                        @else
                            <form action="{{ route('admin.tables.destroy', $table) }}" method="POST" onsubmit="return confirm('Delete this table?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-3 py-2 rounded-lg bg-red-50 text-red-700 font-medium hover:bg-red-100">Delete</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-xl border border-slate-200 bg-white p-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p class="text-slate-500 mb-3">No tables yet</p>
                    <a href="{{ route('admin.tables.create') }}" class="text-[#f59e0b] hover:underline font-medium">+ Add your
                        first table</a>
                </div>
            @endforelse
        </div>

        @if($tables->hasPages())
            <div class="mt-4">{{ $tables->links() }}</div>
        @endif
    </div>
@endsection