@extends('admin.layout')

@section('title', 'Book Reviews')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Book Reviews</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium {{ $currentStatus === 'pending' ? 'bg-amber-500 text-white' : 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50' }}">
                Pending
            </a>
            <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium {{ $currentStatus === 'approved' ? 'bg-green-500 text-white' : 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50' }}">
                Approved
            </a>
        </div>
    </div>

    @if(session('status'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        @if($reviews->count() > 0)
            <div class="divide-y divide-slate-200">
                @foreach($reviews as $review)
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="font-medium text-slate-800">{{ $review->user->name ?? 'Unknown User' }}</span>
                                    <span class="text-sm text-slate-500">{{ $review->user->email ?? '' }}</span>
                                    <span class="text-xs text-slate-400">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-sm font-medium text-blue-600">{{ $review->book->title ?? 'Unknown Book' }}</span>
                                </div>
                                <div class="flex items-center gap-1 mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                    <span class="text-sm text-slate-500 ml-1">({{ $review->rating }}/5)</span>
                                </div>
                                @if($review->review)
                                    <p class="text-sm text-slate-600">{{ $review->review }}</p>
                                @endif
                            </div>
                            <div class="flex gap-2 ml-4">
                                @if(!$review->is_approved)
                                    <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition-colors">
                                            Approve
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.reviews.reject', $review) }}" onsubmit="return confirm('Are you sure you want to reject this review?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="p-4 border-t border-slate-200">
                {{ $reviews->links() }}
            </div>
        @else
            <div class="p-8 text-center text-slate-500">
                <p>No {{ $currentStatus }} reviews found.</p>
            </div>
        @endif
    </div>
</div>
@endsection
