<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Post;

class FoodController extends Controller
{


public function pizza()
{
    $posts=Post::where('category', 'pizza')->paginate(5);
    return view('food.pizza',['posts'=>$posts]);
} 

public function burger()
{
    $posts=Post::where('category','burger')->paginate(5);
    return view('food.burger',['posts'=>$posts]);
}
   
public function salad()
{   
    $posts=Post::where('category', 'salad')->paginate(5); 
    return view('food.salad', ['posts'=>$posts]);
}

public function drinks()
{
    $posts=Post::where('category', 'drinks')->paginate(5); 
    return view('food.drinks', ['posts'=>$posts]);
}


public function view($id)
{
    $posts = Post::findOrFail($id);
    return view('food.view', $posts);
}




}
