@csrf
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="block text-sm font-medium mb-1">Start Date</label>
        <input name="start_date" type="date" required value="{{ old('start_date', isset($closure) ? $closure->start_date->format('Y-m-d') : '') }}" class="w-full rounded border border-slate-300 px-3 py-2 text-sm" />
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">End Date</label>
        <input name="end_date" type="date" required value="{{ old('end_date', isset($closure) ? $closure->end_date->format('Y-m-d') : '') }}" class="w-full rounded border border-slate-300 px-3 py-2 text-sm" />
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Reason</label>
        <input name="reason" type="text" value="{{ old('reason', $closure->reason ?? '') }}" class="w-full rounded border border-slate-300 px-3 py-2 text-sm" />
    </div>
</div>

@if($errors->any())
    <div class="mt-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mt-6 flex space-x-3">
    <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-white text-sm font-medium hover:bg-indigo-700">Save</button>
    <a href="{{ route('admin.closures.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm text-slate-700">Cancel</a>
</div>
