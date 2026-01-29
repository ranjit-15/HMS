@extends('layouts.student')

@section('title', $book->title)
@section('header', $book->title)
@section('subheader', $book->author)

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex gap-6">
            <div class="w-32 flex-shrink-0">
                @if($book->cover_image)
                    <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}" class="w-32 h-48 object-cover rounded shadow-sm">
                @else
                    <div class="w-32 h-48 bg-slate-100 rounded flex items-center justify-center text-slate-300">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                @endif
            </div>

            <div class="flex-1">
                <h2 class="text-2xl font-bold text-slate-900">{{ $book->title }}</h2>
                <p class="text-sm text-slate-500 mb-4">{{ $book->author }} • {{ $book->published_at?->format('Y') ?? '' }}</p>

                <div class="prose prose-sm text-slate-700">{!! nl2br(e($book->description ?? 'No description available.')) !!}</div>

                <div class="mt-6 flex items-center gap-4">
                    @if(isset($borrowRequest) && in_array($borrowRequest->status, ['pending','approved','borrowed']))
                        <div class="px-3 py-1 bg-blue-50 text-blue-700 rounded text-xs font-bold">{{ ucfirst($borrowRequest->status) }}</div>
                        @if($borrowRequest->status === 'borrowed' && $borrowRequest->due_at)
                            <div class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded text-xs font-bold">
                                <span class="due-countdown" data-due="{{ $borrowRequest->due_at->toIsoString() }}">{{ $borrowRequest->due_at->diffForHumans() }}</span>
                            </div>
                        @endif
                    @elseif($book->copies_available > 0)
                        <form method="POST" action="{{ route('student.library.borrow') }}">
                            @csrf
                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                            <button class="px-4 py-2 bg-[#8b0000] text-white rounded font-medium">Borrow</button>
                        </form>
                    @else
                        @if($waitlistStatus)
                            <div class="px-3 py-1 bg-amber-50 text-amber-700 rounded text-xs font-bold">On Waitlist</div>
                        @else
                            <form method="POST" action="{{ route('student.library.waitlist', $book->id) }}">
                                @csrf
                                <button class="px-4 py-2 border border-[#8b0000] text-[#8b0000] rounded font-medium">Join Waitlist</button>
                            </form>
                        @endif
                    @endif

                    <form method="POST" action="{{ route('student.favorites.toggle', $book) }}">
                        @csrf
                        <button class="px-3 py-1 border rounded text-sm {{ $isFavorited ? 'bg-rose-50 text-rose-600' : 'text-slate-600' }}">{{ $isFavorited ? 'Favorited' : 'Favorite' }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Reviews --}}
    <div class="max-w-4xl mx-auto mt-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">Reviews</h3>
                    <div class="text-sm text-slate-500">Average: {{ $book->average_rating ?? '—' }} / 5</div>
                </div>
                <div class="text-sm text-slate-500">{{ $book->approvedReviews()->count() }} approved</div>
            </div>

            @if($reviews->count() > 0)
                <div class="space-y-4">
                    @foreach($reviews as $review)
                        <div class="border border-slate-100 rounded p-3">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-medium text-slate-800">{{ $review->user->name ?? 'User' }}</div>
                                <div class="text-xs text-slate-500">{{ $review->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="flex items-center gap-1 mt-2">
                                @for($i=1;$i<=5;$i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                                <span class="text-sm text-slate-500 ml-2">({{ $review->rating }}/5)</span>
                            </div>
                            @if($review->review)
                                <p class="text-sm text-slate-600 mt-2">{{ $review->review }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">{{ $reviews->links() }}</div>
            @else
                <div class="text-sm text-slate-500">No reviews yet.</div>
            @endif

            @if($canReview)
                <form method="POST" action="{{ route('student.library.review', $book) }}" class="mt-6">
                    @csrf
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-slate-700">Rating</label>
                        <select name="rating" required class="rounded border px-3 py-1 text-sm">
                            <option value="">Select</option>
                            @for($i=1;$i<=5;$i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mt-3">
                        <label class="text-sm text-slate-700">Review (optional)</label>
                        <textarea name="review" rows="3" class="w-full rounded border mt-1 p-2 text-sm" placeholder="Share your thoughts..."></textarea>
                    </div>
                    <div class="mt-3">
                        <button class="px-4 py-2 bg-[#8b0000] text-white rounded text-sm">Submit Review</button>
                    </div>
                </form>
            @elseif(auth()->check())
                <div class="mt-4 text-sm text-slate-500">You can leave a review after borrowing this book.</div>
            @endif
        </div>
    </div>
@endsection
 
