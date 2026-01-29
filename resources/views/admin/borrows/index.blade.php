@extends('admin.layout')

@section('title', 'Borrow Requests')
@section('header', 'Borrow Requests')

@section('content')
<div class="space-y-4">
    {{-- Stats Row --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="rounded-lg bg-amber-50 border border-amber-200 p-4">
            <div class="text-2xl font-bold text-amber-700">{{ $borrows->where('status', 'pending')->count() }}</div>
            <div class="text-xs text-amber-600 font-medium">Pending</div>
        </div>
        <div class="rounded-lg bg-cyan-50 border border-cyan-200 p-4">
            <div class="text-2xl font-bold text-cyan-700">{{ $borrows->where('status', 'approved')->count() }}</div>
            <div class="text-xs text-cyan-600 font-medium">Approved</div>
        </div>
        <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
            <div class="text-2xl font-bold text-blue-700">{{ $borrows->where('status', 'borrowed')->count() }}</div>
            <div class="text-xs text-blue-600 font-medium">Borrowed</div>
        </div>
        <div class="rounded-lg bg-red-50 border border-red-200 p-4">
            <div class="text-2xl font-bold text-red-700">{{ $borrows->where('status', 'borrowed')->filter(fn($b) => $b->due_at && $b->due_at->isPast())->count() }}</div>
            <div class="text-xs text-red-600 font-medium">Overdue</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Student</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Book</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Due Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Time Left</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($borrows as $borrow)
                        @php
                            $isOverdue = $borrow->status === 'borrowed' && $borrow->due_at && $borrow->due_at->isPast();
                            $daysLeft = $borrow->due_at ? now()->diffInDays($borrow->due_at, false) : null;
                        @endphp
                        <tr class="hover:bg-slate-50 {{ $isOverdue ? 'bg-red-50' : '' }}">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-800">{{ $borrow->user->name }}</div>
                                <div class="text-xs text-slate-500">{{ $borrow->user->email }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="font-medium text-slate-800 max-w-[200px] truncate">{{ optional($borrow->book)->title ?? 'Deleted book' }}</div>
                                    @if(optional($borrow->book) && optional($borrow->book)->trashed())
                                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded">Deleted</span>
                                    @endif
                                </div>
                                <div class="text-xs text-slate-500">{{ optional($borrow->book)->author ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @if($borrow->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">⏳ Pending</span>
                                @elseif($borrow->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-cyan-100 text-cyan-700">✓ Approved</span>
                                @elseif($borrow->status === 'borrowed')
                                    @if($isOverdue)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">⚠️ Overdue</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">📖 Borrowed</span>
                                    @endif
                                @elseif($borrow->status === 'returned')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">✅ Returned</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">{{ ucfirst($borrow->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                @if($borrow->due_at)
                                    <div class="{{ $isOverdue ? 'text-red-600 font-semibold' : '' }}">
                                        {{ $borrow->due_at->format('M j, Y') }}
                                    </div>
                                    <div class="text-xs text-slate-400">{{ $borrow->due_at->format('l') }}</div>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($borrow->status === 'borrowed' && $borrow->due_at)
                                    <span class="due-countdown {{ $isOverdue ? 'text-red-600 font-semibold text-xs' : 'text-emerald-600 font-medium text-xs' }}" data-due="{{ $borrow->due_at->toIsoString() }}">{{ $borrow->due_at->diffForHumans() }}</span>
                                @elseif($borrow->status === 'returned')
                                    <span class="text-slate-400 text-xs">Completed</span>
                                @else
                                    <span class="text-slate-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('admin.audit.index', ['target_type' => 'borrow_request', 'target_id' => $borrow->id]) }}" target="_blank" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-700 hover:bg-slate-50">Logs</a>
                                    @if($borrow->status === 'pending')
                                        <form method="POST" action="{{ route('admin.borrows.approve', $borrow) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700 transition-colors">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.borrows.decline', $borrow) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700 transition-colors">Decline</button>
                                        </form>
                                    @elseif($borrow->status === 'approved')
                                        <form method="POST" action="{{ route('admin.borrows.borrowed', $borrow) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700 transition-colors">Mark Borrowed</button>
                                        </form>
                                    @elseif($borrow->status === 'borrowed')
                                        <form method="POST" action="{{ route('admin.borrows.returned', $borrow) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-900 transition-colors">Mark Returned</button>
                                        </form>
                                    @elseif($borrow->status === 'returned')
                                        @if($borrow->admin_confirmed_at)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">✓ Confirmed</span>
                                        @else
                                            <form method="POST" action="{{ route('admin.borrows.makeAvailable', $borrow) }}">
                                                @csrf
                                                <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700 transition-colors">Confirm Return</button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                No borrow requests found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($borrows->hasPages())
        <div class="mt-4">{{ $borrows->links() }}</div>
    @endif
</div>
@endsection
