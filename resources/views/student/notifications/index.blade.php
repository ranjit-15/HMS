@extends('layouts.student')

@section('title', 'Notifications')
@section('header', 'Notifications')
@section('subheader', 'Stay updated with your library and hive activities')

@section('content')
<div class="space-y-4">
    @forelse($notifications as $notification)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start gap-4">
                {{-- Icon based on type --}}
                <div class="flex-shrink-0">
                    @if($notification->type === 'info')
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                    @elseif($notification->type === 'warning')
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-amber-100 text-amber-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </span>
                    @elseif($notification->type === 'success')
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-100 text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                    @elseif($notification->type === 'error')
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-red-100 text-red-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                    @else
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-100 text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </span>
                    @endif
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-semibold text-slate-800">{{ $notification->title }}</h3>
                    <p class="mt-1 text-sm text-slate-600">{{ $notification->message }}</p>
                    <div class="mt-2 flex items-center gap-3 text-xs text-slate-400">
                        <span>{{ $notification->created_at->diffForHumans() }}</span>
                        @if($notification->admin)
                            <span>&bull;</span>
                            <span>From: {{ $notification->admin->name ?? 'Admin' }}</span>
                        @endif
                    </div>
                </div>

                {{-- Type badge --}}
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium capitalize
                        @if($notification->type === 'info') bg-blue-100 text-blue-700
                        @elseif($notification->type === 'warning') bg-amber-100 text-amber-700
                        @elseif($notification->type === 'success') bg-emerald-100 text-emerald-700
                        @elseif($notification->type === 'error') bg-red-100 text-red-700
                        @else bg-slate-100 text-slate-700
                        @endif">
                        {{ $notification->type ?? 'notice' }}
                    </span>
                </div>
            </div>
        </div>
    @empty
        {{-- Empty State --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <div class="mx-auto w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-700">No notifications yet</h3>
            <p class="mt-1 text-sm text-slate-500">You're all caught up! Check back later for updates.</p>
        </div>
    @endforelse
</div>

@if($notifications->hasPages())
    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
@endif
@endsection
