<?php

namespace App\Livewire;

use App\Models\ApprovalNotification;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class StoreApprovalList extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = 'all';

    public string $dateFrom = '';

    public string $dateTo = '';

    public function mount(): void
    {
        $this->ensureAdmin();
        $this->search = (string) request()->query('search', '');
        $this->status = (string) request()->query('status', 'all');
        $this->dateFrom = (string) request()->query('from', '');
        $this->dateTo = (string) request()->query('to', '');
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status', 'dateFrom', 'dateTo'], true)) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'dateFrom', 'dateTo']);
        $this->status = 'all';
        $this->resetPage();
    }

    public function approve(int $storeId): void
    {
        $this->ensureAdmin();
        $store = Store::findOrFail($storeId);

        DB::transaction(function () use ($store): void {
            $store->update(['status' => 'approved']);
            ApprovalNotification::create([
                'user_id' => $store->user_id,
                'role' => 'store',
                'title' => 'Store approved',
                'message' => "Your store, {$store->stores}, has been approved. You can now manage it and start receiving orders.",
                'status' => 'approved',
            ]);
        });

        $this->dispatch('toast', type: 'success', message: "Store '{$store->stores}' approved.");
    }

    public function reject(int $storeId): void
    {
        $this->ensureAdmin();
        $store = Store::findOrFail($storeId);

        DB::transaction(function () use ($store): void {
            $store->update(['status' => 'rejected']);
            ApprovalNotification::create([
                'user_id' => $store->user_id,
                'role' => 'store',
                'title' => 'Store application update',
                'message' => "Your store, {$store->stores}, needs attention. Please review your application details and try again.",
                'status' => 'rejected',
            ]);
        });

        $this->dispatch('toast', type: 'success', message: "Store '{$store->stores}' rejected.");
    }

    private function ensureAdmin(): void
    {
        abort_unless(Auth::user()?->usertype === 'admin', 403);
    }

    public function render()
    {
        $this->ensureAdmin();
        $status = in_array($this->status, ['all', 'pending', 'approved', 'rejected'], true) ? $this->status : 'all';
        $search = trim($this->search);
        $stores = Store::with('user')
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('stores', 'like', "%{$search}%")
                    ->orWhere('owner', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->when($this->dateFrom !== '', fn ($query) => $query->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo !== '', fn ($query) => $query->whereDate('created_at', '<=', $this->dateTo))
            ->latest()
            ->paginate(12);

        return view('livewire.store-approval-list', [
            'stores' => $stores,
            'totalCount' => Store::count(),
            'pendingCount' => Store::where('status', 'pending')->count(),
            'approvedCount' => Store::where('status', 'approved')->count(),
            'rejectedCount' => Store::where('status', 'rejected')->count(),
        ]);
    }
}
