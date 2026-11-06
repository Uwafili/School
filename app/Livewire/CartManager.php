<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\Store;
use Livewire\Component;

class CartManager extends Component
{
    public array $cart = [];

    public ?string $notice = null;

    public function mount(): void
    {
        $this->cart = session('cart', []);
    }

    public function add(int $postId): void
    {
        $post = Post::findOrFail($postId);
        $this->cart = session('cart', []);

        if (isset($this->cart[$postId])) {
            $this->cart[$postId]['quantity']++;
        } else {
            $store = Store::where('user_id', $post->user_id)->where('status', 'approved')->first();
            $this->cart[$postId] = [
                'title' => $post->title,
                'price' => $post->price,
                'quantity' => 1,
                'image' => $post->image,
                'category' => $post->category,
                'store_id' => $store?->id,
            ];
        }

        $this->persistCart('Added '.$post->title.' to your cart.');
    }

    public function increase(int $postId): void
    {
        $this->cart = session('cart', []);
        abort_unless(isset($this->cart[$postId]), 404);
        abort_if($this->cart[$postId]['quantity'] >= 99, 422, 'A maximum of 99 of each item can be added.');

        $this->cart[$postId]['quantity']++;
        $this->persistCart();
    }

    public function decrease(int $postId): void
    {
        $this->cart = session('cart', []);
        abort_unless(isset($this->cart[$postId]), 404);

        if ($this->cart[$postId]['quantity'] > 1) {
            $this->cart[$postId]['quantity']--;
        }

        $this->persistCart();
    }

    public function remove(int $postId): void
    {
        $this->cart = session('cart', []);
        abort_unless(isset($this->cart[$postId]), 404);

        unset($this->cart[$postId]);
        $this->persistCart();
    }

    private function persistCart(?string $notice = null): void
    {
        session()->put('cart', $this->cart);
        $this->notice = $notice;
        $this->dispatch('cart-updated', count: count($this->cart));
    }

    public function render()
    {
        return view('livewire.cart-manager');
    }
}
