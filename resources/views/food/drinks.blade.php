@extends('layouts.navbar')
@section('content')
<div class="min-h-screen bg-slate-50 px-3 py-6 sm:px-6 lg:px-8"><div class="mx-auto max-w-7xl">
<div class="mb-6 flex items-end justify-between"><div><p class="text-xs font-black uppercase tracking-[.16em] text-orange-500">Fresh from FoodStore</p><h1 class="mt-1 text-2xl font-black text-gray-900">Drinks picks</h1></div><span class="text-sm font-bold text-gray-400">{{ $posts->total() }} items</span></div>
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
   
@foreach ($posts as $post)
  
<x-postscard :post="$post"/>
@endforeach
</div>
<div class="mt-6">{{ $posts->links() }}</div></div></div>
@endsection