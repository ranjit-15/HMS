<?php

namespace App\Console\Commands;

use App\Mail\BookDueReminder;
use App\Models\BorrowRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendBookDueReminders extends Command
{
    protected $signature = 'books:due-reminders';
    protected $description = 'Send email reminders for books due in 3 days and overdue books';

    public function handle(): int
    {
        $this->info('Checking for books due soon and overdue...');

        // Get books due in 3 days
        $dueSoon = BorrowRequest::where('status', 'borrowed')
            ->whereNotNull('due_at')
            ->whereDate('due_at', now()->addDays(3)->toDateString())
            ->with(['user', 'book'])
            ->get();

        $sentDueSoon = 0;
        foreach ($dueSoon as $borrow) {
            if ($borrow->user && $borrow->user->email) {
                try {
                    Mail::to($borrow->user->email)->send(new BookDueReminder($borrow, 3, false));
                    $sentDueSoon++;
                    $this->line("  → Sent 3-day reminder to {$borrow->user->email} for '{$borrow->book->title}'");
                } catch (\Exception $e) {
                    $this->error("  ✗ Failed to send to {$borrow->user->email}: {$e->getMessage()}");
                }
            }
        }

        // Get overdue books
        $overdue = BorrowRequest::where('status', 'borrowed')
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->with(['user', 'book'])
            ->get();

        $sentOverdue = 0;
        foreach ($overdue as $borrow) {
            if ($borrow->user && $borrow->user->email) {
                $daysOverdue = now()->diffInDays($borrow->due_at);
                try {
                    Mail::to($borrow->user->email)->send(new BookDueReminder($borrow, $daysOverdue, true));
                    $sentOverdue++;
                    $this->line("  → Sent overdue reminder to {$borrow->user->email} for '{$borrow->book->title}' ({$daysOverdue} days)");
                } catch (\Exception $e) {
                    $this->error("  ✗ Failed to send to {$borrow->user->email}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Done! Sent {$sentDueSoon} due reminder(s) and {$sentOverdue} overdue reminder(s).");

        return Command::SUCCESS;
    }
}
