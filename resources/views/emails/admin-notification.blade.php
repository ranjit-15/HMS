<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification from Techspire</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f3f4f6; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #8b0000 0%, #bf4040 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 10px 0 0; opacity: 0.9; }
        .content { padding: 30px; }
        .notification-box { padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .type-info { background: #dbeafe; border-left: 4px solid #3b82f6; }
        .type-success { background: #dcfce7; border-left: 4px solid #22c55e; }
        .type-warning { background: #fef3c7; border-left: 4px solid #f59e0b; }
        .type-urgent { background: #fee2e2; border-left: 4px solid #ef4444; }
        .notification-title { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
        .notification-message { white-space: pre-line; }
        .meta { margin-top: 20px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b; }
        .footer { background: #f1f5f9; padding: 20px; text-align: center; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📢 Techspire HMS</h1>
            <p>Important Notification</p>
        </div>
        
        <div class="content">
            <div class="notification-box type-{{ $notification->type }}">
                <div class="notification-title">
                    @if($notification->type === 'urgent')🚨 @elseif($notification->type === 'warning')⚠️ @elseif($notification->type === 'success')✅ @else ℹ️ @endif
                    {{ $notification->title }}
                </div>
                <div class="notification-message">{{ $notification->message }}</div>
            </div>

            <div class="meta">
                <p><strong>From:</strong> Administration</p>
                <p><strong>Sent:</strong> {{ $notification->created_at->format('F j, Y g:i A') }}</p>
                @if($notification->expires_at)
                    <p><strong>Valid Until:</strong> {{ $notification->expires_at->format('F j, Y') }}</p>
                @endif
            </div>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} Techspire College • New Baneshwor, Kathmandu</p>
            <p>This is an automated notification from the HMS system.</p>
        </div>
    </div>
</body>
</html>
