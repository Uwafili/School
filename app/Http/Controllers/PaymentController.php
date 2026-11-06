<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Order;
use App\Models\Notification as UserNotification;
use App\Models\Post;
use App\Models\OrderItem;
use App\Jobs\ProcessPaystackRefund;
use App\Services\OrderNotificationService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $normalizedCart = is_array($cart) && $this->cartItemsAreValid($cart)
            ? $this->authoritativeCart($cart)
            : null;
        if ($normalizedCart === null) {
            $request->session()->forget('cart');

            return redirect()->route('cart')->with('error', 'Your cart could not be loaded. Please add your items again.');
        }

        $request->session()->put('cart', $normalizedCart);
        $subtotal = collect($normalizedCart)->sum(fn ($item) => $item['price'] * $item['quantity']);
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
            'customer_phone' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9][0-9\s().-]{6,19}$/'],
        ]);

        $cart = $request->session()->get('cart', []);
        $normalizedCart = is_array($cart) && $this->cartItemsAreValid($cart)
            ? $this->authoritativeCart($cart)
            : null;
        if ($normalizedCart === null) {
            $request->session()->forget('cart');

            return redirect()->route('cart')->with('error', 'Your cart could not be loaded. Please add your items again.');
        }
        $cart = $normalizedCart;

        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        $cartStoreIds = collect($cart)->map(fn ($item) => $item['store_id'] ?? null);
        if ($cartStoreIds->contains(null)) {
            return redirect()->route('cart')->with('error', 'One or more items no longer belong to an approved store. Remove them and try again.');
        }

        $storeIds = $cartStoreIds->unique()->values();
        $approvedStoreIds = Store::whereIn('id', $storeIds)
            ->where('status', 'approved')
            ->pluck('id')
            ->map(fn ($id) => (int) $id);
        if ($approvedStoreIds->count() !== $storeIds->count()) {
            return redirect()->route('cart')->with('error', 'One or more stores are not accepting orders right now. Please review your cart.');
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
            'customer_phone' => $validated['customer_phone'],
            'payment_method' => 'paystack',
            'reference' => $reference,
            'cart' => $cart,
        ]);

        return redirect()->away($authorizationUrl);
    }

    private function cartItemsAreValid(array $cart): bool
    {
        if ($cart === []) {
            return true;
        }

        return Validator::make(['cart' => $cart], [
            'cart' => ['required', 'array'],
            'cart.*' => ['required', 'array'],
            'cart.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ])->passes();
    }

    private function authoritativeCart(array $cart): ?array
    {
        $postIds = collect(array_keys($cart))
            ->map(fn ($id) => filter_var($id, FILTER_VALIDATE_INT))
            ->filter(fn ($id) => $id !== false && $id > 0)
            ->unique()
            ->values();

        if ($postIds->count() !== count($cart)) {
            return null;
        }

        $posts = Post::whereIn('id', $postIds)->get()->keyBy('id');
        if ($posts->count() !== $postIds->count()) {
            return null;
        }

        $stores = Store::whereIn('user_id', $posts->pluck('user_id')->unique())
            ->where('status', 'approved')
            ->get()
            ->keyBy('user_id');
        $normalized = [];

        foreach ($cart as $id => $item) {
            $post = $posts->get((int) $id);
            $store = $post ? $stores->get($post->user_id) : null;
            if (! $post || ! $store) {
                return null;
            }

            $normalized[$post->id] = [
                'title' => $post->title,
                'price' => (float) $post->price,
                'quantity' => (int) $item['quantity'],
                'image' => $post->image,
                'category' => $post->category,
                'store_id' => $store->id,
            ];
        }

        return $normalized;
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

    public function paystackCallback(Request $request, OrderNotificationService $notifications)
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

        $this->createPaidOrders($payment, 'paystack', $notifications);
        $request->session()->forget(['cart', 'pending_payment']);

        return redirect()->route('dashboard')->with('success', 'Payment completed successfully.');
    }

    private function createPaidOrders(array $payment, string $method, OrderNotificationService $notifications): void
    {
        $cart = $payment['cart'] ?? [];
        if (empty($cart)) {
            return;
        }

        $paymentReference = (string) ($payment['reference'] ?? '');
        if ($paymentReference === '') {
            throw new \RuntimeException('Verified Paystack payment is missing its reference.');
        }

        $dispatches = DB::transaction(function () use ($cart, $payment, $method, $paymentReference, $notifications) {
            $existingOrders = Order::where('payment_reference', $paymentReference)->with('store')->get();
            if ($existingOrders->isNotEmpty()) {
                return $existingOrders->map(fn (Order $order) => [
                    'order' => $order,
                    'store_user_id' => $order->store->user_id,
                    'notification' => UserNotification::where('user_id', $order->store->user_id)
                        ->where('order_id', $order->id)
                        ->latest()
                        ->first(),
                ])->all();
            }

            $groups = collect($cart)->groupBy('store_id', true);
            $groupCount = $groups->count();
            $deliveryFeeCents = (int) round(((float) ($payment['delivery_fee'] ?? 0)) * 100);
            $baseDeliveryFeeCents = intdiv($deliveryFeeCents, $groupCount);
            $remainderDeliveryFeeCents = $deliveryFeeCents % $groupCount;
            $groupIndex = 0;
            $dispatches = [];

            foreach ($groups as $storeId => $items) {
                $store = Store::find($storeId);
                if (! $store) {
                    throw new \RuntimeException('A store in the verified order is no longer available.');
                }
                $storeAcceptingOrders = $store->status === 'approved';

                $itemsTotal = $items->sum(fn ($item) => $item['price'] * $item['quantity']);
                $groupDeliveryFeeCents = $baseDeliveryFeeCents + ($groupIndex < $remainderDeliveryFeeCents ? 1 : 0);
                $deliveryFee = $groupDeliveryFeeCents / 100;
                $groupIndex++;
                $description = $items->map(fn ($item) => $item['title'] . ' x' . $item['quantity'])->implode(', ');
                $itemsSnapshot = $items->map(fn ($item, $id) => [
                    'post_id' => (int) $id,
                    'title' => $item['title'],
                    'unit_price' => (float) $item['price'],
                    'quantity' => (int) $item['quantity'],
                    'line_total' => round((float) $item['price'] * (int) $item['quantity'], 2),
                ])->values()->all();

                $order = Order::create([
                    'store_id' => $store->id,
                    'customer_id' => Auth::id(),
                    'customer_name' => Auth::user()->name,
                    'customer_phone' => $payment['customer_phone'],
                    'customer_address' => $payment['delivery_method'] === 'pickup'
                        ? ($payment['pickup_station'] ?? 'Pickup station')
                        : ($payment['delivery_address'] ?? 'Location not provided'),
                    'total_price' => $itemsTotal + $deliveryFee,
                    'delivery_fee' => $deliveryFee,
                    'items_description' => $description,
                    'items' => $itemsSnapshot,
                    'status' => $storeAcceptingOrders ? 'pending' : 'rejected',
                    'store_approval_status' => $storeAcceptingOrders ? 'pending' : 'rejected',
                    'store_rejected_at' => $storeAcceptingOrders ? null : now(),
                    'store_rejection_reason' => $storeAcceptingOrders ? null : 'Store is no longer accepting orders.',
                    'payment_status' => $storeAcceptingOrders ? 'paid' : 'refund_pending',
                    'payment_method' => $method,
                    'payment_reference' => $paymentReference,
                    'refund_status' => $storeAcceptingOrders ? 'none' : 'pending',
                    'recipient_code' => str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT),
                ]);

                foreach ($itemsSnapshot as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'post_id' => $item['post_id'],
                        'product_name' => $item['title'],
                        'unit_price' => $item['unit_price'],
                        'quantity' => $item['quantity'],
                        'line_total' => $item['line_total'],
                    ]);
                }

                $notification = $notifications->sendToUser(
                    $store->user_id,
                    $order,
                    $storeAcceptingOrders ? 'New food order received' : 'Refund required for unavailable store',
                    $storeAcceptingOrders
                        ? "Paid order #{$order->id} needs your approval at {$store->stores}."
                        : "Order #{$order->id} was automatically rejected because {$store->stores} is not accepting orders.",
                    $storeAcceptingOrders ? 'order_created' : 'order_rejected'
                );
                if (! $storeAcceptingOrders) {
                    $notifications->sendToCustomer(
                        $order,
                        'Store could not accept your order',
                        "Order #{$order->id} was rejected because {$store->stores} is not accepting orders. A refund is being arranged.",
                        'order_rejected'
                    );
                }

                $dispatches[] = [
                    'order' => $order,
                    'store_user_id' => $store->user_id,
                    'notification' => $notification,
                ];
            }

            return $dispatches;
        });

        foreach ($dispatches as $dispatch) {
            if ($dispatch['order']->refund_status === 'pending') {
                ProcessPaystackRefund::dispatch($dispatch['order']->id);
            }
        }

    }


}
