<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Rider;
use App\Models\Store;
use App\Services\OrderRiderAssignmentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class OrderAssignment extends Component
{
    #[Locked]
    public bool $adminMode = false;

    public string $orderId = '';

    public string $riderId = '';

    public function mount(bool $adminMode = false): void
    {
        $this->adminMode = $adminMode;
        $this->authorizeView();
    }

    public function assign(OrderRiderAssignmentService $assignments): void
    {
        $this->authorizeView();
        $validated = $this->validate([
            'orderId' => ['required', 'integer', 'exists:orders,id'],
            'riderId' => ['required', 'integer', 'exists:riders,id'],
        ]);
        $order = Order::findOrFail((int) $validated['orderId']);
        $assignments->assign($order, (int) $validated['riderId'], Auth::user());

        $this->reset(['orderId', 'riderId']);
        $this->dispatch('toast', type: 'success', message: 'Order assigned to rider. The rider has been notified.');
    }

    private function authorizeView(): void
    {
        if ($this->adminMode) {
            abort_unless(Auth::user()?->usertype === 'admin', 403);

            return;
        }

        abort_unless(Store::where('user_id', Auth::id())->exists(), 403);
    }

    public function render()
    {
        $this->authorizeView();
        $orders = Order::query()
            ->when(! $this->adminMode, fn ($query) => $query->whereIn('store_id', Store::where('user_id', Auth::id())->select('id')))
            ->where('status', 'ready_for_pickup')
            ->where('payment_status', 'paid')
            ->where('store_approval_status', 'approved')
            ->whereNull('rider_id')
            ->with('store')
            ->latest()
            ->get();
        $riders = Rider::query()
            ->where('status', 'approved')
            ->where('is_active', true)
            ->where('is_online', true)
            ->get()
            ->filter(function (Rider $rider) {
                if ($this->adminMode) {
                    return true;
                }

                $store = Store::where('user_id', Auth::id())->first();
                if (! $store?->latitude || ! $store?->longitude || ! $rider->latitude || ! $rider->longitude) {
                    return false;
                }

                return $this->distanceInKilometres(
                    (float) $store->latitude,
                    (float) $store->longitude,
                    (float) $rider->latitude,
                    (float) $rider->longitude
                ) <= 25;
            })
            ->values();

        return view('livewire.order-assignment', compact('orders', 'riders'));
    }

    private function distanceInKilometres(float $latitudeOne, float $longitudeOne, float $latitudeTwo, float $longitudeTwo): float
    {
        $earthRadius = 6371;
        $latitudeDelta = deg2rad($latitudeTwo - $latitudeOne);
        $longitudeDelta = deg2rad($longitudeTwo - $longitudeOne);
        $a = sin($latitudeDelta / 2) ** 2
            + cos(deg2rad($latitudeOne)) * cos(deg2rad($latitudeTwo)) * sin($longitudeDelta / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
