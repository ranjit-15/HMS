@extends('admin.layout')

@section('title', 'User Details - ' . $user->name)

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Users
        </a>
    </div>

    <!-- User Profile Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                @if($user->avatar_path)
                    <img src="{{ asset('storage/' . $user->avatar_path) }}" class="w-16 h-16 rounded-full object-cover" />
                @else
                    <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-blue-600 font-bold text-2xl">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">{{ $user->name }}</h1>
                    <p class="text-slate-500">{{ $user->email }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="px-2 py-1 text-xs rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                        @if($user->is_banned)
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">Banned</span>
                        @endif
                    </div>
                </div>
            </div>
            @if($user->role !== 'admin')
                <form method="POST" action="{{ route('admin.users.toggleBan', $user) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium {{ $user->is_banned ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-red-600 text-white hover:bg-red-700' }}">
                        {{ $user->is_banned ? 'Unban User' : 'Ban User' }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <p class="text-sm text-slate-500">Total Borrows</p>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['total_borrows'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <p class="text-sm text-slate-500">Active Borrows</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['active_borrows'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <p class="text-sm text-slate-500">Overdue</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['overdue_borrows'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <p class="text-sm text-slate-500">Total Bookings</p>
            <p class="text-2xl font-bold text-amber-600">{{ $stats['total_bookings'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Borrows -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200">
            <div class="px-4 py-3 border-b border-slate-200">
                <h2 class="font-semibold text-slate-800">Recent Borrows</h2>
            </div>
            <div class="divide-y divide-slate-200">
                @forelse($recentBorrows as $borrow)
                    <div class="p-4">
                        <p class="font-medium text-slate-800">{{ $borrow->book->title ?? 'Unknown Book' }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs px-2 py-0.5 rounded-full 
                                {{ $borrow->status === 'returned' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $borrow->status === 'borrowed' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $borrow->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                {{ $borrow->status === 'declined' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ ucfirst($borrow->status) }}
                            </span>
                            <span class="text-xs text-slate-400">{{ $borrow->requested_at?->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-500 text-sm">No borrow history</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200">
            <div class="px-4 py-3 border-b border-slate-200">
                <h2 class="font-semibold text-slate-800">Recent Hive Bookings</h2>
            </div>
            <div class="divide-y divide-slate-200">
                @forelse($recentBookings as $booking)
                    <div class="p-4">
                        <p class="font-medium text-slate-800">{{ $booking->table->name ?? 'Unknown Table' }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs px-2 py-0.5 rounded-full 
                                {{ $booking->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $booking->status === 'confirmed' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $booking->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                {{ $booking->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                            <span class="text-xs text-slate-400">{{ $booking->start_at?->format('M d, Y g:i A') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-500 text-sm">No booking history</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
