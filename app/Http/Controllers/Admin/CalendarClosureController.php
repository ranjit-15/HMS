<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarClosure;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class CalendarClosureController extends Controller
{
    public function index()
    {
        $closures = CalendarClosure::orderBy('start_date', 'desc')->paginate(15);
        return view('admin.closures.index', compact('closures'));
    }

    public function create()
    {
        return view('admin.closures.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['created_by'] = auth('admin')->id();
        CalendarClosure::create($data);
        return redirect()->route('admin.closures.index')->with('status', 'Closure added.');
    }

    public function edit(CalendarClosure $closure)
    {
        return view('admin.closures.edit', compact('closure'));
    }

    public function update(Request $request, CalendarClosure $closure)
    {
        $data = $this->validated($request);
        $closure->update($data);
        return redirect()->route('admin.closures.index')->with('status', 'Closure updated.');
    }

    public function destroy(CalendarClosure $closure)
    {
        try {
            $closure->delete();
            return redirect()->route('admin.closures.index')->with('status', 'Closure deleted.');
        } catch (QueryException $e) {
            return back()->with('error', 'Cannot delete closure: it is referenced by other records.');
        }
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
