<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BorrowRequest;
use App\Models\CalendarClosure;
use App\Models\CalendarEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(): View
    {
        return view('admin.calendar.index');
    }

    /**
     * Calendar events API for admin: all users' Hive bookings, Library due dates, closures, and admin events.
     */
    public function events(Request $request): JsonResponse
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $events = [];

        // Admin-created calendar events
        $calendarEvents = CalendarEvent::visible()
            ->when($start && $end, function ($q) use ($start, $end) {
                return $q->inRange($start, $end);
            })
            ->get();
        foreach ($calendarEvents as $event) {
            $fc = $event->toFullCalendarEvent();
            $fc['extendedProps'] = $fc['extendedProps'] ?? [];
            $fc['extendedProps']['type'] = $fc['extendedProps']['type'] ?? 'event';
            $events[] = $fc;
        }

        // Closures
        $closures = CalendarClosure::query()
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->where(function ($q2) use ($start, $end) {
                    $q2->whereBetween('start_date', [$start, $end])
                        ->orWhereBetween('end_date', [$start, $end])
                        ->orWhere(function ($q3) use ($start, $end) {
                            $q3->where('start_date', '<=', $start)->where('end_date', '>=', $end);
                        });
                });
            })
            ->get();
        foreach ($closures as $c) {
            $events[] = [
                'id' => 'closure-' . $c->id,
                'title' => $c->reason ?: 'Closed',
                'start' => $c->start_date->format('Y-m-d'),
                'end' => $c->end_date->copy()->addDay()->format('Y-m-d'),
                'allDay' => true,
                'backgroundColor' => '#000000',
                'borderColor' => '#000000',
                'extendedProps' => ['type' => 'closure', 'description' => 'Library and Hive closed'],
            ];
        }

        // All users' Hive bookings (active only)
        $hiveQuery = Booking::query()
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('end_at', '>', now())
            ->with(['user', 'table']);
        if ($start && $end) {
            $hiveQuery->where('start_at', '<', $end)->where('end_at', '>', $start);
        }
        foreach ($hiveQuery->get() as $b) {
            $events[] = [
                'id' => 'hive-' . $b->id,
                'title' => ($b->user->name ?? 'User') . ' – ' . ($b->table->name ?? 'Table'),
                'start' => $b->start_at->toIso8601String(),
                'end' => $b->end_at->toIso8601String(),
                'allDay' => false,
                'backgroundColor' => '#f59e0b',
                'borderColor' => '#f59e0b',
                'extendedProps' => [
                    'type' => 'hive',
                    'description' => 'Hive booking',
                    'actionUrl' => route('admin.tables.index'),
                ],
            ];
        }

        // All users' Library due dates (borrowed)
        $borrowsQuery = BorrowRequest::query()
            ->where('status', 'borrowed')
            ->whereNotNull('due_at')
            ->with(['user', 'book']);
        if ($start && $end) {
            $borrowsQuery->whereDate('due_at', '>=', $start)->whereDate('due_at', '<=', $end);
        }
        foreach ($borrowsQuery->get() as $br) {
            $isOverdue = $br->due_at->isPast();
            $events[] = [
                'id' => 'library-' . $br->id,
                'title' => ($isOverdue ? 'Overdue: ' : 'Due: ') . ($br->book->title ?? 'Book') . ' – ' . ($br->user->name ?? 'User'),
                'start' => $br->due_at->format('Y-m-d'),
                'end' => $br->due_at->copy()->addDay()->format('Y-m-d'),
                'allDay' => true,
                'backgroundColor' => $isOverdue ? '#ef4444' : '#3b82f6',
                'borderColor' => $isOverdue ? '#ef4444' : '#3b82f6',
                'extendedProps' => [
                    'type' => $isOverdue ? 'overdue' : 'library',
                    'description' => 'Library due',
                    'actionUrl' => route('admin.borrows.index'),
                ],
            ];
        }

        return response()->json($events);
    }
}
