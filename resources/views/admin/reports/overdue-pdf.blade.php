<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Overdue Books Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { color: #dc2626; margin: 0; }
        .header p { color: #64748b; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #e2e8f0; padding: 8px; text-align: left; }
        th { background: #dc2626; color: white; }
        tr:nth-child(even) { background: #fef2f2; }
        .overdue-days { color: #dc2626; font-weight: bold; }
        .footer { text-align: center; margin-top: 20px; color: #64748b; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>⚠️ Overdue Books Report</h1>
        <p>Generated: {{ now()->format('M d, Y g:i A') }}</p>
        <p>Total Overdue: {{ $overdue->count() }}</p>
    </div>

    @if($overdue->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Book</th>
                    <th>Due Date</th>
                    <th>Days Overdue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($overdue as $index => $borrow)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $borrow->user->name ?? 'N/A' }}</td>
                        <td>{{ $borrow->user->email ?? 'N/A' }}</td>
                        <td>{{ $borrow->book->title ?? 'N/A' }}</td>
                        <td>{{ $borrow->due_at?->format('M d, Y') }}</td>
                        <td class="overdue-days">{{ $borrow->due_at?->diffInDays(now()) }} days</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; padding: 40px; color: #059669;">✅ No overdue books at this time!</p>
    @endif

    <div class="footer">
        <p>Techspire College - HMS | techspire.edu.np</p>
    </div>
</body>
</html>
