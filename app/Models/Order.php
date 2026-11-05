<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Services\OrderNotificationService;

class Order extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->order_reference ??= 'FS-' . Str::upper((string) Str::uuid());
        });

        static::created(function (Order $order) {
            $order->statusHistories()->create(['status' => $order->status]);
            app(OrderNotificationService::class)->notifyCustomerOfStatusChange($order, $order->status);

            if ($order->rider_id) {
                $isDelivered = $order->status === 'delivered';
                $isReleased = in_array($order->status, ['cancelled', 'rejected'], true);

                $order->riderAssignments()->create([
                    'rider_id' => $order->rider_id,
                    'status' => $isDelivered ? 'completed' : ($isReleased ? 'released' : ($order->rider_accepted_at ? 'accepted' : 'assigned')),
                    'assigned_at' => $order->created_at ?? now(),
                    'acknowledged_at' => $order->rider_accepted_at,
                    'arrived_at' => $order->rider_arrived_at,
                    'picked_up_at' => $order->picked_up_at,
                    'completed_at' => $isDelivered ? ($order->updated_at ?? now()) : null,
                    'released_at' => $isReleased ? ($order->updated_at ?? now()) : null,
                ]);
            }
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
        'rider_accepted_at',
        'rider_arrived_at',
        'estimated_delivery_at',
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

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('id');
    }

    public function deliveryAttempts(): HasMany
    {
        return $this->hasMany(DeliveryAttempt::class)->orderByDesc('attempted_at');
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function riderAssignments(): HasMany
    {
        return $this->hasMany(RiderAssignment::class)->orderByDesc('assigned_at');
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
            'rider_accepted_at' => 'datetime',
            'rider_arrived_at' => 'datetime',
            'estimated_delivery_at' => 'datetime',
        ];
    }
}
