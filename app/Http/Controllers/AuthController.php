<?php

namespace App\Http\Controllers;

use App\Models\ApprovalNotification;
use App\Models\Order;
use App\Models\Post;
use App\Models\Rider;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $field = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'max:255', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $field['usertype'] = ($request->email === 'uwafilinorbet50@gmail.com') ? 'admin' : 'user';
        $user = User::create($field);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));

    }

    public function login(Request $request)
    {
        $field = $request->validate([
            'email' => ['required', 'max:255', 'email'],
            'password' => ['required'],
        ]);
        if (Auth::attempt($field, $request->remember)) {
            $request->session()->regenerate();

            $signedInUser = Auth::user();
            $activeRole = 'user';
            if ($signedInUser->usertype === 'admin') {
                $activeRole = 'admin';
            } elseif (Rider::where('user_id', $signedInUser->id)->where('status', 'approved')->where('is_active', true)->exists()) {
                $activeRole = 'rider';
            } elseif (Store::where('user_id', $signedInUser->id)->exists()) {
                $activeRole = 'store';
            }
            $request->session()->put('active_role', $activeRole);

            $destination = match ($activeRole) {
                'admin' => route('admin.dashboard'),
                'rider' => route('rider.dashboard'),
                'store' => Store::where('user_id', $signedInUser->id)->where('status', 'approved')->exists()
                   ? route('storedashboard')
                   : route('store.create'),
                default => route('dashboard'),
            };

            return redirect()->intended($destination);
        } else {
            return back()->withErrors([
                'failed' => 'This user information does not exist',
            ]);
        }
    }

    public function showForgotPasswordForm()
    {
        return view('Auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
           ? back()->with('status', __($status))
           : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm(string $token)
    {
        return view('Auth.reset-password', [
            'token' => $token,
            'email' => request('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $validated,
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->setRememberToken(Str::random(60));
            }
        );

        return $status === Password::PASSWORD_RESET
           ? redirect()->route('login')->with('status', __($status))
           : back()->withErrors(['email' => __($status)]);
    }

    public function switchNavigationRole(Request $request)
    {
        $role = $request->validate([
            'role' => ['required', 'in:user,rider,store'],
        ])['role'];

        $user = Auth::user();

        if ($user->usertype === 'admin') {
            $request->session()->put('active_role', 'admin');
        } elseif ($role === 'rider' && Rider::where('user_id', $user->id)->where('status', 'approved')->where('is_active', true)->exists()) {
            $request->session()->put('active_role', 'rider');
        } elseif ($role === 'store' && Store::where('user_id', $user->id)->exists()) {
            $request->session()->put('active_role', 'store');
        } else {
            $request->session()->put('active_role', 'user');
        }

        return back();
    }

    public function adminDashboard()
    {
        if (Auth::check() && Auth::user()->usertype == 'admin') {
            $userCount = User::count();
            $posts = Post::count();
            $storeCount = Store::count();
            $riderCount = Rider::count();

            return view('admin.dashboard', compact('userCount', 'posts', 'storeCount', 'riderCount'));

        } elseif (Auth::check() && Auth::user()->usertype == 'user') {
            return redirect()->route('dashboard');
        } else {
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');

        }
    }

    public function manageUsers()
    {
        $users = User::latest()->paginate(20);

        return view('admin.manage', compact('users'));
    }

    public function sendUserPasswordResetLink(User $user)
    {
        $status = Password::sendResetLink(['email' => $user->email]);

        return back()->with(
            $status === Password::RESET_LINK_SENT ? 'success' : 'error',
            __($status)
        );
    }

    public function editManagedUser(User $user)
    {
        return view('admin.edit-user', compact('user'));
    }

    public function updateManagedUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        return redirect()->route('manage.index')->with('success', 'User updated successfully.');
    }

    public function destroyUser(User $user)
    {
        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }

    public function showrider()
    {
        $Riders = Rider::latest()->get();
        $readyOrders = Order::with('store')
            ->where('status', 'ready_for_pickup')
            ->where('payment_status', 'paid')
            ->where('store_approval_status', 'approved')
            ->whereNull('rider_id')
            ->latest()
            ->get();
        $availableRiders = Rider::where('status', 'approved')->where('is_active', true)->where('is_online', true)->get();
        $activeOrders = Order::with(['rider', 'store'])
            ->whereIn('status', ['rider_assigned', 'picked_up', 'out_for_delivery'])
            ->latest()
            ->get();
        $deliveryHistory = Order::with('rider')
            ->where('status', 'delivered')
            ->latest()
            ->limit(50)
            ->get();

        return view('admin.riders', compact('Riders', 'readyOrders', 'availableRiders', 'activeOrders', 'deliveryHistory'));
    }

    public function viewdetail($id)
    {
        $Riders = Rider::findOrFail($id);

        return view('admin.viewdetail', compact('Riders'));
    }

    public function Approve($id)
    {
        $Riders = Rider::findOrFail($id);
        $Riders->status = 'approved';
        $Riders->save();
        ApprovalNotification::create([
            'user_id' => $Riders->user_id,
            'role' => 'rider',
            'title' => 'Rider application approved',
            'message' => 'Your rider application has been approved. Your delivery dashboard is now ready.',
            'status' => 'approved',
        ]);

        //   return back()->with('success', 'Rider approved successfully!',compact('Riders'));
        return redirect()->back()->with('success', 'Rider approved successfully.');

        //   return redirect()->route('ridersdashboard', compact('Rider->id'));

    }

    public function Reject($id)
    {
        $Riders = Rider::findOrFail($id);
        $Riders->status = 'rejected';
        $Riders->save();
        ApprovalNotification::create([
            'user_id' => $Riders->user_id,
            'role' => 'rider',
            'title' => 'Rider application update',
            'message' => 'Your rider application needs attention. Please review your details and submit them again.',
            'status' => 'rejected',
        ]);

        return back()->with('reject', 'Rider info rejected!');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('home');

    }

    public function editProfile()
    {
        return view('users.profile-edit', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Profile updated successfully.');
    }

    public function destroy(Post $post)
    {

        $post->delete();

        return back()->with('delete', 'Your post was deleted');
    }
}
