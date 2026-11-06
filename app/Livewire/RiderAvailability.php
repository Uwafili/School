<?php

namespace App\Livewire;

use App\Models\Rider;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class RiderAvailability extends Component
{
    #[Locked]
    public int $riderId;

    public function mount(): void
    {
        $this->riderId = Rider::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->where('is_active', true)
            ->value('id') ?? abort(404);
    }

    public function toggle(): void
    {
        $rider = Rider::where('id', $this->riderId)
            ->where('user_id', Auth::id())
            ->where('status', 'approved')
            ->where('is_active', true)
            ->firstOrFail();
        $rider->update(['is_online' => ! $rider->is_online]);
        $this->dispatch('toast', type: 'success', message: $rider->is_online
            ? 'You are now online and can receive jobs.'
            : 'You are now offline.');
    }

    public function render()
    {
        $rider = Rider::where('id', $this->riderId)
            ->where('user_id', Auth::id())
            ->where('status', 'approved')
            ->where('is_active', true)
            ->firstOrFail();

        return view('livewire.rider-availability', compact('rider'));
    }
}
