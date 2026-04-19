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
        'customer_name',
        'customer_phone',
        'customer_address',
        'total_price',
        'items_description',
        'status',
        'notes',
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
}
