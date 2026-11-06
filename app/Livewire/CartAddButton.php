<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CartAddButton extends Component
{
    public int $postId;

    public bool $added = false;

    public function add(): void
    {
        if (! Auth::check()) {
            $this->redirectRoute('login');

            return;
        }

        $post = Post::findOrFail($this->postId);
        $cart = session('cart', []);

        if (isset($cart[$post->id])) {
            abort_if($cart[$post->id]['quantity'] >= 99, 422, 'A maximum of 99 of each item can be added.');
            $cart[$post->id]['quantity']++;
        } else {
            $store = Store::where('user_id', $post->user_id)->where('status', 'approved')->first();
            $cart[$post->id] = [
                'title' => $post->title,
                'price' => $post->price,
                'quantity' => 1,
                'image' => $post->image,
                'category' => $post->category,
                'store_id' => $store?->id,
            ];
        }

        session()->put('cart', $cart);
        $this->added = true;
        $this->dispatch('cart-updated', count: count($cart));
    }

    public function render()
    {
        return view('livewire.cart-add-button');
    }
}
