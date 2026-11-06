<?php

namespace App\Livewire;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ProductCatalog extends Component
{
    use WithFileUploads, WithPagination;

    public string $title = '';

    public string $description = '';

    public string $price = '';

    public string $category = '';

    public $image = null;

    public ?int $editingId = null;

    public function save(): void
    {
        $this->ensureAdmin();
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'category' => ['required', 'in:pizza,burger,salad,drinks'],
            'image' => ['nullable', 'file', 'mimes:jpeg,jpg,png,avif', 'max:3000'],
        ]);

        if ($this->editingId) {
            $post = Post::findOrFail($this->editingId);
            Gate::authorize('modify', $post);
            $oldImage = $post->image;
            if ($this->image) {
                $validated['image'] = $this->image->store('out_imges', 'public');
            } else {
                unset($validated['image']);
            }

            $post->update($validated);
            if (! empty($validated['image']) && $oldImage) {
                Storage::disk('public')->delete($oldImage);
            }

            $message = 'Product updated successfully.';
        } else {
            $validated['user_id'] = Auth::id();
            if ($this->image) {
                $validated['image'] = $this->image->store('out_imges', 'public');
            } else {
                unset($validated['image']);
            }

            Post::create($validated);
            $message = 'Product created successfully.';
        }

        $this->resetForm();
        $this->dispatch('toast', type: 'success', message: $message);
    }

    public function edit(int $postId): void
    {
        $this->ensureAdmin();
        $post = Post::findOrFail($postId);
        Gate::authorize('modify', $post);

        $this->editingId = $post->id;
        $this->title = $post->title;
        $this->description = $post->description;
        $this->price = (string) $post->price;
        $this->category = $post->category;
        $this->reset('image');
        $this->resetValidation();
    }

    public function delete(int $postId): void
    {
        $this->ensureAdmin();
        $post = Post::findOrFail($postId);
        Gate::authorize('modify', $post);

        $image = $post->image;
        $post->delete();
        if ($image) {
            Storage::disk('public')->delete($image);
        }

        $this->dispatch('toast', type: 'success', message: 'Product deleted.');
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['title', 'description', 'price', 'category', 'image', 'editingId']);
        $this->resetValidation();
    }

    private function ensureAdmin(): void
    {
        abort_unless(Auth::user()?->usertype === 'admin', 403);
    }

    public function render()
    {
        $this->ensureAdmin();

        return view('livewire.product-catalog', [
            'posts' => Post::with('user')->latest()->paginate(12),
        ]);
    }
}
