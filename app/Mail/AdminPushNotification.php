<?php

namespace App\Mail;

use App\Models\AdminNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminPushNotification extends Mailable
{
    use Queueable, SerializesModels;

    public AdminNotification $notification;

    public function __construct(AdminNotification $notification)
    {
        $this->notification = $notification;
    }

    public function envelope(): Envelope
    {
        $icon = match ($this->notification->type) {
            'warning' => '⚠️',
            'urgent' => '🚨',
            'success' => '✅',
            default => 'ℹ️',
        };

        return new Envelope(
            subject: $icon . ' ' . $this->notification->title . ' - Techspire HMS',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-notification',
            with: [
                'notification' => $this->notification,
            ],
        );
    }
}
