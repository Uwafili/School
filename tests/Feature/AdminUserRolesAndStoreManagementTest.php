<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('View details')
            ->assertSee('Delete store')
            ->assertSee(route('admin.stores.show', $store));

        $this->get(route('admin.stores.show', $store))
            ->assertOk()
            ->assertSee('Test Store')
            ->assertSee($owner->email);

        $this->delete(route('admin.stores.destroy', $store))
            ->assertRedirect(route('storeapprove'));

        $this->assertDatabaseMissing('stores', ['id' => $store->id]);
        Storage::disk('public')->assertMissing('store_images/logo.png');
    }
}
