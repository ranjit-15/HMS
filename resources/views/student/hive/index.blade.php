@extends('layouts.student')

@section('title', 'Hive - Seat Booking')
@section('header', 'The Hive')
@section('subheader', 'Book your study seat')

@section('content')
    <div x-data="hiveApp()" x-init="init()" class="relative">
        <!-- View Toggle -->
        <div class="flex justify-end mb-4">
            <div class="inline-flex rounded-lg shadow-sm bg-slate-100 overflow-hidden">
                <button type="button" @click="view = 'table'" :class="view === 'table' ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-blue-50'" class="px-4 py-2 font-medium focus:outline-none transition">Table View</button>
                <button type="button" @click="view = 'calendar'" :class="view === 'calendar' ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-blue-50'" class="px-4 py-2 font-medium focus:outline-none transition">Calendar View</button>
            </div>
        </div>

        <!-- Calendar View -->
        <div x-show="view === 'calendar'" x-cloak>
            <div id="hive-calendar" class="bg-white rounded-xl shadow-sm border p-4 mb-6"></div>
            <!-- Calendar Event Modal -->
            <div x-show="showEventModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" @click.self="showEventModal = false" @keydown.escape.window="showEventModal = false">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden" @click.stop>
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                        <h2 class="text-xl font-semibold text-white">Booking Details</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="{
                                'bg-blue-100 text-blue-700': eventDetails.status === 'confirmed',
                                'bg-amber-100 text-amber-700': eventDetails.status === 'pending',
                                'bg-emerald-100 text-emerald-700': eventDetails.status === 'checked_in',
                            }">
                                <template x-if="eventDetails.status === 'confirmed'">Confirmed</template>
                                <template x-if="eventDetails.status === 'pending'">Pending</template>
                                <template x-if="eventDetails.status === 'checked_in'">Checked In</template>
                            </span>
                            <span class="font-semibold text-slate-700">Table: <span x-text="eventDetails.title"></span></span>
                        </div>
                        <div>
                            <span class="block text-slate-600 text-sm">Date:</span>
                            <span class="block font-medium text-slate-800" x-text="formatDate(eventDetails.start)"></span>
                        </div>
                        <div>
                            <span class="block text-slate-600 text-sm">Time:</span>
                            <span class="block font-medium text-slate-800" x-text="formatTime(eventDetails.start) + ' - ' + formatTime(eventDetails.end)"></span>
                        </div>
                        <div class="flex items-center gap-3 pt-2">
                            <button type="button" @click="showEventModal = false" class="flex-1 px-4 py-2 bg-slate-100 text-slate-700 font-medium rounded-lg hover:bg-slate-200 transition-colors">Close</button>
                            <template x-if="eventDetails.status !== 'checked_in'">
                                <form :action="'/student/hive/bookings/' + eventDetails.id + '/cancel'" method="POST" class="flex-1">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">Cancel Booking</button>
                                </form>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Filters removed per request (/html/body/main/div/div[1]) --}}

        {{-- Legend --}}
        <div class="flex flex-wrap items-center gap-4 mb-4 text-sm">
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded-full bg-emerald-500"></span>
                <span class="text-slate-600">Available</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded-full bg-amber-500"></span>
                <span class="text-slate-600">Pending</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded-full bg-red-500"></span>
                <span class="text-slate-600">Occupied</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded-full bg-blue-500"></span>
                <span class="text-slate-600">Your Booking</span>
            </div>
        </div>



        {{-- Table Grid --}}
        @if(empty($tables))
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
                <p class="text-slate-600 mb-2">No tables available yet.</p>
                <p class="text-sm text-slate-500">Tables will appear here once the admin adds them to The Hive.</p>
            </div>
        @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach($tables as $table)
                <div class="bg-white rounded-xl shadow-sm border-2 p-4 text-center transition-all hover:shadow-md
                                        @if($table['is_owner']) border-blue-400 bg-blue-50
                                        @elseif($table['state'] === 'available') border-emerald-200 hover:border-emerald-400
                                        @elseif($table['state'] === 'pending') border-amber-200
                                        @else border-red-200
                                        @endif">

                    {{-- Table Icon --}}
                    <div class="mx-auto w-12 h-12 rounded-full flex items-center justify-center mb-3
                                                @if($table['is_owner']) bg-blue-100 text-blue-600
                                                @elseif($table['state'] === 'available') bg-emerald-100 text-emerald-600
                                                @elseif($table['state'] === 'pending') bg-amber-100 text-amber-600
                                                @else bg-red-100 text-red-600
                                                @endif">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>

                    {{-- Table Info --}}
                    <h3 class="font-semibold text-slate-800">{{ $table['name'] }}</h3>
                    <p class="text-xs text-slate-500 mt-1">{{ $table['capacity'] }} seats</p>

                    {{-- Status Badge --}}
                    <div class="mt-2">
                        @if($table['is_owner'])
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Your
                                Booking</span>
                        @elseif($table['state'] === 'available')
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Available</span>
                        @elseif($table['state'] === 'pending')
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Pending</span>
                        @else
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Occupied</span>
                        @endif
                    </div>

                    {{-- Countdown for active bookings --}}
                    @if($table['end_at'] && ($table['state'] !== 'available'))
                        <div class="mt-2 text-xs text-slate-500" data-countdown data-end="{{ $table['end_at'] instanceof \DateTimeInterface ? $table['end_at']->format(\DateTimeInterface::ATOM) : $table['end_at'] }}">
                            Loading...
                        </div>
                    @endif

                    {{-- Action Button --}}
                    <div class="mt-3">
                        @if($table['state'] === 'available')
                            <button @click='openBookingModal(@json($table))'
                                class="w-full px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                                Book Now
                            </button>
                        @elseif($table['is_owner'])
                            <div class="space-y-2">
                                <form action="{{ route('student.hive.cancel', $table['booking_id']) }}" method="POST"
                                    onsubmit="return confirm('Cancel this booking?')">
                                    @csrf
                                    <button type="submit"
                                        class="w-full px-4 py-2 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 transition-colors">
                                        Cancel Booking
                                    </button>
                                </form>
                                @if($table['booking_status'] === 'pending')
                                    <form action="{{ route('student.hive.confirm', $table['booking_id']) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                            Confirm Booking
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @elseif($table['waitlisted'])
                            <span
                                class="block w-full px-4 py-2 bg-slate-100 text-slate-500 text-sm font-medium rounded-lg cursor-not-allowed">
                                On Waitlist
                            </span>
                        @else
                            <form action="{{ route('student.hive.waitlist', $table['id']) }}" method="POST" class="inline w-full">
                                @csrf
                                <button type="submit"
                                    class="w-full px-4 py-2 bg-slate-200 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-300 transition-colors">
                                    Join Waitlist
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        {{-- Booking Modal --}}
        <div x-show="showModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
            @click.self="showModal = false" @keydown.escape.window="showModal = false">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden" @click.stop>
                {{-- Header --}}
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white">Book a Seat</h2>
                    <p class="text-blue-100 text-sm mt-1">Table: <span x-text="selectedTable?.name"></span></p>
                </div>

                {{-- Form --}}
                <form action="{{ route('student.hive.book') }}" method="POST" class="p-6 space-y-4" id="hive-booking-form">
                    @csrf
                    <input type="hidden" name="table_id" x-model="selectedTableId">

                    <div>
                        <label for="start_at" class="block text-sm font-medium text-slate-700 mb-1">Start Time</label>
                        <input type="datetime-local" name="start_at" id="start_at" x-model="startAt" required
                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('start_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_at" class="block text-sm font-medium text-slate-700 mb-1">End Time</label>
                        <input type="datetime-local" name="end_at" id="end_at" x-model="endAt" required
                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-slate-500 mt-1">Maximum booking: 5 hours</p>
                        @error('end_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Duration hint --}}
                    <div x-show="duration" class="p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm text-blue-700">
                            Duration: <span x-text="duration" class="font-semibold"></span>
                        </p>
                    </div>

                    {{-- Client-side error --}}
                    <div x-show="error" class="p-3 bg-red-50 rounded-lg">
                        <p class="text-sm text-red-700" x-text="error"></p>
                    </div>

                    {{-- Server validation errors (table_id etc.) --}}
                    @error('table_id')
                        <div class="p-3 bg-red-50 rounded-lg">
                            <p class="text-sm text-red-700">{{ $message }}</p>
                        </div>
                    @enderror

                    <div class="flex items-center gap-3 pt-2">
                        <button type="button" @click="showModal = false"
                            class="flex-1 px-4 py-2 bg-slate-100 text-slate-700 font-medium rounded-lg hover:bg-slate-200 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" :disabled="!isValid"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            Confirm Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
        @include('components.fullcalendar-head')
    @endpush

    <script>
        function hiveApp() {
            return {
                search: '',
                filter: 'all',
                showModal: false,
                selectedTable: null,
                selectedTableId: '',
                startAt: '',
                endAt: '',
                error: '',
                filteredCount: {{ count($tables ?? []) }},
                defaultMinutes: {{ $defaultBookingMinutes ?? 120 }},
                view: 'table',
                calendar: null,
                showEventModal: false,
                eventDetails: {},

                init() {
                    if (typeof initCountdowns === 'function') initCountdowns();
                    const oldTable = @json(old('table_id'));
                    const oldStart = @json(old('start_at'));
                    const oldEnd = @json(old('end_at'));
                    if (oldTable && document.getElementById('hive-booking-form')) {
                        const t = @json($tables ?? []);
                        const match = t.find(x => String(x.id) === String(oldTable));
                        if (match) {
                            this.selectedTable = match;
                            this.selectedTableId = match.id;
                            this.startAt = oldStart || '';
                            this.endAt = oldEnd || '';
                            this.showModal = true;
                        }
                    }
                    // Calendar init
                    this.$watch('view', v => {
                        if (v === 'calendar' && !this.calendar) {
                            this.initCalendar();
                        }
                    });
                },

                initCalendar() {
                    if (this.calendar) return;
                    const calendarEl = document.getElementById('hive-calendar');
                    if (!calendarEl) return;
                    this.calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay'
                        },
                        events: '/student/hive/calendar-events',
                        eventClick: info => {
                            this.eventDetails = {
                                id: info.event.id,
                                title: info.event.title,
                                start: info.event.start,
                                end: info.event.end,
                                status: info.event.extendedProps.status,
                            };
                            this.showEventModal = true;
                        },
                        height: 600,
                        nowIndicator: true,
                        eventColor: '#2563eb',
                        eventTextColor: '#fff',
                    });
                    this.calendar.render();
                },

                formatDate(date) {
                    if (!date) return '';
                    const d = new Date(date);
                    return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
                },
                formatTime(date) {
                    if (!date) return '';
                    const d = new Date(date);
                    return d.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });
                },

                matchesFilter(table) {
                    const matchesSearch = !this.search || table.name.toLowerCase().includes(this.search.toLowerCase());
                    const matchesStatus = this.filter === 'all' ||
                        (this.filter === 'available' && table.state === 'available') ||
                        (this.filter === 'booked' && table.state !== 'available');
                    return matchesSearch && matchesStatus;
                },

                openBookingModal(table) {
                    this.selectedTable = table;
                    this.selectedTableId = table ? table.id : '';
                    this.error = '';

                    const now = new Date();
                    const start = this.toLocalInput(now);
                    const end = this.toLocalInput(new Date(now.getTime() + this.defaultMinutes * 60000));

                    this.startAt = start;
                    this.endAt = end;
                    this.showModal = true;
                },

                toLocalInput(date) {
                    const pad = (n) => String(n).padStart(2, '0');
                    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
                },

                get duration() {
                    if (!this.startAt || !this.endAt) return '';
                    const start = new Date(this.startAt);
                    const end = new Date(this.endAt);
                    const diff = (end - start) / 60000;
                    if (diff <= 0) return '';
                    const hrs = Math.floor(diff / 60);
                    const mins = diff % 60;
                    return hrs > 0 ? `${hrs}h ${mins}m` : `${mins}m`;
                },

                get isValid() {
                    if (!this.startAt || !this.endAt) return false;
                    const start = new Date(this.startAt);
                    const end = new Date(this.endAt);
                    const diff = (end - start) / 60000;

                    if (diff <= 0) {
                        this.error = 'End time must be after start time';
                        return false;
                    }
                    if (diff > 300) {
                        this.error = 'Maximum booking duration is 5 hours';
                        return false;
                    }
                    this.error = '';
                    return true;
                }
            };
        }
    </script>
@endsection