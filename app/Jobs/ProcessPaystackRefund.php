<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\OrderNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ProcessPaystackRefund implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 30;

    public function __construct(public int $orderId)
    {
    }

    public function handle(OrderNotificationService $notifications): void
    {
        $order = DB::transaction(function () {
            $order = Order::query()->lockForUpdate()->find($this->orderId);

            if (! $order
                || ! in_array($order->status, ['rejected', 'cancelled'], true)
                || $order->refund_status !== 'pending'
                || $order->refund_reference) {
                return null;
            }

            $order->update([
                'refund_status' => 'processing',
                'refund_attempted_at' => now(),
                'refund_message' => null,
            ]);

            return $order->fresh();
        });

        if (! $order) {
            return;
        }

        $secretKey = config('services.paystack.secret_key');
        if (! $secretKey || ! $order->payment_reference) {
            $this->markPending(
                $order,
                ! $secretKey
                    ? 'Paystack is not configured. Add the Paystack secret key and retry the refund.'
                    : 'This older payment has no Paystack reference. Contact support to arrange the refund.',
                $notifications
            );

            return;
        }

        try {
            $response = Http::withToken($secretKey)
                ->acceptJson()
                ->timeout(15)
                ->post('https://api.paystack.co/refund', [
                    'transaction' => $order->payment_reference,
                    'amount' => (string) (int) round(((float) $order->total_price) * 100),
                    'merchant_note' => "Refund for rejected FoodStore order #{$order->id}",
                    'customer_note' => "Refund for FoodStore order #{$order->id}",
                ]);
        } catch (\Illuminate\Http\Client\ConnectionException $exception) {
            report($exception);
            $this->markPending($order, 'Paystack could not be reached. Retry the refund shortly.', $notifications);

            return;
        }

        if (! $response->successful() || ! $response->json('status')) {
            $this->markPending(
                $order,
                Str::limit((string) $response->json('message', 'Paystack did not accept the refund request.'), 500),
                $notifications
            );

            return;
        }

        $refund = $response->json('data', []);
        $providerStatus = strtolower((string) data_get($refund, 'status', 'pending'));
        $refundReference = data_get($refund, 'id') ? (string) data_get($refund, 'id') : null;

        if (in_array($providerStatus, ['processed', 'success', 'successful'], true)) {
            $order->update([
                'refund_status' => 'refunded',
                'refund_reference' => $refundReference,
                'refund_message' => 'Paystack processed the refund.',
                'payment_status' => 'refunded',
            ]);
            $notifications->sendToCustomer(
                $order,
                'Order refund processed',
                "Paystack processed the refund for order #{$order->id}.",
                'order_refund'
            );

            return;
        }

        $order->update([
            'refund_status' => 'pending',
            'refund_reference' => $refundReference,
            'refund_message' => 'Paystack accepted the request but has not confirmed the refund. Check the Paystack dashboard before retrying.',
        ]);
        $notifications->sendToCustomer(
            $order,
            'Order refund is processing',
            "The refund for order #{$order->id} is still processing with Paystack.",
            'order_refund'
        );
    }

    public function failed(?\Throwable $exception): void
    {
        if ($exception) {
            report($exception);
        }

        $order = Order::find($this->orderId);
        if ($order && $order->refund_status === 'processing') {
            $order->update([
                'refund_status' => 'pending',
                'refund_message' => 'The refund could not be confirmed. Verify its status with Paystack before retrying.',
            ]);
        }
    }

    private function markPending(Order $order, string $message, OrderNotificationService $notifications): void
    {
        $order->update([
            'refund_status' => 'pending',
            'refund_message' => $message,
        ]);
        $notifications->sendToCustomer(
            $order,
            'Order refund needs attention',
            "The refund for order #{$order->id} is pending. {$message}",
            'order_refund'
        );
    }
}
