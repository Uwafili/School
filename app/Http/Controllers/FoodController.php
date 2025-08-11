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
    $post=Post::where('category', 'pizza')->latest()->get(); 
    return view('Food.Pizza', compact('post'));
} 

public function burger()
{
    $post=Post::where('category', 'burger')->latest()->get(); 
    return view('Food.Burger', compact('post'));
}
   
public function salad()
{   
    $post=Post::where('category', 'salad')->latest()->get(); 
    return view('Food.salad', compact('post'));
}

public function drinks()
{
    $post=Post::where('category', 'drinks')->latest()->get(); 
    return view('Food.drinks', compact('post'));
}


public function view($id)
{
    $post = Post::findOrFail($id);
    return view('food.view', compact('post'));
}




}
