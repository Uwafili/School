<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;

class FoodController extends Controller
{


    public function pizza()
{
    $posts=Post::with('user')->where('category','pizza')->paginate(5);
    return view('food.pizza',['posts'=>$posts]);
} 

public function burger()
{
    $posts=Post::with('user')->where('category','burger')->paginate(5);
    return view('food.burger',['posts'=>$posts]);
}
   
public function salad()
{   
    $posts=Post::with('user')->where('category', 'salad')->paginate(5); 
    return view('food.salad', ['posts'=>$posts]);
}

public function drinks()
{
    $posts=Post::with('user')->where('category', 'drinks')->paginate(5); 
    return view('food.drinks', ['posts'=>$posts]);
}


public function view(Post $post)
{
    $post->load('user');

    return view('food.view', compact('post'));
}




}
