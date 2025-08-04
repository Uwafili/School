@extends('layouts.navbar')
@section('content')
<div class="container d-flex flex-wrap justify-content-center align-items-center" style="min-height: 80vh; gap: 2rem;">
    @if(isset($pizzas))
        @foreach($pizzas as $pizza)
        <div class="card shadow-lg mb-4" style="width: 22rem;">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-egg-fried" style="font-size: 3rem; color: #ffc107;"></i>
                </div>
                <h1 class="card-title mb-3">{{ $pizza['name'] }}</h1>
                <p class="card-text mb-4">{{ $pizza['description'] }}</p>
                        <p class="card-text mb-2"><strong>Amount:</strong> ${{ number_format($pizza['amount'], 2) }}</p>
            </div>
        </div>
        @endforeach
    @endif

    @if(isset($burgers))
        @foreach($burgers as $burger)
        <div class="card shadow-lg mb-4" style="width: 22rem;">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-hamburger" style="font-size: 3rem; color: #ff9800;"></i>
                </div>
                <h1 class="card-title mb-3">{{ $burger['name'] }}</h1>
                <p class="card-text mb-4">{{ $burger['description'] }}</p>
            </div>
        </div>
        @endforeach
    @endif

    @if(isset($salads))
        @foreach($salads as $salad)
        <div class="card shadow-lg mb-4" style="width: 22rem;">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-emoji-smile" style="font-size: 3rem; color: #4caf50;"></i>
                </div>
                <h1 class="card-title mb-3">{{ $salad['name'] }}</h1>
                <p class="card-text mb-4">{{ $salad['description'] }}</p>
            </div>
        </div>
        @endforeach
    @endif
</div>
@endsection