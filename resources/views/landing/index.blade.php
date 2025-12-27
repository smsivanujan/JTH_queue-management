@extends('layouts.landing')

@section('title', 'SmartQueue - Universal Queue Management System')
@section('description', 'SmartQueue is a universal queue management system for offices, restaurants, banks, petrol sheds, hospitals, and any business that manages customer flow.')

@push('styles')
<style>
    .hero-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .feature-card:hover {
        transform: translateY(-4px);
        transition: transform 0.3s ease;
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-20 sm:py-28 lg:py-32 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center fade-in">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                Queue Management<br />
                <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    Made Simple
                </span>
            </h1>
            <p class="text-xl sm:text-2xl text-gray-600 mb-10 max-w-3xl mx-auto">
                Universal queue management system for offices, restaurants, banks, petrol sheds, hospitals, and any business that manages customer flow.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('tenant.register') }}" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-semibold text-lg hover:shadow-xl hover:scale-105 transition-all">
                    Get Started Free
                </a>
                <a href="{{ route('pricing') }}" class="px-8 py-4 bg-white text-gray-700 border-2 border-gray-300 rounded-lg font-semibold text-lg hover:border-blue-600 hover:text-blue-600 transition-all">
                    View Pricing
                </a>
            </div>
            <p class="mt-6 text-sm text-gray-500">
                14-day free trial • No credit card required • Setup in minutes
            </p>
        </div>
    </div>
    
    <!-- Decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-72 h-72 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-pulse"></div>
        <div class="absolute top-40 right-10 w-72 h-72 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute -bottom-8 left-1/2 w-72 h-72 bg-indigo-200 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-pulse" style="animation-delay: 4s;"></div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-16 sm:py-20 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                How It Works
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Get started in three simple steps
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
            <!-- Step 1 -->
            <div class="text-center fade-in">
                <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <span class="text-3xl sm:text-4xl font-bold text-white">1</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4">Create Your Account</h3>
                <p class="text-gray-600 text-lg">
                    Sign up for free and create your organization. No credit card required.
                </p>
            </div>
            
            <!-- Step 2 -->
            <div class="text-center fade-in">
                <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <span class="text-3xl sm:text-4xl font-bold text-white">2</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4">Set Up Locations & Services</h3>
                <p class="text-gray-600 text-lg">
                    Add your locations and define your service queues. Customize token types and settings.
                </p>
            </div>
            
            <!-- Step 3 -->
            <div class="text-center fade-in">
                <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <span class="text-3xl sm:text-4xl font-bold text-white">3</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4">Start Managing Queues</h3>
                <p class="text-gray-600 text-lg">
                    Display tokens on screens, manage customer flow, and track statistics in real-time.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Use Cases Section -->
<section class="py-16 sm:py-20 lg:py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                Perfect For Any Business
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                SmartQueue works for any industry that manages customer flow
            </p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            <!-- Office -->
            <div class="bg-white rounded-xl p-6 sm:p-8 shadow-md hover:shadow-xl transition-shadow feature-card">
                <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Offices</h3>
                <p class="text-gray-600">
                    Manage visitor queues, appointments, and customer service desks efficiently.
                </p>
            </div>
            
            <!-- Restaurant -->
            <div class="bg-white rounded-xl p-6 sm:p-8 shadow-md hover:shadow-xl transition-shadow feature-card">
                <div class="w-14 h-14 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Restaurants</h3>
                <p class="text-gray-600">
                    Handle table reservations, takeaway orders, and customer waiting lists smoothly.
                </p>
            </div>
            
            <!-- Bank -->
            <div class="bg-white rounded-xl p-6 sm:p-8 shadow-md hover:shadow-xl transition-shadow feature-card">
                <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Banks</h3>
                <p class="text-gray-600">
                    Organize teller queues, customer service counters, and appointment-based services.
                </p>
            </div>
            
            <!-- Petrol Shed -->
            <div class="bg-white rounded-xl p-6 sm:p-8 shadow-md hover:shadow-xl transition-shadow feature-card">
                <div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Petrol Sheds</h3>
                <p class="text-gray-600">
                    Manage fuel pump queues, service station lines, and customer flow during peak hours.
                </p>
            </div>
            
            <!-- Hospital -->
            <div class="bg-white rounded-xl p-6 sm:p-8 shadow-md hover:shadow-xl transition-shadow feature-card">
                <div class="w-14 h-14 bg-pink-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Hospitals</h3>
                <p class="text-gray-600">
                    Streamline patient queues, OPD management, lab services, and appointment systems.
                </p>
            </div>
            
            <!-- General Service -->
            <div class="bg-white rounded-xl p-6 sm:p-8 shadow-md hover:shadow-xl transition-shadow feature-card">
                <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Any Service Business</h3>
                <p class="text-gray-600">
                    Adaptable to any business that manages queues, appointments, or customer flow.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Key Features Section -->
<section class="py-16 sm:py-20 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                Key Features
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Everything you need to manage queues efficiently
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Multiple Locations</h3>
                    <p class="text-gray-600">
                        Manage multiple locations from a single dashboard. Perfect for businesses with multiple branches.
                    </p>
                </div>
            </div>
            
            <!-- Feature 2 -->
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Custom Services</h3>
                    <p class="text-gray-600">
                        Create unlimited custom service queues. Configure token types (sequential or range) as per your needs.
                    </p>
                </div>
            </div>
            
            <!-- Feature 3 -->
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Digital Display Screens</h3>
                    <p class="text-gray-600">
                        Display current tokens on TV screens or monitors. Real-time updates keep customers informed.
                    </p>
                </div>
            </div>
            
            <!-- Feature 4 -->
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Real-Time Updates</h3>
                    <p class="text-gray-600">
                        Instant synchronization across all screens. Next, previous, and reset tokens with a single click.
                    </p>
                </div>
            </div>
            
            <!-- Feature 5 -->
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Team Management</h3>
                    <p class="text-gray-600">
                        Add staff members with role-based access. Manage permissions and track usage per team member.
                    </p>
                </div>
            </div>
            
            <!-- Feature 6 -->
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Usage Statistics</h3>
                    <p class="text-gray-600">
                        Track queue performance, screen usage, and customer flow metrics. Make data-driven decisions.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 sm:py-20 lg:py-24 bg-gradient-to-r from-blue-600 to-indigo-600">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-6">
            Ready to Get Started?
        </h2>
        <p class="text-xl text-blue-100 mb-10">
            Join thousands of businesses using SmartQueue to manage their customer flow efficiently.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('tenant.register') }}" class="px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold text-lg hover:shadow-xl hover:scale-105 transition-all">
                Start Free Trial
            </a>
            <a href="{{ route('pricing') }}" class="px-8 py-4 bg-blue-700 text-white border-2 border-white/30 rounded-lg font-semibold text-lg hover:bg-blue-800 transition-all">
                View Pricing
            </a>
        </div>
        <p class="mt-6 text-sm text-blue-100">
            No credit card required • 14-day free trial • Cancel anytime
        </p>
    </div>
</section>
@endsection
