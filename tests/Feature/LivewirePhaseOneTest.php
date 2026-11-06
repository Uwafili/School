<?php

namespace Tests\Feature;

use App\Livewire\CartManager;
use App\Livewire\CustomerOrderHistory;
use App\Livewire\OrderAssignment;
use App\Livewire\OrderChat;
use App\Livewire\OrderTrackingProgress;
use App\Livewire\ProductCatalog;
use App\Livewire\RiderDeliveryActions;
use App\Livewire\StoreApprovalList;
use App\Livewire\StoreOrderActions;
use App\Models\Order;
use App\Models\Post;
use App\Models\Rider;
use App\Models\RiderAssignment;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LivewirePhaseOneTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_updates_are_reactive_and_persist_to_the_session(): void
    {
        $customer = User::factory()->create();
        $seller = User::factory()->create();
        $post = Post::create([
            'user_id' => $seller->id,
            'title' => 'Fresh Jollof',
            'description' => 'A test dish',
            'price' => 1500,
            'category' => 'pizza',
        ]);

        $this->actingAs($customer);

        Livewire::test(CartManager::class)
            ->call('add', $post->id)
            ->assertSet("cart.{$post->id}.quantity", 1)
            ->call('increase', $post->id)
            ->assertSet("cart.{$post->id}.quantity", 2)
            ->call('decrease', $post->id)
            ->assertSet("cart.{$post->id}.quantity", 1)
            ->call('remove', $post->id)
            ->assertSet('cart', []);

        $this->assertSame([], session('cart'));
    }

    public function test_order_chat_sends_reactively_and_keeps_participant_authorization(): void
    {
        $owner = User::factory()->create();
        $customer = User::factory()->create();
        $otherUser = User::factory()->create();
        $store = Store::create([
            'user_id' => $owner->id,
            'stores' => 'Chat Kitchen',
            'owner' => $owner->name,
            'email' => $owner->email,
            'phone' => '08012345678',
            'address' => 'Lagos',
            'status' => 'approved',
        ]);
        $order = $this->makeOrder($store, $customer);

        $this->actingAs($owner);

        Livewire::test(OrderChat::class, ['order' => $order, 'contact' => $customer])
            ->set('message', 'Your order is being prepared.')
            ->call('send')
            ->assertHasNoErrors()
            ->assertSet('message', '')
            ->assertSee('Your order is being prepared.');

        $this->assertDatabaseHas('messages', [
            'order_id' => $order->id,
            'sender_id' => $owner->id,
            'receiver_id' => $customer->id,
            'message' => 'Your order is being prepared.',
        ]);
        $this->assertTrue($customer->notifications()->where('data->type', 'store_owner_message')->exists());

        $this->actingAs($otherUser)
            ->get(route('chat.show', [$order, $customer->id]))
            ->assertForbidden();
    }

    public function test_store_approval_actions_update_the_directory_without_a_page_redirect(): void
    {
        $admin = User::factory()->create(['usertype' => 'admin']);
        $owner = User::factory()->create();
        $store = Store::create([
            'user_id' => $owner->id,
            'stores' => 'Reactive Store',
            'owner' => $owner->name,
            'email' => $owner->email,
            'phone' => '08012345678',
            'address' => 'Abuja',
            'status' => 'pending',
        ]);

        $this->actingAs($admin);

        Livewire::test(StoreApprovalList::class)
            ->call('approve', $store->id)
            ->assertSee('Approved')
            ->call('reject', $store->id)
            ->assertSee('Rejected');

        $this->assertDatabaseHas('stores', ['id' => $store->id, 'status' => 'rejected']);
        $this->assertDatabaseHas('approval_notifications', [
            'user_id' => $owner->id,
            'role' => 'store',
            'status' => 'rejected',
        ]);
    }

    public function test_product_catalog_supports_reactive_create_edit_and_delete(): void
    {
        $admin = User::factory()->create(['usertype' => 'admin']);
        $post = Post::create([
            'user_id' => $admin->id,
            'title' => 'Original product',
            'description' => 'Original description',
            'price' => 1000,
            'category' => 'pizza',
        ]);

        $this->actingAs($admin);

        Livewire::test(ProductCatalog::class)
            ->set('title', 'New product')
            ->set('description', 'New description')
            ->set('price', '2200')
            ->set('category', 'burger')
            ->call('save')
            ->assertHasNoErrors();

        $created = Post::where('title', 'New product')->firstOrFail();
        $this->assertSame($admin->id, $created->user_id);

        Livewire::test(ProductCatalog::class)
            ->call('edit', $post->id)
            ->set('title', 'Updated product')
            ->call('save')
            ->assertHasNoErrors();
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Updated product']);

        Livewire::test(ProductCatalog::class)
            ->call('delete', $post->id);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_store_order_actions_and_rider_assignment_update_without_a_redirect(): void
    {
        $owner = User::factory()->create();
        $customer = User::factory()->create();
        $riderUser = User::factory()->create();
        $store = Store::create([
            'user_id' => $owner->id,
            'stores' => 'Reactive Kitchen',
            'owner' => $owner->name,
            'email' => $owner->email,
            'phone' => '08012345678',
            'address' => 'Lagos',
            'status' => 'approved',
            'latitude' => 6.5244,
            'longitude' => 3.3792,
        ]);
        $order = $this->makeOrder($store, $customer);
        $order->update(['payment_status' => 'paid']);
        $rider = Rider::create([
            'user_id' => $riderUser->id,
            'name' => $riderUser->name,
            'email' => $riderUser->email,
            'phone' => '08033334444',
            'license' => 'LIC-123',
            'vehicle_number' => 'ABC-123',
            'vehicle' => 'Motorbike',
            'status' => 'approved',
            'is_active' => true,
            'is_online' => true,
            'latitude' => 6.5244,
            'longitude' => 3.3792,
        ]);

        $this->actingAs($owner);

        Livewire::test(StoreOrderActions::class, ['order' => $order])
            ->call('approve')
            ->assertSee('Approved')
            ->call('startPreparing')
            ->call('markReady')
            ->assertSee('Ready for pickup');

        Livewire::test(OrderAssignment::class)
            ->set('orderId', (string) $order->id)
            ->set('riderId', (string) $rider->id)
            ->call('assign')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'rider_assigned',
            'rider_id' => $rider->id,
        ]);
        $this->assertTrue($rider->notifications()->where('data->type', 'order_assigned')->exists());
    }

    public function test_rider_delivery_actions_enforce_order_ownership_and_complete_a_delivery(): void
    {
        $owner = User::factory()->create();
        $customer = User::factory()->create();
        $riderUser = User::factory()->create();
        $otherRiderUser = User::factory()->create();
        $store = Store::create([
            'user_id' => $owner->id,
            'stores' => 'Delivery Kitchen',
            'owner' => $owner->name,
            'email' => $owner->email,
            'phone' => '08012345678',
            'address' => 'Lagos',
            'status' => 'approved',
        ]);
        $rider = $this->makeRider($riderUser);
        $order = $this->makeOrder($store, $customer, $rider);
        $order->update([
            'status' => 'rider_assigned',
            'store_approval_status' => 'approved',
            'payment_status' => 'paid',
            'recipient_code' => '1234',
        ]);
        RiderAssignment::create([
            'order_id' => $order->id,
            'rider_id' => $rider->id,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        $this->actingAs($riderUser);
        Livewire::test(RiderDeliveryActions::class, ['order' => $order])
            ->call('acceptAssignment')
            ->call('confirmArrival')
            ->call('confirmPickup')
            ->assertSee('Picked up');

        Livewire::test(RiderDeliveryActions::class, ['order' => $order])
            ->call('markOutForDelivery')
            ->set('recipientCode', '1234')
            ->call('confirmDelivery')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'delivered']);
        $this->assertTrue($customer->notifications()->where('data->type', 'order_status_delivered')->exists());
        $this->assertDatabaseHas('rider_assignments', [
            'order_id' => $order->id,
            'rider_id' => $rider->id,
            'status' => 'completed',
        ]);

        $this->actingAs($otherRiderUser);
        Livewire::test(RiderDeliveryActions::class, ['order' => $order])->assertNotFound();
    }

    public function test_rider_can_report_failed_delivery_with_a_valid_reason(): void
    {
        $owner = User::factory()->create();
        $customer = User::factory()->create();
        $riderUser = User::factory()->create();
        $store = Store::create([
            'user_id' => $owner->id,
            'stores' => 'Delivery Kitchen',
            'owner' => $owner->name,
            'email' => $owner->email,
            'phone' => '08012345678',
            'address' => 'Lagos',
            'status' => 'approved',
        ]);
        $rider = $this->makeRider($riderUser);
        $order = $this->makeOrder($store, $customer, $rider);
        $order->update([
            'status' => 'out_for_delivery',
            'store_approval_status' => 'approved',
            'payment_status' => 'paid',
            'picked_up_at' => now(),
        ]);

        $this->actingAs($riderUser);
        Livewire::test(RiderDeliveryActions::class, ['order' => $order])
            ->set('failureReason', 'Customer was unavailable.')
            ->call('reportFailedDelivery')
            ->assertSet('failureReason', '')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('delivery_attempts', [
            'order_id' => $order->id,
            'rider_id' => $rider->id,
            'reason' => 'Customer was unavailable.',
        ]);
        $this->assertTrue($customer->notifications()->where('data->type', 'delivery_attempt_failed')->exists());
    }

    public function test_customer_order_history_and_tracking_refresh_reactively_and_enforce_ownership(): void
    {
        $owner = User::factory()->create();
        $customer = User::factory()->create();
        $otherCustomer = User::factory()->create();
        $store = Store::create([
            'user_id' => $owner->id,
            'stores' => 'Tracking Kitchen',
            'owner' => $owner->name,
            'email' => $owner->email,
            'phone' => '08012345678',
            'address' => 'Lagos',
            'status' => 'approved',
        ]);
        $order = $this->makeOrder($store, $customer);

        $this->actingAs($customer);
        $history = Livewire::test(CustomerOrderHistory::class)
            ->assertSee('Pending')
            ->assertDontSee($otherCustomer->name);
        $tracking = Livewire::test(OrderTrackingProgress::class, ['order' => $order])
            ->assertSee('Order placed')
            ->assertSee('Delivery progress');

        $order->update(['status' => 'approved', 'store_approval_status' => 'approved']);
        $history->dispatch('order-status-updated', orderId: $order->id)->assertSee('Approved');
        $tracking->dispatch('order-status-updated', orderId: $order->id)->assertSee('approved');

        $this->actingAs($otherCustomer);
        Livewire::test(OrderTrackingProgress::class, ['order' => $order])->assertForbidden();
    }

    private function makeRider(User $user): Rider
    {
        return Rider::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '08033334444',
            'license' => 'LIC-123',
            'vehicle_number' => 'ABC-123',
            'vehicle' => 'Motorbike',
            'status' => 'approved',
            'is_active' => true,
            'is_online' => true,
        ]);
    }

    private function makeOrder(Store $store, User $customer, ?Rider $rider = null): Order
    {
        return Order::create([
            'store_id' => $store->id,
            'customer_id' => $customer->id,
            'rider_id' => $rider?->id,
            'customer_name' => $customer->name,
            'customer_phone' => '08022223333',
            'customer_address' => 'Customer delivery address',
            'total_price' => 2500,
            'delivery_fee' => 300,
            'items_description' => 'Rice x1',
            'status' => 'pending',
            'store_approval_status' => 'pending',
            'payment_status' => 'unpaid',
        ]);
    }
}
