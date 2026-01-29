@extends('layouts.student')

@section('title', 'Calendar')
@section('header', 'Calendar')
@section('subheader', 'View your bookings and library due dates')

@section('content')
<div x-data="calendarApp()" x-init="initCalendar()">
    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6">
        <div class="flex flex-wrap items-center gap-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" x-model="showHive" class="w-4 h-4 text-amber-600 border-slate-300 rounded focus:ring-amber-500">
                <span class="text-sm text-slate-700">Show Hive Bookings</span>
                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" x-model="showLibrary" class="w-4 h-4 text-[#8b0000] border-slate-300 rounded focus:ring-[#8b0000]">
                <span class="text-sm text-slate-700">Show Library Due Dates</span>
                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" x-model="showClosures" class="w-4 h-4 text-slate-600 border-slate-300 rounded focus:ring-slate-500">
                <span class="text-sm text-slate-700">Show Closures</span>
                <span class="w-3 h-3 rounded-full bg-slate-800"></span>
            </label>
        </div>
    </div>

    {{-- Legend --}}
    <div class="flex flex-wrap items-center gap-4 mb-4 text-sm">
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 rounded bg-amber-500"></span>
            <span class="text-slate-600">Hive Booking</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 rounded bg-[#8b0000]"></span>
            <span class="text-slate-600">Library Due Date</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 rounded bg-red-500"></span>
            <span class="text-slate-600">Overdue</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 rounded bg-slate-800"></span>
            <span class="text-slate-600">Closed</span>
        </div>
    </div>

    {{-- Calendar Container --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <div id="calendar" class="min-h-[600px]"></div>
    </div>

    {{-- Event Detail Modal --}}
    <div x-show="showEventModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        @click.self="showEventModal = false" @keydown.escape.window="showEventModal = false">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden" @click.stop>
            {{-- Header --}}
            <div class="px-6 py-4" :class="eventModalColor">
                <h2 class="text-xl font-semibold text-white" x-text="eventDetail.title"></h2>
            </div>

            {{-- Content --}}
            <div class="p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-slate-700" x-text="eventDetail.dateDisplay"></span>
                </div>

                <template x-if="eventDetail.timeDisplay">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-slate-700" x-text="eventDetail.timeDisplay"></span>
                    </div>
                </template>

                <template x-if="eventDetail.description">
                    <div class="p-3 bg-slate-50 rounded-lg">
                        <p class="text-sm text-slate-600" x-text="eventDetail.description"></p>
                    </div>
                </template>

                {{-- Type badge --}}
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium capitalize"
                        :class="{
                            'bg-amber-100 text-amber-700': eventDetail.type === 'hive',
                            'bg-blue-100 text-blue-700': eventDetail.type === 'library',
                            'bg-slate-100 text-slate-700': eventDetail.type === 'closure',
                            'bg-red-100 text-red-700': eventDetail.type === 'overdue'
                        }"
                        x-text="eventDetail.type"></span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                <div class="flex items-center gap-3">
                    <button type="button" @click="showEventModal = false"
                        class="flex-1 px-4 py-2 bg-slate-200 text-slate-700 font-medium rounded-lg hover:bg-slate-300 transition-colors">
                        Close
                    </button>
                    <template x-if="eventDetail.actionUrl">
                        <a :href="eventDetail.actionUrl"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors text-center">
                            View Details
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Closures List (below calendar) --}}
    @if($closures->isNotEmpty())
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <h3 class="text-lg font-semibold text-slate-800 mb-3">Upcoming Closures</h3>
            <div class="space-y-2">
                @foreach($closures as $closure)
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                        <div>
                            <p class="font-medium text-slate-700">{{ $closure->reason ?: 'Library Closed' }}</p>
                            <p class="text-sm text-slate-500">
                                {{ $closure->start_date->format('M j, Y') }}
                                @if($closure->start_date->ne($closure->end_date))
                                    – {{ $closure->end_date->format('M j, Y') }}
                                @endif
                            </p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-slate-200 text-slate-700">
                            {{ $closure->start_date->diffForHumans() }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }

    /* FullCalendar overrides */
    .fc {
        --fc-border-color: #e6e6e9;
        --fc-button-bg-color: #8b0000;
        --fc-button-border-color: #8b0000;
        --fc-button-hover-bg-color: #b91c1c;
        --fc-button-hover-border-color: #b91c1c;
        --fc-button-active-bg-color: #b91c1c;
        --fc-today-bg-color: rgba(139,0,0,0.06);
    }

    .fc .fc-button { border-radius: 0.6rem; font-weight:600; padding:.45rem .65rem; box-shadow:0 8px 20px rgba(139,0,0,0.06); }
    .fc .fc-toolbar-title { font-size:1.125rem; font-weight:700; letter-spacing:0.2px }
    .fc-event { border-radius:0.5rem; padding:6px 8px; font-size:0.85rem; box-shadow:0 6px 16px rgba(15,23,42,0.06); }
</style>
@endpush

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
function calendarApp() {
    return {
        calendar: null,
        showHive: true,
        showLibrary: true,
        showClosures: true,
        showEventModal: false,
        eventDetail: {},

        get eventModalColor() {
            const type = this.eventDetail.type;
            if (type === 'hive') return 'bg-gradient-to-r from-amber-500 to-amber-600';
            if (type === 'library') return 'bg-gradient-to-r from-blue-500 to-blue-600';
            if (type === 'overdue') return 'bg-gradient-to-r from-red-500 to-red-600';
            return 'bg-gradient-to-r from-slate-600 to-slate-700';
        },

        initCalendar() {
            const calendarEl = document.getElementById('calendar');
            const self = this;

            this.calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: {
                    url: '{{ route("student.calendar.events") }}',
                    method: 'GET',
                    failure: function() {
                        console.error('Failed to load events');
                    }
                },
                eventClick: function(info) {
                    self.openEventModal(info.event);
                },
                eventDidMount: function(info) {
                    const type = info.event.extendedProps.type;

                    // Filter visibility
                    if (type === 'hive' && !self.showHive) {
                        info.el.style.display = 'none';
                    } else if ((type === 'library' || type === 'due' || type === 'overdue') && !self.showLibrary) {
                        info.el.style.display = 'none';
                    } else if (type === 'closure' && !self.showClosures) {
                        info.el.style.display = 'none';
                    }
                },
                height: 'auto',
                nowIndicator: true,
                dayMaxEvents: 3
            });

            this.calendar.render();

            // Watch filter changes
            this.$watch('showHive', () => this.calendar.refetchEvents());
            this.$watch('showLibrary', () => this.calendar.refetchEvents());
            this.$watch('showClosures', () => this.calendar.refetchEvents());
        },

        openEventModal(event) {
            const start = event.start;
            const end = event.end;

            this.eventDetail = {
                title: event.title,
                type: event.extendedProps?.type || 'event',
                description: event.extendedProps?.description || '',
                dateDisplay: start ? start.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : '',
                timeDisplay: !event.allDay && start ? start.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) + (end ? ' - ' + end.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) : '') : null,
                actionUrl: event.extendedProps?.actionUrl || null
            };

            this.showEventModal = true;
        }
    };
}
</script>
@endsection
