<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $books = Book::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        return view('admin.books.index', compact('books', 'search'));
    }

    public function create()
    {
        return view('admin.books.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $book = Book::create($data);
        $this->log('created', 'book', $book->id);
        return redirect()->route('admin.books.index')->with('status', 'Book created.');
    }

    public function edit(Book $book)
    {
        return view('admin.books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $data = $this->validated($request, $book->id);
        $book->update($data);
        $this->log('updated', 'book', $book->id);
        return redirect()->route('admin.books.index')->with('status', 'Book updated.');
    }

    public function destroy(Book $book)
    {
        try {
            $book->delete();
            $this->log('deleted', 'book', $book->id);
            return redirect()->route('admin.books.index')->with('status', 'Book deleted.');
        } catch (QueryException $e) {
            return back()->with('error', 'Cannot delete book: it is referenced by borrow requests or other records.');
        }
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:255', Rule::unique('books', 'isbn')->ignore($ignoreId)],
            'category' => ['nullable', 'string', 'in:' . implode(',', array_keys(Book::CATEGORIES))],
            'description' => ['nullable', 'string', 'max:2000'],
            'location' => ['nullable', 'string', 'max:100'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
            'copies_total' => ['required', 'integer', 'min:1'],
            'copies_available' => ['required', 'integer', 'min:0', 'lte:copies_total'],
            'published_at' => ['nullable', 'date'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('book-covers', 'public');
        }

        return $data;
    }

    private function log(string $action, string $targetType, ?int $targetId = null): void
    {
        AuditLog::create([
            'admin_id' => auth('admin')->id(),
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
        ]);
    }
}
