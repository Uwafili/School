<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @keyframes fadeInOut {
            0%, 100% { opacity: 0; }
            50% { opacity: 1; }
        }
        .fade-in-out {
            animation: fadeInOut 2s ease-in-out infinite;
        }
    </style>
</head>
<nav x-data="{ open: false, darkMode: false }" :class="darkMode ? 'bg-gray-900 text-white' : 'bg-white text-gray-800'" class="shadow-md transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center gap-4">
                <a href="{{ route('home') }}" class="text-xl sm:text-2xl font-bold text-yellow-500">FoodStore</a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex space-x-2 items-center">
                @auth
                    <a href="{{ route('home') }}" :class="darkMode ? 'hover:text-yellow-400' : 'hover:text-yellow-500'" class="px-3 py-2 rounded transition text-sm">Home</a>
                    
                    <!-- Cart with Badge -->
                    @php
                        $cartItems = session()->get('cart', []);
                        $cartCount = count($cartItems);
                    @endphp
                    <div class="relative">
                        <a href="{{ route('cart') }}" :class="darkMode ? 'hover:text-yellow-400' : 'hover:text-yellow-500'" class="px-3 py-2 rounded transition flex items-center gap-1 text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Cart
                        </a>
                        @if($cartCount > 0)
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $cartCount }}</span>
                        @endif
                    </div>

                    <a href="{{ route('about') }}" :class="darkMode ? 'hover:text-yellow-400' : 'hover:text-yellow-500'" class="px-3 py-2 rounded transition text-sm">About</a>
                @endauth

                @guest
                    <a href="{{ route('home') }}" :class="darkMode ? 'hover:text-yellow-400' : 'hover:text-yellow-500'" class="px-3 py-2 rounded transition text-sm">Home</a>
                    <a href="{{ route('register') }}" :class="darkMode ? 'hover:text-yellow-400' : 'hover:text-yellow-500'" class="px-3 py-2 rounded transition text-sm">Register</a>
                    <a href="{{ route('login') }}" :class="darkMode ? 'hover:text-yellow-400' : 'hover:text-yellow-500'" class="px-3 py-2 rounded transition text-sm">Login</a>
                @endguest

                <!-- Shop Owner Dashboard -->
                @php
                    $userStore = \App\Models\Store::where('user_id', Auth::id())->first();
                    $userRider = \App\Models\Rider::where('user_id', Auth::id())->first();
                @endphp
                @if($userStore && $userStore->status === 'approved')
                    <a href="{{ route('storedashboard') }}" :class="darkMode ? 'bg-orange-600 hover:bg-orange-700' : 'bg-orange-500 hover:bg-orange-600'" class="text-white px-3 py-2 rounded-full font-bold flex items-center gap-1 text-sm transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/></svg>
                        Store
                    </a>
                @endif

                <!-- Rider Dashboard -->
                @if($userRider && $userRider->status === 'approved')
                    <a href="{{ route('rider.dashboard') }}" :class="darkMode ? 'bg-blue-600 hover:bg-blue-700' : 'bg-blue-500 hover:bg-blue-600'" class="text-white px-3 py-2 rounded-full font-bold flex items-center gap-1 text-sm transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.22.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm11 0c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>
                        Rider
                    </a>
                @endif

                <!-- Admin Panel -->
                @if(Auth::check() && Auth::user()->usertype === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-full bg-purple-600 hover:bg-purple-700 text-white font-bold text-sm transition">Admin</a>
                @endif

                <!-- Dashboard Dropdown -->
                @auth
                <div class="relative group">
                    <button class="flex items-center gap-1 px-3 py-2 rounded transition text-sm" :class="darkMode ? 'hover:text-yellow-400' : 'hover:text-yellow-500'">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 13h2v8H3zm4-8h2v16H7zm4-2h2v18h-2zm4-2h2v20h-2zm4 4h2v16h-2z"/></svg>
                        Dashboard
                    </button>
                    <div class="absolute right-0 mt-0 w-44 bg-white dark:bg-gray-800 rounded-lg shadow-lg z-20 hidden group-hover:block">
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-yellow-100 dark:hover:bg-gray-700 rounded-t-lg text-sm">My Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-yellow-100 dark:hover:bg-gray-700 rounded-b-lg text-sm">Logout</button>
                        </form>
                    </div>
                </div>
                @endauth
            </div>

            <!-- Right Side Icons -->
            <div class="flex items-center gap-2">
                <!-- Dark/Light Mode Button -->
                <button @click="darkMode = !darkMode" class="p-2 rounded transition" :class="darkMode ? 'bg-gray-700 text-yellow-400' : 'bg-yellow-100 text-yellow-600'">
                    <span x-show="!darkMode"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m8.66-8.66l-.71.71M4.05 4.05l-.71.71m16.97 0l-.71-.71M4.05 19.95l-.71-.71M21 12h1M3 12H2"/></svg></span>
                    <span x-show="darkMode"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg></span>
                </button>

                <!-- Mobile Menu Button -->
                <button @click="open = !open" class="lg:hidden p-2 rounded transition" :class="darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100'">
                    <svg :class="open ? 'hidden' : 'block'" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg :class="open ? 'block' : 'hidden'" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open" @click.outside="open = false" class="lg:hidden bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 transition-all">
        <div class="max-w-7xl mx-auto px-2 py-4 space-y-2">
            @auth
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded hover:bg-yellow-100 dark:hover:bg-gray-700 text-sm">🏠 Home</a>
                
                <!-- Mobile Cart -->
                @php
                    $cartItems = session()->get('cart', []);
                    $cartCount = count($cartItems);
                @endphp
                <a href="{{ route('cart') }}" class="block px-3 py-2 rounded hover:bg-yellow-100 dark:hover:bg-gray-700 text-sm relative">
                    🛒 Cart @if($cartCount > 0)<span class="ml-1 text-red-500 font-bold">({{ $cartCount }})</span>@endif
                </a>

                <a href="{{ route('about') }}" class="block px-3 py-2 rounded hover:bg-yellow-100 dark:hover:bg-gray-700 text-sm">ℹ️ About</a>

                <!-- Mobile Store Dashboard -->
                @if($userStore && $userStore->status === 'approved')
                    <a href="{{ route('storedashboard') }}" class="block px-3 py-2 rounded bg-orange-500 hover:bg-orange-600 text-white font-semibold text-sm">🏪 Store Dashboard</a>
                @endif

                <!-- Mobile Rider Dashboard -->
                @if($userRider && $userRider->status === 'approved')
                    <a href="{{ route('rider.dashboard') }}" class="block px-3 py-2 rounded bg-blue-500 hover:bg-blue-600 text-white font-semibold text-sm">🏍️ Rider Dashboard</a>
                @endif

                <!-- Mobile Admin Panel -->
                @if(Auth::user()->usertype === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded bg-purple-600 hover:bg-purple-700 text-white font-semibold text-sm">👨‍💼 Admin Panel</a>
                @endif

                <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>

                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded hover:bg-yellow-100 dark:hover:bg-gray-700 text-sm">📊 My Dashboard</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded hover:bg-yellow-100 dark:hover:bg-gray-700 text-sm text-red-600 dark:text-red-400 font-semibold">🚪 Logout</button>
                </form>
            @endauth

            @guest
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded hover:bg-yellow-100 dark:hover:bg-gray-700 text-sm">🏠 Home</a>
                <a href="{{ route('register') }}" class="block px-3 py-2 rounded bg-green-500 hover:bg-green-600 text-white font-semibold text-sm">✏️ Register</a>
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded bg-blue-500 hover:bg-blue-600 text-white font-semibold text-sm">🔐 Login</a>
            @endguest
        </div>
    </div>
</nav>
<!-- Page Loader -->
<div id="page-loader" class="fixed inset-0 bg-white flex items-center justify-center z-50 opacity-0 transition-opacity duration-500">
    <img src="{{ asset('asset/logo.png') }}" class="fade-in-out w-32 h-32" alt="Loading FoodStore">
</div>
<!-- filepath: c:\Users\Bishop\School\resources\views\layouts\navbar.blade.php -->

<div x-data="{ openFood: false }" class="fixed bottom-6 right-6 z-50">
    <div class="relative">
        <button @click="openFood = !openFood"
            class="bg-yellow-500 hover:bg-yellow-600 text-white rounded-full shadow-lg p-4 flex items-center justify-center transition duration-300 focus:outline-none">
            <svg class="w-7 h-7 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <span class="font-semibold">Categories</span>
        </button>
        <div x-show="openFood" @click.away="openFood = false"
            class="mt-2 w-56 bg-white rounded-lg shadow-lg py-2 absolute right-0 bottom-16"
            x-transition>
            <a href="{{ route('food.pizza') }}" class="flex items-center w-full text-left px-4 py-2 text-gray-700 hover:bg-yellow-100">
                <img src="{{ ('asset/generated.jpg') }}" alt="Pizza" class="w-5 h-5 mr-2"> Pizza
            </a> 
            <a href="{{ route('food.burger') }}" class="flex items-center w-full text-left px-4 py-2 text-gray-700 hover:bg-yellow-100">
                <img src="{{ ('asset/front.avif') }}" alt="Burger" class="w-5 h-5 mr-2"> Burger
            </a>
            <a href="{{ route('food.salad') }}" class="flex items-center w-full text-left px-4 py-2 text-gray-700 hover:bg-yellow-100">
                <img src="{{ ('asset/brown.jpg') }}" alt="Salad" class="w-5 h-5 mr-2"> Salad
            </a>
            <a href="{{ route('food.drinks') }}" class="flex items-center w-full text-left px-4 py-2 text-gray-700 hover:bg-yellow-100">
                <img src="{{('asset/drink.webp') }}" alt="Drinks" class="w-5 h-5 mr-2"> Drinks
            </a>
        </div>
    </div>
</div>
<!-- Floating message icons (yellow) -->





@yield('content')

@include('layouts.footer')

<script src="//unpkg.com/alpinejs" defer>
 .floating-label { display: none; }
 
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('page-loader').style.opacity = '1';
});
window.addEventListener('load', function() {
    const loader = document.getElementById('page-loader');
    loader.style.opacity = '0';
    setTimeout(() => {
        loader.style.display = 'none';
    }, 500);
});
</script>