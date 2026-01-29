<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\WaitlistService;
use Illuminate\Console\Command;

class CleanupBookings extends Command
{
    protected $signature = 'bookings:cleanup';

    protected $description = 'Cancel stale bookings that never checked in';

    public function handle(): int
    {
        $threshold = now()->subMinutes(15);

        $stale = Booking::query()
            ->where('status', 'pending')
            ->where('start_at', '<=', $threshold)
            ->get(['id', 'table_id']);

        if ($stale->isEmpty()) {
            $this->info('Cancelled 0 pending bookings older than 15 minutes.');
            return self::SUCCESS;
        }

        Booking::whereIn('id', $stale->pluck('id'))->update([
            'status' => 'cancelled',
            'released_at' => now(),
            'end_at' => now(),
        ]);

        $service = app(WaitlistService::class);
        foreach ($stale->pluck('table_id')->filter()->unique() as $tableId) {
            $service->notifyNextForTable($tableId);
        }

        $this->info('Cancelled ' . $stale->count() . ' pending bookings older than 15 minutes.');

        return self::SUCCESS;
    }
}
