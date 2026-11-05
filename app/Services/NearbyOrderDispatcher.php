<?php

namespace App\Services;

use App\Events\NearbyOrderAvailable;
use App\Models\Order;
use App\Models\Rider;
use App\Models\Store;

class NearbyOrderDispatcher
{
    public function dispatch(Order $order): void
    {
        if (config('broadcasting.default') !== 'pusher'
            || ! config('broadcasting.connections.pusher.key')
            || $order->status !== 'ready_for_pickup'
            || $order->payment_status !== 'paid'
            || $order->rider_id) {
            return;
        }

        $store = Store::find($order->store_id);
        if (! $store || $store->latitude === null || $store->longitude === null) {
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
            $distance = $this->distanceInKilometres(
                $latitude,
                $longitude,
                (float) $rider->latitude,
                (float) $rider->longitude
            );

            if ($distance > $radius) {
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
                'distance_km' => round($distance, 1),
            ]));
        }
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
