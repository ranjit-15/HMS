@csrf
<div class="grid gap-4 md:grid-cols-2">
    {{-- Title --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Title <span class="text-red-500">*</span></label>
        <input name="title" type="text" required value="{{ old('title', $book->title ?? '') }}" 
               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[#bd281e] focus:ring-1 focus:ring-[#bd281e] focus:outline-none" />
    </div>

    {{-- Author --}}
    <div>
        <label class="block text-sm font-medium mb-1">Author</label>
        <input name="author" type="text" value="{{ old('author', $book->author ?? '') }}" 
               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[#bd281e] focus:ring-1 focus:ring-[#bd281e] focus:outline-none" />
    </div>

    {{-- ISBN --}}
    <div>
        <label class="block text-sm font-medium mb-1">ISBN</label>
        <input name="isbn" type="text" value="{{ old('isbn', $book->isbn ?? '') }}" 
               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[#bd281e] focus:ring-1 focus:ring-[#bd281e] focus:outline-none" />
    </div>

    {{-- Category --}}
    <div>
        <label class="block text-sm font-medium mb-1">Category</label>
        <select name="category" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[#bd281e] focus:ring-1 focus:ring-[#bd281e] focus:outline-none">
            <option value="">Select Category</option>
            @foreach(\App\Models\Book::CATEGORIES as $key => $label)
                <option value="{{ $key }}" {{ old('category', $book->category ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    {{-- Location --}}
    <div>
        <label class="block text-sm font-medium mb-1">Shelf Location</label>
        <input name="location" type="text" value="{{ old('location', $book->location ?? '') }}" 
               placeholder="e.g., Shelf A-12"
               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[#bd281e] focus:ring-1 focus:ring-[#bd281e] focus:outline-none" />
    </div>

    {{-- Copies --}}
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium mb-1">Copies Total <span class="text-red-500">*</span></label>
            <input name="copies_total" type="number" min="1" required value="{{ old('copies_total', $book->copies_total ?? 1) }}" 
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[#bd281e] focus:ring-1 focus:ring-[#bd281e] focus:outline-none" />
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Copies Available <span class="text-red-500">*</span></label>
            <input name="copies_available" type="number" min="0" required value="{{ old('copies_available', $book->copies_available ?? 1) }}" 
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[#bd281e] focus:ring-1 focus:ring-[#bd281e] focus:outline-none" />
        </div>
    </div>

    {{-- Published At --}}
    <div>
        <label class="block text-sm font-medium mb-1">Published At</label>
        <input name="published_at" type="date" value="{{ old('published_at', isset($book->published_at) ? $book->published_at->format('Y-m-d') : '') }}" 
               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[#bd281e] focus:ring-1 focus:ring-[#bd281e] focus:outline-none" />
    </div>

    {{-- Cover Image --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Cover Image</label>
        <div class="flex items-start gap-4">
            @if(isset($book) && $book->cover_image)
                <div class="flex-shrink-0">
                    <img src="{{ Storage::url($book->cover_image) }}" alt="Current cover" class="w-24 h-32 object-cover rounded-lg border border-slate-200">
                </div>
            @endif
            <div class="flex-1">
                <input name="cover_image" type="file" accept="image/*" 
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm file:mr-3 file:rounded file:border-0 file:bg-[#bd281e] file:px-3 file:py-1 file:text-white file:text-sm focus:border-[#bd281e] focus:outline-none" />
                <p class="text-xs text-slate-500 mt-1">Max 2MB. Recommended: 300x400 pixels.</p>
            </div>
        </div>
    </div>

    {{-- Description --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Description</label>
        <textarea name="description" rows="4" 
                  class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[#bd281e] focus:ring-1 focus:ring-[#bd281e] focus:outline-none"
                  placeholder="Brief description of the book...">{{ old('description', $book->description ?? '') }}</textarea>
        <p class="text-xs text-slate-500 mt-1">Max 2000 characters.</p>
    </div>

    {{-- Active Status --}}
    <div class="flex items-center space-x-2 mt-2">
        <input type="checkbox" name="is_active" value="1" 
               {{ old('is_active', $book->is_active ?? true) ? 'checked' : '' }} 
               class="rounded border-slate-300 text-[#bd281e] focus:ring-[#bd281e]" />
        <span class="text-sm">Active (visible to students)</span>
    </div>
</div>

@if($errors->any())
    <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mt-6 flex space-x-3">
    <button type="submit" class="rounded-lg bg-[#bd281e] px-5 py-2.5 text-white text-sm font-semibold hover:bg-[#9c1c17] transition-colors">
        Save Book
    </button>
    <a href="{{ route('admin.books.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
        Cancel
    </a>
</div>
