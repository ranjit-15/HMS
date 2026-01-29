<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Contracts\View\View;

class BookingController extends Controller
{
    public function index(): View
    {
        $bookings = Booking::with(['user', 'table'])
            ->orderByDesc('start_at')
            ->paginate(20);

        return view('admin.bookings.index', compact('bookings'));
    }
}
