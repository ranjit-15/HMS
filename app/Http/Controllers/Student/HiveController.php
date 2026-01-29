<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\HiveTable;
use App\Models\Notification;
use App\Models\Waitlist;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HiveController extends Controller
{
    public function index()
    {
        $now = now();
        $userId = auth()->id();

        $activeBookings = Booking::query()
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('start_at', '<=', $now)
            ->where('end_at', '>', $now)
            ->select('id', 'table_id', 'user_id', 'status', 'start_at', 'end_at')
            ->get()
            ->groupBy('table_id');

        // Only surface active, non-deleted tables to students.
        $tables = HiveTable::where('is_active', true)
            ->orderBy('y')
            ->orderBy('x')
            ->get();

        $maxX = $tables->max('x') ?? 0;
        $columns = max($maxX, 1);

        $tableWaitlists = Waitlist::query()
            ->where('user_id', $userId)
            ->whereNotNull('table_id')
            ->whereIn('status', ['pending', 'notified'])
            ->pluck('status', 'table_id');

        $tableStates = $tables->map(function ($table) use ($activeBookings, $userId, $tableWaitlists) {
            $state = 'available';
            $startAt = null;
            $endAt = null;
            $bookingId = null;
            $bookingStatus = null;
            $isOwner = false;
            if (isset($activeBookings[$table->id])) {
                $booking = $activeBookings[$table->id]->first();
                $state = $booking->status === 'pending' ? 'pending' : 'booked';
                $startAt = $booking->start_at;
                $endAt = $booking->end_at;
                $bookingId = $booking->id;
                $bookingStatus = $booking->status;
                $isOwner = $booking->user_id === $userId;
            }
            return [
                'id' => $table->id,
                'name' => $table->name,
                'x' => $table->x,
                'y' => $table->y,
                'capacity' => $table->capacity,
                'state' => $state,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'booking_id' => $bookingId,
                'booking_status' => $bookingStatus,
                'is_owner' => $isOwner,
                'waitlisted' => $tableWaitlists->has($table->id),
            ];
        });

        $defaultBookingMinutes = (int) DB::table('settings')->where('key', 'default_booking_duration_minutes')->value('value') ?? 120;
        $pendingTimeoutMinutes = 15; // Matches CleanupBookings command
        $unreadNotificationsCount = Notification::where('user_id', $userId)->unread()->count();

        return view('student.hive.index', [
            'tables' => $tableStates,
            'columns' => $columns,
            'defaultBookingMinutes' => $defaultBookingMinutes,
            'pendingTimeoutMinutes' => $pendingTimeoutMinutes,
            'unreadNotificationsCount' => $unreadNotificationsCount,
        ]);
    }

    /**
     * Return Hive bookings as FullCalendar events (for current user).
     */
    public function calendarEvents()
    {
        $userId = auth()->id();
        $bookings = Booking::where('user_id', $userId)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->with('table')
            ->get();

        $events = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'title' => 'Table ' . ($booking->table->name ?? $booking->table_id),
                'start' => $booking->start_at->toIso8601String(),
                'end' => $booking->end_at->toIso8601String(),
                'status' => $booking->status,
                'table_id' => $booking->table_id,
                'backgroundColor' => $booking->status === 'pending' ? '#f59e42' : ($booking->status === 'checked_in' ? '#059669' : '#2563eb'),
                'borderColor' => '#1e293b',
                'textColor' => '#fff',
            ];
        });

        return response()->json($events);
    }
}
