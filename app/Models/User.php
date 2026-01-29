<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'role',
        'avatar_path',
        'is_banned',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_banned' => 'boolean',
        ];
    }

    /**
     * Get all borrow requests for this user
     */
    public function borrowRequests(): HasMany
    {
        return $this->hasMany(BorrowRequest::class);
    }

    /**
     * Get all bookings for this user
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all favorites for this user
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get all book reviews by this user
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(BookReview::class);
    }

    /**
     * Get all notifications for this user
     */
    public function userNotifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get all waitlist entries for this user
     */
    public function waitlists(): HasMany
    {
        return $this->hasMany(Waitlist::class);
    }

    /**
     * Get favorite books
     */
    public function favoriteBooks(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'favorites')->withTimestamps();
    }
}
