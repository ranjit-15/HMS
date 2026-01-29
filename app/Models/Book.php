<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'category',
        'cover_image',
        'description',
        'location',
        'copies_total',
        'copies_available',
        'published_at',
        'is_active',
    ];

    protected $casts = [
        'published_at' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Book categories available in the library
     */
    public const CATEGORIES = [
        // Narrowed to IT-related categories for the college
        'programming' => 'Programming',
        'web-development' => 'Web Development',
        'databases' => 'Databases',
        'networking' => 'Networking & Infrastructure',
        'cyber-security' => 'Cyber Security',
        'software-engineering' => 'Software Engineering',
        'data-science' => 'Data Science',
        'ai-ml' => 'AI & Machine Learning',
        'cloud-computing' => 'Cloud Computing',
        'operating-systems' => 'Operating Systems',
        'computer-architecture' => 'Computer Architecture',
        'reference' => 'Reference',
        'periodicals' => 'Periodicals & Journals',
    ];

    /**
     * Get the reviews for this book
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(BookReview::class);
    }

    /**
     * Get approved reviews for this book
     */
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(BookReview::class)->where('is_approved', true);
    }

    /**
     * Users who favorited this book
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    /**
     * Get the borrow requests for this book
     */
    public function borrowRequests(): HasMany
    {
        return $this->hasMany(BorrowRequest::class);
    }

    /**
     * Get average rating
     */
    public function getAverageRatingAttribute(): ?float
    {
        $avg = $this->approvedReviews()->avg('rating');
        return $avg ? round($avg, 1) : null;
    }

    /**
     * Check if a user has favorited this book
     */
    public function isFavoritedBy(?User $user): bool
    {
        if (!$user)
            return false;
        return $this->favoritedBy()->where('user_id', $user->id)->exists();
    }

    /**
     * Scope for available books
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)->where('copies_available', '>', 0);
    }

    /**
     * Scope for filtering by category
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('author', 'like', "%{$term}%")
                ->orWhere('isbn', 'like', "%{$term}%");
        });
    }
}
