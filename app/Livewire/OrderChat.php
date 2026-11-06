<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderNotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Component;

class OrderChat extends Component
{
    #[Locked]
    public int $orderId;

    #[Locked]
    public int $contactId;

    #[Locked]
    public array $unreadMessageIds = [];

    public string $message = '';

    public function mount(Order $order, User $contact): void
    {
        $this->orderId = $order->id;
        $this->contactId = $contact->id;
        $this->authorizeConversation();
        $this->unreadMessageIds = Message::where('order_id', $this->orderId)
            ->where('sender_id', $this->contactId)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
        $this->markMessagesRead();
    }

    public function send(OrderNotificationService $notifications): void
    {
        $this->authorizeConversation();
        $validated = $this->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);
        $order = Order::with('store')->findOrFail($this->orderId);

        DB::transaction(function () use ($order, $validated, $notifications): void {
            Message::create([
                'order_id' => $order->id,
                'sender_id' => Auth::id(),
                'receiver_id' => $this->contactId,
                'message' => $validated['message'],
            ]);

            $senderId = (int) Auth::id();
            $isCustomerMessage = $senderId === (int) $order->customer_id;
            $isStoreOwnerMessage = $senderId === (int) $order->store?->user_id;
            $notifications->sendToUser(
                $this->contactId,
                $order,
                $isCustomerMessage ? 'New customer message' : ($isStoreOwnerMessage ? 'New message from store owner' : 'New order message'),
                'You received a new message about order #'.$order->id.'.',
                $isCustomerMessage ? 'customer_message' : ($isStoreOwnerMessage ? 'store_owner_message' : 'order_message')
            );
        });

        $this->reset('message');
        $this->dispatch('chat-message-sent');
    }

    public function refreshMessages(): void
    {
        $this->authorizeConversation();
        $this->unreadMessageIds = [];
        $this->markMessagesRead();
    }

    private function authorizeConversation(): void
    {
        $order = Order::with(['store', 'rider'])->findOrFail($this->orderId);
        $participants = array_filter([
            (int) $order->customer_id,
            (int) $order->store?->user_id,
            (int) $order->rider?->user_id,
        ]);

        abort_unless(
            in_array((int) Auth::id(), $participants, true)
                && in_array($this->contactId, $participants, true)
                && $this->contactId !== (int) Auth::id(),
            403
        );
    }

    private function markMessagesRead(): void
    {
        Message::where('order_id', $this->orderId)
            ->where('sender_id', $this->contactId)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        $this->authorizeConversation();
        $messages = Message::where('order_id', $this->orderId)
            ->where(function ($query) {
                $query->where(function ($conversation) {
                    $conversation->where('sender_id', Auth::id())->where('receiver_id', $this->contactId);
                })->orWhere(function ($conversation) {
                    $conversation->where('sender_id', $this->contactId)->where('receiver_id', Auth::id());
                });
            })
            ->with('sender')
            ->oldest()
            ->get();

        return view('livewire.order-chat', [
            'messages' => $messages,
            'order' => Order::findOrFail($this->orderId),
            'contact' => User::findOrFail($this->contactId),
        ]);
    }
}
