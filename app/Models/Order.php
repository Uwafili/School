<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'rider_id',
        'customer_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'total_price',
        'delivery_fee',
        'items_description',
        'status',
        'notes',
        'recipient_code',
        'recipient_verified_at',
        'payment_status',
        'payment_method',
        'picked_up_at',
    ];

    /**
     * Get the store that owns this order
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the rider assigned to this order
     */
    public function rider()
    {
        return $this->belongsTo(Rider::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function deliveryBids()
    {
        return $this->hasMany(DeliveryBid::class);
    }

    protected function casts(): array
    {
        return [
            'recipient_verified_at' => 'datetime',
            'picked_up_at' => 'datetime',
            'delivery_fee' => 'decimal:2',
        ];
    }
}
