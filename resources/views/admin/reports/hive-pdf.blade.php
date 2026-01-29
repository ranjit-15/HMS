<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hive Usage Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { color: #f59e0b; margin: 0; }
        .header p { color: #64748b; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #e2e8f0; padding: 8px; text-align: left; }
        th { background: #f59e0b; color: white; }
        tr:nth-child(even) { background: #fefce8; }
        .footer { text-align: center; margin-top: 20px; color: #64748b; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🐝 Techspire HMS - Hive Usage Report</h1>
        <p>{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
        <p>Generated: {{ now()->format('M d, Y g:i A') }}</p>
    </div>

    <table style="width: auto; margin: 0 auto 20px;">
        <tr>
            <td style="padding: 10px; text-align: center; background: #fef3c7;"><strong>{{ $stats['total_bookings'] }}</strong><br><small>Total</small></td>
            <td style="padding: 10px; text-align: center; background: #dbeafe;"><strong>{{ $stats['confirmed'] }}</strong><br><small>Confirmed</small></td>
            <td style="padding: 10px; text-align: center; background: #d1fae5;"><strong>{{ $stats['completed'] }}</strong><br><small>Completed</small></td>
            <td style="padding: 10px; text-align: center; background: #fee2e2;"><strong>{{ $stats['cancelled'] }}</strong><br><small>Cancelled</small></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Table</th>
                <th>Status</th>
                <th>Start Time</th>
                <th>End Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $index => $booking)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $booking->user->name ?? 'N/A' }}</td>
                    <td>{{ $booking->table->name ?? 'N/A' }}</td>
                    <td>{{ ucfirst($booking->status) }}</td>
                    <td>{{ $booking->start_at?->format('M d, Y g:i A') }}</td>
                    <td>{{ $booking->end_at?->format('M d, Y g:i A') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Techspire College - HMS | techspire.edu.np</p>
    </div>
</body>
</html>
