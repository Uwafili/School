<?php

namespace App\Http\Controllers;
use App\Models\Post;

use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addTocart(Request $request, $id){
         $posts=Post::findOrFail($id);
         $cart=session()->get('cart', []);
         if(isset($cart[$id])){
            $cart[$id]['quantity']++;

         }  else{
                $cart[$id]=[
                    'title'=>$posts->title,
                     "price" => $posts->price,
                      "quantity" => 1,
                      "image" => $posts->image
                ];
         }  
         session()->put('cart', $cart);                    
     return back()->with('success', 'Item added to cart!');
        }

}
