@extends('admin.layout')

@section('title', 'Audit Logs')
@section('header', 'Audit Logs')

@section('content')
<div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h2 class="text-lg font-semibold">Audit Logs</h2>
        <p class="text-sm text-slate-500">Filter by admin or action.</p>
    </div>
    <form method="GET" action="{{ route('admin.audit.index') }}" class="flex flex-wrap gap-2 items-center">
        @if(!empty($targetType) || !empty($targetId))
            <input type="hidden" name="target_type" value="{{ $targetType }}" />
            <input type="hidden" name="target_id" value="{{ $targetId }}" />
        @endif
        <select name="admin_id" class="rounded border border-slate-300 px-3 py-2 text-sm">
            <option value="">All Admins</option>
            @foreach($admins as $admin)
                <option value="{{ $admin->id }}" {{ ($adminFilter == $admin->id) ? 'selected' : '' }}>{{ $admin->name }}</option>
            @endforeach
        </select>
        <select name="action" class="rounded border border-slate-300 px-3 py-2 text-sm">
            <option value="">All Actions</option>
            @foreach($actions as $action)
                <option value="{{ $action }}" {{ ($actionFilter === $action) ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded bg-slate-800 px-3 py-2 text-white text-sm font-medium hover:bg-slate-900">Apply</button>
        @if($adminFilter || $actionFilter || $targetType || $targetId)
            <a href="{{ route('admin.audit.index') }}" class="text-sm text-slate-600 hover:text-slate-800">Reset</a>
        @endif
    </form>
</div>

@if(!empty($targetType) || !empty($targetId))
    <div class="mb-4 text-sm text-slate-600">
        <strong>Filtered target:</strong>
        <span class="ml-2">{{ ucfirst($targetType ?? 'target') }} @if(!empty($targetId)) #{{ $targetId }}@endif</span>
    </div>
@endif

<div class="rounded-lg border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="grid grid-cols-4 gap-2 px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
        <span>Admin</span>
        <span>Action</span>
        <span>Target</span>
        <span>When</span>
    </div>
    @forelse($logs as $log)
        <div class="grid grid-cols-4 gap-2 px-4 py-3 text-sm items-center border-t border-slate-100">
            <span class="text-slate-800">{{ $log->admin?->name ?? 'Admin' }}</span>
            <span class="text-slate-700">{{ $log->action }}</span>
            <span class="text-slate-700">{{ ucfirst($log->target_type) }} @if($log->target_id)#{{ $log->target_id }}@endif</span>
            <span class="text-slate-600">{{ $log->created_at->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</span>
        </div>
    @empty
        <div class="px-4 py-3 text-sm text-slate-600">No audit logs.</div>
    @endforelse
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection
