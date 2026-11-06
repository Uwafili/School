<?php

namespace Tests\Feature;

use App\Events\NearbyOrderAvailable;
use App\Models\Order;
use App\Models\Post;
use App\Models\Rider;
use App\Models\Store;
use App\Models\User;
use App\Models\DeliveryBid;
use App\Notifications\OrderUpdateNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessPaystackRefund;
use Tests\TestCase;

class OrderDispatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_adding_food_shows_a_cart_notification(): void
    {
        $customer = User::factory()->create();
        $seller = User::factory()->create();
        $post = Post::create([
            'user_id' => $seller->id,
            'title' => 'Jollof Rice',
            'description' => 'A test dish',
            'price' => '1500',
            'category' => 'pizza',
        ]);

        $this->actingAs($customer)
            ->from(route('dashboard'))
            ->post(route('add.cart', $post->id))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('cart_added', 'Jollof Rice added to your cart.');

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Jollof Rice added to your cart.')
            ->assertSee('Cart, 1 items', false);
    }

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

        $order = Order::where('store_id', $store->id)->firstOrFail();
        $this->assertSame(0.0, (float) $order->total_price);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertDatabaseHas('order_notifications', [
            'user_id' => $owner->id,
            'order_id' => $order->id,
            'title' => 'New order draft created',
        ]);
        $this->assertTrue($owner->notifications()->where('data->order_id', $order->id)->exists());

        $this->get(route('storedashboard'))
            ->assertOk()
            ->assertSee('New order draft created');
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
        $post = Post::create([
            'user_id' => $owner->id,
            'title' => 'Jollof Rice',
            'description' => 'A test dish',
            'price' => '2400',
            'category' => 'pizza',
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
            'broadcasting.enabled' => true,
            'broadcasting.connections.pusher.key' => 'test-key',
            'services.paystack.secret_key' => 'sk_test_example',
        ]);
        Event::fake();
        Http::fake([
            'https://api.paystack.co/transaction/verify/paystack-test-reference' => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'reference' => 'paystack-test-reference',
                    'amount' => 290000,
                    'currency' => 'NGN',
                    'customer' => ['email' => $customer->email],
                ],
            ]),
        ]);

        $this->actingAs($customer)->withSession([
            'cart' => [$post->id => [
                'store_id' => $store->id,
                'title' => 'Changed cart item',
                'price' => 5000,
                'quantity' => 1,
            ]],
            'pending_payment' => [
                'amount' => 2900,
                'delivery_fee' => 500,
                'delivery_method' => 'delivery',
                'delivery_address' => 'Customer Address',
                'customer_phone' => '08012345678',
                'reference' => 'paystack-test-reference',
                'cart' => [$post->id => [
                    'store_id' => $store->id,
                    'title' => 'Jollof Rice',
                    'price' => 2400,
                    'quantity' => 1,
                ]],
            ],
        ])->get(route('payment.paystack.callback', ['reference' => 'paystack-test-reference']))
            ->assertRedirect(route('dashboard'));

        $order = Order::where('store_id', $store->id)->firstOrFail();
        $this->assertDatabaseHas('order_notifications', [
            'user_id' => $owner->id,
            'order_id' => $order->id,
            'title' => 'New food order received',
        ]);
        $this->assertSame('Jollof Rice x1', $order->items_description);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'post_id' => $post->id,
            'product_name' => 'Jollof Rice',
            'unit_price' => 2400,
            'quantity' => 1,
            'line_total' => 2400,
        ]);
        $this->assertSame('pending', $order->store_approval_status);
        $this->assertSame('paystack-test-reference', $order->payment_reference);
        Event::assertNotDispatched(NearbyOrderAvailable::class);

        $this->actingAs($customer)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Order history')
            ->assertSee('Store approval:');
        $this->actingAs($customer)->post(route('chat.send', $order), [
            'receiver_id' => $owner->id,
            'message' => 'Please include napkins.',
        ])->assertRedirect();
        $this->assertDatabaseHas('messages', [
            'order_id' => $order->id,
            'sender_id' => $customer->id,
            'receiver_id' => $owner->id,
            'message' => 'Please include napkins.',
        ]);
        $this->assertTrue($owner->notifications()
            ->where('type', OrderUpdateNotification::class)
            ->where('data->type', 'customer_message')
            ->exists());
        $this->actingAs($owner)->get(route('storedashboard'))
            ->assertOk()
            ->assertSee('Orders awaiting store approval')
            ->assertSee('Jollof Rice x1');

        $this->actingAs($owner)
            ->post(route('store.orders.approve', $order))
            ->assertRedirect();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'store_approval_status' => 'approved']);
        $this->actingAs($owner)
            ->post(route('store.orders.preparing', $order))
            ->assertRedirect();
        $this->post(route('store.orders.ready', $order))->assertRedirect();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'ready_for_pickup']);
        $this->get(route('order.view', $order->id))
            ->assertOk()
            ->assertSee('Payment Summary')
            ->assertSee('Store approval');
        Event::assertDispatched(NearbyOrderAvailable::class, fn ($event) =>
            $event->riderId === $rider->id
            && $event->broadcastOn()[0]->name === 'private-riders.' . $rider->id
        );
        Event::assertNotDispatched(NearbyOrderAvailable::class, fn ($event) => $event->riderId === $farRider->id);

        $this->post(route('order.assign'), [
            'order_id' => $order->id,
            'rider_id' => $rider->id,
        ])->assertRedirect(route('storedashboard'));
        $this->assertTrue($rider->notifications()
            ->where('type', OrderUpdateNotification::class)
            ->where('data->type', 'order_assigned')
            ->exists());
        $this->actingAs($riderUser)
            ->get(route('rider.notifications'))
            ->assertOk()
            ->assertSee('New order assigned')
            ->assertSee('Customer Address');

        $this->actingAs($riderUser)
            ->post(route('rider.accept', $order))
            ->assertRedirect();
        $this->post(route('rider.arrived', $order))->assertRedirect();
        $this->post(route('rider.pickup', $order))->assertRedirect();
        $this->post(route('rider.out-for-delivery', $order))->assertRedirect();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'out_for_delivery']);

        $order->refresh();
        $this->post(route('rider.deliver', $order), [
            'recipient_code' => $order->recipient_code,
        ])->assertRedirect();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'delivered']);
        $this->assertDatabaseHas('rider_assignments', [
            'order_id' => $order->id,
            'rider_id' => $rider->id,
            'status' => 'completed',
        ]);

        $this->actingAs($customer)
            ->get(route('customer.orders.show', $order))
            ->assertOk()
            ->assertSee('Delivered')
            ->assertSee($order->order_reference);

        $this->actingAs($owner)->get(route('storedashboard'))
            ->assertOk()
            ->assertSee('New food order received');
    }

    public function test_order_chat_supports_preapproval_messages_polling_and_unread_indicators(): void
    {
        $owner = User::factory()->create(['name' => 'Store Owner']);
        $customer = User::factory()->create(['name' => 'Order Customer']);
        $store = Store::create([
            'user_id' => $owner->id,
            'stores' => 'Chat Kitchen',
            'owner' => $owner->name,
            'email' => $owner->email,
            'phone' => '08012345678',
            'address' => 'Lagos',
            'status' => 'approved',
        ]);
        $order = $this->makeOrder($store, $customer, null, 'Rice x1');
        $order->update(['status' => 'pending', 'store_approval_status' => 'pending']);

        $this->actingAs($customer)->post(route('chat.send', $order), [
            'receiver_id' => $owner->id,
            'message' => 'Please include utensils.',
        ])->assertRedirect();
        $firstMessageId = \App\Models\Message::where('order_id', $order->id)->value('id');

        $this->actingAs($owner)->get(route('storedashboard'))
            ->assertOk()
            ->assertSee('1 new');
        $this->post(route('store.orders.preparing', $order))
            ->assertRedirect()
            ->assertSessionHasErrors('status');
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'pending']);
        $this->post(route('chat.send', $order), [
            'receiver_id' => $customer->id,
            'message' => 'Certainly, we will include them.',
        ])->assertRedirect();

        $this->actingAs($customer)->get(route('chat.show', [$order, $owner->id]))
            ->assertOk()
            ->assertSee('Store Owner')
            ->assertSee('Please include utensils.')
            ->assertSee('Certainly, we will include them.')
            ->assertSee('Unread');

        $this->get(route('chat.messages', [$order, $owner->id]) . '?after=' . $firstMessageId)
            ->assertOk()
            ->assertJsonPath('messages.0.message', 'Certainly, we will include them.');
        $this->assertDatabaseMissing('messages', [
            'order_id' => $order->id,
            'sender_id' => $owner->id,
            'receiver_id' => $customer->id,
            'read_at' => null,
        ]);
    }

    public function test_store_can_assign_only_ready_orders_and_accept_a_delivery_bid(): void
    {
        $owner = User::factory()->create();
        $customer = User::factory()->create();
        $store = Store::create([
            'user_id' => $owner->id,
            'stores' => 'Dispatch Kitchen',
            'owner' => $owner->name,
            'email' => $owner->email,
            'phone' => '08012345678',
            'address' => 'Lagos',
            'status' => 'approved',
        ]);
        $riderUser = User::factory()->create();
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
        ]);
        $order = $this->makeOrder($store, $customer, null, 'Manual assignment');

        $this->actingAs($owner)->post(route('order.assign'), [
            'order_id' => $order->id,
            'rider_id' => $rider->id,
            'delivery_fee' => 99999,
        ])->assertRedirect(route('storedashboard'));
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'rider_assigned',
            'rider_id' => $rider->id,
            'delivery_fee' => 300,
        ]);
        $this->assertDatabaseHas('rider_assignments', [
            'order_id' => $order->id,
            'rider_id' => $rider->id,
            'status' => 'assigned',
            'released_at' => null,
        ]);

        $bidOrder = $this->makeOrder($store, $customer, null, 'Bid assignment');
        $bid = DeliveryBid::create([
            'order_id' => $bidOrder->id,
            'rider_id' => $rider->id,
            'amount' => 350,
            'status' => 'pending',
        ]);

        $this->post(route('order.bid.accept', $bid->id))
            ->assertRedirect(route('storedashboard'));
        $this->assertDatabaseHas('orders', [
            'id' => $bidOrder->id,
            'status' => 'rider_assigned',
            'rider_id' => $rider->id,
            'delivery_fee' => 350,
        ]);
        $this->assertDatabaseHas('delivery_bids', ['id' => $bid->id, 'status' => 'accepted']);
    }

    public function test_checkout_initializes_paystack_with_the_server_calculated_amount(): void
    {
        $customer = User::factory()->create();
        $storeOwner = User::factory()->create();
        $store = Store::create([
            'user_id' => $storeOwner->id,
            'stores' => 'Test Kitchen',
            'owner' => 'Store Owner',
            'email' => $storeOwner->email,
            'phone' => '08012345678',
            'address' => 'Lagos',
            'status' => 'approved',
        ]);
        $post = Post::create([
            'user_id' => $storeOwner->id,
            'title' => 'Jollof Rice',
            'description' => 'A test dish',
            'price' => '2400',
            'category' => 'pizza',
        ]);
        config(['services.paystack.secret_key' => 'sk_test_example']);
        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.com/test-session'],
            ]),
        ]);

        $this->actingAs($customer)
            ->get(route('payment.checkout'))
            ->assertOk()
            ->assertSee('Paystack')
            ->assertDontSee('Flutterwave')
            ->assertDontSee('Cash on delivery');

        $this->actingAs($customer)->withSession([
            'cart' => [$post->id => [
                'store_id' => $store->id,
                'title' => 'Forged title',
                'price' => 1,
                'quantity' => 1,
            ]],
        ])->post(route('payment.pay'), [
            'amount' => 1,
            'payment_method' => 'paystack',
            'delivery_method' => 'pickup',
            'pickup_station' => 'ikeja',
            'customer_phone' => '08012345678',
        ])->assertRedirect('https://checkout.paystack.com/test-session');

        $this->assertSame(2400.0, session('pending_payment.amount'));
        $this->assertSame('Jollof Rice', session("pending_payment.cart.{$post->id}.title"));
        $this->assertSame(2400.0, session("pending_payment.cart.{$post->id}.price"));
        $this->assertSame('08012345678', session('pending_payment.customer_phone'));
        Http::assertSent(fn ($request) =>
            $request->url() === 'https://api.paystack.co/transaction/initialize'
            && $request['amount'] === '240000'
            && $request['currency'] === 'NGN'
        );
    }

    public function test_customer_dashboard_renders_the_food_hero_image(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Find something delicious today.')
            ->assertSee('aria-roledescription="carousel"', false)
            ->assertSee('background-image: linear-gradient', false);
    }

    public function test_customer_dashboard_paginates_popular_food_eight_at_a_time(): void
    {
        $customer = User::factory()->create();
        $seller = User::factory()->create();

        foreach (range(1, 9) as $number) {
            Post::create([
                'user_id' => $seller->id,
                'title' => 'Pagination dish ' . $number,
                'description' => 'A test dish',
                'price' => '1500',
                'category' => 'pizza',
            ]);
        }

        $this->actingAs($customer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Showing 1 to 8 of 9')
            ->assertSee('page=2#food-feed', false)
            ->assertViewHas('posts', fn ($posts) => $posts->count() === 8 && $posts->total() === 9);

        $this->get(route('dashboard', ['page' => 2]))
            ->assertOk()
            ->assertSee('Showing 9 to 9 of 9')
            ->assertViewHas('posts', fn ($posts) => $posts->count() === 1 && $posts->currentPage() === 2);
    }

    public function test_food_hero_slides_feature_products_from_the_top_approved_store(): void
    {
        $customer = User::factory()->create();
        $topOwner = User::factory()->create();
        $otherOwner = User::factory()->create();
        $topStore = Store::create([
            'user_id' => $topOwner->id,
            'stores' => 'Top Kitchen',
            'owner' => 'Top Owner',
            'email' => 'top-kitchen@example.test',
            'phone' => '08012345678',
            'address' => 'Lagos',
            'status' => 'approved',
        ]);
        Store::create([
            'user_id' => $otherOwner->id,
            'stores' => 'Other Kitchen',
            'owner' => 'Other Owner',
            'email' => 'other-kitchen@example.test',
            'phone' => '08012345679',
            'address' => 'Lagos',
            'status' => 'approved',
        ]);

        foreach (range(1, 3) as $number) {
            Post::create([
                'user_id' => $topOwner->id,
                'title' => 'Top dish ' . $number,
                'description' => 'Featured dish from the top kitchen',
                'price' => '2500',
                'category' => 'pizza',
                'image' => 'top-dish-' . $number . '.jpg',
            ]);
        }
        Post::create([
            'user_id' => $otherOwner->id,
            'title' => 'Other seller dish',
            'description' => 'Dish from another seller',
            'price' => '1500',
            'category' => 'burger',
        ]);

        $response = $this->actingAs($customer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Find something delicious today.')
            ->assertSee('Top dish 1')
            ->assertSee('Top dish 2')
            ->assertSee('Top dish 3')
            ->assertSee($topStore->stores)
            ->assertSee('top-dish-1.jpg');

        libxml_use_internal_errors(true);
        $document = new \DOMDocument();
        $document->loadHTML($response->getContent());
        libxml_clear_errors();
        $slides = (new \DOMXPath($document))->query('//*[@data-food-slide]');
        $slideContent = '';
        foreach ($slides as $slide) {
            $slideContent .= $slide->textContent;
        }

        $this->assertStringNotContainsString('Other seller dish', $slideContent);
    }

    public function test_checkout_rejects_removed_payment_methods(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)->from(route('payment.checkout'))->withSession([
            'cart' => [['store_id' => 1, 'title' => 'Meal', 'price' => 1000, 'quantity' => 1]],
        ])->post(route('payment.pay'), [
            'payment_method' => 'flutterwave',
            'delivery_method' => 'pickup',
            'pickup_station' => 'ikeja',
        ])->assertRedirect(route('payment.checkout'))
            ->assertSessionHas('error', 'Paystack is the only available payment method.');
    }

    public function test_unverified_paystack_callback_does_not_create_an_order(): void
    {
        $customer = User::factory()->create();
        config(['services.paystack.secret_key' => 'sk_test_example']);
        Http::fake([
            'https://api.paystack.co/transaction/verify/unverified-reference' => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'reference' => 'unverified-reference',
                    'amount' => 1,
                    'currency' => 'NGN',
                    'customer' => ['email' => $customer->email],
                ],
            ]),
        ]);

        $this->actingAs($customer)->withSession([
            'cart' => [['store_id' => 1, 'title' => 'Meal', 'price' => 1000, 'quantity' => 1]],
            'pending_payment' => [
                'amount' => 1000,
                'reference' => 'unverified-reference',
                'cart' => [['store_id' => 1, 'title' => 'Meal', 'price' => 1000, 'quantity' => 1]],
            ],
        ])->get(route('payment.paystack.callback', ['reference' => 'unverified-reference']))
            ->assertRedirect(route('payment.checkout'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('orders', 0);
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
        $approvalPendingOrder = $this->makeOrder($store, $customer, null, 'Awaiting store approval item');
        $approvalPendingOrder->update(['store_approval_status' => 'pending']);
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
            ->assertSee('Assigned items')
            ->assertSee('Order #' . $nearbyOrder->id)
            ->assertDontSee('Awaiting store approval item')
            ->assertDontSee('Distant paid items');

        $this->get(route('rider.assigned-orders'))
            ->assertOk()
            ->assertSee('Order #' . $nearbyOrder->id)
            ->assertDontSee('Order #' . $farOrder->id);

        $this->post(route('rider.pickup', $nearbyOrder->id))->assertNotFound();
        $this->from(route('rider.dashboard'))->post(route('order.bid', $nearbyOrder->id), ['amount' => 300])
            ->assertRedirect(route('rider.dashboard'));
        $this->assertDatabaseHas('delivery_bids', ['order_id' => $nearbyOrder->id, 'rider_id' => $rider->id]);
        $this->from(route('rider.dashboard'))->post(route('order.bid', $farOrder->id), ['amount' => 300])
            ->assertSessionHas('error');
        $this->assertDatabaseMissing('delivery_bids', ['order_id' => $farOrder->id, 'rider_id' => $rider->id]);
    }

    public function test_store_rejection_queues_refund_and_persists_reason(): void
    {
        Queue::fake();
        $owner = User::factory()->create();
        $customer = User::factory()->create();
        $store = Store::create([
            'user_id' => $owner->id,
            'stores' => 'Refund Kitchen',
            'owner' => $owner->name,
            'email' => $owner->email,
            'phone' => '08012345678',
            'address' => 'Lagos',
            'status' => 'approved',
        ]);
        $order = $this->makeOrder($store, $customer, null, 'Rice x1');
        $order->update([
            'status' => 'pending',
            'store_approval_status' => 'pending',
            'payment_reference' => 'paystack-refund-test',
        ]);

        $this->actingAs($owner)
            ->post(route('store.orders.reject', $order), ['reason' => 'Item unavailable'])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'rejected',
            'store_approval_status' => 'rejected',
            'store_rejection_reason' => 'Item unavailable',
            'payment_status' => 'refund_pending',
            'refund_status' => 'pending',
        ]);
        Queue::assertPushed(ProcessPaystackRefund::class, fn ($job) => $job->orderId === $order->id);
        $this->assertDatabaseHas('order_notifications', [
            'user_id' => $customer->id,
            'order_id' => $order->id,
            'type' => 'order_status_rejected',
        ]);
    }

    public function test_payment_to_a_store_that_stops_accepting_orders_is_rejected_and_refunded(): void
    {
        Queue::fake();
        $owner = User::factory()->create();
        $customer = User::factory()->create();
        $store = Store::create([
            'user_id' => $owner->id,
            'stores' => 'Unavailable Kitchen',
            'owner' => $owner->name,
            'email' => $owner->email,
            'phone' => '08012345678',
            'address' => 'Lagos',
            'status' => 'rejected',
        ]);
        $post = Post::create([
            'user_id' => $owner->id,
            'title' => 'Meal',
            'description' => 'A test dish',
            'price' => '1000',
            'category' => 'pizza',
        ]);
        config(['services.paystack.secret_key' => 'sk_test_example']);
        Http::fake([
            'https://api.paystack.co/transaction/verify/store-status-changed' => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'reference' => 'store-status-changed',
                    'amount' => 100000,
                    'currency' => 'NGN',
                    'customer' => ['email' => $customer->email],
                ],
            ]),
        ]);

        $this->actingAs($customer)->withSession([
            'pending_payment' => [
                'amount' => 1000,
                'delivery_fee' => 0,
                'delivery_method' => 'pickup',
                'customer_phone' => '08012345678',
                'reference' => 'store-status-changed',
                'cart' => [
                    $post->id => [
                        'store_id' => $store->id,
                        'title' => 'Meal',
                        'price' => 1000,
                        'quantity' => 1,
                    ],
                ],
            ],
        ])->get(route('payment.paystack.callback', ['reference' => 'store-status-changed']))
            ->assertRedirect(route('dashboard'));

        $order = Order::where('payment_reference', 'store-status-changed')->firstOrFail();
        $this->assertSame('rejected', $order->status);
        $this->assertSame('pending', $order->refund_status);
        Queue::assertPushed(ProcessPaystackRefund::class, fn ($job) => $job->orderId === $order->id);
    }

    public function test_customer_can_cancel_only_before_store_approval_and_gets_a_refund(): void
    {
        Queue::fake();
        $owner = User::factory()->create();
        $customer = User::factory()->create();
        $store = Store::create([
            'user_id' => $owner->id,
            'stores' => 'Customer Kitchen',
            'owner' => $owner->name,
            'email' => $owner->email,
            'phone' => '08012345678',
            'address' => 'Lagos',
            'status' => 'approved',
        ]);
        $order = $this->makeOrder($store, $customer, null, 'Rice x1');
        $order->update([
            'status' => 'pending',
            'store_approval_status' => 'pending',
            'payment_reference' => 'paystack-customer-cancel',
        ]);

        $this->actingAs($customer)
            ->post(route('customer.orders.cancel', $order))
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled',
            'payment_status' => 'refund_pending',
            'refund_status' => 'pending',
        ]);
        Queue::assertPushed(ProcessPaystackRefund::class, fn ($job) => $job->orderId === $order->id);
    }

    public function test_paystack_refund_job_marks_a_confirmed_refund_and_notifies_customer(): void
    {
        $customer = User::factory()->create();
        $owner = User::factory()->create();
        $store = Store::create([
            'user_id' => $owner->id,
            'stores' => 'Refunded Kitchen',
            'owner' => $owner->name,
            'email' => $owner->email,
            'phone' => '08012345678',
            'address' => 'Lagos',
            'status' => 'approved',
        ]);
        $order = $this->makeOrder($store, $customer, null, 'Refunded items');
        $order->update([
            'status' => 'rejected',
            'payment_status' => 'refund_pending',
            'store_approval_status' => 'rejected',
            'payment_reference' => 'paystack-confirmed-refund',
            'refund_status' => 'pending',
        ]);
        config(['services.paystack.secret_key' => 'sk_test_example']);
        Http::fake([
            'https://api.paystack.co/refund' => Http::response([
                'status' => true,
                'data' => ['status' => 'processed', 'id' => 999],
            ]),
        ]);

        ProcessPaystackRefund::dispatchSync($order->id);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'refund_status' => 'refunded',
            'refund_reference' => '999',
            'payment_status' => 'refunded',
        ]);
        Http::assertSent(fn ($request) =>
            $request->url() === 'https://api.paystack.co/refund'
            && $request['transaction'] === 'paystack-confirmed-refund'
            && $request['amount'] === '250000'
        );
        $this->assertDatabaseHas('order_notifications', [
            'user_id' => $customer->id,
            'order_id' => $order->id,
            'type' => 'order_refund',
        ]);
    }

    public function test_order_chat_notifies_the_recipient_and_marks_messages_read(): void
    {
        $owner = User::factory()->create();
        $customer = User::factory()->create();
        $store = Store::create([
            'user_id' => $owner->id,
            'stores' => 'Chat Kitchen',
            'owner' => $owner->name,
            'email' => $owner->email,
            'phone' => '08012345678',
            'address' => 'Lagos',
            'status' => 'approved',
        ]);
        $order = $this->makeOrder($store, $customer, null, 'Rice x1');

        $this->actingAs($owner)
            ->post(route('chat.send', $order), [
                'receiver_id' => $customer->id,
                'message' => 'Your order is being prepared.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('order_notifications', [
            'user_id' => $customer->id,
            'order_id' => $order->id,
            'type' => 'store_owner_message',
        ]);
        $this->assertTrue($customer->notifications()
            ->where('type', OrderUpdateNotification::class)
            ->where('data->type', 'store_owner_message')
            ->exists());

        $messageId = \App\Models\Message::where('order_id', $order->id)->value('id');
        $this->actingAs($customer)
            ->get(route('chat.show', [$order->id, $owner->id]))
            ->assertOk()
            ->assertSee('Your order is being prepared.');
        $this->assertDatabaseHas('messages', ['id' => $messageId]);
        $this->assertNotNull(\App\Models\Message::findOrFail($messageId)->read_at);
    }

    public function test_customer_can_view_order_details_using_a_unique_order_reference(): void
    {
        $owner = User::factory()->create();
        $customer = User::factory()->create();
        $otherCustomer = User::factory()->create();
        $store = Store::create([
            'user_id' => $owner->id,
            'stores' => 'Details Kitchen',
            'owner' => $owner->name,
            'email' => $owner->email,
            'phone' => '08012345678',
            'address' => 'Lagos',
            'status' => 'approved',
        ]);
        $order = $this->makeOrder($store, $customer, null, 'Jollof Rice x1');
        $anotherOrder = $this->makeOrder($store, $customer, null, 'Fried Rice x1');

        $this->assertNotEmpty($order->order_reference);
        $this->assertNotSame($order->order_reference, $anotherOrder->order_reference);

        $this->actingAs($customer)
            ->get(route('customer.orders.show', $order))
            ->assertOk()
            ->assertSee($order->order_reference)
            ->assertSee('Jollof Rice x1')
            ->assertSee('Message store');

        $this->actingAs($otherCustomer)
            ->get(route('customer.orders.show', $order))
            ->assertForbidden();
    }

    public function test_rider_delivery_is_confirmed_by_recipient_code_and_declines_return_orders_to_store(): void
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
        ]);
        $order = $this->makeOrder($store, $customer, $rider, 'Delivery items');
        $order->update(['recipient_code' => '1234']);

        $this->actingAs($riderUser)
            ->get(route('rider.assigned-orders'))
            ->assertOk()
            ->assertSee('Accept / acknowledge delivery')
            ->assertSee('Decline assignment');
        $this->post(route('rider.accept', $order))
            ->assertRedirect();
        $this->post(route('rider.arrived', $order))
            ->assertRedirect();
        $this->post(route('rider.pickup', $order))
            ->assertRedirect();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'picked_up']);
        $this->assertNotNull($order->fresh()->picked_up_at);
        $this->assertDatabaseHas('rider_assignments', [
            'order_id' => $order->id,
            'rider_id' => $rider->id,
            'status' => 'picked_up',
        ]);

        $this->post(route('rider.out-for-delivery', $order))
            ->assertRedirect();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'out_for_delivery']);
        $this->post(route('rider.delivery-failed', $order), ['reason' => 'Customer unavailable'])
            ->assertRedirect();
        $this->assertDatabaseHas('delivery_attempts', [
            'order_id' => $order->id,
            'rider_id' => $rider->id,
            'reason' => 'Customer unavailable',
        ]);
        $this->assertDatabaseHas('order_notifications', [
            'user_id' => $customer->id,
            'order_id' => $order->id,
            'type' => 'delivery_attempt_failed',
        ]);

        $this->post(route('rider.deliver', $order), ['recipient_code' => '0000'])
            ->assertRedirect()
            ->assertSessionHas('error');
        $this->post(route('rider.deliver', $order), ['recipient_code' => '1234'])
            ->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'delivered',
            'recipient_code' => '1234',
        ]);
        $this->assertNotNull($order->fresh()->recipient_verified_at);
        $this->assertDatabaseHas('rider_assignments', [
            'order_id' => $order->id,
            'rider_id' => $rider->id,
            'status' => 'completed',
        ]);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'status' => 'delivered',
        ]);
        $this->assertDatabaseHas('order_notifications', [
            'user_id' => $customer->id,
            'order_id' => $order->id,
            'type' => 'order_status_delivered',
        ]);

        $declinedOrder = $this->makeOrder($store, $customer, $rider, 'Declined items');
        $this->post(route('order.reject', $declinedOrder), ['reason' => 'Vehicle issue'])
            ->assertRedirect(route('rider.assigned-orders'));
        $this->assertDatabaseHas('orders', [
            'id' => $declinedOrder->id,
            'status' => 'ready_for_pickup',
            'rider_id' => null,
        ]);
        $this->assertDatabaseHas('order_notifications', [
            'user_id' => $owner->id,
            'order_id' => $declinedOrder->id,
            'type' => 'order_rider_declined',
        ]);
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
            'status' => $rider ? 'rider_assigned' : 'ready_for_pickup',
            'store_approval_status' => 'approved',
            'payment_status' => 'paid',
        ]);
    }
}