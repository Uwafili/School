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
    $posts=Post::where('category', 'pizza')->latest()->get(); 
    return view('Food.Pizza', compact('posts'));
} 

public function burger()
{
    $posts=Post::where('category', 'burger')->latest()->get(); 
    return view('Food.Burger', compact('posts'));
}
   
public function salad()
{   
    $posts=Post::where('category', 'salad')->latest()->get(); 
    return view('Food.salad', compact('posts'));
}

public function drinks()
{
    $posts=Post::where('category', 'drinks')->latest()->get(); 
    return view('Food.drinks', compact('posts'));
}


public function view($id)
{
    $posts = Post::findOrFail($id);
    return view('food.view', compact('posts'));
}




}
