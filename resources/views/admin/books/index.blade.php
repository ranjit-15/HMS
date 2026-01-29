@extends('admin.layout')

@section('title', 'Books')
@section('header', 'Books')

@section('content')
    <div class="flex flex-col gap-3 mb-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-lg font-semibold">Books</h2>
            <p class="text-sm text-slate-500">Search by title or ISBN.</p>
        </div>
        <div class="flex gap-2 items-center">
            <form method="GET" action="{{ route('admin.books.index') }}" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search books..."
                    class="rounded border border-slate-300 px-3 py-2 text-sm" />
                <button type="submit"
                    class="rounded bg-slate-800 px-3 py-2 text-white text-sm font-medium hover:bg-slate-900">Search</button>
                @if(!empty($search))
                    <a href="{{ route('admin.books.index') }}" class="text-sm text-slate-600 hover:text-slate-800">Reset</a>
                @endif
            </form>
            <a href="{{ route('admin.books.create') }}"
                class="rounded bg-indigo-600 px-3 py-2 text-white text-sm font-medium hover:bg-indigo-700">Add Book</a>
        </div>
    </div>

    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr class="text-left text-sm text-slate-600">
                    <th class="px-4 py-3">Book</th>
                    <th class="px-4 py-3">ISBN</th>
                    <th class="px-4 py-3">Copies</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($books as $book)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($book->cover_image)
                                    <img src="{{ Storage::url($book->cover_image) }}" alt=""
                                        class="w-10 h-14 object-cover rounded border border-slate-200 shadow-sm">
                                @else
                                    <div
                                        class="w-10 h-14 bg-slate-100 rounded border border-slate-200 flex items-center justify-center text-slate-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <div class="font-medium text-slate-900">{{ $book->title }}</div>
                                    <div class="text-slate-500 text-xs">{{ $book->author }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $book->isbn }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $book->copies_available > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $book->copies_available }} / {{ $book->copies_total }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $book->is_active ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $book->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('admin.books.edit', $book) }}"
                                class="text-[#8b0000] hover:text-[#6b0000] font-medium">Edit</a>
                            <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete this book?')"
                                    class="text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">No books found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $books->links() }}</div>
@endsection