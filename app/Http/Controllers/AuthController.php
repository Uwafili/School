<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Models\Post;

use App\Models\User;
use App\Models\Rider;
use App\Models\Store;
use App\Models\ApprovalNotification;


class AuthController extends Controller
{
   public function register(Request $request){

    $field=$request->validate([
         'name'=>['required','string','max:255'],
        'email'=>['required', 'max:255','email', 'unique:users,email'],
         'password'=>['required','string','min:8','confirmed']
    ]);
    
    $field['usertype'] = ($request->email === 'uwafilinorbet50@gmail.com') ? 'admin' : 'user';
    $user = User::create($field);
    
    Auth::login($user);
   $request->session()->regenerate();


   return redirect()->intended(route('dashboard'));

   }

   public function login(Request $request){
         $field=$request->validate([
            'email'=>['required', 'max:255','email'],
            'password'=>['required']
         ]);
         if(Auth::attempt($field,$request->remember)){
               $request->session()->regenerate();

               $signedInUser = Auth::user();
               $activeRole = 'user';
               if ($signedInUser->usertype === 'admin') {
                  $activeRole = 'admin';
               } elseif (Rider::where('user_id', $signedInUser->id)->where('status', 'approved')->exists()) {
                  $activeRole = 'rider';
               } elseif (Store::where('user_id', $signedInUser->id)->where('status', 'approved')->exists()) {
                  $activeRole = 'store';
               }
               $request->session()->put('active_role', $activeRole);

               $destination = Auth::user()->usertype === 'admin'
                  ? route('admin.dashboard')
                  : route('dashboard');

               return redirect()->intended($destination);
         }
         else{
            return back()->withErrors([
                'failed'=>'This user information does not exist'
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
      } elseif ($role === 'rider' && Rider::where('user_id', $user->id)->where('status', 'approved')->exists()) {
         $request->session()->put('active_role', 'rider');
      } elseif ($role === 'store' && Store::where('user_id', $user->id)->where('status', 'approved')->exists()) {
         $request->session()->put('active_role', 'store');
      } else {
         $request->session()->put('active_role', 'user');
      }

      return back();
   }
   
   public function adminDashboard(){
      if(Auth::check()&& Auth::user()->usertype=='admin'){
          $userCount = User::count();
          $posts=Post::latest()->get();
          $users = User::latest()->get();
          $storeCount = Store::count();
          $riderCount = Rider::count();
          return view('admin.dashboard', compact('userCount', 'posts', 'users', 'storeCount', 'riderCount'));
          
         
      }
      else if(Auth::check()&& Auth::user()->usertype=='user'){
         return redirect()->route('dashboard');
      }
      else{
         return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
         
         
         
      }
   }

   public function manageUsers() {
    $users = User::latest()->get();
    return view('admin.manage', compact('users'));
}

public function showrider(){
     $Riders=Rider::latest()->get();
     return view('admin.riders',compact('Riders'));
}

public function viewdetail($id){
  $Riders=Rider::findOrFail($id);
  return view('admin.viewdetail', compact('Riders'));
}
public function Approve($id){
  $Riders=Rider::findOrFail($id);
  $Riders->status='approved';
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

public function Reject($id){
  $Riders=Rider::findOrFail($id);
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

   public function logout(Request $request){
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
         'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
         'password' => ['nullable', 'string', 'min:8', 'confirmed'],
      ]);

      $user->name = $validated['name'];
      $user->email = $validated['email'];
      if (!empty($validated['password'])) {
         $user->password = Hash::make($validated['password']);
      }
      $user->save();

      return redirect()->route('dashboard')->with('success', 'Profile updated successfully.');
   }


public function destroy(Post $post){
      
        $post->delete();

        return back()->with('delete', 'Your post was deleted');
    }
   
   
}
