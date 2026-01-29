<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HiveController extends Controller
{
    /**
     * Check if a table is available for the given time slot (no overlapping bookings).
     * Used by booking modal for conflict checking.
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        $tableId = $request->query('table_id');
        $startAt = $request->query('start_at');
        $endAt = $request->query('end_at');

        if (! $tableId || ! $startAt || ! $endAt) {
            return response()->json([
                'conflict' => true,
                'message' => 'Missing table_id, start_at, or end_at.',
            ], 400);
        }

        $start = \Carbon\Carbon::parse($startAt);
        $end = \Carbon\Carbon::parse($endAt);

        if ($end->lte($start)) {
            return response()->json([
                'conflict' => true,
                'message' => 'End time must be after start time.',
            ]);
        }

        $overlap = Booking::query()
            ->where('table_id', $tableId)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where(function ($q) use ($start, $end) {
                $q->where('start_at', '<', $end)
                    ->where('end_at', '>', $start);
            })
            ->exists();

        return response()->json([
            'conflict' => $overlap,
            'message' => $overlap ? 'This time slot conflicts with an existing booking.' : null,
        ]);
    }
}
