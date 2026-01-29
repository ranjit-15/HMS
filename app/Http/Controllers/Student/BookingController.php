<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use App\Services\WaitlistService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;
use RuntimeException;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService, private WaitlistService $waitlistService)
    {
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'table_id' => ['required', 'integer', 'exists:tables,id'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
        ], [
            'table_id.required' => 'Please select a table.',
            'table_id.exists' => 'The selected table is invalid.',
            'start_at.required' => 'Start time is required.',
            'start_at.date' => 'Start time must be a valid date and time.',
            'end_at.required' => 'End time is required.',
            'end_at.date' => 'End time must be a valid date and time.',
            'end_at.after' => 'End time must be after start time.',
        ]);

        $userId = $request->user()->id;
        $startAt = Carbon::parse($data['start_at']);
        $endAt = Carbon::parse($data['end_at']);

        // Allow 5 minutes grace period for "book now" actions
        if ($startAt->lt(now()->subMinutes(5))) {
            return redirect()->route('student.hive')->with('error', 'Start time cannot be in the past.')->withInput();
        }

        try {
            $this->bookingService->createBooking($userId, (int) $data['table_id'], $startAt, $endAt);
            return redirect()->route('student.activity')->with('status', 'Booking confirmed. Check-in QR is below.');
        } catch (RuntimeException $e) {
            return redirect()->route('student.hive')->with('error', $e->getMessage())->withInput();
        }
    }

    public function confirm(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            return back()->with('error', 'Not authorized to confirm this booking.');
        }

        $booking->update(['status' => 'confirmed']);
        return back()->with('status', 'Booking confirmed.');
    }

    public function cancel(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            return back()->with('error', 'Not authorized to cancel this booking.');
        }

        $booking->update([
            'status' => 'cancelled',
            'released_at' => now(),
        ]);

        $this->waitlistService->notifyNextForTable($booking->table_id);

        return back()->with('status', 'Booking cancelled.');
    }
}
