<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarEvent extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'all_day',
        'color',
        'type',
        'is_visible',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'all_day' => 'boolean',
        'is_visible' => 'boolean',
    ];

    /**
     * Event type colors for display
     * Updated palette: Holiday=Red, Event=Yellow, Exam=Green, Closure=Black
     */
    public const TYPE_COLORS = [
        'holiday' => '#ef4444',  // Red
        'event' => '#f59e0b',    // Yellow/Amber
        'exam' => '#10b981',     // Green/Emerald
        'closure' => '#000000',  // Black
        'deadline' => '#ef4444', // Red (same as holiday)
        'other' => '#6b7280',    // Gray
    ];

    /**
     * Get the admin who created this event
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to only visible events
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope to events within a date range
     */
    public function scopeInRange($query, $start, $end)
    {
        return $query->where(function ($q) use ($start, $end) {
            $q->whereBetween('start_date', [$start, $end])
              ->orWhereBetween('end_date', [$start, $end])
              ->orWhere(function ($q2) use ($start, $end) {
                  $q2->where('start_date', '<=', $start)
                     ->where('end_date', '>=', $end);
              });
        });
    }

    /**
     * Format event for FullCalendar JSON
     */
    public function toFullCalendarEvent(): array
    {
        $event = [
            'id' => $this->id,
            'title' => $this->title,
            'start' => $this->start_date->format('Y-m-d'),
            'allDay' => $this->all_day,
            'backgroundColor' => $this->color,
            'borderColor' => $this->color,
            'extendedProps' => [
                'description' => $this->description,
                'type' => $this->type,
            ],
        ];

        if ($this->end_date) {
            // FullCalendar end date is exclusive, so add 1 day (use copy to avoid mutating model)
            $event['end'] = $this->end_date->copy()->addDay()->format('Y-m-d');
        }

        if (!$this->all_day && $this->start_time) {
            $event['start'] = $this->start_date->format('Y-m-d') . 'T' . $this->start_time;
            if ($this->end_time) {
                $endDate = $this->end_date ?? $this->start_date;
                $event['end'] = $endDate->copy()->format('Y-m-d') . 'T' . $this->end_time;
            }
            $event['allDay'] = false;
        }

        return $event;
    }
}
