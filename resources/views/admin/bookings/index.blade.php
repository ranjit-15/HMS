@extends('admin.layout')

@section('title', 'Hive Bookings')
@section('header', 'Hive Bookings')

@section('content')
    <div class="space-y-4">
        <p class="text-sm text-slate-600">All table bookings across the Hive.</p>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                        <tr>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Table</th>
                            <th class="px-4 py-3">Start</th>
                            <th class="px-4 py-3">End</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($bookings as $b)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-slate-800">{{ $b->user->name ?? '—' }}</div>
                                    <div class="text-xs text-slate-500">{{ $b->user->email ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $b->table->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $b->start_at->format('M j, g:i A') }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $b->end_at->format('M j, g:i A') }}</td>
                                <td class="px-4 py-3">
                                    @if($b->status === 'confirmed' || $b->status === 'checked_in')
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-700">{{ $b->status }}</span>
                                    @elseif($b->status === 'pending')
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-700">Pending</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-600">{{ $b->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500">No bookings yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($bookings->hasPages())
                <div class="px-4 py-3 border-t border-slate-200">{{ $bookings->links() }}</div>
            @endif
        </div>
    </div>
@endsection
