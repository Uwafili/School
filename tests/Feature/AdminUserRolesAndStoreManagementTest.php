<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminUserRolesAndStoreManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_switch_user_roles_but_cannot_change_their_own_role(): void
    {
        $admin = User::factory()->create(['usertype' => 'admin']);
        $anotherAdmin = User::factory()->create(['usertype' => 'admin']);
        $user = User::factory()->create(['usertype' => 'user']);

        $this->actingAs($admin)
            ->get(route('manage.index'))
            ->assertOk()
            ->assertSee('Make admin')
            ->assertSee('Make user');

        $this->get(route('user.edit', $user))
            ->assertOk()
            ->assertSee('Edit user')
            ->assertSee($user->email);

        $this->put(route('user.update', $user), [
            'name' => 'Updated Test User',
            'email' => $user->email,
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect(route('manage.index'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Test User']);

        $this->patch(route('user.role', $user), ['usertype' => 'admin'])
            ->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'usertype' => 'admin']);

        $this->patch(route('user.role', $anotherAdmin), ['usertype' => 'user'])
            ->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $anotherAdmin->id, 'usertype' => 'user']);

        $this->patch(route('user.role', $admin), ['usertype' => 'user'])
            ->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'usertype' => 'admin']);

        $this->patch(route('user.role', $user), ['usertype' => 'invalid'])
            ->assertSessionHasErrors('usertype');
    }

    public function test_admin_can_view_store_details_and_delete_store_and_its_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['usertype' => 'admin']);
        $owner = User::factory()->create();
        Storage::disk('public')->put('store_images/logo.png', 'image');
        $store = Store::create([
            'user_id' => $owner->id,
            'stores' => 'Test Store',
            'owner' => $owner->name,
            'email' => $owner->email,
            'phone' => '08012345678',
            'address' => 'Test address',
            'image' => 'store_images/logo.png',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get(route('storeapprove'))
            ->assertOk()
            ->assertSee('Details')
            ->assertSee('Delete store')
            ->assertSee(route('admin.stores.show', $store));

        $this->get(route('admin.stores.show', $store))
            ->assertOk()
            ->assertSee('Test Store')
            ->assertSee($owner->email)
            ->assertSee('Application timeline');

        $this->get(route('stores.edit', $store))
            ->assertOk()
            ->assertSee('Edit store profile');

        $this->put(route('stores.update', $store), [
            'stores' => 'Updated Test Store',
            'owner' => $owner->name,
            'email' => $owner->email,
            'phone' => '08012345678',
            'address' => 'Updated address',
        ])->assertRedirect(route('storeapprove'));
        $this->assertDatabaseHas('stores', ['id' => $store->id, 'address' => 'Updated address']);

        $this->delete(route('admin.stores.destroy', $store))
            ->assertRedirect(route('storeapprove'));

        $this->assertDatabaseMissing('stores', ['id' => $store->id]);
        Storage::disk('public')->assertMissing('store_images/logo.png');
    }

    public function test_admin_can_filter_stores_by_search_status_and_registration_dates(): void
    {
        $admin = User::factory()->create(['usertype' => 'admin']);
        $approvedOwner = User::factory()->create();
        $pendingOwner = User::factory()->create();

        $approvedStore = Store::create([
            'user_id' => $approvedOwner->id,
            'stores' => 'Harbor Kitchen',
            'owner' => 'Taylor Reed',
            'email' => $approvedOwner->email,
            'phone' => '08012345678',
            'address' => 'Lagos',
            'status' => 'approved',
        ]);
        $approvedStore->forceFill(['created_at' => Carbon::now()->subDay()])->save();
        $pendingStore = Store::create([
            'user_id' => $pendingOwner->id,
            'stores' => 'Garden Cafe',
            'owner' => 'Jordan Lane',
            'email' => $pendingOwner->email,
            'phone' => '08012345679',
            'address' => 'Abuja',
            'status' => 'pending',
        ]);
        $pendingStore->forceFill(['created_at' => Carbon::now()->subDays(10)])->save();

        $this->actingAs($admin)
            ->get(route('storeapprove', [
                'search' => 'Taylor',
                'status' => 'approved',
                'from' => Carbon::now()->subDays(2)->toDateString(),
                'to' => Carbon::now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Harbor Kitchen')
            ->assertDontSee('Garden Cafe');

        $this->get(route('storeapprove', ['status' => 'pending']))
            ->assertOk()
            ->assertSee('Garden Cafe')
            ->assertDontSee('Harbor Kitchen');
    }
}
