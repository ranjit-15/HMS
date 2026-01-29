<?php

namespace App\Console\Commands;

use App\Models\BorrowRequest;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendDueTodayNotifications extends Command
{
    protected $signature = 'notifications:due-today';

    protected $description = 'Notify students when a borrowed book is due today.';

    public function handle(): int
    {
        $today = Carbon::today();

        $dueBorrows = BorrowRequest::where('status', 'borrowed')
            ->whereDate('due_at', $today)
            ->get();

        $count = 0;

        foreach ($dueBorrows as $borrow) {
            $exists = Notification::where('user_id', $borrow->user_id)
                ->where('type', 'borrow_due_today')
                ->where('target_type', 'borrow_request')
                ->where('target_id', $borrow->id)
                ->exists();

            if ($exists) {
                continue;
            }

            Notification::create([
                'user_id' => $borrow->user_id,
                'type' => 'borrow_due_today',
                'message' => 'Your borrowed book is due today.',
                'target_type' => 'borrow_request',
                'target_id' => $borrow->id,
            ]);

            $count++;
        }

        $this->info("Created {$count} notifications.");

        return Command::SUCCESS;
    }
}
