<div class="mx-auto flex max-w-3xl flex-col overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-gray-100" style="min-height: 32rem;">
    <div class="flex items-center gap-3 border-b border-gray-100 p-4 sm:p-5">
        <a href="{{ route('dashboard') }}" class="text-xl text-gray-400 hover:text-yellow-600" aria-label="Back to dashboard">‹</a>
        <span class="grid h-11 w-11 place-items-center rounded-full bg-purple-100 font-black text-purple-700">{{ strtoupper(substr($contact->name, 0, 1)) }}</span>
        <div class="min-w-0"><h1 class="truncate font-black text-gray-900">{{ $contact->name }}</h1><p class="text-xs text-gray-500">Order #{{ $order->id }} · {{ ucfirst(str_replace('_', ' ', $order->status)) }}</p></div>
    </div>
    <div wire:poll.3s="refreshMessages" x-data x-init="$nextTick(() => $el.scrollTop = $el.scrollHeight)" x-on:chat-message-sent.window="$nextTick(() => $el.scrollTop = $el.scrollHeight)" class="flex-1 space-y-3 overflow-y-auto bg-slate-50 p-4 sm:p-6">
        @forelse($messages as $message)
            <div wire:key="message-{{ $message->id }}" class="flex {{ (int) $message->sender_id === (int) Auth::id() ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[85%] rounded-2xl px-4 py-3 text-sm {{ (int) $message->sender_id === (int) Auth::id() ? 'rounded-br-sm bg-yellow-400 text-gray-900' : 'rounded-bl-sm bg-white text-gray-700 shadow-sm' }}">
                    <span class="mb-1 block text-[10px] font-bold opacity-60">{{ $message->sender->name ?? 'User' }}@if(in_array((int) $message->id, $unreadMessageIds, true)) · Unread @endif</span>
                    {{ $message->message }}
                    <time class="mt-1 block text-[10px] opacity-60">{{ $message->created_at->format('M j, Y · g:i A') }}</time>
                </div>
            </div>
        @empty
            <div class="flex h-full min-h-48 items-center justify-center text-center"><div><p class="font-black text-gray-700">Start the conversation</p><p class="mt-1 text-sm text-gray-500">Ask about your order or delivery.</p></div></div>
        @endforelse
    </div>
    <form wire:submit="send" class="flex gap-2 border-t border-gray-100 p-4">
        <div class="min-w-0 flex-1">
            <input wire:model="message" required maxlength="2000" placeholder="Write a message..." class="w-full rounded-xl border-gray-200 px-4 py-3 text-sm focus:border-yellow-500 focus:ring-yellow-300">
            @error('message')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
        </div>
        <button type="submit" wire:loading.attr="disabled" wire:target="send" class="rounded-xl bg-gray-900 px-4 py-3 text-sm font-black text-white transition hover:bg-yellow-500 hover:text-gray-900 disabled:opacity-60">
            <span wire:loading.remove wire:target="send">Send</span><span wire:loading wire:target="send">Sending…</span>
        </button>
    </form>
</div>
