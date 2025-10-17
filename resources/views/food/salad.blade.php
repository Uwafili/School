@extends('layouts.navbar')
@section('content')

{{-- <div class="container mx-auto my-8">
    @foreach ($posts as $post)
        <x-postscard :post="$post"/>
    @endforeach
</div> --}}


<div class="grid gap-6 grid-cols-1 sm:grid-cols-8 lg:grid-cols-3">
   @foreach ($posts as $post)
       <x-postscard :post="$post"/>
   @endforeach
</div>

@endsection