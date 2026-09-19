@extends('layouts.navbar')

@section('content')
<div class="min-h-screen bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto flex max-w-3xl flex-col overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-gray-100" style="min-height: 32rem;">
        <div class="flex items-center gap-3 border-b border-gray-100 p-4 sm:p-5">
            <a href="{{ route('dashboard') }}" class="text-xl text-gray-400 hover:text-yellow-600" aria-label="Back to dashboard">‹</a>
            <span class="grid h-11 w-11 place-items-center rounded-full bg-purple-100 font-black text-purple-700">{{ strtoupper(substr($contact->name, 0, 1)) }}</span>
            <div class="min-w-0"><h1 class="truncate font-black text-gray-900">{{ $contact->name }}</h1><p class="text-xs text-gray-500">Order #{{ $order->id }} · {{ ucfirst($order->status) }}</p></div>
        </div>
        <div class="flex-1 space-y-3 overflow-y-auto bg-slate-50 p-4 sm:p-6">
            @forelse($messages as $message)
                <div class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[85%] rounded-2xl px-4 py-3 text-sm {{ $message->sender_id === Auth::id() ? 'rounded-br-sm bg-yellow-400 text-gray-900' : 'rounded-bl-sm bg-white text-gray-700 shadow-sm' }}">
                        {{ $message->message }}
                        <span class="mt-1 block text-[10px] opacity-60">{{ $message->created_at->format('H:i') }}</span>
                    </div>
                </div>
            @empty
                <div class="flex h-full min-h-48 items-center justify-center text-center"><div><p class="font-black text-gray-700">Start the conversation</p><p class="mt-1 text-sm text-gray-500">Ask about your order or delivery.</p></div></div>
            @endforelse
        </div>
        <form method="POST" action="{{ route('chat.send', $order) }}" class="flex gap-2 border-t border-gray-100 p-4">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $contact->id }}">
            <input name="message" required maxlength="2000" placeholder="Write a message..." class="min-w-0 flex-1 rounded-xl border-gray-200 px-4 py-3 text-sm focus:border-yellow-500 focus:ring-yellow-300">
            <button type="submit" class="rounded-xl bg-gray-900 px-4 py-3 text-sm font-black text-white transition hover:bg-yellow-500 hover:text-gray-900">Send</button>
        </form>
    </div>
</div>
@endsection
