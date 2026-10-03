<?php

namespace Tests\Feature;

use App\Events\NearbyOrderAvailable;
use App\Models\Order;
use App\Models\Post;
use App\Models\Rider;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderDispatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_owner_is_notified_when_an_order_is_created(): void
    {
        $owner = User::factory()->create();
        $store = Store::create([
            'user_id' => $owner->id,
            'stores' => 'Test Kitchen',
            'owner' => 'Store Owner',
            'email' => 'owner@example.test',
            'phone' => '08012345678',
            'address' => 'Lagos',
            'status' => 'approved',
        ]);

        $this->actingAs($owner)->post(route('order.create'), [
            'customer_name' => 'Test Customer',
            'customer_phone' => '08087654321',
            'customer_address' => 'Customer Address',
            'total_price' => 2500,
            'delivery_fee' => 300,
            'items_description' => 'Rice x1',
        ])->assertRedirect(route('storedashboard'));

        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'order_id' => Order::where('store_id', $store->id)->value('id'),
            'title' => 'New food order received',
        ]);

        $this->get(route('storedashboard'))
            ->assertOk()
            ->assertSee('New food order received');
    }

    public function test_store_owner_is_notified_when_a_customer_pays_for_food(): void
    {
        $owner = User::factory()->create();
        $customer = User::factory()->create();
        $store = Store::create([
            'user_id' => $owner->id,
            'stores' => 'Test Kitchen',
            'owner' => 'Store Owner',
            'email' => 'owner@example.test',
            'phone' => '08012345678',
            'address' => 'Lagos',
            'status' => 'approved',
            'latitude' => 6.5244,
            'longitude' => 3.3792,
        ]);
        $riderUser = User::factory()->create();
        $rider = Rider::create([
            'user_id' => $riderUser->id,
            'name' => $riderUser->name,
            'email' => $riderUser->email,
            'phone' => '08011112222',
            'license' => 'LIC-1',
            'vehicle_number' => '123456789',
            'vehicle' => 'Motorbike',
            'status' => 'approved',
            'is_online' => true,
            'latitude' => 6.5300,
            'longitude' => 3.3800,
        ]);
        $farRiderUser = User::factory()->create();
        $farRider = Rider::create([
            'user_id' => $farRiderUser->id,
            'name' => $farRiderUser->name,
            'email' => $farRiderUser->email,
            'phone' => '08033334444',
            'license' => 'LIC-2',
            'vehicle_number' => '987654321',
            'vehicle' => 'Motorbike',
            'status' => 'approved',
            'is_online' => true,
            'latitude' => 7.1,
            'longitude' => 3.3792,
        ]);
        config([
            'broadcasting.default' => 'pusher',
            'broadcasting.connections.pusher.key' => 'test-key',
        ]);
        Event::fake();

        $this->actingAs($customer)->withSession([
            'cart' => [[
                'store_id' => $store->id,
                'title' => 'Jollof Rice',
                'price' => 2400,
                'quantity' => 1,
            ]],
            'pending_payment' => [
                'amount' => 2900,
                'delivery_fee' => 500,
                'delivery_method' => 'delivery',
                'delivery_address' => 'Customer Address',
            ],
        ])->post(route('bank.confirm'), [
            'payment_method' => 'bank',
            'name' => $customer->name,
            'email' => $customer->email,
        ])->assertRedirect(route('dashboard'));

        $order = Order::where('store_id', $store->id)->firstOrFail();
        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'order_id' => $order->id,
            'title' => 'New food order received',
        ]);
        Event::assertDispatched(NearbyOrderAvailable::class, fn ($event) =>
            $event->riderId === $rider->id
            && $event->broadcastOn()[0]->name === 'private-riders.' . $rider->id
        );
        Event::assertNotDispatched(NearbyOrderAvailable::class, fn ($event) => $event->riderId === $farRider->id);

        $this->actingAs($owner)->get(route('storedashboard'))
            ->assertOk()
            ->assertSee('New food order received');
    }

    public function test_rider_sees_nearby_paid_orders_but_not_distant_unassigned_orders(): void
    {
        $riderUser = User::factory()->create();
        $customer = User::factory()->create();
        $storeOwner = User::factory()->create();
        $store = Store::create([
            'user_id' => $storeOwner->id,
            'stores' => 'Test Kitchen',
            'owner' => 'Store Owner',
            'email' => 'owner@example.test',
            'phone' => '08012345678',
            'address' => 'Lagos',
            'status' => 'approved',
            'latitude' => 6.5244,
            'longitude' => 3.3792,
        ]);
        $rider = Rider::create([
            'user_id' => $riderUser->id,
            'name' => $riderUser->name,
            'email' => $riderUser->email,
            'phone' => '08011112222',
            'license' => 'LIC-1',
            'vehicle_number' => 'ABC-123',
            'vehicle' => 'Motorbike',
            'status' => 'approved',
            'is_online' => true,
            'latitude' => 6.5244,
            'longitude' => 3.3792,
        ]);

        $assignedOrder = $this->makeOrder($store, $customer, $rider, 'Assigned items');
        $nearbyOrder = $this->makeOrder($store, $customer, null, 'Nearby paid items');
        $farStore = Store::create([
            'user_id' => $storeOwner->id,
            'stores' => 'Distant Kitchen',
            'owner' => 'Store Owner',
            'email' => 'far-owner@example.test',
            'phone' => '08012345678',
            'address' => 'Distant address',
            'status' => 'approved',
            'latitude' => 7.1,
            'longitude' => 3.3792,
        ]);
        $farOrder = $this->makeOrder($farStore, $customer, null, 'Distant paid items');

        $this->actingAs($riderUser)
            ->get(route('rider.dashboard'))
            ->assertOk()
            ->assertSee('Order #' . $assignedOrder->id)
            ->assertSee('Order #' . $nearbyOrder->id)
            ->assertDontSee('Order #' . $farOrder->id);

        $this->get(route('rider.assigned-orders'))
            ->assertOk()
            ->assertSee('Order #' . $nearbyOrder->id)
            ->assertDontSee('Order #' . $farOrder->id);

        $this->post(route('order.accept', $nearbyOrder->id))->assertNotFound();
        $this->from(route('rider.dashboard'))->post(route('order.bid', $nearbyOrder->id), ['amount' => 300])
            ->assertRedirect(route('rider.dashboard'));
        $this->assertDatabaseHas('delivery_bids', ['order_id' => $nearbyOrder->id, 'rider_id' => $rider->id]);
        $this->from(route('rider.dashboard'))->post(route('order.bid', $farOrder->id), ['amount' => 300])
            ->assertSessionHas('error');
        $this->assertDatabaseMissing('delivery_bids', ['order_id' => $farOrder->id, 'rider_id' => $rider->id]);
    }

    public function test_food_selection_pages_show_eight_products_per_page(): void
    {
        $user = User::factory()->create();
        foreach (range(1, 9) as $number) {
            Post::create([
                'user_id' => $user->id,
                'title' => 'Pizza item ' . $number,
                'description' => 'Test food',
                'price' => '1000',
                'category' => 'pizza',
            ]);
        }

        $this->actingAs($user)
            ->get(route('food.pizza'))
            ->assertOk()
            ->assertSee('Pizza item 1')
            ->assertDontSee('Pizza item 9');
    }

    public function test_registration_saves_selected_store_and_rider_coordinates(): void
    {
        $storeOwner = User::factory()->create();
        $this->actingAs($storeOwner)->post(route('store'), [
            'stores' => 'Mapped Kitchen',
            'owner' => 'Store Owner',
            'email' => 'mapped-store@example.test',
            'phone' => '08012345678',
            'address' => 'Lagos',
            'latitude' => 6.5244,
            'longitude' => 3.3792,
        ])->assertRedirect(route('store.info'));

        $store = Store::where('user_id', $storeOwner->id)->firstOrFail();
        $this->assertEquals(6.5244, (float) $store->latitude);
        $this->assertEquals(3.3792, (float) $store->longitude);

        $riderUser = User::factory()->create();
        $this->actingAs($riderUser)->post(route('rider.store'), [
            'name' => 'Test Rider',
            'email' => 'mapped-rider@example.test',
            'phone' => '08087654321',
            'license' => 'LIC-123',
            'vehicle_number' => '123456789',
            'vehicle' => 'Motorbike',
            'latitude' => 6.5300,
            'longitude' => 3.3800,
        ])->assertRedirect(route('rider.create'));

        $rider = Rider::where('user_id', $riderUser->id)->firstOrFail();
        $this->assertEquals(6.5300, (float) $rider->latitude);
        $this->assertEquals(3.3800, (float) $rider->longitude);
    }

    private function makeOrder(Store $store, User $customer, ?Rider $rider, string $items): Order
    {
        return Order::create([
            'store_id' => $store->id,
            'rider_id' => $rider?->id,
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => '08022223333',
            'customer_address' => 'Customer Address',
            'total_price' => 2500,
            'delivery_fee' => 300,
            'items_description' => $items,
            'status' => $rider ? 'assigned' : 'pending',
            'payment_status' => 'paid',
        ]);
    }
}