<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'table_id',
        'status',
        'start_at',
        'end_at',
        'released_at',
        'notes',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(HiveTable::class, 'table_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
