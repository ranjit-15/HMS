<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class CalendarEventController extends Controller
{
    public function index()
    {
        $events = CalendarEvent::orderBy('start_date', 'desc')->paginate(15);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['created_by'] = auth('admin')->id();
        
        // Set color based on type if not provided
        if (empty($data['color'])) {
            $data['color'] = CalendarEvent::TYPE_COLORS[$data['type']] ?? '#dc2626';
        }
        
        CalendarEvent::create($data);
        return redirect()->route('admin.events.index')->with('status', 'Event created successfully.');
    }

    public function edit(CalendarEvent $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, CalendarEvent $event)
    {
        $data = $this->validated($request);
        
        // Set color based on type if not provided
        if (empty($data['color'])) {
            $data['color'] = CalendarEvent::TYPE_COLORS[$data['type']] ?? '#dc2626';
        }
        
        $event->update($data);
        return redirect()->route('admin.events.index')->with('status', 'Event updated successfully.');
    }

    public function destroy(CalendarEvent $event)
    {
        try {
            $event->delete();
            return redirect()->route('admin.events.index')->with('status', 'Event deleted.');
        } catch (QueryException $e) {
            return back()->with('error', 'Cannot delete event.');
        }
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after_or_equal:start_time'],
            'all_day' => ['boolean'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'type' => ['required', Rule::in(['event', 'holiday', 'exam', 'deadline', 'other'])],
            'is_visible' => ['boolean'],
        ]);
    }
}
