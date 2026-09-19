<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FoodStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            color-scheme: light;
            --page-bg: #ffffff;
            --surface: #ffffff;
            --surface-muted: #f8fafc;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
        }

        html.dark {
            color-scheme: dark;
        }

        body {
            background: var(--page-bg);
            color: var(--text-main);
            transition: background-color .25s ease, color .25s ease;
        }

        body.theme-dark {
            --page-bg: #111827;
            --surface: #1f2937;
            --surface-muted: #374151;
            --text-main: #f9fafb;
            --text-muted: #d1d5db;
            --border: #4b5563;
        }

        body.theme-dark .bg-white,
        body.theme-dark .bg-gray-50,
        body.theme-dark .bg-gray-100,
        body.theme-dark .bg-stone-50,
        body.theme-dark .bg-stone-100 {
            background-color: var(--surface) !important;
        }

        body.theme-dark .text-gray-800,
        body.theme-dark .text-gray-700,
        body.theme-dark .text-stone-900,
        body.theme-dark .text-stone-700,
        body.theme-dark .text-gray-600,
        body.theme-dark .text-stone-600,
        body.theme-dark .text-gray-500,
        body.theme-dark .text-stone-500 {
            color: var(--text-muted) !important;
        }

        body.theme-dark .border-gray-200,
        body.theme-dark .border-gray-300,
        body.theme-dark .border-stone-200,
        body.theme-dark .border-gray-100 {
            border-color: var(--border) !important;
        }

        body.theme-dark input,
        body.theme-dark select,
        body.theme-dark textarea {
            background-color: var(--surface-muted);
            border-color: var(--border);
            color: var(--text-main);
        }

        body.theme-dark input::placeholder,
        body.theme-dark textarea::placeholder {
            color: #9ca3af;
        }

        body.theme-dark .auth-page {
            background: var(--page-bg);
        }

        body.theme-dark .auth-page>div {
            background-color: var(--surface);
        }

        body.theme-dark .auth-social {
            border-color: var(--border);
            color: var(--text-main);
        }

        body.theme-dark .auth-social:hover {
            background-color: #374151;
        }

        body.theme-dark #page-loader {
            background-color: var(--page-bg) !important;
        }

        body.theme-dark .bg-yellow-100 {
            background-color: #713f12 !important;
        }

        .site-nav {
            position: sticky;
            top: 0;
            z-index: 40;
            border-bottom: 1px solid rgba(229, 231, 235, .8);
            background: rgba(255, 255, 255, .9);
            backdrop-filter: blur(18px);
        }

        body.theme-dark .site-nav {
            border-bottom-color: rgba(75, 85, 99, .8);
            background: rgba(17, 24, 39, .92);
        }

        .brand-mark {
            display: grid;
            width: 2.55rem;
            height: 2.55rem;
            place-items: center;
            overflow: hidden;
            border-radius: .85rem;
            background: #facc15;
            box-shadow: 0 8px 18px rgba(234, 179, 8, .24);
        }

        .brand-mark img {
            width: 2.1rem;
            height: 2.1rem;
            object-fit: contain;
        }

        .nav-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            border-radius: .75rem;
            padding: .55rem .75rem;
            color: #4b5563;
            font-size: .875rem;
            font-weight: 600;
            transition: color .2s ease, background-color .2s ease, transform .2s ease;
        }

        .nav-link:hover {
            background: #fef3c7;
            color: #a16207;
            transform: translateY(-1px);
        }

        body.theme-dark .nav-link {
            color: #d1d5db;
        }

        body.theme-dark .nav-link:hover {
            background: #374151;
            color: #fde68a;
        }

        .nav-pill {
            border-radius: 9999px;
            padding: .6rem 1rem;
            font-size: .8rem;
            font-weight: 800;
            transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease;
        }

        .nav-pill:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(15, 23, 42, .12);
        }

        .mobile-menu {
            border-top: 1px solid var(--border);
            background: var(--surface);
            box-shadow: 0 18px 30px rgba(15, 23, 42, .12);
        }

        .mobile-link {
            display: flex;
            align-items: center;
            min-height: 2.75rem;
            border-radius: .75rem;
            padding: .65rem .8rem;
            color: var(--text-main);
            font-size: .9rem;
            font-weight: 600;
        }

        .mobile-link:hover {
            background: #fef3c7;
            color: #a16207;
        }

        body.theme-dark .mobile-link:hover {
            background: #374151;
            color: #fde68a;
        }

        .theme-toggle {
            display: grid;
            width: 2.5rem;
            height: 2.5rem;
            place-items: center;
            border: 1px solid var(--border);
            border-radius: 9999px;
        }

        .phone-bottom-nav {
            display: none;
        }

        @media (max-width: 639px) {
            .mobile-menu-trigger,
            .mobile-menu {
                display: none !important;
            }

            .food-category-menu {
                position: static !important;
                inset: auto !important;
            }

            .food-category-menu > .relative > button {
                display: none;
            }

            .food-category-menu > .relative > div {
                position: fixed;
                right: .75rem;
                bottom: 5.25rem;
                z-index: 51;
                width: 14rem;
                margin-top: 0;
            }

            .phone-bottom-nav {
                position: fixed;
                right: 0;
                bottom: 0;
                left: 0;
                z-index: 50;
                display: grid;
                grid-template-columns: repeat(5, minmax(0, 1fr));
                align-items: end;
                min-height: 4.5rem;
                padding: .55rem .35rem calc(.55rem + env(safe-area-inset-bottom));
                border-top: 1px solid var(--border);
                background: rgba(255, 255, 255, .96);
                box-shadow: 0 -8px 24px rgba(15, 23, 42, .1);
                backdrop-filter: blur(18px);
            }

            body.theme-dark .phone-bottom-nav {
                background: rgba(17, 24, 39, .96);
            }

            .phone-nav-link {
                display: flex;
                min-width: 0;
                min-height: 3.35rem;
                align-items: center;
                justify-content: center;
                gap: .18rem;
                flex-direction: column;
                border-radius: .8rem;
                color: var(--text-muted);
                font-size: .64rem;
                font-weight: 700;
                line-height: 1;
                transition: color .2s ease, background-color .2s ease;
            }

            .phone-nav-link svg {
                width: 1.35rem;
                height: 1.35rem;
            }

            .phone-nav-link:hover,
            .phone-nav-link:focus-visible {
                color: #a16207;
                background: #fef3c7;
                outline: none;
            }

            .phone-nav-link.phone-nav-featured {
                width: 3.35rem;
                min-height: 3.35rem;
                margin: 0 auto .35rem;
                border-radius: 9999px;
                color: #ffffff;
                background: #171717;
                box-shadow: 0 8px 18px rgba(15, 23, 42, .2);
            }

            .phone-nav-link.phone-nav-featured:hover,
            .phone-nav-link.phone-nav-featured:focus-visible {
                color: #ffffff;
                background: #404040;
            }

            .phone-dashboard-menu {
                position: fixed;
                right: .75rem;
                bottom: 5.25rem;
                left: .75rem;
                z-index: 60;
                overflow: hidden;
                border: 1px solid var(--border);
                border-radius: 1rem;
                background: var(--surface);
                box-shadow: 0 12px 30px rgba(15, 23, 42, .18);
            }

            body.theme-dark .phone-nav-link:hover,
            body.theme-dark .phone-nav-link:focus-visible {
                color: #fde68a;
                background: #374151;
            }

            body.theme-dark .phone-nav-link.phone-nav-featured {
                color: #111827;
                background: #facc15;
            }

            body.theme-dark .phone-nav-link.phone-nav-featured:hover,
            body.theme-dark .phone-nav-link.phone-nav-featured:focus-visible {
                color: #111827;
                background: #fde047;
            }

            body {
                padding-bottom: 5.25rem;
            }
        }

        @keyframes fadeInOut {

            0%,
            100% {
                opacity: 0;
            }

            50% {
                opacity: 1;
            }
        }

        .fade-in-out {
            animation: fadeInOut 2s ease-in-out infinite;
        }
    </style>
</head>

<body x-data="{ darkMode: localStorage.getItem('foodstore-theme') === 'dark' }"
    x-init="$watch('darkMode', value => { localStorage.setItem('foodstore-theme', value ? 'dark' : 'light'); document.documentElement.classList.toggle('dark', value); document.body.classList.toggle('theme-dark', value); }); document.documentElement.classList.toggle('dark', darkMode); document.body.classList.toggle('theme-dark', darkMode)"
    :class="darkMode ? 'theme-dark' : ''">
    <nav x-data="{ open: false, dashboardMenu: false }" :class="darkMode ? 'bg-gray-900 text-white' : 'bg-white text-gray-800'"
        class="site-nav transition-colors duration-300">
        @php
            $isAdmin = Auth::check() && Auth::user()->usertype === 'admin';
            $userStore = Auth::check() ? \App\Models\Store::where('user_id', Auth::id())->where('status', 'approved')->first() : null;
            $userRider = Auth::check() ? \App\Models\Rider::where('user_id', Auth::id())->where('status', 'approved')->first() : null;
            $activeRole = $isAdmin ? 'admin' : session('active_role', 'user');
            if (! $isAdmin && $activeRole === 'rider' && ! $userRider) {
                $activeRole = 'user';
            }
            if (! $isAdmin && $activeRole === 'store' && ! $userStore) {
                $activeRole = 'user';
            }
            $customerNav = $isAdmin || $activeRole === 'user';
        @endphp
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="FoodStore home">
                        <span class="brand-mark"><img src="{{ asset('asset/logo.png') }}" alt=""></span>
                        <span class="leading-none"><strong
                                class="block text-xl font-black tracking-tight text-yellow-500">FoodStore</strong><small
                                class="hidden text-[10px] font-bold uppercase tracking-[.16em] text-gray-400 sm:block">Fresh
                                • Fast • Local</small></span>
                    </a>
                </div>

                <div class="ml-auto flex items-center gap-3">
                    <!-- Desktop Menu -->
                    <div class="hidden xl:flex items-center gap-1">
                        @auth
                            @if($customerNav)
                            <a href="{{ route('home') }}" class="nav-link">Home</a>

                            <!-- Cart with Badge -->
                            @php
                                $cartItems = session()->get('cart', []);
                                $cartCount = count($cartItems);
                            @endphp
                            <div class="relative">
                                <a href="{{ route('cart') }}" class="nav-link">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Cart
                                </a>
                                @if($cartCount > 0)
                                    <span
                                        class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $cartCount }}</span>
                                @endif
                            </div>

                            <a href="{{ route('about') }}" class="nav-link">About</a>
                            @endif
                        @endauth

                        @guest
                            <a href="{{ route('home') }}" class="nav-link">Home</a>
                            <a href="{{ route('register') }}" class="nav-link">Register</a>
                            <a href="{{ route('login') }}"
                                class="nav-pill bg-yellow-500 text-white hover:bg-yellow-600">Sign in</a>
                        @endguest

                        <!-- Shop Owner Dashboard -->
                        @if($userStore && ($isAdmin || $activeRole === 'user' || $activeRole === 'store'))
                            <a href="{{ route('storedashboard') }}"
                                :class="darkMode ? 'bg-orange-600 hover:bg-orange-700' : 'bg-orange-500 hover:bg-orange-600'"
                                class="nav-pill bg-orange-500 text-white hover:bg-orange-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z" />
                                </svg>
                                Store
                            </a>
                        @endif

                        <!-- Rider Dashboard -->
                        @if($userRider && ($isAdmin || $activeRole === 'user' || $activeRole === 'rider'))
                            <a href="{{ route('rider.dashboard') }}"
                                :class="darkMode ? 'bg-blue-600 hover:bg-blue-700' : 'bg-blue-500 hover:bg-blue-600'"
                                class="nav-pill bg-blue-500 text-white hover:bg-blue-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.22.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm11 0c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z" />
                                </svg>
                                Rider
                            </a>
                        @endif

                        <!-- Admin Panel -->
                        @if($isAdmin)
                            <a href="{{ route('admin.dashboard') }}"
                                class="nav-pill bg-purple-600 hover:bg-purple-700 text-white">Admin</a>
                        @endif

                        <!-- Dashboard Dropdown -->
                        @auth
                            <div class="relative group">
                                <button class="nav-link">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 13h2v8H3zm4-8h2v16H7zm4-2h2v18h-2zm4-2h2v20h-2zm4 4h2v16h-2z" />
                                    </svg>
                                    Dashboard
                                </button>
                                <div
                                    class="absolute right-0 mt-0 w-44 bg-white dark:bg-gray-800 rounded-lg shadow-lg z-20 hidden group-hover:block">
                                    @if($customerNav)
                                    <a href="{{ route('dashboard') }}"
                                        class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-yellow-100 dark:hover:bg-gray-700 rounded-t-lg text-sm">My
                                        Dashboard</a>
                                    @elseif($activeRole === 'rider')
                                    <a href="{{ route('rider.dashboard') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-yellow-100 dark:hover:bg-gray-700 rounded-t-lg text-sm">Rider Dashboard</a>
                                    @else
                                    <a href="{{ route('storedashboard') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-yellow-100 dark:hover:bg-gray-700 rounded-t-lg text-sm">Store Dashboard</a>
                                    @endif
                                    @if(!$isAdmin && $activeRole !== 'user')
                                        <form method="POST" action="{{ route('navigation.role') }}">
                                            @csrf
                                            <input type="hidden" name="role" value="user">
                                            <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-yellow-100 dark:hover:bg-gray-700 text-sm">Switch to User View</button>
                                        </form>
                                    @elseif(!$isAdmin && $activeRole === 'user')
                                        @if($userRider)
                                            <form method="POST" action="{{ route('navigation.role') }}">@csrf<input type="hidden" name="role" value="rider"><button type="submit" class="w-full text-left px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-yellow-100 dark:hover:bg-gray-700 text-sm">Rider View</button></form>
                                        @elseif($userStore)
                                            <form method="POST" action="{{ route('navigation.role') }}">@csrf<input type="hidden" name="role" value="store"><button type="submit" class="w-full text-left px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-yellow-100 dark:hover:bg-gray-700 text-sm">Store View</button></form>
                                        @endif
                                    @endif
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-yellow-100 dark:hover:bg-gray-700 rounded-b-lg text-sm">Logout</button>
                                    </form>
                                </div>
                            </div>
                        @endauth
                    </div>

                    <!-- Right Side Icons -->
                    <div class="flex items-center gap-2">
                        <!-- Dark/Light Mode Button -->
                        <button @click="darkMode = !darkMode" class="theme-toggle transition"
                            :class="darkMode ? 'bg-gray-700 text-yellow-400' : 'bg-yellow-100 text-yellow-600'"
                            title="Toggle light and dark mode" aria-label="Toggle light and dark mode">
                            <span x-show="!darkMode"><svg class="w-5 h-5" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 3v1m0 16v1m8.66-8.66l-.71.71M4.05 4.05l-.71.71m16.97 0l-.71-.71M4.05 19.95l-.71-.71M21 12h1M3 12H2" />
                                </svg></span>
                            <span x-show="darkMode"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                                </svg></span>
                        </button>

                        <!-- Mobile Menu Button -->
                        <button @click="open = !open" :aria-expanded="open.toString()"
                            aria-label="Toggle navigation menu" class="mobile-menu-trigger xl:hidden p-2 rounded transition"
                            :class="darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100'">
                            <svg :class="open ? 'hidden' : 'block'" class="w-6 h-6" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg :class="open ? 'block' : 'hidden'" class="w-6 h-6" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="open" @click.outside="open = false" class="mobile-menu xl:hidden transition-all">
            <div class="max-w-7xl mx-auto px-4 py-4 space-y-1 sm:px-6">
                @auth
                    @if($customerNav)
                    <a href="{{ route('home') }}" class="mobile-link">Home</a>

                    <!-- Mobile Cart -->
                    @php
                        $cartItems = session()->get('cart', []);
                        $cartCount = count($cartItems);
                    @endphp
                    <a href="{{ route('cart') }}" class="mobile-link justify-between">
                        <span>Cart</span> @if($cartCount > 0)<span
                        class="rounded-full bg-red-500 px-2 py-0.5 text-xs font-bold text-white">{{ $cartCount }}</span>@endif
                    </a>

                    <a href="{{ route('about') }}" class="mobile-link">About</a>
                    @endif

                    <!-- Mobile Store Dashboard -->
                    @if($userStore && ($isAdmin || $activeRole === 'user' || $activeRole === 'store'))
                        <a href="{{ route('storedashboard') }}"
                            class="mobile-link bg-orange-500 text-white hover:bg-orange-600">Store Dashboard</a>
                    @endif

                    <!-- Mobile Rider Dashboard -->
                    @if($userRider && ($isAdmin || $activeRole === 'user' || $activeRole === 'rider'))
                        <a href="{{ route('rider.dashboard') }}"
                            class="mobile-link bg-blue-500 text-white hover:bg-blue-600">Rider Dashboard</a>
                    @endif

                    <!-- Mobile Admin Panel -->
                    @if($isAdmin)
                        <a href="{{ route('admin.dashboard') }}"
                            class="mobile-link bg-purple-600 text-white hover:bg-purple-700">Admin Panel</a>
                    @endif

                    @if(!$isAdmin && $activeRole !== 'user')
                        <form method="POST" action="{{ route('navigation.role') }}">
                            @csrf
                            <input type="hidden" name="role" value="user">
                            <button type="submit" class="mobile-link w-full text-left text-yellow-700 dark:text-yellow-300">Switch to User View</button>
                        </form>
                    @elseif(!$isAdmin && $activeRole === 'user')
                        @if($userRider)
                            <form method="POST" action="{{ route('navigation.role') }}">@csrf<input type="hidden" name="role" value="rider"><button type="submit" class="mobile-link w-full text-left">Switch to Rider View</button></form>
                        @elseif($userStore)
                            <form method="POST" action="{{ route('navigation.role') }}">@csrf<input type="hidden" name="role" value="store"><button type="submit" class="mobile-link w-full text-left">Switch to Store View</button></form>
                        @endif
                    @endif

                    <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>

                    @if($customerNav)
                        <a href="{{ route('dashboard') }}" class="mobile-link">My Dashboard</a>
                    @elseif($activeRole === 'rider')
                        <a href="{{ route('rider.dashboard') }}" class="mobile-link">Rider Dashboard</a>
                    @else
                        <a href="{{ route('storedashboard') }}" class="mobile-link">Store Dashboard</a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="mobile-link w-full text-left text-red-600 dark:text-red-400">Log
                            out</button>
                    </form>
                @endauth

                @guest
                    <a href="{{ route('home') }}" class="mobile-link">Home</a>
                    <a href="{{ route('register') }}"
                        class="block px-3 py-2 rounded bg-yellow-500 hover:bg-yellow-600 text-white font-semibold text-sm">Register</a>
                    <a href="{{ route('login') }}"
                        class="block px-3 py-2 rounded bg-yellow-500 hover:bg-yellow-600 text-white font-semibold text-sm">Login</a>
                @endguest
            </div>
        </div>
    </nav>
    <!-- Page Loader -->
    <div id="page-loader"
        class="fixed inset-0 bg-white flex items-center justify-center z-50 opacity-0 transition-opacity duration-500">
        <img src="{{ asset('asset/logo.png') }}" class="fade-in-out w-32 h-32" alt="Loading FoodStore">
    </div>
    <!-- filepath: c:\Users\Bishop\School\resources\views\layouts\navbar.blade.php -->

    <div x-data="{ openFood: false }" class="food-category-menu fixed bottom-6 right-6 z-50">
        <div class="relative">
            <button @click="openFood = !openFood"
                class="bg-yellow-500 hover:bg-yellow-600 text-white rounded-full shadow-lg p-4 flex items-center justify-center transition duration-300 focus:outline-none">
                <svg class="w-7 h-7 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span class="font-semibold">Categories</span>
            </button>
            <div x-show="openFood" @click.away="openFood = false"
                class="mt-2 w-56 bg-white rounded-lg shadow-lg py-2 absolute right-0 bottom-16" x-transition>
                <a href="{{ route('food.pizza') }}"
                    class="flex items-center w-full text-left px-4 py-2 text-gray-700 hover:bg-yellow-100">
                    <img src="{{ ('asset/generated.jpg') }}" alt="Pizza" class="w-5 h-5 mr-2"> Pizza
                </a>
                <a href="{{ route('food.burger') }}"
                    class="flex items-center w-full text-left px-4 py-2 text-gray-700 hover:bg-yellow-100">
                    <img src="{{ ('asset/front.avif') }}" alt="Burger" class="w-5 h-5 mr-2"> Burger
                </a>
                <a href="{{ route('food.salad') }}"
                    class="flex items-center w-full text-left px-4 py-2 text-gray-700 hover:bg-yellow-100">
                    <img src="{{ ('asset/brown.jpg') }}" alt="Salad" class="w-5 h-5 mr-2"> Salad
                </a>
                <a href="{{ route('food.drinks') }}"
                    class="flex items-center w-full text-left px-4 py-2 text-gray-700 hover:bg-yellow-100">
                    <img src="{{('asset/drink.webp') }}" alt="Drinks" class="w-5 h-5 mr-2"> Drinks
                </a>
            </div>
        </div>

        <nav x-data="{ dashboardMenu: false }" class="phone-bottom-nav" aria-label="Mobile navigation">
            @if($customerNav)
            <a href="{{ route('home') }}" class="phone-nav-link" aria-label="Home">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V10Z" />
                </svg>
                <span>Home</span>
            </a>
            <a href="{{ route('cart') }}" class="phone-nav-link" aria-label="Cart">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l1.2 10.2a2 2 0 0 0 2 1.8h7.6a2 2 0 0 0 1.9-1.4L20 7H6M10 20a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm8 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" />
                </svg>
                <span>Cart</span>
            </a>
            <button type="button" @click="openFood = !openFood" class="phone-nav-link phone-nav-featured" aria-label="Categories">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="7" />
                    <path stroke-linecap="round" d="m16.5 16.5 4 4" />
                </svg>
                <span>Categories</span>
            </button>
            <a href="{{ route('about') }}" class="phone-nav-link" aria-label="About">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" />
                    <path stroke-linecap="round" d="M12 10v6m0-9h.01" />
                </svg>
                <span>About</span>
            </a>
            @endif
            @auth
                <button type="button" @click="dashboardMenu = !dashboardMenu" class="phone-nav-link" aria-label="Open dashboard menu" :aria-expanded="dashboardMenu.toString()">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 13h6V4H4v9Zm0 7h6v-4H4v4Zm10 0h6v-9h-6v9Zm0-13h6V4h-6v3Z" />
                    </svg>
                    <span>Dashboard</span>
                </button>
                <div x-show="dashboardMenu" @click.outside="dashboardMenu = false" class="phone-dashboard-menu" x-transition>
                    @if($isAdmin)
                        <a href="{{ route('admin.dashboard') }}" class="mobile-link border-b border-gray-100 text-purple-700 dark:border-gray-700 dark:text-purple-300">Admin Panel</a>
                        <a href="{{ route('dashboard') }}" class="mobile-link border-b border-gray-100 dark:border-gray-700">My Dashboard</a>
                    @elseif($activeRole === 'rider')
                        <a href="{{ route('rider.dashboard') }}" class="mobile-link border-b border-gray-100 dark:border-gray-700">Rider Dashboard</a>
                        <form method="POST" action="{{ route('navigation.role') }}">@csrf<input type="hidden" name="role" value="user"><button type="submit" class="mobile-link w-full border-b border-gray-100 text-left text-yellow-700 dark:border-gray-700 dark:text-yellow-300">Switch to User View</button></form>
                    @elseif($activeRole === 'store')
                        <a href="{{ route('storedashboard') }}" class="mobile-link border-b border-gray-100 dark:border-gray-700">Store Dashboard</a>
                        <form method="POST" action="{{ route('navigation.role') }}">@csrf<input type="hidden" name="role" value="user"><button type="submit" class="mobile-link w-full border-b border-gray-100 text-left text-yellow-700 dark:border-gray-700 dark:text-yellow-300">Switch to User View</button></form>
                    @else
                        <a href="{{ route('dashboard') }}" class="mobile-link border-b border-gray-100 dark:border-gray-700">My Dashboard</a>
                        @if($userRider)
                            <form method="POST" action="{{ route('navigation.role') }}">@csrf<input type="hidden" name="role" value="rider"><button type="submit" class="mobile-link w-full border-b border-gray-100 text-left dark:border-gray-700">Rider View</button></form>
                        @elseif($userStore)
                            <form method="POST" action="{{ route('navigation.role') }}">@csrf<input type="hidden" name="role" value="store"><button type="submit" class="mobile-link w-full border-b border-gray-100 text-left dark:border-gray-700">Store View</button></form>
                        @endif
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="mobile-link w-full text-left text-red-600 dark:text-red-400">Log out</button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="phone-nav-link" aria-label="Login">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4m-3-5 3-3m0 0-3-3m3 3H3" />
                    </svg>
                    <span>Login</span>
                </a>
            @endauth
        </nav>
    </div>
    <!-- Floating message icons (yellow) -->





    @yield('content')

    @include('layouts.footer')

    <style>
        .floating-label {
            display: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('page-loader').style.opacity = '1';
        });
        window.addEventListener('load', function () {
            const loader = document.getElementById('page-loader');
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500);
        });
    </script>
</body>

</html>