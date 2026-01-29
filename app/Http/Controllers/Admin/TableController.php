<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HiveTable;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Services\WaitlistService;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class TableController extends Controller
{
    public function __construct(private WaitlistService $waitlistService)
    {
    }

    public function index()
    {
        $tables = HiveTable::withTrashed()
            ->with(['bookings' => function ($q) {
                $q->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                  ->where('end_at', '>', now());
            }])
            ->orderBy('y')->orderBy('x')->paginate(20);

        // attach active booking id for view convenience
        $tables->getCollection()->transform(function ($table) {
            $table->active_booking_id = $table->bookings->first()->id ?? null;
            return $table;
        });

        return view('admin.tables.index', compact('tables'));
    }
    public function restore($id)
    {
        $table = HiveTable::withTrashed()->findOrFail($id);
        if ($table->deleted_at) {
            $table->restore();
            $this->log('restored', 'table', $table->id);
            return redirect()->route('admin.tables.index')->with('status', 'Table restored.');
        }
        return back()->with('error', 'Table is not deleted.');
    }

    public function create()
    {
        return view('admin.tables.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        // Auto-assign next coordinates to avoid manual x/y entry
        [$nextX, $nextY] = $this->nextCoordinates();
        $data['x'] = $nextX;
        $data['y'] = $nextY;

        $table = HiveTable::create($data);
        $this->log('created', 'table', $table->id);
        return redirect()->route('admin.tables.index')->with('status', 'Table created.');
    }

    public function edit(HiveTable $table)
    {
        return view('admin.tables.edit', compact('table'));
    }

    public function update(Request $request, HiveTable $table)
    {
        $data = $this->validated($request, $table->id);
        // Keep existing coordinates; only update editable fields
        $table->update($data);
        $this->log('updated', 'table', $table->id);
        return redirect()->route('admin.tables.index')->with('status', 'Table updated.');
    }

    public function destroy(HiveTable $table)
    {
        try {
            $table->delete();
            $this->log('deleted', 'table', $table->id);
            return redirect()->route('admin.tables.index')->with('status', 'Table deleted.');
        } catch (QueryException $e) {
            return back()->with('error', 'Cannot delete table: it is referenced by bookings.');
        }
    }

    public function endBooking(Request $request, HiveTable $table)
    {
        $active = Booking::where('table_id', $table->id)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('end_at', '>', now())
            ->first();

        if (! $active) {
            return back()->with('error', 'No active booking found for this table.');
        }

        $endedAt = now();
        if ($endedAt->lt($active->start_at)) {
            $endedAt = $active->start_at;
        }

        $active->update([
            'status' => 'cancelled',
            'released_at' => now(),
            'end_at' => $endedAt,
        ]);

        $this->log('ended_booking', 'table', $table->id);

        $this->waitlistService->notifyNextForTable($table->id);

        return back()->with('status', 'Booking ended for table.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'boolean'],
        ], [
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }

    private function nextCoordinates(): array
    {
        $columns = 5;
        $last = HiveTable::withTrashed()
            ->orderByDesc('y')
            ->orderByDesc('x')
            ->first();

        if (! $last) {
            return [1, 1];
        }

        $nextX = $last->x + 1;
        $nextY = $last->y;

        if ($nextX > $columns) {
            $nextX = 1;
            $nextY = $last->y + 1;
        }

        return [$nextX, $nextY];
    }

    private function log(string $action, string $targetType, ?int $targetId = null): void
    {
        AuditLog::create([
            'admin_id' => auth('admin')->id(),
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
        ]);
    }
}
