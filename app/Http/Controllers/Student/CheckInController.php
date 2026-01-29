<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\HiveTable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckInController extends Controller
{
    public function __invoke(Request $request, HiveTable $table, string $hash): View
    {
        if ($table->check_in_secret !== $hash) {
            abort(404);
        }

        $now = now();
        $userId = $request->user()->id;

        $booking = Booking::query()
            ->where('table_id', $table->id)
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('start_at', '<=', $now)
            ->where('end_at', '>', $now)
            ->first();

        if ($booking) {
            $booking->update(['status' => 'checked_in']);
            $status = 'success';
            $message = 'Check-in successful. Enjoy your session!';
        } else {
            $status = 'error';
            $message = 'No active booking found for this table right now.';
        }

        return view('student.checkin.result', [
            'table' => $table,
            'status' => $status,
            'message' => $message,
        ]);
    }
}
