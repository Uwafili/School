<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Order;
use App\Models\Rider;
use App\Models\Notification as UserNotification;
use App\Events\StoreOrderCreated;
use App\Events\NearbyOrderAvailable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        $deliveryFee = $this->deliveryFee(Auth::user()?->latitude, Auth::user()?->longitude);

        return view('check.checkout', [
            'amount' => $subtotal + $deliveryFee,
            'subtotal' => $subtotal,
            'deliveryFee' => $deliveryFee,
        ]);
    }

    public function pay(Request $request)
    {
        if ($request->input('payment_method') !== 'paystack') {
            return back()->with('error', 'Paystack is the only available payment method.');
        }

        $validated = $request->validate([
            'delivery_method' => ['required', 'in:location,pickup'],
            'delivery_address' => ['required_if:delivery_method,location', 'nullable', 'string', 'max:1000'],
            'pickup_station' => ['required_if:delivery_method,pickup', 'nullable', 'string', 'max:255'],
        ]);

        $cart = $request->session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        $secretKey = config('services.paystack.secret_key');
        if (!$secretKey) {
            return back()->withInput()->with('error', 'Paystack is not configured yet. Add your Paystack secret key and try again.');
        }

        $deliveryFee = $validated['delivery_method'] === 'pickup'
            ? 0
            : $this->deliveryFee(Auth::user()?->latitude, Auth::user()?->longitude);
        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        $amount = $subtotal + $deliveryFee;
        $reference = 'foodstore-' . Str::uuid();

        try {
            $response = Http::withToken($secretKey)
                ->acceptJson()
                ->timeout(15)
                ->post('https://api.paystack.co/transaction/initialize', [
                    'email' => Auth::user()->email,
                    'amount' => (string) (int) round($amount * 100),
                    'currency' => 'NGN',
                    'reference' => $reference,
                    'callback_url' => route('payment.paystack.callback'),
                    'metadata' => ['user_id' => Auth::id()],
                ]);
        } catch (ConnectionException) {
            return back()->withInput()->with('error', 'Paystack could not be reached. Please try again.');
        }

        $authorizationUrl = $response->json('data.authorization_url');
        if (!$response->successful()
            || !$response->json('status')
            || !is_string($authorizationUrl)
            || parse_url($authorizationUrl, PHP_URL_SCHEME) !== 'https'
            || parse_url($authorizationUrl, PHP_URL_HOST) !== 'checkout.paystack.com') {
            return back()->withInput()->with('error', 'Paystack could not start your payment. Please try again.');
        }

        $request->session()->put('pending_payment', [
            'amount' => $amount,
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'delivery_method' => $validated['delivery_method'],
            'delivery_address' => $validated['delivery_address'] ?? null,
            'pickup_station' => $validated['pickup_station'] ?? null,
            'payment_method' => 'paystack',
            'reference' => $reference,
            'cart' => $cart,
        ]);

        return redirect()->away($authorizationUrl);
    }

    private function deliveryFee(?float $latitude, ?float $longitude): float
    {
        if ($latitude === null || $longitude === null) {
            return 500;
        }

        $nearestDistance = Store::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['latitude', 'longitude'])
            ->map(fn ($store) => $this->distanceInKm($latitude, $longitude, (float) $store->latitude, (float) $store->longitude))
            ->min();

        if ($nearestDistance === null) {
            return 500;
        }

        return min(2500, 300 + ($nearestDistance * 120));
    }

    private function distanceInKm(float $latitudeOne, float $longitudeOne, float $latitudeTwo, float $longitudeTwo): float
    {
        $earthRadius = 6371;
        $latitudeDelta = deg2rad($latitudeTwo - $latitudeOne);
        $longitudeDelta = deg2rad($longitudeTwo - $longitudeOne);
        $a = sin($latitudeDelta / 2) ** 2 + cos(deg2rad($latitudeOne)) * cos(deg2rad($latitudeTwo)) * sin($longitudeDelta / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    public function paystackCallback(Request $request)
    {
        $payment = $request->session()->get('pending_payment', []);
        $reference = (string) $request->query('reference', '');

        if (empty($payment['reference']) || empty($payment['cart']) || !$reference || !hash_equals($payment['reference'], $reference)) {
            return redirect()->route('payment.checkout')->with('error', 'Payment session expired or the reference is invalid.');
        }

        $secretKey = config('services.paystack.secret_key');
        if (!$secretKey) {
            return redirect()->route('payment.checkout')->with('error', 'Paystack is not configured yet.');
        }

        try {
            $response = Http::withToken($secretKey)
                ->acceptJson()
                ->timeout(15)
                ->get('https://api.paystack.co/transaction/verify/' . rawurlencode($reference));
        } catch (ConnectionException) {
            return redirect()->route('payment.checkout')->with('error', 'We could not verify your Paystack payment. Please retry.');
        }

        $transaction = $response->json('data', []);
        $expectedAmount = (int) round(((float) $payment['amount']) * 100);
        $paidEmail = strtolower((string) data_get($transaction, 'customer.email'));
        $expectedEmail = strtolower((string) Auth::user()->email);

        if (!$response->successful()
            || !$response->json('status')
            || data_get($transaction, 'status') !== 'success'
            || data_get($transaction, 'reference') !== $reference
            || (int) data_get($transaction, 'amount') !== $expectedAmount
            || data_get($transaction, 'currency') !== 'NGN'
            || !$paidEmail
            || !hash_equals($expectedEmail, $paidEmail)) {
            return redirect()->route('payment.checkout')->with('error', 'Paystack did not confirm this payment. No order was charged.');
        }

        $this->createPaidOrders($request, $payment, 'paystack');
        $request->session()->forget(['cart', 'pending_payment']);

        return redirect()->route('dashboard')->with('success', 'Payment completed successfully.');
    }

    private function createPaidOrders(Request $request, array $payment, string $method): void
    {
        $cart = $payment['cart'] ?? [];
        if (empty($cart)) {
            return;
        }

        $fallbackStore = Store::where('status', 'approved')->first();
        $groups = collect($cart)->groupBy(fn ($item) => $item['store_id'] ?? $fallbackStore?->id);
        $groupCount = max(1, $groups->count());

        foreach ($groups as $storeId => $items) {
            $store = Store::find($storeId) ?? $fallbackStore;
            if (!$store) {
                continue;
            }

            $itemsTotal = $items->sum(fn ($item) => $item['price'] * $item['quantity']);
            $deliveryFee = (float) ($payment['delivery_fee'] ?? 0) / $groupCount;
            $description = $items->map(fn ($item) => $item['title'] . ' x' . $item['quantity'])->implode(', ');

            $order = Order::create([
                'store_id' => $store->id,
                'customer_id' => Auth::id(),
                'customer_name' => Auth::user()->name,
                'customer_phone' => Auth::user()->phone ?? 'Not provided',
                'customer_address' => $payment['delivery_method'] === 'pickup'
                    ? ($payment['pickup_station'] ?? 'Pickup station')
                    : ($payment['delivery_address'] ?? 'Location not provided'),
                'total_price' => $itemsTotal + $deliveryFee,
                'delivery_fee' => $deliveryFee,
                'items_description' => $description,
                'status' => 'pending',
                'payment_status' => 'paid',
                'payment_method' => $method,
                'recipient_code' => str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT),
            ]);

            $notification = UserNotification::create([
                'user_id' => $store->user_id,
                'order_id' => $order->id,
                'title' => 'New food order received',
                'message' => "Order #{$order->id} has been placed at {$store->stores}.",
                'type' => 'order_assigned',
            ]);

            if (config('broadcasting.default') === 'pusher' && config('broadcasting.connections.pusher.key')) {
                event(new StoreOrderCreated($store->user_id, [
                    'id' => $notification->id,
                    'order_id' => $order->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                ]));
                $this->broadcastNearbyOrder($order, $store);
            }
        }
    }

    private function broadcastNearbyOrder(Order $order, Store $store): void
    {
        if ($store->latitude === null || $store->longitude === null) {
            return;
        }

        $radius = (float) config('services.delivery.radius_km', 25);
        $latitude = (float) $store->latitude;
        $longitude = (float) $store->longitude;
        $latitudeRange = $radius / 111.045;
        $longitudeRange = $radius / max(111.045 * abs(cos(deg2rad($latitude))), 0.01);

        $riders = Rider::where('status', 'approved')
            ->where('is_online', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereBetween('latitude', [$latitude - $latitudeRange, $latitude + $latitudeRange])
            ->whereBetween('longitude', [$longitude - $longitudeRange, $longitude + $longitudeRange])
            ->get(['id', 'latitude', 'longitude']);

        foreach ($riders as $rider) {
            if ($this->distanceInKm($latitude, $longitude, (float) $rider->latitude, (float) $rider->longitude) > $radius) {
                continue;
            }

            event(new NearbyOrderAvailable($rider->id, [
                'id' => $order->id,
                'store_name' => $store->stores,
                'store_address' => $store->address,
                'customer_address' => $order->customer_address,
                'items_description' => $order->items_description,
                'total_price' => (float) $order->total_price,
                'delivery_fee' => (float) $order->delivery_fee,
                'distance_km' => round($this->distanceInKm($latitude, $longitude, (float) $rider->latitude, (float) $rider->longitude), 1),
            ]));
        }
    }


}


