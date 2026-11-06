<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Rider;
use App\Models\Store;
use App\Models\User;
use App\Notifications\OrderUpdateNotification;
use App\Services\OrderWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiderManagementAndTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_broadcast_notifications_require_a_complete_pusher_configuration(): void
    {
        $notification = new OrderUpdateNotification('Order update', 'Order changed.', 'order_status_preparing', 1, 'FS-1');
        config([
            'broadcasting.default' => 'pusher',
            'broadcasting.enabled' => false,
            'broadcasting.connections.pusher.key' => 'public-key',
        ]);
        $this->assertSame(['database'], $notification->via(new User));

        config([
            'broadcasting.enabled' => true,
            'broadcasting.connections.pusher.app_id' => 'app-id',
            'broadcasting.connections.pusher.secret' => 'private-secret',
            'broadcasting.connections.pusher.options.cluster' => 'eu',
        ]);
        $this->assertSame(['database', 'broadcast'], $notification->via(new User));
    }

    public function test_admin_can_activate_riders_assign_ready_orders_and_view_delivery_operations(): void
    {
        $admin = User::factory()->create(['usertype' => 'admin']);
        $owner = User::factory()->create();
        $customer = User::factory()->create();
        $riderUser = User::factory()->create();
        $store = $this->makeStore($owner);
        $rider = $this->makeRider($riderUser, ['is_online' => true]);
        $order = $this->makeOrder($store, $customer);

        $this->actingAs($admin)
            ->get(route('riders'))
            ->assertOk()
            ->assertSee($rider->name)
            ->assertSee('Assign a ready order');

        $this->post(route('admin.riders.toggle-active', $rider))
            ->assertRedirect()
            ->assertSessionHas('success');
        $this->assertDatabaseHas('riders', ['id' => $rider->id, 'is_active' => 0, 'is_online' => 0]);

        $this->post(route('admin.riders.toggle-active', $rider))->assertRedirect();
        $rider->refresh()->update(['is_online' => true]);
        $this->assertDatabaseHas('riders', ['id' => $rider->id, 'is_active' => 1, 'is_online' => 1]);

        $this->post(route('order.assign'), [
            'order_id' => $order->id,
            'rider_id' => $rider->id,
        ])->assertRedirect(route('riders'))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'rider_id' => $rider->id,
            'status' => 'rider_assigned',
        ]);
        $this->assertTrue($customer->notifications()
            ->where('type', OrderUpdateNotification::class)
            ->where('data->type', 'order_status_rider_assigned')
            ->exists());
        $this->assertTrue($rider->notifications()
            ->where('type', OrderUpdateNotification::class)
            ->where('data->type', 'order_assigned')
            ->exists());
        $this->assertDatabaseHas('rider_assignments', [
            'order_id' => $order->id,
            'rider_id' => $rider->id,
            'status' => 'assigned',
        ]);
    }

    public function test_riders_can_only_view_and_update_their_own_assigned_orders(): void
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();
        $firstRider = $this->makeRider($firstUser);
        $secondRider = $this->makeRider($secondUser);
        $owner = User::factory()->create();
        $customer = User::factory()->create(['name' => 'Own Delivery Customer']);
        $otherCustomer = User::factory()->create(['name' => 'Other Delivery Customer']);
        $store = $this->makeStore($owner);
        $ownOrder = $this->makeOrder($store, $customer, $firstRider);
        $otherOrder = $this->makeOrder($store, $otherCustomer, $secondRider);

        $this->actingAs($firstUser)
            ->get(route('rider.assigned-orders'))
            ->assertOk()
            ->assertSee('Own Delivery Customer')
            ->assertDontSee('Other Delivery Customer');

        $this->post(route('rider.accept', $otherOrder))
            ->assertNotFound();
        $this->post(route('rider.accept', $ownOrder))
            ->assertRedirect();
        $this->assertNotNull($ownOrder->fresh()->rider_accepted_at);
        $this->assertDatabaseHas('rider_assignments', [
            'order_id' => $ownOrder->id,
            'rider_id' => $firstRider->id,
            'status' => 'accepted',
        ]);
        $this->assertTrue($owner->notifications()
            ->where('type', OrderUpdateNotification::class)
            ->where('data->type', 'order_assignment_accepted')
            ->exists());
    }

    public function test_customer_tracking_shows_status_history_and_assigned_rider_contact(): void
    {
        $owner = User::factory()->create();
        $customer = User::factory()->create();
        $riderUser = User::factory()->create(['name' => 'Delivery Rider']);
        $store = $this->makeStore($owner);
        $rider = $this->makeRider($riderUser, ['phone' => '08012345678']);
        $order = $this->makeOrder($store, $customer);
        $order = app(OrderWorkflowService::class)->transition($order, 'rider_assigned', [
            'rider_id' => $rider->id,
            'recipient_code' => '1234',
        ]);

        $this->actingAs($owner)
            ->get(route('chat.show', [$order, $customer->id]))
            ->assertOk()
            ->assertSee($customer->name);

        $this->post(route('chat.send', $order), [
            'receiver_id' => $customer->id,
            'message' => 'Your order is on the way.',
        ])->assertRedirect();

        $this->actingAs($customer)
            ->get(route('customer.orders.show', $order))
            ->assertOk()
            ->assertSee($order->order_reference)
            ->assertSee('Ready for pickup')
            ->assertSee('Delivery Rider')
            ->assertSee('08012345678')
            ->assertSee('Order placed')
            ->assertSee('deliveryTrackingMap')
            ->assertSee('MIN ETA')
            ->assertSee('Motorbike')
            ->assertSee('Delivery confirmation code');
        $this->assertDatabaseHas('messages', [
            'order_id' => $order->id,
            'sender_id' => $owner->id,
            'receiver_id' => $customer->id,
            'message' => 'Your order is on the way.',
        ]);
        $this->get(route('chat.show', [$order, $owner->id]))
            ->assertOk()
            ->assertSee('Your order is on the way.');

        $stranger = User::factory()->create();
        $this->actingAs($stranger)
            ->get(route('chat.show', [$order, $customer->id]))
            ->assertForbidden();
    }

    private function makeStore(User $owner): Store
    {
        return Store::create([
            'user_id' => $owner->id,
            'stores' => 'Delivery Kitchen',
            'owner' => $owner->name,
            'email' => $owner->email,
            'phone' => '08011112222',
            'address' => 'Store pickup location',
            'status' => 'approved',
        ]);
    }

    private function makeRider(User $user, array $attributes = []): Rider
    {
        return Rider::create(array_merge([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '08033334444',
            'license' => 'LIC-123',
            'vehicle_number' => 'ABC-123',
            'vehicle' => 'Motorbike',
            'status' => 'approved',
            'is_online' => true,
        ], $attributes));
    }

    private function makeOrder(Store $store, User $customer, ?Rider $rider = null): Order
    {
        return Order::create([
            'store_id' => $store->id,
            'rider_id' => $rider?->id,
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => '08022223333',
            'customer_address' => 'Customer delivery address',
            'total_price' => 2500,
            'delivery_fee' => 300,
            'items_description' => 'Rice x1',
            'status' => $rider ? 'rider_assigned' : 'ready_for_pickup',
            'store_approval_status' => 'approved',
            'payment_status' => 'paid',
        ]);
    }
}
