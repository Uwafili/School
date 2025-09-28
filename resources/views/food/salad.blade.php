@extends('layouts.navbar')
@section('content')

<div class="container mx-auto my-8">
    @foreach ($posts as $post)
        <x-postscard :post="$post"/>
    @endforeach
</div>

@endsection