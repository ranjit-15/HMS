<?php

namespace App\Services;

use App\Models\Waitlist;
use App\Models\Notification;
use Illuminate\Support\Carbon;

class WaitlistService
{
    public function notifyNextForTable(int $tableId): ?Waitlist
    {
        $entry = Waitlist::query()
            ->where('table_id', $tableId)
            ->whereNull('book_id')
            ->whereIn('status', ['pending', 'notified'])
            ->orderBy('created_at')
            ->first();

        return $this->notify($entry, 'table');
    }

    public function notifyNextForBook(int $bookId): ?Waitlist
    {
        $entry = Waitlist::query()
            ->where('book_id', $bookId)
            ->whereNull('table_id')
            ->whereIn('status', ['pending', 'notified'])
            ->orderBy('created_at')
            ->first();

        return $this->notify($entry, 'book');
    }

    private function notify(?Waitlist $entry, string $type): ?Waitlist
    {
        if (! $entry) {
            return null;
        }

        $entry->update([
            'status' => 'notified',
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);

        $message = $type === 'table'
            ? 'A table you waited for is free. You have 30 minutes to claim it.'
            : 'A book you waited for is available. You have 30 minutes to claim it.';

        Notification::create([
            'user_id' => $entry->user_id,
            'type' => $type . '_waitlist_available',
            'message' => $message,
            'target_type' => $type . '_waitlist',
            'target_id' => $entry->id,
        ]);

        return $entry;
    }
}
