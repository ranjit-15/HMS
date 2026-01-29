<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'status',
        'requested_at',
        'due_at',
        'borrowed_at',
        'returned_at',
        'admin_confirmed_at',
        'notes',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'due_at' => 'datetime',
        'borrowed_at' => 'datetime',
        'returned_at' => 'datetime',
        'admin_confirmed_at' => 'datetime',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
