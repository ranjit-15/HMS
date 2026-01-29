<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BorrowRequest;
use App\Models\CalendarClosure;
use App\Models\CalendarEvent;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(): View
    {
        $closures = CalendarClosure::orderBy('start_date')->get();
        $unreadNotificationsCount = Notification::where('user_id', auth()->id())->unread()->count();

        return view('student.calendar.index', [
            'closures' => $closures,
            'unreadNotificationsCount' => $unreadNotificationsCount,
        ]);
    }

    /**
     * Get calendar events as JSON for FullCalendar.
     * Includes: admin events, closures, user's Hive bookings (timed), user's Library due dates (all-day).
     */
    public function events(Request $request): JsonResponse
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $userId = auth()->id();

        $events = [];

        // Admin-created calendar events
        $calendarEvents = CalendarEvent::visible()
            ->when($start && $end, function ($query) use ($start, $end) {
                $query->inRange($start, $end);
            })
            ->get();

        foreach ($calendarEvents as $event) {
            $fc = $event->toFullCalendarEvent();
            $fc['extendedProps'] = $fc['extendedProps'] ?? [];
            $fc['extendedProps']['type'] = $fc['extendedProps']['type'] ?? 'event';
            $events[] = $fc;
        }

        // Closures (all-day, gray)
        $closures = CalendarClosure::query()
            ->when($start && $end, function ($query) use ($start, $end) {
                $query->where(function ($q) use ($start, $end) {
                    $q->whereBetween('start_date', [$start, $end])
                        ->orWhereBetween('end_date', [$start, $end])
                        ->orWhere(function ($q2) use ($start, $end) {
                            $q2->where('start_date', '<=', $start)
                                ->where('end_date', '>=', $end);
                        });
                });
            })
            ->get();

        foreach ($closures as $closure) {
            $events[] = [
                'id' => 'closure-' . $closure->id,
                'title' => $closure->reason ?: 'Closed',
                'start' => $closure->start_date->format('Y-m-d'),
                'end' => $closure->end_date->copy()->addDay()->format('Y-m-d'),
                'allDay' => true,
                'backgroundColor' => '#000000',
                'borderColor' => '#000000',
                'extendedProps' => [
                    'type' => 'closure',
                    'description' => 'Library and Hive closed',
                ],
            ];
        }

        // User's Hive bookings (timed events)
        $hiveQuery = Booking::query()
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('end_at', '>', now());
        if ($start && $end) {
            $hiveQuery->where('start_at', '<', $end)
                ->where('end_at', '>', $start);
        }
        $hiveBookings = $hiveQuery->with('table')->get();

        foreach ($hiveBookings as $b) {
            $events[] = [
                'id' => 'hive-' . $b->id,
                'title' => 'Hive: ' . ($b->table->name ?? 'Table'),
                'start' => $b->start_at->toIso8601String(),
                'end' => $b->end_at->toIso8601String(),
                'allDay' => false,
                'backgroundColor' => '#f59e0b',
                'borderColor' => '#f59e0b',
                'extendedProps' => [
                    'type' => 'hive',
                    'description' => 'Table booking',
                    'actionUrl' => route('student.activity'),
                ],
            ];
        }

        // User's Library due dates (all-day; overdue = red)
        $borrowsQuery = BorrowRequest::query()
            ->where('user_id', $userId)
            ->where('status', 'borrowed')
            ->whereNotNull('due_at');
        if ($start && $end) {
            $borrowsQuery->whereDate('due_at', '>=', $start)
                ->whereDate('due_at', '<=', $end);
        }
        $borrows = $borrowsQuery->with('book')->get();

        foreach ($borrows as $br) {
            $isOverdue = $br->due_at->isPast();
            $events[] = [
                'id' => 'library-' . $br->id,
                'title' => ($isOverdue ? 'Overdue: ' : 'Due: ') . ($br->book->title ?? 'Book'),
                'start' => $br->due_at->format('Y-m-d'),
                'end' => $br->due_at->copy()->addDay()->format('Y-m-d'),
                'allDay' => true,
                'backgroundColor' => $isOverdue ? '#ef4444' : '#3b82f6',
                'borderColor' => $isOverdue ? '#ef4444' : '#3b82f6',
                'extendedProps' => [
                    'type' => $isOverdue ? 'overdue' : 'library',
                    'description' => 'Library due date',
                    'actionUrl' => route('student.library'),
                ],
            ];
        }

        return response()->json($events);
    }
}
