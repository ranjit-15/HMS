<div class="space-y-6">
    <!-- Title -->
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1" for="title">Title <span class="text-red-500">*</span></label>
        <input type="text" name="title" id="title" required
               value="{{ old('title', $event->title ?? '') }}"
               class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-[#d9534f] focus:outline-none focus:ring-2 focus:ring-[#d9534f]/20"
               placeholder="e.g., Mid-Term Exams, Dashain Holiday">
        @error('title')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Type -->
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1" for="type">Type <span class="text-red-500">*</span></label>
        <select name="type" id="type" required
                class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-[#d9534f] focus:outline-none focus:ring-2 focus:ring-[#d9534f]/20">
            <option value="event" {{ old('type', $event->type ?? 'event') === 'event' ? 'selected' : '' }}>📅 Event</option>
            <option value="holiday" {{ old('type', $event->type ?? '') === 'holiday' ? 'selected' : '' }}>🎉 Holiday</option>
            <option value="exam" {{ old('type', $event->type ?? '') === 'exam' ? 'selected' : '' }}>📝 Exam</option>
            <option value="deadline" {{ old('type', $event->type ?? '') === 'deadline' ? 'selected' : '' }}>⏰ Deadline</option>
            <option value="other" {{ old('type', $event->type ?? '') === 'other' ? 'selected' : '' }}>📌 Other</option>
        </select>
        @error('type')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Description -->
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1" for="description">Description</label>
        <textarea name="description" id="description" rows="3"
                  class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-[#d9534f] focus:outline-none focus:ring-2 focus:ring-[#d9534f]/20"
                  placeholder="Optional details about this event">{{ old('description', $event->description ?? '') }}</textarea>
        @error('description')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Date Range -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1" for="start_date">Start Date <span class="text-red-500">*</span></label>
            <input type="date" name="start_date" id="start_date" required
                   value="{{ old('start_date', isset($event) ? $event->start_date->format('Y-m-d') : '') }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-[#d9534f] focus:outline-none focus:ring-2 focus:ring-[#d9534f]/20">
            @error('start_date')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1" for="end_date">End Date</label>
            <input type="date" name="end_date" id="end_date"
                   value="{{ old('end_date', isset($event) && $event->end_date ? $event->end_date->format('Y-m-d') : '') }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-[#d9534f] focus:outline-none focus:ring-2 focus:ring-[#d9534f]/20">
            <p class="mt-1 text-xs text-slate-500">Leave empty for single-day events</p>
            @error('end_date')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- All Day Toggle -->
    <div class="flex items-center gap-3">
        <input type="hidden" name="all_day" value="0">
        <input type="checkbox" name="all_day" id="all_day" value="1"
               {{ old('all_day', $event->all_day ?? true) ? 'checked' : '' }}
               class="rounded border-slate-300 text-[#d9534f] focus:ring-[#d9534f]"
               onchange="toggleTimeInputs()">
        <label class="text-sm text-slate-700" for="all_day">All day event</label>
    </div>

    <!-- Time Range (shown only when not all-day) -->
    <div id="timeInputs" class="grid grid-cols-1 md:grid-cols-2 gap-4" style="display: none;">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1" for="start_time">Start Time</label>
            <input type="time" name="start_time" id="start_time"
                   value="{{ old('start_time', $event->start_time ?? '') }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-[#d9534f] focus:outline-none focus:ring-2 focus:ring-[#d9534f]/20">
            @error('start_time')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1" for="end_time">End Time</label>
            <input type="time" name="end_time" id="end_time"
                   value="{{ old('end_time', $event->end_time ?? '') }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-[#d9534f] focus:outline-none focus:ring-2 focus:ring-[#d9534f]/20">
            @error('end_time')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Color (optional override) -->
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1" for="color">Color</label>
        <div class="flex items-center gap-3">
            <input type="color" name="color" id="color"
                   value="{{ old('color', $event->color ?? '#bd281e') }}"
                   class="h-10 w-14 rounded border border-slate-300 cursor-pointer">
            <span class="text-xs text-slate-500">Defaults to type color if left unchanged</span>
        </div>
        @error('color')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Visibility -->
    <div class="flex items-center gap-3">
        <input type="hidden" name="is_visible" value="0">
        <input type="checkbox" name="is_visible" id="is_visible" value="1"
               {{ old('is_visible', $event->is_visible ?? true) ? 'checked' : '' }}
               class="rounded border-slate-300 text-[#d9534f] focus:ring-[#d9534f]">
        <label class="text-sm text-slate-700" for="is_visible">Visible to students</label>
    </div>
</div>

<script>
function toggleTimeInputs() {
    const allDay = document.getElementById('all_day').checked;
    document.getElementById('timeInputs').style.display = allDay ? 'none' : 'grid';
}
// Run on page load
document.addEventListener('DOMContentLoaded', toggleTimeInputs);
</script>
