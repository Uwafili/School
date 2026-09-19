<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'order_id',
        'customer_id',
        'rating',
        'review',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
