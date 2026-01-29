@extends('admin.layout')

@section('title', 'Add Event')
@section('header', 'Add Calendar Event')

@section('content')
    <div class="max-w-2xl">
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm p-6">
            <form method="POST" action="{{ route('admin.events.store') }}">
                @csrf
                @include('admin.events._form', ['event' => null])
                
                <div class="mt-6 flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-[#bd281e] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#9c1c17] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Create Event
                    </button>
                    <a href="{{ route('admin.events.index') }}" class="text-sm text-slate-600 hover:text-slate-800 transition-colors">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
