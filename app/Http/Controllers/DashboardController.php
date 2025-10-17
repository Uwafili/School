<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\Post;


use Illuminate\Http\Request;

class DashboardController extends Controller
{
   public function index(){
    
    $posts=Post::latest()->get();
    return view('users.dashboard', ['posts'=>$posts]);
   }

}
