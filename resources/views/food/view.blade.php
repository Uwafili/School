@extends('layouts.navbar')
@section('content')
<div class="container mx-auto my-8">
    @foreach ($posts as $post)
        
    <div class="flex flex-wrap justify-center gap-6">
        
        <!-- Card 1 -->
        <div class="bg-white shadow-lg rounded-2xl w-72 overflow-hidden">
      <img src="https://source.unsplash.com/400x300/?burger" alt="Burger" class="w-full h-48 object-cover">
      <div class="p-4">
        <h5 class="text-lg font-semibold">{{$post->title}}</h5>
        <p class="text-gray-600 text-sm mt-1">{{$post->body}}</p>
             <p class="text-green-600 font-bold mt-2">{{$post->price}}</p>
        <button class="mt-3 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">view more</button>
      </div>



      
    </div>


               
    
  </div>
    @endforeach
</div>
@endsection