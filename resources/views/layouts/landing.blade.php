<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'SmartQueue - Universal Queue Management System')</title>
    <meta name="description" content="@yield('description', 'SmartQueue is a universal queue management system for offices, restaurants, banks, petrol sheds, hospitals, and any business that manages customer flow.')">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Styles -->
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }
    </style>
    
    @stack('styles')
</head>
<body class="antialiased bg-white">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                        SmartQueue
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex md:items-center md:space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-blue-600 font-semibold' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('pricing') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('pricing') ? 'text-blue-600 font-semibold' : '' }}">
                        Pricing
                    </a>
                    <a href="/login" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">
                        Login
                    </a>
                    <a href="{{ route('tenant.register') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:shadow-lg transition-all">
                        Get Started
                    </a>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div class="hidden mobile-menu md:hidden border-t border-gray-200" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('home') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md {{ request()->routeIs('home') ? 'text-blue-600 bg-blue-50' : '' }}">
                    Home
                </a>
                <a href="{{ route('pricing') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md {{ request()->routeIs('pricing') ? 'text-blue-600 bg-blue-50' : '' }}">
                    Pricing
                </a>
                <a href="/login" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">
                    Login
                </a>
                <a href="{{ route('tenant.register') }}" class="block px-3 py-2 text-base font-medium bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-md text-center hover:shadow-lg transition-all">
                    Get Started
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-50 border-t border-gray-200 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-4">
                        SmartQueue
                    </h3>
                    <p class="text-gray-600 text-sm max-w-md">
                        Universal queue management system for offices, restaurants, banks, petrol sheds, hospitals, and any business that manages customer flow.
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Home</a></li>
                        <li><a href="{{ route('pricing') }}" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Pricing</a></li>
                        <li><a href="/login" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Login</a></li>
                        <li><a href="{{ route('tenant.register') }}" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Register</a></li>
                    </ul>
                </div>
                
                <!-- Legal -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('legal.privacy') }}" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('legal.terms') }}" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Terms of Service</a></li>
                        <li><a href="{{ route('legal.refunds') }}" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Refund Policy</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-8 pt-8 border-t border-gray-200">
                <p class="text-center text-sm text-gray-600">
                    &copy; {{ date('Y') }} SmartQueue. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        document.querySelector('.mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            const isHidden = menu.classList.contains('hidden');
            
            if (isHidden) {
                menu.classList.remove('hidden');
                this.querySelector('svg.block').classList.add('hidden');
                this.querySelector('svg.hidden').classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
                this.querySelector('svg.block').classList.remove('hidden');
                this.querySelector('svg.hidden').classList.add('hidden');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>

