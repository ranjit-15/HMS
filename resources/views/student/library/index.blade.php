@extends('layouts.student')

@section('title', 'Library')
@section('header', 'Library')
@section('subheader', 'Browse books, search, and check availability')

@section('content')
    <div>
        {{-- Modern Compact Filters --}}
        <form method="GET" action="{{ route('student.library') }}" class="mb-6">
            <div class="flex flex-wrap items-center gap-3">
                {{-- Category Pills --}}
                <div class="flex flex-wrap gap-2">
                    <button type="submit" name="category" value="" class="px-3 py-1 rounded-full text-xs font-medium border transition @if(empty($currentCategory)) bg-blue-600 text-white border-blue-600 @else bg-white text-slate-700 border-slate-200 hover:bg-blue-50 @endif">All</button>
                    @foreach($categories ?? [] as $key => $label)
                        <button type="submit" name="category" value="{{ $key }}" class="px-3 py-1 rounded-full text-xs font-medium border transition @if(($currentCategory ?? '') === $key) bg-blue-600 text-white border-blue-600 @else bg-white text-slate-700 border-slate-200 hover:bg-blue-50 @endif">{{ $label }}</button>
                    @endforeach
                </div>
                {{-- Availability Toggle --}}
                <div class="flex items-center gap-2 ml-4">
                    <span class="text-xs text-slate-500">Show:</span>
                    <button type="submit" name="availability" value="" class="px-3 py-1 rounded-full text-xs font-medium border transition @if(empty($currentAvailability)) bg-emerald-600 text-white border-emerald-600 @else bg-white text-slate-700 border-slate-200 hover:bg-emerald-50 @endif">All</button>
                    <button type="submit" name="availability" value="available" class="px-3 py-1 rounded-full text-xs font-medium border transition @if(($currentAvailability ?? '') === 'available') bg-emerald-600 text-white border-emerald-600 @else bg-white text-slate-700 border-slate-200 hover:bg-emerald-50 @endif">Available Only</button>
                </div>
            </div>
        </form>

        {{-- Results Info --}}
        @if($currentSearch || $currentCategory || $currentAvailability)
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-slate-600">
                    Found <span class="font-bold text-slate-900">{{ $books->total() }}</span> results
                </p>
                <a href="{{ route('student.library') }}"
                    class="text-sm text-blue-600 hover:text-blue-700 font-medium hover:underline">
                    Clear Filters
                </a>
            </div>
        @endif

        {{-- Books Grid --}}
        @if($books->isEmpty())
            <div class="text-center py-12 bg-white rounded-xl border border-dashed border-slate-300">
                <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <h3 class="text-lg font-medium text-slate-900">No books found</h3>
                <p class="text-slate-500 mt-1">Try adjusting your search criteria</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-4 gap-4">
                @foreach($books as $book)
                    @php
                        $available = $book->copies_available > 0;
                        $request = $borrowRequests[$book->id] ?? null;
                        $isFavorited = in_array($book->id, $favorites ?? []);
                    @endphp

                    <div
                        class="group bg-white rounded-lg shadow-sm border border-slate-100 overflow-hidden hover:shadow-md hover:border-blue-100 transition-all flex flex-col h-72 sm:h-80 md:h-80">
                        {{-- Book Cover --}}
                        <div class="relative bg-slate-100 overflow-hidden h-48 sm:h-52 md:h-56">
                            @if($book->cover_image)
                                <a href="{{ route('student.library.show', $book) }}">
                                    <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}"
                                        class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                                </a>
                            @else
                                <a href="{{ route('student.library.show', $book) }}">
                                    <div
                                        class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-blue-50 to-slate-100">
                                        <svg class="w-10 h-10 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                </a>
                            @endif

                            {{-- Badges --}}
                            <div class="absolute top-2 left-2 flex flex-col gap-1">
                                @if($book->category)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-white/90 text-slate-700 shadow-sm backdrop-blur-sm">
                                        {{ \App\Models\Book::CATEGORIES[$book->category] ?? $book->category }}
                                    </span>
                                @endif
                            </div>

                            {{-- Favorite Button --}}
                            <form method="POST" action="{{ route('student.favorites.toggle', $book) }}"
                                class="absolute top-2 right-2">
                                @csrf
                                <button type="submit"
                                    class="p-1 rounded-full bg-white/90 shadow-sm hover:bg-rose-50 transition-colors backdrop-blur-sm group/fav">
                                    <svg class="w-3 h-3 {{ $isFavorited ? 'text-rose-500 fill-rose-500' : 'text-slate-400 group-hover/fav:text-rose-500' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </button>
                            </form>

                            {{-- Availability Overlay (Only on hover if available) --}}
                            @if($available)
                                <div
                                    class="absolute inset-x-0 bottom-0 p-3 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex justify-end">
                                    <span
                                        class="text-xs font-bold text-white bg-emerald-500/90 px-2 py-1 rounded backdrop-blur-sm shadow-sm">
                                        {{ $book->copies_available }} left
                                    </span>
                                </div>
                            @else
                                <div class="absolute inset-x-0 bottom-0 p-2 bg-red-500/90 backdrop-blur-sm text-center">
                                    <span class="text-xs font-bold text-white uppercase tracking-wide">Out of Stock</span>
                                </div>
                            @endif
                        </div>
 

                        {{-- Book Info --}}
                        <div class="p-2 flex-1 flex flex-col">
                            <h3 class="font-semibold text-slate-800 text-xs leading-tight line-clamp-2 mb-1 group-hover:text-[#8b0000] transition-colors"
                                title="{{ $book->title }}">
                                <a href="{{ route('student.library.show', $book) }}">{{ $book->title }}</a>
                            </h3>
                            <p class="text-[11px] text-slate-500 mb-3">{{ $book->author }}</p>

                            <div class="mt-auto pt-3 border-t border-slate-50 space-y-2">
                                {{-- Status/Action Area --}}
                                @if($request)
                                    <div
                                        class="w-full py-1 bg-blue-50 text-blue-700 text-[11px] font-bold uppercase tracking-wide text-center rounded">
                                        {{ ucfirst($request->status) }}
                                    </div>
                                    @if($request->status === 'borrowed' && $request->due_at)
                                        <div class="w-full mt-1 text-center text-xs text-emerald-600 font-medium">
                                            <span class="due-countdown" data-due="{{ $request->due_at->toIsoString() }}">{{ $request->due_at->diffForHumans() }}</span>
                                        </div>
                                    @endif
                                @elseif($available)
                                    <form method="POST" action="{{ route('student.library.borrow') }}" class="block">
                                        @csrf
                                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                                        <button type="submit"
                                            class="w-full py-1 bg-[#8b0000] text-white text-[11px] font-bold uppercase tracking-wide rounded hover:bg-[#6b0000] hover:shadow-md transition-all">
                                            Borrow
                                        </button>
                                    </form>
                                @else
                                    @if(isset($waitlists[$book->id]))
                                        <div
                                            class="w-full py-1 bg-amber-50 text-amber-700 text-[11px] font-bold uppercase tracking-wide text-center rounded">
                                            On Waitlist
                                        </div>
                                    @else
                                        <form method="POST" action="{{ route('student.library.waitlist', $book->id) }}">
                                            @csrf
                                            <button type="submit"
                                                class="w-full py-1 border border-[#8b0000] text-[#8b0000] text-[11px] font-bold uppercase tracking-wide rounded hover:bg-[#8b0000] hover:text-white transition-all">
                                                Join Waitlist
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $books->links() }}
            </div>
        @endif
    </div>
@endsection