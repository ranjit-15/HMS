<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Due Reminder</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #8b0000 0%, #bf4040 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }

        .content {
            padding: 30px;
        }

        .alert-box {
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-warning {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
        }

        .alert-danger {
            background: #fee2e2;
            border: 1px solid #ef4444;
            color: #991b1b;
        }

        .book-info {
            background: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .book-title {
            font-size: 18px;
            font-weight: bold;
            color: #8b0000;
            margin-bottom: 10px;
        }

        .book-detail {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .book-detail:last-child {
            border-bottom: none;
        }

        .label {
            color: #64748b;
        }

        .value {
            font-weight: 600;
            color: #334155;
        }

        .cta-button {
            display: inline-block;
            background: #8b0000;
            color: white;
            padding: 14px 28px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 20px;
        }

        .footer {
            background: #f1f5f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>📚 Techspire College Library</h1>
            <p>Book Return Reminder</p>
        </div>

        <div class="content">
            <p>Dear {{ $borrow->user->name }},</p>

            @if($isOverdue)
                <div class="alert-box alert-danger">
                    <strong>⚠️ Overdue Notice</strong><br>
                    Your borrowed book is <strong>{{ abs($daysRemaining) }} day(s) overdue</strong>. Please return it as
                    soon as possible to avoid any penalties.
                </div>
            @else
                <div class="alert-box alert-warning">
                    <strong>📌 Reminder</strong><br>
                    Your borrowed book is due in <strong>{{ $daysRemaining }} day(s)</strong>. Please return it on time.
                </div>
            @endif

            <div class="book-info">
                <div class="book-title">{{ $borrow->book->title }}</div>
                <div class="book-detail">
                    <span class="label">Author</span>
                    <span class="value">{{ $borrow->book->author }}</span>
                </div>
                <div class="book-detail">
                    <span class="label">Due Date</span>
                    <span class="value">{{ $borrow->due_at?->format('F j, Y') ?? 'Not set' }}</span>
                </div>
                <div class="book-detail">
                    <span class="label">Borrowed On</span>
                    <span
                        class="value">{{ $borrow->borrowed_at?->format('F j, Y') ?? $borrow->created_at->format('F j, Y') }}</span>
                </div>
            </div>

            <p>Please visit the library during operating hours (7:00 AM - 6:00 PM) to return your book.</p>

            <p>Thank you for using the Techspire College Library!</p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} Techspire College • New Baneshwor, Kathmandu</p>
            <p>This is an automated message. Please do not reply.</p>
        </div>
    </div>
</body>

</html>