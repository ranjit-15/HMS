@extends('layouts.student')

@section('title', 'My Activity')
@section('header', 'My Activity')
@section('subheader', 'Manage your seat bookings and library borrows')

@section('content')
    <div class="grid gap-8 lg:grid-cols-2">
        {{-- Hive Bookings Section --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-[#8b0000] flex items-center gap-2">
                    <span class="w-2 h-6 bg-[#f59e0b] rounded-full"></span>
                    Hive Bookings
                </h3>
                <a href="{{ route('student.hive') }}" class="text-sm font-medium text-slate-500 hover:text-[#8b0000] transition-colors">
                    Book a Seat &rarr;
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                @if($bookings->isEmpty())
                    <div class="p-8 text-center text-slate-500">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <p class="font-medium">No active bookings</p>
                        <p class="text-xs mt-1">Visit the Hive to book a seat</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($bookings as $booking)
                            <div class="p-4 hover:bg-slate-50 transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-bold text-slate-800">Seat #{{ $booking->table->name ?? $booking->table_id }}</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold uppercase tracking-wide
                                        @if($booking->status === 'checked_in') bg-emerald-100 text-emerald-700
                                        @elseif($booking->status === 'confirmed') bg-[#fee2e2] text-[#8b0000]
                                        @else bg-amber-100 text-amber-700 @endif">
                                        {{ $booking->status === 'checked_in' ? 'Checked In' : ucfirst($booking->status) }}
                                    </span>
                                </div>
                                <div class="text-sm text-slate-600 space-y-1">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $booking->start_at->format('M j, g:i A') }} - {{ $booking->end_at->format('g:i A') }}
                                    </div>
                                    @if($booking->status !== 'checked_in')
                                        <div class="flex items-center gap-2 text-slate-500">
                                            <span class="w-4 h-4 flex items-center justify-center">⏳</span>
                                            <span data-countdown data-end="{{ $booking->end_at->toIso8601String() }}" class="text-xs font-medium font-mono">
                                                Loading...
                                            </span>
                                        </div>
                                    @endif
                                    @if($booking->table && $booking->table->check_in_secret)
                                        @php
                                            $checkInUrl = url('check-in/' . $booking->table_id . '/' . $booking->table->check_in_secret);
                                        @endphp
                                        <div class="mt-2 flex items-center gap-2">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&amp;data={{ urlencode($checkInUrl) }}" alt="Check-in QR" class="w-10 h-10 rounded border border-slate-200" />
                                            <div class="text-xs text-slate-500">
                                                <span class="font-medium text-slate-600">Check-in QR</span> — scan at table
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-2 flex gap-2">
                                    <form action="{{ route('student.hive.cancel', $booking) }}" method="POST" class="inline" onsubmit="return confirm('Cancel this booking?');">
                                        @csrf
                                        <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Library Borrows Section --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-[#8b0000] flex items-center gap-2">
                    <span class="w-2 h-6 bg-[#8b0000] rounded-full"></span>
                    Library Borrows
                </h3>
                <a href="{{ route('student.library') }}" class="text-sm font-medium text-slate-500 hover:text-[#8b0000] transition-colors">
                    Browse Books &rarr;
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                @if($borrows->isEmpty())
                    <div class="p-8 text-center text-slate-500">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <p class="font-medium">No active borrows</p>
                        <p class="text-xs mt-1">Browse the library to find books</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3">Book</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-right">Deadline</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($borrows as $borrow)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                @if(optional($borrow->book)->cover_image)
                                                    <img src="{{ Storage::url(optional($borrow->book)->cover_image) }}" class="w-8 h-12 object-cover rounded shadow-sm" alt="">
                                                @else
                                                    <div class="w-8 h-12 bg-slate-100 rounded flex items-center justify-center text-slate-300">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div>
                                                    @php $bookTitle = optional($borrow->book)->title; $bookAuthor = optional($borrow->book)->author; @endphp
                                                    <div class="font-medium text-slate-800 line-clamp-1" title="{{ $bookTitle ?? 'Deleted book' }}">
                                                        {{ $bookTitle ?? 'Deleted book' }}
                                                        @unless($borrow->book)
                                                            <span class="ml-2 text-[10px] px-1 py-0.5 rounded bg-red-100 text-red-700">Deleted</span>
                                                        @endunless
                                                    </div>
                                                    <div class="text-xs text-slate-500">{{ $bookAuthor ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold uppercase tracking-wide
                                                @if($borrow->status === 'borrowed') bg-emerald-100 text-emerald-700
                                                @elseif($borrow->status === 'approved') bg-[#fee2e2] text-[#8b0000]
                                                @elseif($borrow->status === 'pending') bg-amber-100 text-amber-700
                                                @else bg-slate-100 text-slate-700 @endif">
                                                {{ ucfirst($borrow->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            @if($borrow->due_at)
                                                @php
                                                    $isOverdue = $borrow->due_at->isPast();
                                                    $isNear = $borrow->due_at->diffInDays(now()) <= 3;
                                                @endphp
                                                <div class="font-medium {{ $isOverdue ? 'text-red-600' : ($isNear ? 'text-amber-600' : 'text-slate-600') }}">
                                                    {{ $borrow->due_at->format('M j') }}
                                                </div>
                                                <div class="text-[10px] {{ $isOverdue ? 'text-red-500' : 'text-slate-400' }}">
                                                    @if($borrow->due_at)
                                                        <span class="due-countdown" data-due="{{ $borrow->due_at->toIsoString() }}">{{ $borrow->due_at->diffForHumans() }}</span>
                                                    @else
                                                        -
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            @if($borrow->status === 'borrowed' && $borrow->due_at)
                                                <div class="flex items-center justify-end gap-3">
                                                    <form action="{{ route('student.library.extend', $borrow) }}" method="POST" class="inline" onsubmit="return confirm('Extend due date by 7 days?');">
                                                        @csrf
                                                        <button type="submit" class="text-xs font-medium text-[#8b0000] hover:text-[#6b0000]">Extend</button>
                                                    </form>
                                                    <form action="{{ route('student.library.return', $borrow) }}" method="POST" class="inline" onsubmit="return confirm('Return this book now?');">
                                                        @csrf
                                                        <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700">Return</button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-slate-400">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
