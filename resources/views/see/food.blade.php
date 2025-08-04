@extends('layouts.navbar')
@section('content')
<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>{{ $description }}</p>

    <a href="/food/pizza">Pizza</a> |
    <a href="/food/burger">Burger</a> |
    <a href="/food/salad">Salad</a>
</body>
</html>
@endsection