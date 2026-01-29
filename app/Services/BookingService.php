<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\CalendarClosure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use RuntimeException;

class BookingService
{
    /**
     * Create a booking with overlap prevention (transaction + row lock).
     */
    public function createBooking(int $userId, int $tableId, Carbon $startAt, Carbon $endAt): Booking
    {
        if ($endAt->lte($startAt)) {
            throw new RuntimeException('End time must be after start time.');
        }

        // Enforce maximum booking length (default 300 minutes / 5 hours, or from settings)
        $maxMinutes = (int) (DB::table('settings')->where('key', 'max_booking_duration_minutes')->value('value') ?? 300);
        $durationMinutes = $startAt->diffInMinutes($endAt);

        if ($durationMinutes > $maxMinutes) {
            throw new RuntimeException('Booking duration exceeds the allowed limit of ' . $maxMinutes . ' minutes.');
        }

        $hasClosure = CalendarClosure::query()
            ->where('start_date', '<=', $endAt->toDateString())
            ->where('end_date', '>=', $startAt->toDateString())
            ->exists();

        if ($hasClosure) {
            throw new RuntimeException('Cannot book during a closure period.');
        }

        try {
            return DB::transaction(function () use ($userId, $tableId, $startAt, $endAt) {
                // Prevent overlapping bookings by the same user across any table.
                $userOverlap = Booking::where('user_id', $userId)
                    ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                    ->where(function ($q) use ($startAt, $endAt) {
                        $q->where('start_at', '<', $endAt)
                          ->where('end_at', '>', $startAt);
                    })
                    ->lockForUpdate()
                    ->exists();

                if ($userOverlap) {
                    throw new RuntimeException('You already have a booking that overlaps this time.');
                }

                // Lock existing bookings for this table to prevent race conditions.
                $overlapExists = Booking::where('table_id', $tableId)
                    ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                    ->where(function ($q) use ($startAt, $endAt) {
                        $q->where('start_at', '<', $endAt)
                          ->where('end_at', '>', $startAt);
                    })
                    ->lockForUpdate()
                    ->exists();

                if ($overlapExists) {
                    throw new RuntimeException('Table is already booked for the selected time.');
                }

                return Booking::create([
                    'user_id' => $userId,
                    'table_id' => $tableId,
                    'status' => 'confirmed',
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                ]);
            });
        } catch (QueryException $e) {
            throw new RuntimeException('Unable to create booking. Please try again.');
        }
    }
}
