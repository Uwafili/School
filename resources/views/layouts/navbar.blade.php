<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<nav x-data="{ open: false, darkMode: false }" :class="darkMode ? 'bg-gray-900 text-white' : 'bg-white text-gray-800'" class="shadow-md transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-yellow-500">FoodStore</a>
            </div>
            <!-- Links -->
            <div class="hidden md:flex space-x-6 items-center">
                @auth
               
                    
                <a href="{{ route('home') }}" :class="darkMode ? 'hover:text-yellow-400' : 'hover:text-yellow-500'" class="transition">Home</a>
                @endauth
                @guest
                    
                <a href="{{ route('register') }}" :class="darkMode ? 'hover:text-yellow-400' : 'hover:text-yellow-500'" class="transition">Register</a>
                <a href="{{ route('login') }}" :class="darkMode ? 'hover:text-yellow-400' : 'hover:text-yellow-500'" class="transition">Login</a>
               
                @endguest

                @auth
                    
                <a href="{{ route('about') }}" :class="darkMode ? 'hover:text-yellow-400' : 'hover:text-yellow-500'" class="transition">About</a> 
   
            @if(Auth::user()->usertype === 'admin')
           <a href="{{ route('admin.dashboard') }}" class="hover:text-yellow-500">Admin Panel</a>
           @endif


   
                {{-- <a href="{{ route('contact') }}" :class="darkMode ? 'hover:text-yellow-400' : 'hover:text-yellow-500'" class="transition">Contact</a>
                <a href="{{ route('service') }}" :class="darkMode ? 'hover:text-yellow-400' : 'hover:text-yellow-500'" class="transition">Services</a>--}}
                
                @endauth
                <!-- Dashboard Dropdown -->
              
                <div class="relative group">
                    <button class="flex items-center focus:outline-none" :class="darkMode ? 'hover:text-yellow-400' : 'hover:text-yellow-500'">
                        Dashboard
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="absolute right-0 mt-2 w-44 bg-white dark:bg-gray-800 rounded-md shadow-lg z-20 hidden group-hover:block">
                         <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-yellow-100 dark:hover:bg-gray-700">Main Dashboard</a>
                       {{-- <a href="{{ route('profile') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-yellow-100 dark:hover:bg-gray-700">Profile</a>
                        <a href="{{ route('settings') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-yellow-100 dark:hover:bg-gray-700">Settings</a>
                         --}}<form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-yellow-100 dark:hover:bg-gray-700">Logout</button>
                        </form>
                    </div>
                </div>
                <!-- Dark/Light Mode Button -->
                <button @click="darkMode = !darkMode" class="ml-4 p-2 rounded transition" :class="darkMode ? 'bg-gray-700 text-yellow-400' : 'bg-yellow-400 text-gray-800'">
                    <span x-show="!darkMode">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m8.66-8.66l-.71.71M4.05 4.05l-.71.71m16.97 0l-.71-.71M4.05 19.95l-.71-.71M21 12h1M3 12H2" />
                        </svg>
                    </span>

                    <span x-show="darkMode">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                        </svg>
                    </span>
                </button>
            </div>
            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button @click="open = !open" class="text-2xl focus:outline-none">
                    <svg :class="open ? 'hidden' : 'block'" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg :class="open ? 'block' : 'hidden'" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <!-- Dark/Light Mode Button for mobile -->
                <button @click="darkMode = !darkMode" class="ml-4 p-2 rounded transition" :class="darkMode ? 'bg-gray-700 text-yellow-400' : 'bg-yellow-400 text-gray-800'">
                    <span x-show="!darkMode">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m8.66-8.66l-.71.71M4.05 4.05l-.71.71m16.97 0l-.71-.71M4.05 19.95l-.71-.71M21 12h1M3 12H2" />
                        </svg>
                    </span>
                    <span x-show="darkMode">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                        </svg>
                    </span>
                </button>
            </div>
        </div>
    </div>
    <!-- Mobile Menu -->
    <div x-show="open" class="md:hidden bg-white dark:bg-gray-900 px-4 pb-4 space-y-2 transition">
        <a href="{{ route('home') }}" class="block py-2 border-b border-gray-200 dark:border-gray-700 hover:text-yellow-500 dark:hover:text-yellow-400">Home</a>
      
        @guest
           <a href="{{ route('register') }}" :class="darkMode ? 'hover:text-yellow-400' : 'hover:text-yellow-500'" class="transition">Register</a>
           <a href="{{ route('login') }}" :class="darkMode ? 'hover:text-yellow-400' : 'hover:text-yellow-500'" class="transition">Login</a>
               
           @endguest
           <a href="{{ route('about') }}" class="block py-2 border-b border-gray-200 dark:border-gray-700 hover:text-yellow-500 dark:hover:text-yellow-400">About</a> 
        {{-- <a href="{{ route('contact') }}" class="block py-2 border-b border-gray-200 dark:border-gray-700 hover:text-yellow-500 dark:hover:text-yellow-400">Contact</a>
        <a href="{{ route('service') }}" class="block py-2 border-b border-gray-200 dark:border-gray-700 hover:text-yellow-500 dark:hover:text-yellow-400">Services</a>--}}
        <div class="py-2">
            <span class="block text-gray-500 dark:text-gray-300 font-semibold mb-1">Dashboard</span>
            <a href="{{ route('dashboard') }}" class="block pl-4 py-1 hover:text-yellow-500 dark:hover:text-yellow-400">Main Dashboard</a>
           {{--  <a href="{{ route('profile') }}" class="block pl-4 py-1 hover:text-yellow-500 dark:hover:text-yellow-400">Profile</a>
            <a href="{{ route('settings') }}" class="block pl-4 py-1 hover:text-yellow-500 dark:hover:text-yellow-400">Settings</a> --}}
            <form method="POST" action="{{ route('logout') }}" class="block pl-4 py-1">
                @csrf
                <button type="submit" class="text-left w-full hover:text-yellow-500 dark:hover:text-yellow-400">Logout</button>
            </form>
        </div>
    </div>

</nav>
<!-- filepath: c:\Users\Bishop\School\resources\views\layouts\navbar.blade.php -->
<!-- Replace the floating WhatsApp icon with a floating "Select Food" dropdown button -->

<!-- filepath: c:\Users\Bishop\School\resources\views\layouts\navbar.blade.php -->
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
                <img src="{{ ('asset/pizza.png') }}" alt="Pizza" class="w-5 h-5 mr-2"> Pizza
            </a> 
            <a href="{{ route('food.burger') }}" class="flex items-center w-full text-left px-4 py-2 text-gray-700 hover:bg-yellow-100">
                <img src="{{ asset('icons/burger.png') }}" alt="Burger" class="w-5 h-5 mr-2"> Burger
            </a>
            <a href="{{ route('food.salad') }}" class="flex items-center w-full text-left px-4 py-2 text-gray-700 hover:bg-yellow-100">
                <img src="{{ asset('icons/salad.png') }}" alt="Salad" class="w-5 h-5 mr-2"> Salad
            </a>
            <a href="{{ route('food.drinks') }}" class="flex items-center w-full text-left px-4 py-2 text-gray-700 hover:bg-yellow-100">
                <img src="{{ asset('icons/drink.png') }}" alt="Drinks" class="w-5 h-5 mr-2"> Drinks
            </a>
        </div>
    </div>
</div>
@yield('content')

<!-- filepath: c:\Users\Bishop\School\resources\views\layouts\navbar.blade.php -->
<footer class="bg-gray-900 text-gray-200 mt-16">
    <div class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Logo & About -->
        <div>
            <div class="flex items-center mb-4">
                <img src="{{ asset('asset/logo.png') }}" alt="FoodStore Logo" class="w-10 h-10 mr-2 rounded-full bg-yellow-400">
                <span class="text-2xl font-bold text-yellow-400">FoodStore</span>
            </div>
            <p class="text-gray-400">
                Delicious meals, delivered fresh to your door. Fast, reliable, and always tasty!
            </p>
        </div>
        <!-- Quick Links -->
        <div>
            <h4 class="text-lg font-semibold text-yellow-400 mb-3">Quick Links</h4>
            <ul class="space-y-2">
                <li><a href="{{ route('home') }}" class="hover:text-yellow-400 transition">Home</a></li>
                <li><a href="{{ route('about') }}" class="hover:text-yellow-400 transition">About</a></li>
                <li><a href="{{ route('register') }}" class="hover:text-yellow-400 transition">Register</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-yellow-400 transition">Login</a></li>
            </ul>
        </div>
        <!-- Contact & Social -->
        <div>
            <h4 class="text-lg font-semibold text-yellow-400 mb-3">Contact Us</h4>
            <p class="text-gray-400 mb-2">Email: <a href="mailto:support@foodstore.com" class="hover:text-yellow-400">support@foodstore.com</a></p>
            <p class="text-gray-400 mb-4">Phone: <a href="tel:+1234567890" class="hover:text-yellow-400">+1 234 567 890</a></p>
            <div class="flex space-x-4">
                <a href="#" class="hover:text-yellow-400" title="Facebook">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M22 12a10 10 0 10-11.5 9.95v-7.05h-2.1V12h2.1v-1.6c0-2.07 1.23-3.22 3.12-3.22.9 0 1.84.16 1.84.16v2.02h-1.04c-1.03 0-1.35.64-1.35 1.3V12h2.3l-.37 2.9h-1.93v7.05A10 10 0 0022 12z"/>
                    </svg>
                </a>
                <a href="#" class="hover:text-yellow-400" title="Twitter">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M22.46 6c-.77.35-1.6.58-2.47.69a4.3 4.3 0 001.88-2.37 8.59 8.59 0 01-2.72 1.04 4.28 4.28 0 00-7.29 3.9A12.13 12.13 0 013 5.16a4.28 4.28 0 001.32 5.71c-.7-.02-1.36-.21-1.94-.53v.05a4.28 4.28 0 003.44 4.19c-.33.09-.68.14-1.04.14-.25 0-.5-.02-.74-.07a4.29 4.29 0 004 2.98A8.6 8.6 0 012 19.54a12.14 12.14 0 006.56 1.92c7.88 0 12.2-6.53 12.2-12.2 0-.19 0-.37-.01-.56A8.72 8.72 0 0024 4.59a8.49 8.49 0 01-2.54.7z"/>
                    </svg>
                </a>
                <a href="#" class="hover:text-yellow-400" title="Instagram">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7.75 2h8.5A5.75 5.75 0 0122 7.75v8.5A5.75 5.75 0 0116.25 22h-8.5A5.75 5.75 0 012 16.25v-8.5A5.75 5.75 0 017.75 2zm0 1.5A4.25 4.25 0 003.5 7.75v8.5A4.25 4.25 0 007.75 20.5h8.5a4.25 4.25 0 004.25-4.25v-8.5A4.25 4.25 0 0016.25 3.5zm4.25 2.25a5.25 5.25 0 110 10.5 5.25 5.25 0 010-10.5zm0 1.5a3.75 3.75 0 100 7.5 3.75 3.75 0 000-7.5zm5.25 1.25a1 1 0 110 2 1 1 0 010-2z"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    <div class="border-t border-gray-800 mt-8 pt-4 text-center text-gray-500 text-sm">
        &copy; {{ date('Y') }} FoodStore. All rights reserved.
    </div>
</footer>

<script src="//unpkg.com/alpinejs" defer></script>