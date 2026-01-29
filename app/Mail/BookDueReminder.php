<?php

namespace App\Mail;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookDueReminder extends Mailable
{
    use Queueable, SerializesModels;

    public BorrowRequest $borrow;
    public int $daysRemaining;
    public bool $isOverdue;

    public function __construct(BorrowRequest $borrow, int $daysRemaining, bool $isOverdue = false)
    {
        $this->borrow = $borrow;
        $this->daysRemaining = $daysRemaining;
        $this->isOverdue = $isOverdue;
    }

    public function envelope(): Envelope
    {
        $subject = $this->isOverdue
            ? '⚠️ Overdue Book Return Reminder - ' . $this->borrow->book->title
            : '📚 Book Due Reminder - ' . $this->daysRemaining . ' Days Left';

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.book-due-reminder',
            with: [
                'borrow' => $this->borrow,
                'daysRemaining' => $this->daysRemaining,
                'isOverdue' => $this->isOverdue,
            ],
        );
    }
}
