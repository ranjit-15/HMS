@csrf
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <input name="name" type="text" required value="{{ old('name', $table->name ?? '') }}" class="w-full rounded border border-slate-300 px-3 py-2 text-sm" />
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Capacity</label>
        <input name="capacity" type="number" min="1" required value="{{ old('capacity', $table->capacity ?? 1) }}" class="w-full rounded border border-slate-300 px-3 py-2 text-sm" />
    </div>
    <div class="flex items-center space-x-2 mt-6">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $table->is_active ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
        <span class="text-sm">Active</span>
    </div>
</div>

<div class="mt-4 rounded border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
    <p class="font-medium">Placement</p>
    <p>Tables are placed automatically in the grid; the next open spot is used when you save.</p>
    @if(isset($table))
        <p class="mt-1 text-xs text-slate-500">Current position: X{{ $table->x }} · Y{{ $table->y }}</p>
    @endif
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
    <a href="{{ route('admin.tables.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm text-slate-700">Cancel</a>
</div>
