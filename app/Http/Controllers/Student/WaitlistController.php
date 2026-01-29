<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Waitlist;
use App\Models\HiveTable;
use App\Models\Book;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WaitlistController extends Controller
{
    public function joinTable(Request $request, HiveTable $table): RedirectResponse
    {
        $userId = $request->user()->id;

        $isOccupied = Booking::query()
            ->where('table_id', $table->id)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('start_at', '<=', now())
            ->where('end_at', '>', now())
            ->exists();

        if (! $isOccupied) {
            return back()->with('error', 'Table is currently available. Please book directly.');
        }

        try {
            Waitlist::firstOrCreate(
                ['user_id' => $userId, 'table_id' => $table->id],
                ['status' => 'pending']
            );
        } catch (\Throwable $e) {
            return back()->with('error', 'Already on the waitlist for this table.');
        }

        return back()->with('status', 'Joined the waitlist for this table.');
    }

    public function joinBook(Request $request, Book $book): RedirectResponse
    {
        $userId = $request->user()->id;

        if ($book->copies_available > 0) {
            return back()->with('error', 'Book is available now. Please borrow directly.');
        }

        try {
            Waitlist::firstOrCreate(
                ['user_id' => $userId, 'book_id' => $book->id],
                ['status' => 'pending']
            );
        } catch (\Throwable $e) {
            return back()->with('error', 'Already on the waitlist for this book.');
        }

        return back()->with('status', 'Joined the waitlist for this book.');
    }
}
