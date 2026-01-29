<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AdminNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'user_id',
        'title',
        'message',
        'type',
        'is_broadcast',
        'expires_at',
    ];

    protected $casts = [
        'is_broadcast' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public const TYPES = [
        'info' => 'Information',
        'warning' => 'Warning',
        'success' => 'Success',
        'urgent' => 'Urgent',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function readByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'admin_notification_reads')
            ->withPivot('read_at');
    }

    /**
     * Check if this notification has been read by a specific user
     */
    public function isReadBy(?User $user): bool
    {
        if (!$user)
            return false;
        return $this->readByUsers()->where('user_id', $user->id)->exists();
    }

    /**
     * Mark as read by a user
     */
    public function markAsReadBy(User $user): void
    {
        if (!$this->isReadBy($user)) {
            $this->readByUsers()->attach($user->id, ['read_at' => now()]);
        }
    }

    /**
     * Scope for notifications visible to a specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('is_broadcast', true)
                ->orWhere('user_id', $userId);
        })->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope for active notifications
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }
}
