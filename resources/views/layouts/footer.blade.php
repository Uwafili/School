<!-- Footer -->
<footer class="bg-gray-800 dark:bg-gray-900 text-gray-200 mt-16 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8 py-12">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
            <!-- About Section -->
            <div>
                <h3 class="text-yellow-500 font-bold text-lg mb-4">About FoodStore</h3>
                <p class="text-sm leading-relaxed text-gray-400">
                    FoodStore is your ultimate food delivery platform connecting hungry customers with amazing restaurants and reliable riders.
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-yellow-500 font-bold text-lg mb-4">Quick Links</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-yellow-400 transition">Home</a></li>
                    <li><a href="{{ route('about') }}" class="text-gray-300 hover:text-yellow-400 transition">About Us</a></li>
                    <li><a href="{{ route('cart') }}" class="text-gray-300 hover:text-yellow-400 transition">My Cart</a></li>
                    @guest
                        {{-- <li><a href="{{ route('register') }}" class="text-gray-300 hover:text-yellow-400 transition">Register</a></li> --}}
                    @endguest
                </ul>
            </div>

            <!-- For Business -->
            <div>
                <h3 class="text-yellow-500 font-bold text-lg mb-4">For Business</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('store.create') }}" class="text-gray-300 hover:text-yellow-400 transition">Open Your Store</a></li>
                    <li><a href="{{ route('rider.create') }}" class="text-gray-300 hover:text-yellow-400 transition">Become a Rider</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition">Business Solutions</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition">Partner with Us</a></li>
                </ul>
            </div>

            <!-- Contact & Social -->
            <div>
                <h3 class="text-yellow-500 font-bold text-lg mb-4">Contact & Follow</h3>
                <div class="space-y-3">
                    <div class="text-sm">
                        <span class="font-semibold">📞 Phone:</span><br>
                        <a href="tel:+2347010282697" class="text-gray-300 hover:text-yellow-400 transition">+234 7010282697</a>
                    </div>
                    <div class="text-sm">
                        <span class="font-semibold">📧 Email:</span><br>
                        <a href="mailto:uwafilinorbet50@gmail.com" class="text-gray-300 hover:text-yellow-400 transition">uwafilinorbet50@gmail.com</a>
                    </div>
                    <div class="flex gap-4 mt-4">
                        <a href="#" class="hover:text-yellow-400 transition" title="Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="hover:text-yellow-400 transition" title="Twitter">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2s9 5 20 5a9.5 9.5 0 00-9-5.5c4.75 2.25 7-7 7-7a10.6 10.6 0 01-3-10.67z"/>
                            </svg>
                        </a>
                        <a href="#" class="hover:text-yellow-400 transition" title="Instagram">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2"/>
                                <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37Z" fill="currentColor"/>
                                <circle cx="17.5" cy="6.5" r="1.5" fill="currentColor"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-700 my-8"></div>

        <!-- Bottom Footer -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 text-sm">
            <p class="text-center md:text-left text-gray-400">
                &copy; 2026 <span class="text-yellow-500 font-bold">FoodStore</span>. All rights reserved.
            </p>
            <div class="flex flex-wrap gap-4 justify-center md:justify-end">
                <a href="#" class="text-gray-300 hover:text-yellow-400 transition text-xs sm:text-sm">Privacy Policy</a>
                <a href="#" class="text-gray-300 hover:text-yellow-400 transition text-xs sm:text-sm">Terms of Service</a>
                <a href="#" class="text-gray-300 hover:text-yellow-400 transition text-xs sm:text-sm">Cookie Policy</a>
            </div>
        </div>

        <!-- Mobile Info -->
        <div class="pt-8 border-t border-gray-700 text-center text-xs text-gray-500">
            <p>Made with ❤️ for food lovers everywhere</p>
            <p class="mt-2">Download our app: <span class="text-yellow-500 font-semibold">Coming Soon</span></p>
        </div>
    </div>
</footer>
