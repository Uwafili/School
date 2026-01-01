<!-- filepath: c:\Users\Bishop\School\resources\views\about.blade.php -->
@extends('layouts.navbar')

@section('content')
<header class="relative h-64 flex items-center justify-center" style="background-image: url('{{ asset('asset/about-bg.jpg') }}'); background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-black bg-opacity-60"></div>
    <div class="relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-yellow-400 drop-shadow-lg">About FoodStore</h1>
        <p class="mt-4 text-lg md:text-2xl text-white drop-shadow">Your favorite meals, delivered with care and speed.</p>
    </div>
</header>

<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4">
        <div class="mb-12 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Who We Are</h2>
            <p class="text-gray-600 text-lg">
                FoodStore is a passionate team of food lovers, chefs, and tech enthusiasts dedicated to bringing the best meals from your favorite restaurants right to your doorstep. We believe in quality, freshness, and a seamless delivery experience.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
            <div>
                <img src="{{ asset('asset/private.jpg') }}" alt="Our Team" class="rounded-lg shadow-lg w-full object-cover h-64">
            </div>
            <div>
                <h3 class="text-2xl font-semibold text-yellow-600 mb-2">Our Mission</h3>
                <p class="text-gray-700 mb-4">
                    To connect people with the food they love, while supporting local restaurants and delivery partners. We strive to make every meal a delightful experience, from order to delivery.
                </p>
                <ul class="list-disc list-inside text-gray-600 space-y-2">
                    <li>Fresh and high-quality ingredients</li>
                    <li>Fast and reliable delivery</li>
                    <li>24/7 customer support</li>
                    <li>Empowering local businesses</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-yellow-50">
    <div class="max-w-4xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Meet the Team</h2>
        <div class="flex flex-wrap justify-center gap-8">
            <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col items-center w-64">
                <img src="{{ asset('asset/cook1.jpg') }}" alt="Sarah" class="w-20 h-20 object-cover rounded-full mb-4">
                <h4 class="text-xl font-semibold text-gray-800">Sarah Kim</h4>
                <span class="text-yellow-600 font-medium mb-2">CEO & Co-Founder</span>
                <p class="text-gray-600 text-center">Visionary leader and food enthusiast, Sarah brings passion and innovation to FoodStore.</p>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col items-center w-64">
                <img src="{{ asset('asset/cook3.jpg') }}" alt="James" class="w-20 h-20 object-cover rounded-full mb-4">
                <h4 class="text-xl font-semibold text-gray-800">James Lee</h4>
                <span class="text-yellow-600 font-medium mb-2">Head of Operations</span>
                <p class="text-gray-600 text-center">James ensures every order is delivered on time and every customer is happy.</p>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col items-center w-64">
                <img src="{{ asset('asset/cook2.jpg') }}" alt="Maria" class="w-20 h-20 object-cover rounded-full mb-4">
                <h4 class="text-xl font-semibold text-gray-800">Maria Perez</h4>
                <span class="text-yellow-600 font-medium mb-2">Customer Success</span>
                <p class="text-gray-600 text-center">Maria is always ready to help and support our valued customers and partners.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Join Our Journey</h2>
        <p class="text-gray-600 mb-6">
            Whether you’re a food lover, a restaurant owner, or a delivery partner, FoodStore welcomes you to be part of our growing family. Let’s make every meal memorable—together!
        </p>
        <a href="{{ route('register') }}" class="inline-block bg-yellow-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-yellow-600 transition">Get Started</a>
    </div>
</section>
@endsection