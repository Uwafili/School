<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Store;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'delivery_method' => 'required',
        ]);

        $deliveryFee = $request->delivery_method === 'pickup'
            ? 0
            : $this->deliveryFee(Auth::user()?->latitude, Auth::user()?->longitude);
        $subtotal = collect($request->session()->get('cart', []))->sum(fn ($item) => $item['price'] * $item['quantity']);

       
        $request->session()->put('pending_payment', [
            'amount' => $subtotal + $deliveryFee,
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'delivery_method' => $request->delivery_method,
            'delivery_address' => $request->delivery_address,
            'pickup_station' => $request->pickup_station,
            'payment_method' => $request->payment_method,
        ]);

        return redirect()->route('bank');
        // return back()->with('success','Payment Successful');
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

    public function bank(Request $request)
    {
        $payment = $request->session()->get('pending_payment', []);

        if (empty($payment)) {
            return redirect()->route('payment.checkout')->with('error', 'Start checkout before opening payment.');
        }

        return view('check.bank', compact('payment'));
    }

    public function confirmBankTransfer(Request $request)
    {
        $paymentMethod = $request->input('payment_method', 'bank');
        $payment = $request->session()->get('pending_payment');
        if (empty($payment)) {
            return redirect()->route('payment.checkout')->with('error', 'Your checkout session expired. Please try again.');
        }

        if ($paymentMethod === 'wallet') {
            $user = Auth::user();
            $amount = (float) $payment['amount'];

            if ((float) $user->wallet_balance < $amount) {
                return back()->withErrors(['wallet' => 'Your wallet balance is not enough for this order.'])->withInput();
            }

            User::where('id', $user->id)->update([
                'wallet_balance' => number_format((float) $user->wallet_balance - $amount, 2, '.', ''),
            ]);
            $request->session()->put('payment_confirmation', [
                'name' => $user->name,
                'email' => $user->email,
                'amount' => $amount,
                'method' => 'wallet',
                'confirmed_at' => now()->toDateTimeString(),
            ]);
            $this->createPaidOrders($request, $payment, 'wallet');
            $request->session()->forget(['cart', 'pending_payment']);

            return redirect()->route('dashboard')->with('success', 'Wallet payment completed successfully.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $request->session()->put('payment_confirmation', [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'amount' => $payment['amount'],
            'method' => 'bank transfer',
            'confirmed_at' => now()->toDateTimeString(),
        ]);
        $this->createPaidOrders($request, $payment, 'bank transfer');
        $request->session()->forget(['cart', 'pending_payment']);

        return redirect()->route('dashboard')->with('success', 'Payment confirmation received. We will verify your transfer shortly.');
    }

    private function createPaidOrders(Request $request, array $payment, string $method): void
    {
        $cart = $request->session()->get('cart', []);
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

            Order::create([
                'store_id' => $store->id,
                'customer_id' => Auth::id(),
                'customer_name' => Auth::user()->name,
                'customer_phone' => Auth::user()->phone ?? 'Not provided',
                'customer_address' => $payment['delivery_method'] === 'pickup'
                    ? ($payment['pickup_station'] ?? 'Pickup station')
                    : ($payment['delivery_address'] ?? 'Location not provided'),
                'total_price' => $itemsTotal + $deliveryFee,
                'items_description' => $description,
                'status' => 'pending',
                'payment_status' => 'paid',
                'payment_method' => $method,
                'recipient_code' => str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT),
            ]);
        }
    }


}


