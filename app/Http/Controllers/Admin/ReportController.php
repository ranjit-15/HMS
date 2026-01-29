<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Booking;
use App\Models\BorrowRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    /**
     * Display report generation page.
     */
    public function index(): View
    {
        return view('admin.reports.index');
    }

    /**
     * Generate borrowing statistics report.
     */
    public function borrowingReport(Request $request)
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'format' => ['required', 'in:pdf,csv'],
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $borrows = BorrowRequest::with(['book', 'user'])
            ->whereBetween('requested_at', [$startDate, $endDate])
            ->orderByDesc('requested_at')
            ->get();

        $stats = [
            'total_requests' => $borrows->count(),
            'pending' => $borrows->where('status', 'pending')->count(),
            'approved' => $borrows->where('status', 'approved')->count(),
            'borrowed' => $borrows->where('status', 'borrowed')->count(),
            'returned' => $borrows->where('status', 'returned')->count(),
            'declined' => $borrows->where('status', 'declined')->count(),
        ];

        if ($request->format === 'csv') {
            return $this->generateCsv($borrows, 'borrowing_report');
        }

        $pdf = Pdf::loadView('admin.reports.borrowing-pdf', [
            'borrows' => $borrows,
            'stats' => $stats,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        return $pdf->download('borrowing_report_' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Generate hive usage report.
     */
    public function hiveReport(Request $request)
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'format' => ['required', 'in:pdf,csv'],
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $bookings = Booking::with(['table', 'user'])
            ->whereBetween('start_at', [$startDate, $endDate])
            ->orderByDesc('start_at')
            ->get();

        $stats = [
            'total_bookings' => $bookings->count(),
            'pending' => $bookings->where('status', 'pending')->count(),
            'confirmed' => $bookings->where('status', 'confirmed')->count(),
            'checked_in' => $bookings->where('status', 'checked_in')->count(),
            'completed' => $bookings->where('status', 'completed')->count(),
            'cancelled' => $bookings->where('status', 'cancelled')->count(),
        ];

        if ($request->format === 'csv') {
            return $this->generateBookingsCsv($bookings, 'hive_usage_report');
        }

        $pdf = Pdf::loadView('admin.reports.hive-pdf', [
            'bookings' => $bookings,
            'stats' => $stats,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        return $pdf->download('hive_usage_report_' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Generate overdue books report.
     */
    public function overdueReport(Request $request)
    {
        $format = $request->query('format', 'pdf');

        $overdue = BorrowRequest::with(['book', 'user'])
            ->where('status', 'borrowed')
            ->where('due_at', '<', now())
            ->orderBy('due_at')
            ->get();

        if ($format === 'csv') {
            return $this->generateCsv($overdue, 'overdue_books_report');
        }

        $pdf = Pdf::loadView('admin.reports.overdue-pdf', [
            'overdue' => $overdue,
        ]);

        return $pdf->download('overdue_books_' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Generate CSV from borrow requests.
     */
    private function generateCsv($borrows, string $filename): Response
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}_" . now()->format('Y-m-d') . ".csv\"",
        ];

        $callback = function () use ($borrows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'User', 'Email', 'Book', 'Status', 'Requested At', 'Due At', 'Returned At']);

            foreach ($borrows as $borrow) {
                fputcsv($file, [
                    $borrow->id,
                    $borrow->user->name ?? 'N/A',
                    $borrow->user->email ?? 'N/A',
                    $borrow->book->title ?? 'N/A',
                    $borrow->status,
                    $borrow->requested_at?->format('Y-m-d H:i'),
                    $borrow->due_at?->format('Y-m-d H:i'),
                    $borrow->returned_at?->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate CSV from bookings.
     */
    private function generateBookingsCsv($bookings, string $filename): Response
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}_" . now()->format('Y-m-d') . ".csv\"",
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'User', 'Table', 'Status', 'Start At', 'End At']);

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->id,
                    $booking->user->name ?? 'N/A',
                    $booking->table->name ?? 'N/A',
                    $booking->status,
                    $booking->start_at?->format('Y-m-d H:i'),
                    $booking->end_at?->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
