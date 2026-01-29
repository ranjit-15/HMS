@extends('admin.layout')

@section('title', 'Edit Event')
@section('header', 'Edit Calendar Event')

@section('content')
    <div class="max-w-2xl">
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm p-6">
            <form method="POST" action="{{ route('admin.events.update', $event) }}">
                @csrf
                @method('PUT')
                @include('admin.events._form', ['event' => $event])
                
                <div class="mt-6 flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-[#bd281e] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#9c1c17] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Update Event
                    </button>
                    <a href="{{ route('admin.events.index') }}" class="text-sm text-slate-600 hover:text-slate-800 transition-colors">Cancel</a>
                </div>
            </form>
        </div>
        
        <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4">
            <h4 class="text-sm font-semibold text-red-800 mb-2">Danger Zone</h4>
            <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Are you sure you want to delete this event? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-red-300 bg-white px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete Event
                </button>
            </form>
        </div>
    </div>
@endsection
