<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Borrowing Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { color: #8b0000; margin: 0; }
        .header p { color: #64748b; margin: 5px 0; }
        .stats { display: flex; justify-content: space-around; margin-bottom: 20px; }
        .stat-box { text-align: center; padding: 10px; background: #f1f5f9; border-radius: 8px; }
        .stat-box .value { font-size: 24px; font-weight: bold; color: #8b0000; }
        .stat-box .label { color: #64748b; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #e2e8f0; padding: 8px; text-align: left; }
        th { background: #8b0000; color: white; }
        tr:nth-child(even) { background: #f8fafc; }
        .status-pending { color: #d97706; }
        .status-approved { color: #2563eb; }
        .status-borrowed { color: #7c3aed; }
        .status-returned { color: #059669; }
        .status-declined { color: #dc2626; }
        .footer { text-align: center; margin-top: 20px; color: #64748b; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Techspire HMS - Borrowing Report</h1>
        <p>{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
        <p>Generated: {{ now()->format('M d, Y g:i A') }}</p>
    </div>

    <table style="width: auto; margin: 0 auto 20px;">
        <tr>
            <td style="padding: 10px; text-align: center; background: #fef3c7;"><strong>{{ $stats['pending'] }}</strong><br><small>Pending</small></td>
            <td style="padding: 10px; text-align: center; background: #dbeafe;"><strong>{{ $stats['approved'] }}</strong><br><small>Approved</small></td>
            <td style="padding: 10px; text-align: center; background: #ede9fe;"><strong>{{ $stats['borrowed'] }}</strong><br><small>Borrowed</small></td>
            <td style="padding: 10px; text-align: center; background: #d1fae5;"><strong>{{ $stats['returned'] }}</strong><br><small>Returned</small></td>
            <td style="padding: 10px; text-align: center; background: #fee2e2;"><strong>{{ $stats['declined'] }}</strong><br><small>Declined</small></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Book</th>
                <th>Status</th>
                <th>Requested</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($borrows as $index => $borrow)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $borrow->user->name ?? 'N/A' }}</td>
                    <td>{{ $borrow->book->title ?? 'N/A' }}</td>
                    <td class="status-{{ $borrow->status }}">{{ ucfirst($borrow->status) }}</td>
                    <td>{{ $borrow->requested_at?->format('M d, Y') }}</td>
                    <td>{{ $borrow->due_at?->format('M d, Y') ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Techspire College - HMS | techspire.edu.np</p>
    </div>
</body>
</html>
