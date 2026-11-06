<?php

namespace App\Http\Controllers;

use App\Models\ApprovalNotification;
use App\Models\Order;
use App\Models\Rider;
use App\Models\Store;
use App\Models\User;
use App\Services\OrderNotificationService;
use App\Services\OrderWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function createManagedUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        $user = $this->createManagedAccount($validated['name'], $validated['email']);

        return $this->sendSetupLink($user, 'manage.index', 'User account created.');
    }

    public function createManagedRider(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'max:15', 'regex:/^\\+?[0-9]{1,4}?[-. ]?([0-9]{1,3}[-. ]?){1,4}[0-9]{1,4}$/'],
            'license' => ['required', 'string', 'max:255'],
            'vehicle_number' => ['required', 'max:15', 'regex:/^\\+?[0-9]{1,4}?[-. ]?([0-9]{1,3}[-. ]?){1,4}[0-9]{1,4}$/'],
            'vehicle' => ['required', 'string', 'max:255'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = $this->createManagedAccount($validated['name'], $validated['email']);

            Rider::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'license' => $validated['license'],
                'vehicle_number' => $validated['vehicle_number'],
                'vehicle' => $validated['vehicle'],
                'status' => 'pending',
            ]);

            return $user;
        });

        return $this->sendSetupLink($user, 'riders', 'Rider account created and submitted for approval.');
    }

    public function createManagedStore(Request $request)
    {
        $validated = $request->validate([
            'stores' => ['required', 'string', 'max:255'],
            'owner' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'max:15', 'regex:/^\\+?[0-9]{1,4}?[-. ]?([0-9]{1,3}[-. ]?){1,4}[0-9]{1,4}$/'],
            'address' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,avif', 'max:3000'],
        ]);

        $imagePath = $request->file('image')?->store('store_images', 'public');

        try {
            $user = DB::transaction(function () use ($validated, $imagePath) {
                $user = $this->createManagedAccount($validated['owner'], $validated['email']);

                Store::create([
                    'user_id' => $user->id,
                    'stores' => $validated['stores'],
                    'owner' => $validated['owner'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                    'image' => $imagePath,
                    'status' => 'pending',
                ]);

                return $user;
            });
        } catch (\Throwable $exception) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }

        return $this->sendSetupLink($user, 'storeapprove', 'Store owner account created and submitted for approval.');
    }

    private function createManagedAccount(string $name, string $email): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => Str::random(48),
            'usertype' => 'user',
        ]);
    }

    private function sendSetupLink(User $user, string $route, string $message)
    {
        try {
            $status = Password::sendResetLink(['email' => $user->email]);
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->route($route)->with('warning', $message.' Password setup email could not be sent; use Reset key to retry.');
        }

        if ($status !== Password::RESET_LINK_SENT) {
            return redirect()->route($route)->with('warning', $message.' Password setup email could not be sent; use Reset key to retry.');
        }

        return redirect()->route($route)->with('success', $message.' A password setup link was sent.');
    }

    /**
     * Display all stores for admin approval
     */
    public function storeApprove(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:all,pending,approved,rejected'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        return view('admin.storeapprove', [
            'totalCount' => Store::count(),
            'pendingCount' => Store::where('status', 'pending')->count(),
            'approvedCount' => Store::where('status', 'approved')->count(),
            'rejectedCount' => Store::where('status', 'rejected')->count(),
        ]);
    }

    public function updateUserRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'usertype' => ['required', 'in:user,admin'],
        ]);

        if ($user->is(auth()->user())) {
            return back()->with('error', 'You cannot change your own account role.');
        }

        $roleUpdated = DB::transaction(function () use ($user, $validated) {
            if ($user->usertype === 'admin' && $validated['usertype'] === 'user') {
                $adminCount = User::where('usertype', 'admin')->lockForUpdate()->count();

                if ($adminCount <= 1) {
                    return false;
                }
            }

            $user->update(['usertype' => $validated['usertype']]);

            return true;
        });

        if (! $roleUpdated) {
            return back()->with('error', 'The last administrator cannot be changed to a user.');
        }

        return back()->with('success', "{$user->name}'s role was changed to {$validated['usertype']}.");
    }

    /**
     * Approve a store application
     */
    public function approveStore(Store $store)
    {
        $store->update(['status' => 'approved']);
        ApprovalNotification::create([
            'user_id' => $store->user_id,
            'role' => 'store',
            'title' => 'Store approved',
            'message' => "Your store, {$store->stores}, has been approved. You can now manage it and start receiving orders.",
            'status' => 'approved',
        ]);

        return back()->with('success', "✅ Store '{$store->stores}' has been approved! Owner will be notified.");
    }

    /**
     * Reject a store application
     */
    public function rejectStore(Store $store)
    {
        $store->update(['status' => 'rejected']);
        ApprovalNotification::create([
            'user_id' => $store->user_id,
            'role' => 'store',
            'title' => 'Store application update',
            'message' => "Your store, {$store->stores}, needs attention. Please review your application details and try again.",
            'status' => 'rejected',
        ]);

        return back()->with('warning', "❌ Store '{$store->stores}' has been rejected.");
    }

    public function showStore(Store $store)
    {
        $store->load('user');
        $approvalHistory = ApprovalNotification::where('user_id', $store->user_id)
            ->where('role', 'store')
            ->latest()
            ->get();

        return view('admin.store-details', compact('store', 'approvalHistory'));
    }

    public function destroyStore(Store $store)
    {
        $storeName = $store->stores;
        $image = $store->image;

        DB::transaction(function () use ($store) {
            $store->delete();
        });

        $imageDeleted = ! $image || Storage::disk('public')->delete($image);

        if (! $imageDeleted) {
            return redirect()->route('storeapprove')->with(
                'warning',
                "Store '{$storeName}' and its related records were deleted, but the store image could not be removed."
            );
        }

        return redirect()->route('storeapprove')->with('success', "Store '{$storeName}' and its related records were deleted.");
    }

    public function editRider(Rider $rider)
    {
        return view('admin.rider-edit', compact('rider'));
    }

    public function updateRider(Request $request, Rider $rider)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:225'],
            'phone' => ['required', 'max:15'],
            'license' => ['required', 'max:255'],
            'vehicle_number' => ['required', 'max:15'],
            'vehicle' => ['required', 'max:255'],
            'image' => ['nullable', 'file', 'mimes:jpg,png,jpeg,avif', 'max:3000'],
        ]);

        if ($request->hasFile('image')) {
            if ($rider->image) {
                Storage::disk('public')->delete($rider->image);
            }
            $validated['image'] = $request->file('image')->store('rider_images', 'public');
        }

        $rider->update($validated);

        return redirect()->route('riders')->with('success', 'Rider updated successfully.');
    }

    public function toggleRiderActive(Rider $rider, OrderWorkflowService $workflow, OrderNotificationService $notifications)
    {
        $activate = ! $rider->is_active;

        if (! $activate && Order::where('rider_id', $rider->id)
            ->whereIn('status', ['picked_up', 'out_for_delivery'])
            ->exists()) {
            return back()->with('error', 'This rider has an active delivery. Complete it before deactivating the rider.');
        }

        DB::transaction(function () use ($rider, $activate, $workflow, $notifications) {
            $lockedRider = Rider::query()->lockForUpdate()->findOrFail($rider->id);
            $lockedRider->update([
                'is_active' => $activate,
                'is_online' => $activate && $lockedRider->is_online,
            ]);

            if (! $activate) {
                Order::where('rider_id', $lockedRider->id)
                    ->where('status', 'rider_assigned')
                    ->get()
                    ->each(function (Order $order) use ($workflow, $notifications, $lockedRider) {
                        $releasedOrder = $workflow->transition($order, 'ready_for_pickup', [
                            'rider_id' => null,
                            'recipient_code' => null,
                            'rider_accepted_at' => null,
                            'rider_arrived_at' => null,
                        ], fn (Order $lockedOrder): bool => $lockedOrder->rider_id === $lockedRider->id);

                        $notifications->sendToStore(
                            $releasedOrder,
                            'Rider deactivated',
                            "Order {$releasedOrder->order_reference} is ready for reassignment.",
                            'order_rider_deactivated'
                        );
                    });
            }
        });

        return back()->with('success', $activate ? 'Rider activated.' : 'Rider deactivated and unstarted orders released for reassignment.');
    }

    public function editStore(Store $store)
    {
        return view('admin.store-edit', compact('store'));
    }

    public function updateStore(Request $request, Store $store)
    {
        $validated = $request->validate([
            'stores' => ['required', 'string', 'max:255'],
            'owner' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'max:15'],
            'address' => ['required', 'max:255'],
            'image' => ['nullable', 'file', 'mimes:jpg,png,jpeg,avif', 'max:3000'],
        ]);

        if ($request->hasFile('image')) {
            if ($store->image) {
                Storage::disk('public')->delete($store->image);
            }
            $validated['image'] = $request->file('image')->store('store_images', 'public');
        }

        $store->update($validated);

        return redirect()->route('storeapprove')->with('success', 'Store updated successfully.');
    }
}
