<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rider extends Model
{
    /** @use HasFactory<\Database\Factories\RiderFactory> */
    use HasFactory;
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
        'latitude',
        'longitude',
        'location_updated_at',
    ];
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
}
