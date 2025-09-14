<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\Post;

use App\Models\User;
use Illuminate\Foundation\Auth\User as AuthUser;

class AuthController extends Controller
{
   public function register(Request $request){

    $field=$request->validate([
        'name'=>['required','max:255'],
        'email'=>['required', 'max:255','email', 'unique:users,email'],
        'password'=>['required','min:3','confirmed']
    ]);
    
    $field['usertype'] = ($request->email === 'uwafilinorbet50@gmail.com') ? 'admin' : 'user';
    $user = User::create($field);
    
    Auth::login($user);


    return redirect()->route('home');

   }

   public function login(Request $request){
         $field=$request->validate([
            'email'=>['required', 'max:255','email'],
            'password'=>['required']
         ]);
         if(Auth::attempt($field,$request->remember)){
                return redirect()->intended('home');
         }
         else{
            return back()->withErrors([
                'failed'=>'This user information does not exist'
            ]);
         }
   }
   
   public function adminDashboard(){
      if(Auth::check()&& Auth::user()->usertype=='admin'){
          $userCount = User::count();
          $posts=Post::latest()->get();
          $users = User::latest()->get();
          return view('admin.dashboard', compact('userCount', 'posts', 'users'));
          
         
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

   public function logout(Request $request){
      Auth::logout();

      $request->session()->invalidate();

      $request->session()->regenerateToken();

      return redirect()->route('home');

   }


   public function destroy($id)
{
    $user = \App\Models\User::findOrFail($id);
    $user->delete();

    return redirect()->route('manage')->with('success', 'User deleted successfully.');
}

   
}
