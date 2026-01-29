@extends('admin.layout')

@section('title', 'Closures')
@section('header', 'Calendar Closures')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-semibold">Closed Dates</h2>
    <a href="{{ route('admin.closures.create') }}" class="rounded bg-indigo-600 px-3 py-2 text-white text-sm font-medium hover:bg-indigo-700">Add Closure</a>
</div>

<div class="overflow-x-auto bg-white shadow rounded-lg">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr class="text-left text-sm text-slate-600">
                <th class="px-4 py-3">Start</th>
                <th class="px-4 py-3">End</th>
                <th class="px-4 py-3">Reason</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm">
            @forelse($closures as $closure)
            <tr>
                <td class="px-4 py-3">{{ $closure->start_date->format('Y-m-d') }}</td>
                <td class="px-4 py-3">{{ $closure->end_date->format('Y-m-d') }}</td>
                <td class="px-4 py-3">{{ $closure->reason }}</td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.closures.edit', $closure) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                    <form action="{{ route('admin.closures.destroy', $closure) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this closure?')" class="text-red-600 hover:text-red-800">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-6 text-center text-slate-500">No closures yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $closures->links() }}</div>
@endsection
