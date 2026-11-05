<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Rider extends Model
{
    /** @use HasFactory<\Database\Factories\RiderFactory> */
    use HasFactory, Notifiable;
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'license',
        'vehicle_number',
        'vehicle',
        'image',
        'status',
        'is_online',
        'is_active',
        'latitude',
        'longitude',
        'location_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'is_online' => 'boolean',
            'is_active' => 'boolean',
            'location_updated_at' => 'datetime',
        ];
    }
      public function user()
            {
                return $this->belongsTo(User::class);
            }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function deliveryBids(): HasMany
    {
        return $this->hasMany(DeliveryBid::class);
    }

    public function deliveryAttempts(): HasMany
    {
        return $this->hasMany(DeliveryAttempt::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(RiderAssignment::class);
    }
}
