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
use Illuminate\Support\Facades\Http;
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
            'cart' => [[
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
                'reference' => 'paystack-test-reference',
                'cart' => [[
                    'store_id' => $store->id,
                    'title' => 'Jollof Rice',
                    'price' => 2400,
                    'quantity' => 1,
                ]],
            ],
        ])->get(route('payment.paystack.callback', ['reference' => 'paystack-test-reference']))
            ->assertRedirect(route('dashboard'));

        $order = Order::where('store_id', $store->id)->firstOrFail();
        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'order_id' => $order->id,
            'title' => 'New food order received',
        ]);
        $this->assertSame('Jollof Rice x1', $order->items_description);
        Event::assertDispatched(NearbyOrderAvailable::class, fn ($event) =>
            $event->riderId === $rider->id
            && $event->broadcastOn()[0]->name === 'private-riders.' . $rider->id
        );
        Event::assertNotDispatched(NearbyOrderAvailable::class, fn ($event) => $event->riderId === $farRider->id);

        $this->actingAs($owner)->get(route('storedashboard'))
            ->assertOk()
            ->assertSee('New food order received');
    }

    public function test_checkout_initializes_paystack_with_the_server_calculated_amount(): void
    {
        $customer = User::factory()->create();
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
            'cart' => [[
                'store_id' => 1,
                'title' => 'Jollof Rice',
                'price' => 2400,
                'quantity' => 1,
            ]],
        ])->post(route('payment.pay'), [
            'amount' => 1,
            'payment_method' => 'paystack',
            'delivery_method' => 'pickup',
            'pickup_station' => 'ikeja',
        ])->assertRedirect('https://checkout.paystack.com/test-session');

        $this->assertSame(2400, session('pending_payment.amount'));
        $this->assertSame('Jollof Rice', session('pending_payment.cart.0.title'));
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