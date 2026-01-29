<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $actionFilter = trim((string) $request->query('action', ''));
        $adminFilter = $request->query('admin_id');
        $targetType = trim((string) $request->query('target_type', ''));
        $targetId = $request->query('target_id');

        $logs = AuditLog::with('admin')
            ->when($actionFilter, fn ($q) => $q->where('action', $actionFilter))
            ->when($adminFilter, fn ($q) => $q->where('admin_id', $adminFilter))
            ->when($targetType, fn($q) => $q->where('target_type', $targetType))
            ->when($targetId, fn($q) => $q->where('target_id', $targetId))
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        $actions = AuditLog::query()->select('action')->distinct()->pluck('action');
        $admins = AuditLog::query()
            ->select(['admin_id'])
            ->whereNotNull('admin_id')
            ->distinct()
            ->with('admin:id,name')
            ->get()
            ->pluck('admin')
            ->filter();

        return view('admin.audit.index', [
            'logs' => $logs,
            'actions' => $actions,
            'admins' => $admins,
            'actionFilter' => $actionFilter,
            'adminFilter' => $adminFilter,
            'targetType' => $targetType,
            'targetId' => $targetId,
        ]);
    }
}
