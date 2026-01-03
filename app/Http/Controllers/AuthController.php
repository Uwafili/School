<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\Post;

use App\Models\User;
use App\Models\Rider;

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
                return redirect()->intended('dashboard');
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
//   return back()->with('success', 'Rider approved successfully!',compact('Riders'));
    return redirect()->back()->with('success', 'Rider approved successfully.');

      //   return redirect()->route('ridersdashboard', compact('Rider->id'));


}

public function Reject($id){
  $Riders=Rider::findOrFail($id);
  $Riders->status = 'rejected';
  $Riders->save();
  return back()->with('reject', 'Rider info rejected!');
}

   public function logout(Request $request){
      Auth::logout();

      $request->session()->invalidate();

      $request->session()->regenerateToken();

      return redirect()->route('home');

   }


public function destroy(Post $post){
      
        $post->delete();

        return back()->with('delete', 'Your post was deleted');
    }
   
   
}
