<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Booking;

class HiveTable extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tables';

    protected $fillable = [
        'name',
        'x',
        'y',
        'capacity',
        'is_active',
        'check_in_secret',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $table) {
            if (! $table->check_in_secret) {
                $table->check_in_secret = bin2hex(random_bytes(20));
            }
        });
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'table_id');
    }
}
