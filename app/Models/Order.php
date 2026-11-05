<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->order_reference ??= 'FS-' . Str::upper((string) Str::uuid());
        });
    }

    protected $fillable = [
        'store_id',
        'order_reference',
        'rider_id',
        'customer_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'total_price',
        'delivery_fee',
        'items_description',
        'items',
        'status',
        'store_approval_status',
        'store_approved_at',
        'store_rejected_at',
        'store_rejection_reason',
        'notes',
        'recipient_code',
        'recipient_verified_at',
        'payment_status',
        'payment_method',
        'payment_reference',
        'refund_status',
        'refund_reference',
        'refund_message',
        'refund_attempted_at',
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

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadMessages()
    {
        return $this->messages()->whereNull('read_at');
    }

    protected function casts(): array
    {
        return [
            'recipient_verified_at' => 'datetime',
            'picked_up_at' => 'datetime',
            'delivery_fee' => 'decimal:2',
            'items' => 'array',
            'store_approved_at' => 'datetime',
            'store_rejected_at' => 'datetime',
            'refund_attempted_at' => 'datetime',
        ];
    }
}
