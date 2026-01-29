@extends('admin.layout')

@section('title', 'Calendar Events')
@section('header', 'Calendar Events')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-slate-600">Manage events visible on the student calendar</p>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.closures.index') }}"
                class="text-sm text-slate-600 hover:text-[#bd281e] transition-colors">
                <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Closures
            </a>
            <a href="{{ route('admin.events.create') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-[#bd281e] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#9c1c17] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Event
            </a>
        </div>
    </div>

    <div class="rounded-lg border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Title</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Type</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Date</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Visible</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($events as $event)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full" style="background-color: {{ $event->color }}"></span>
                                <span class="font-medium text-slate-800">{{ $event->title }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium capitalize
                                        @if($event->type === 'holiday') bg-red-100 text-red-800
                                        @elseif($event->type === 'event') bg-amber-100 text-amber-800
                                        @elseif($event->type === 'exam') bg-emerald-100 text-emerald-800
                                        @elseif($event->type === 'deadline') bg-red-100 text-red-800
                                        @else bg-slate-100 text-slate-800
                                        @endif">
                                {{ $event->type }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ $event->start_date->format('M j, Y') }}
                            @if($event->end_date && $event->start_date->ne($event->end_date))
                                – {{ $event->end_date->format('M j, Y') }}
                            @endif
                            @if(!$event->all_day && $event->start_time)
                                <span class="text-slate-400 text-xs ml-1">{{ $event->start_time }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($event->is_visible)
                                <span class="inline-flex items-center gap-1 text-green-600 text-xs font-medium">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd"
                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Visible
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-slate-400 text-xs font-medium">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                                            clip-rule="evenodd" />
                                        <path
                                            d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                    </svg>
                                    Hidden
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.events.edit', $event) }}"
                                    class="text-slate-500 hover:text-[#d9534f] transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.events.destroy', $event) }}" class="inline"
                                    onsubmit="return confirm('Delete this event?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-500 hover:text-red-600 transition-colors"
                                        title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                            No events found. <a href="{{ route('admin.events.create') }}"
                                class="text-[#d9534f] hover:underline">Create one</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($events->hasPages())
        <div class="mt-4">
            {{ $events->links() }}
        </div>
    @endif
@endsection