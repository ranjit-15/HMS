<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        $timezone = config('app.timezone');

        $hourly = Booking::query()
            ->where('status', 'checked_in')
            ->selectRaw('HOUR(CONVERT_TZ(start_at, "+00:00", "' . $this->tzOffset($timezone) . '")) as hour, COUNT(*) as total')
            ->groupBy('hour')
            ->pluck('total', 'hour');

        $hours = range(0, 23);
        $hourTotals = array_map(fn ($h) => (int) ($hourly[$h] ?? 0), $hours);

        $dowRaw = Booking::query()
            ->where('status', 'checked_in')
            ->selectRaw('DAYOFWEEK(CONVERT_TZ(start_at, "+00:00", "' . $this->tzOffset($timezone) . '")) as dow, COUNT(*) as total')
            ->groupBy('dow')
            ->pluck('total', 'dow');

        $dowOrder = [2, 3, 4, 5, 6, 7, 1]; // Mon..Sun
        $dowLabels = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
        $dowTotals = array_map(fn ($dow) => (int) ($dowRaw[$dow] ?? 0), $dowOrder);

        return view('admin.analytics.index', [
            'hours' => $hours,
            'hourTotals' => $hourTotals,
            'dowLabels' => $dowLabels,
            'dowTotals' => $dowTotals,
        ]);
    }

    private function tzOffset(string $tz): string
    {
        $now = Carbon::now($tz);
        return $now->format('P'); // e.g. +05:45
    }
}
