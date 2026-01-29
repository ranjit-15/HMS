<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ExpireBookings extends Command
{
    protected $signature = 'bookings:expire';

    protected $description = 'Expire bookings whose end time has passed.';

    public function handle(): int
    {
        $now = Carbon::now();

        $count = Booking::query()
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('end_at', '<=', $now)
            ->update([
                'status' => 'expired',
                'released_at' => $now,
                'updated_at' => $now,
            ]);

        $this->info("Expired {$count} booking(s).");

        return Command::SUCCESS;
    }
}
