@extends('layouts.navbar')
@section('content')
<div class="grid gap-6 grid-cols-1 sm:grid-cols-8 lg:grid-cols-3">
   
    @foreach ($posts as $post)
      <x-postscard :post="$post"/>
    @endforeach
  </div>
<div class="mt-6">
    {{ $posts->links() }}
  </div>
@endsection