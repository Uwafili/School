<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutAndStoreNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_discards_a_malformed_session_cart_with_a_clear_message(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)->withSession([
            'cart' => [['title' => 'Meal without a price', 'quantity' => 1]],
        ])->get(route('payment.checkout'))
            ->assertRedirect(route('cart'))
            ->assertSessionHas('error', 'Your cart could not be loaded. Please add your items again.')
            ->assertSessionMissing('cart');
    }

    public function test_payment_submission_does_not_process_a_malformed_session_cart(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)->withSession([
            'cart' => [['title' => 'Meal without a price', 'quantity' => 1]],
        ])->post(route('payment.pay'), [
            'payment_method' => 'paystack',
            'delivery_method' => 'pickup',
            'pickup_station' => 'ikeja',
            'customer_phone' => '08012345678',
        ])->assertRedirect(route('cart'))
            ->assertSessionHas('error', 'Your cart could not be loaded. Please add your items again.')
            ->assertSessionMissing('cart');
    }

    public function test_login_routes_a_pending_store_owner_to_their_store_application_and_shows_phone_navigation(): void
    {
        $storeOwner = User::factory()->create();
        Store::create([
            'user_id' => $storeOwner->id,
            'stores' => 'Pending Kitchen',
            'owner' => $storeOwner->name,
            'email' => $storeOwner->email,
            'phone' => '08012345678',
            'address' => 'Lagos',
            'status' => 'pending',
        ]);

        $this->post(route('login.store'), [
            'email' => $storeOwner->email,
            'password' => 'password',
        ])->assertRedirect(route('store.create'))
            ->assertSessionHas('active_role', 'store');

        $this->get(route('store.create'))
            ->assertOk()
            ->assertSee('class="phone-bottom-nav"', false)
            ->assertSee('aria-label="Store application"', false)
            ->assertSee('Store application')
            ->assertSee(route('store.create'), false);
    }
}
