@extends('layouts.student')

@section('title', 'My Favorites')
@section('header', 'My Favorites')
@section('subheader', 'Books you\'ve saved for later')

@section('content')
    @if($favorites->isEmpty())
        <div class="rounded-xl border border-slate-200 bg-white p-12 text-center shadow-sm">
            <div class="mx-auto w-16 h-16 rounded-full bg-rose-100 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-rose-400" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-800 mb-2">No favorites yet</h3>
            <p class="text-slate-500 mb-6">Start adding books to your favorites to see them here!</p>
            <a href="{{ route('student.library') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-[#bd281e] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#9c1c17] transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Browse Library
            </a>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-4 gap-4">
            @foreach($favorites as $favorite)
                <div class="group bg-white rounded-lg shadow-sm border border-slate-100 overflow-hidden hover:shadow-md hover:border-blue-100 transition-all flex flex-col h-72 sm:h-80 md:h-80">
                    {{-- Book Cover --}}
                    <div class="relative bg-slate-100 overflow-hidden h-48 sm:h-52 md:h-56">
                        @if($favorite->book->cover_image)
                            <img src="{{ Storage::url($favorite->book->cover_image) }}" alt="{{ $favorite->book->title }}"
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                        @endif

                        {{-- Remove from favorites button --}}
                        <form method="POST" action="{{ route('student.favorites.toggle', $favorite->book) }}"
                            class="absolute top-3 right-3">
                            @csrf
                            <button type="submit" class="rounded-full bg-white/90 p-2 shadow-lg hover:bg-rose-100 transition-colors"
                                title="Remove from favorites">
                                <svg class="w-5 h-5 text-rose-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                </svg>
                            </button>
                        </form>

                        {{-- Availability Badge --}}
                        <div class="absolute bottom-3 left-3">
                            @if($favorite->book->copies_available > 0)
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 shadow">
                                    {{ $favorite->book->copies_available }} available
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 shadow">
                                    Not available
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Book Info --}}
                    <div class="p-2 flex-1 flex flex-col">
                        <h3 class="font-semibold text-slate-800 text-sm leading-tight line-clamp-2 mb-1 group-hover:text-[#8b0000] transition-colors"
                            title="{{ $favorite->book->title }}">
                            {{ $favorite->book->title }}
                        </h3>
                        <p class="text-[11px] text-slate-500 mb-3">{{ $favorite->book->author }}</p>

                        <div class="mt-auto pt-3 border-t border-slate-50 space-y-2">
                            @if($favorite->book->copies_available > 0)
                                <form method="POST" action="{{ route('student.library.borrow') }}" class="block">
                                    @csrf
                                    <input type="hidden" name="book_id" value="{{ $favorite->book->id }}">
                                    <button type="submit"
                                        class="w-full py-1 bg-[#8b0000] text-white text-[11px] font-bold uppercase tracking-wide rounded hover:bg-[#6b0000] hover:shadow-md transition-all">
                                        Borrow
                                    </button>
                                </form>
                            @else
                                @if(isset($waitlists[$favorite->book->id]))
                                    <div class="w-full py-1 bg-amber-50 text-amber-700 text-[11px] font-bold uppercase tracking-wide text-center rounded">On Waitlist</div>
                                @else
                                    <form method="POST" action="{{ route('student.library.waitlist', $favorite->book) }}">
                                        @csrf
                                        <button type="submit" class="w-full py-1 border border-[#8b0000] text-[#8b0000] text-[11px] font-bold uppercase tracking-wide rounded hover:bg-[#8b0000] hover:text-white transition-all">Join Waitlist</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($favorites->hasPages())
            <div class="mt-6">
                {{ $favorites->links() }}
            </div>
        @endif
    @endif
@endsection