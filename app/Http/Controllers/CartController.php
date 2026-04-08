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

         public function increase($id)
    {
        $cart = session()->get('cart');

        $cart[$id]['quantity']++;

        session()->put('cart', $cart);

        return back();
    }

    // Decrease quantity
    public function decrease($id)
    {
        $cart = session()->get('cart');

        if($cart[$id]['quantity'] > 1){
            $cart[$id]['quantity']--;
        }

        session()->put('cart', $cart);

        return back();
    }

    // Remove item
    public function remove($id)
    {
        $cart = session()->get('cart');

        unset($cart[$id]);

        session()->put('cart', $cart);

        return back();
    }

    // View cart page
    public function viewCart()
    {
        return view('cart.index');
    }


}
