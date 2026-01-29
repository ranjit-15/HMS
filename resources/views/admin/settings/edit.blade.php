@extends('admin.layout')

@section('title', 'Settings')
@section('header', 'Settings')

@section('content')
<div class="grid gap-6 lg:grid-cols-2">
    <!-- General Settings -->
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">⚙️ General Settings</h2>
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-800 mb-1">Default booking duration (minutes)</label>
                <input type="number" name="default_booking_duration_minutes" value="{{ old('default_booking_duration_minutes', $settings['default_booking_duration_minutes']) }}" class="w-full rounded border border-slate-300 px-3 py-2 text-sm" min="15" max="480" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-800 mb-1">Booking auto-release (minutes)</label>
                <input type="number" name="booking_auto_release_minutes" value="{{ old('booking_auto_release_minutes', $settings['booking_auto_release_minutes']) }}" class="w-full rounded border border-slate-300 px-3 py-2 text-sm" min="5" max="240" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-800 mb-1">Default borrow duration (days)</label>
                <input type="number" name="default_borrow_duration_days" value="{{ old('default_borrow_duration_days', $settings['default_borrow_duration_days']) }}" class="w-full rounded border border-slate-300 px-3 py-2 text-sm" min="1" max="60" required>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-white text-sm font-semibold hover:bg-indigo-700">Save Settings</button>
            </div>
        </form>
    </div>

    <!-- Quick Links -->
    <div class="space-y-4">
        <!-- Calendar Management -->
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">📅 Calendar Management</h2>
            <div class="space-y-3">
                <a href="{{ route('admin.closures.index') }}" class="flex items-center justify-between p-3 rounded-lg border border-slate-200 hover:border-red-300 hover:bg-red-50 transition-colors">
                    <div>
                        <p class="font-medium text-slate-800">🚫 Closures</p>
                        <p class="text-sm text-slate-500">Manage library/hive closed dates</p>
                    </div>
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="{{ route('admin.events.index') }}" class="flex items-center justify-between p-3 rounded-lg border border-slate-200 hover:border-blue-300 hover:bg-blue-50 transition-colors">
                    <div>
                        <p class="font-medium text-slate-800">📆 Events</p>
                        <p class="text-sm text-slate-500">Manage calendar events</p>
                    </div>
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Moderation -->
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">🛡️ Moderation</h2>
            <div class="space-y-3">
                <a href="{{ route('admin.reviews.index') }}" class="flex items-center justify-between p-3 rounded-lg border border-slate-200 hover:border-amber-300 hover:bg-amber-50 transition-colors">
                    <div>
                        <p class="font-medium text-slate-800">⭐ Book Reviews</p>
                        <p class="text-sm text-slate-500">Moderate user reviews</p>
                    </div>
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
