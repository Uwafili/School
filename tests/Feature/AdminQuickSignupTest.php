<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminQuickSignupTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_user_rider_and_store_owner_from_their_management_pages(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['usertype' => 'admin']);

        $this->actingAs($admin)
            ->get(route('manage.index'))
            ->assertOk()
            ->assertSee('Quick sign up: User');
        $this->get(route('riders'))
            ->assertOk()
            ->assertSee('Quick sign up: Rider');
        $this->get(route('storeapprove'))
            ->assertOk()
            ->assertSee('Quick sign up: Store owner');

        $this->post(route('admin.users.store'), [
            'name' => 'Quick User',
            'email' => 'quick-user@example.test',
        ])->assertRedirect(route('manage.index'));

        $this->post(route('admin.riders.store'), [
            'name' => 'Quick Rider',
            'email' => 'quick-rider@example.test',
            'phone' => '08031234567',
            'license' => 'LIC-123',
            'vehicle_number' => '08012345678',
            'vehicle' => 'Motorbike',
        ])->assertRedirect(route('riders'));

        $this->post(route('admin.stores.store'), [
            'stores' => 'Quick Kitchen',
            'owner' => 'Quick Owner',
            'email' => 'quick-store@example.test',
            'phone' => '08012345678',
            'address' => 'Lagos',
        ])->assertRedirect(route('storeapprove'));

        $user = User::where('email', 'quick-user@example.test')->firstOrFail();
        $riderUser = User::where('email', 'quick-rider@example.test')->firstOrFail();
        $storeUser = User::where('email', 'quick-store@example.test')->firstOrFail();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'usertype' => 'user']);
        $this->assertDatabaseHas('riders', ['user_id' => $riderUser->id, 'status' => 'pending']);
        $this->assertDatabaseHas('stores', ['user_id' => $storeUser->id, 'status' => 'pending']);

        Notification::assertSentTo($user, ResetPassword::class);
        Notification::assertSentTo($riderUser, ResetPassword::class);
        Notification::assertSentTo($storeUser, ResetPassword::class);
    }
}