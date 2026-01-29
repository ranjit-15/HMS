@extends('admin.layout')

@section('title', 'Calendar')
@section('header', 'Calendar – All Hive & Library Events')

@section('content')
<div x-data="adminCalendarApp()" x-init="initCalendar()">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6">
        <div class="flex flex-wrap items-center gap-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" x-model="showHive" class="w-4 h-4 text-amber-600 border-slate-300 rounded focus:ring-amber-500">
                <span class="text-sm text-slate-700">Hive Bookings</span>
                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" x-model="showLibrary" class="w-4 h-4 text-[#8b0000] border-slate-300 rounded focus:ring-[#8b0000]">
                <span class="text-sm text-slate-700">Library Due Dates</span>
                <span class="w-3 h-3 rounded-full bg-[#8b0000]"></span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" x-model="showClosures" class="w-4 h-4 text-slate-600 border-slate-300 rounded focus:ring-slate-500">
                <span class="text-sm text-slate-700">Closures</span>
                <span class="w-3 h-3 rounded-full bg-slate-800"></span>
            </label>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-4 mb-4 text-sm">
        <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-amber-500"></span><span class="text-slate-600">Hive</span></div>
        <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-[#8b0000]"></span><span class="text-slate-600">Library Due</span></div>
        <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-red-500"></span><span class="text-slate-600">Overdue</span></div>
        <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-slate-800"></span><span class="text-slate-600">Closed</span></div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <div id="admin-calendar" class="min-h-[600px]"></div>
    </div>

    <div x-show="showEventModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        @click.self="showEventModal = false" @keydown.escape.window="showEventModal = false">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden" @click.stop>
                <div class="px-6 py-4" :class="eventModalColor">
                <h2 class="text-xl font-semibold text-white" x-text="eventDetail.title"></h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-slate-700" x-text="eventDetail.dateDisplay"></span>
                </div>
                <template x-if="eventDetail.timeDisplay">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-slate-700" x-text="eventDetail.timeDisplay"></span>
                    </div>
                </template>
                <template x-if="eventDetail.description">
                    <div class="p-3 bg-slate-50 rounded-lg"><p class="text-sm text-slate-600" x-text="eventDetail.description"></p></div>
                </template>
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium capitalize"
                        :class="{'bg-amber-100 text-amber-700': eventDetail.type === 'hive', 'bg-[#fee2e2] text-[#8b0000]': eventDetail.type === 'library', 'bg-slate-100 text-slate-700': eventDetail.type === 'closure', 'bg-red-100 text-red-700': eventDetail.type === 'overdue'}"
                        x-text="eventDetail.type"></span>
                </div>
            </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex gap-3">
                <button type="button" @click="showEventModal = false" class="flex-1 px-4 py-2 bg-slate-200 text-slate-700 font-medium rounded-lg hover:bg-slate-300">Close</button>
                <template x-if="eventDetail.actionUrl">
                    <a :href="eventDetail.actionUrl" class="flex-1 px-4 py-2 bg-[#8b0000] text-white font-medium rounded-lg hover:bg-[#6b0000] text-center">View</a>
                </template>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    [x-cloak]{display:none !important;}
    /* FullCalendar modern / futuristic theme overrides */
    .fc{
        --fc-border-color: #e6e6e9;
        --fc-button-bg-color: #8b0000;
        --fc-button-border-color: #8b0000;
        --fc-today-bg-color: rgba(139,0,0,0.06);
    }
    .fc .fc-toolbar-title{font-weight:700;letter-spacing:0.2px}
    .fc .fc-button{border-radius:0.75rem;font-weight:600;padding:0.5rem 0.75rem;box-shadow:0 6px 18px rgba(139,0,0,0.08);}
    .fc .fc-button-primary{background:linear-gradient(135deg,#8b0000,#b91c1c);border:none;color:#fff}
    .fc .fc-button:hover{transform:translateY(-2px);transition:transform .15s ease}
    .fc .fc-daygrid-event, .fc .fc-timegrid-event{
        border-radius:0.6rem;padding:6px 10px;font-size:0.85rem;background:linear-gradient(90deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01));box-shadow:0 6px 14px rgba(15,23,42,0.06);backdrop-filter: blur(4px);
    }
    .fc .fc-daygrid-event-dot{display:none}
    .fc .fc-daygrid-event:hover{filter:brightness(1.02);transform:translateY(-2px)}
</style>
@endpush
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
function adminCalendarApp() {
    return {
        calendar: null,
        showHive: true,
        showLibrary: true,
        showClosures: true,
        showEventModal: false,
        eventDetail: {},
        get eventModalColor() {
            const t = this.eventDetail.type;
            if (t === 'hive') return 'bg-gradient-to-r from-amber-500 to-amber-600';
            if (t === 'library') return 'bg-gradient-to-r from-[#8b0000] to-[#b91c1c]';
            if (t === 'overdue') return 'bg-gradient-to-r from-red-500 to-red-600';
            return 'bg-gradient-to-r from-slate-600 to-slate-700';
        },
        initCalendar() {
            const el = document.getElementById('admin-calendar');
            const self = this;
            this.calendar = new FullCalendar.Calendar(el, {
                initialView: 'dayGridMonth',
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
                events: { url: '{{ route("admin.calendar.events") }}', method: 'GET' },
                eventClick: function(info) { self.openModal(info.event); },
                eventDidMount: function(info) {
                    const t = info.event.extendedProps.type;
                    if (t === 'hive' && !self.showHive) info.el.style.display = 'none';
                    else if ((t === 'library' || t === 'due' || t === 'overdue') && !self.showLibrary) info.el.style.display = 'none';
                    else if (t === 'closure' && !self.showClosures) info.el.style.display = 'none';
                },
                height: 'auto',
                nowIndicator: true,
                dayMaxEvents: 3
            });
            this.calendar.render();
            this.$watch('showHive', () => this.calendar.refetchEvents());
            this.$watch('showLibrary', () => this.calendar.refetchEvents());
            this.$watch('showClosures', () => this.calendar.refetchEvents());
        },
        openModal(event) {
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
@endpush
