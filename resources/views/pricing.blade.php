<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Affordable queue management system for hospitals and clinics in Sri Lanka. Start your free trial today with WhatsApp support.">
    <title>Pricing - SmartQueue Hospital Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
</head>
<body class="bg-white antialiased">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-2">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-teal-600 bg-clip-text text-transparent">SmartQueue</h1>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900 transition-colors">Home</a>
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 transition-colors">Login</a>
                    <a href="{{ route('tenant.register') }}" class="bg-gradient-to-r from-blue-600 to-teal-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:shadow-lg transition-all">
                        Get Started Free
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-blue-50 via-white to-teal-50 py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto text-center fade-in">
            <div class="inline-block px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm font-semibold mb-6">
                ✨ 14 Days Free Trial - No Credit Card Required
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                Simple Pricing for <span class="bg-gradient-to-r from-blue-600 to-teal-600 bg-clip-text text-transparent">Sri Lankan Hospitals</span>
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                Manage your patient queues efficiently without breaking your budget. Designed specifically for hospitals and clinics across Sri Lanka.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-6 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Try free for 14 days</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>WhatsApp support included</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Cancel anytime</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Cards -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-3 gap-8 lg:gap-10 max-w-6xl mx-auto">
                <!-- Starter Plan -->
                <div class="bg-white border-2 border-gray-200 rounded-2xl p-8 hover:border-blue-300 hover:shadow-xl transition-all fade-in">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Starter</h3>
                        <div class="flex items-baseline justify-center mb-4">
                            <span class="text-5xl font-bold text-gray-900">Rs. 4,900</span>
                            <span class="text-gray-600 ml-2 text-lg">/month</span>
                        </div>
                        <p class="text-gray-600">Perfect for small clinics</p>
                    </div>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Up to 3 clinics</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">2 staff members</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Real-time queue display</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Custom service management</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">WhatsApp support</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">14-day free trial</span>
                        </li>
                    </ul>

                    <a href="{{ route('tenant.register') }}" class="block w-full text-center bg-gradient-to-r from-blue-600 to-teal-600 text-white py-3 rounded-lg font-semibold hover:shadow-lg hover:scale-105 transition-all">
                        Start Free Trial
                    </a>
                </div>

                <!-- Professional Plan (Most Popular) -->
                <div class="bg-gradient-to-br from-blue-600 to-teal-600 rounded-2xl p-8 text-white transform scale-105 shadow-2xl relative fade-in">
                    <div class="absolute top-0 right-0 bg-yellow-400 text-gray-900 px-4 py-1.5 rounded-bl-lg rounded-tr-2xl text-sm font-bold">
                        Most Popular
                    </div>
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold mb-2">Professional</h3>
                        <div class="flex items-baseline justify-center mb-4">
                            <span class="text-5xl font-bold">Rs. 12,900</span>
                            <span class="ml-2 text-lg opacity-90">/month</span>
                        </div>
                        <p class="opacity-90">Best for medium hospitals</p>
                    </div>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-yellow-300 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Up to 10 clinics</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-yellow-300 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Up to 5 staff members</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-yellow-300 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Multiple display screens</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-yellow-300 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>All Starter features</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-yellow-300 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Priority WhatsApp support</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-yellow-300 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>14-day free trial</span>
                        </li>
                    </ul>

                    <a href="{{ route('tenant.register') }}" class="block w-full text-center bg-white text-blue-600 py-3 rounded-lg font-semibold hover:bg-gray-50 hover:scale-105 transition-all shadow-lg">
                        Start Free Trial
                    </a>
                </div>

                <!-- Enterprise Plan -->
                <div class="bg-white border-2 border-gray-200 rounded-2xl p-8 hover:border-blue-300 hover:shadow-xl transition-all fade-in">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Enterprise</h3>
                        <div class="flex items-baseline justify-center mb-4">
                            <span class="text-5xl font-bold text-gray-900">Rs. 24,900</span>
                            <span class="text-gray-600 ml-2 text-lg">/month</span>
                        </div>
                        <p class="text-gray-600">For large hospital networks</p>
                    </div>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Unlimited clinics</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Unlimited staff members</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Unlimited display screens</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">All Professional features</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Dedicated WhatsApp support</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Custom setup assistance</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">14-day free trial</span>
                        </li>
                    </ul>

                    <a href="{{ route('tenant.register') }}" class="block w-full text-center bg-gradient-to-r from-blue-600 to-teal-600 text-white py-3 rounded-lg font-semibold hover:shadow-lg hover:scale-105 transition-all">
                        Start Free Trial
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-gray-50 to-blue-50">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Why Hospitals Choose SmartQueue</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Trusted by healthcare providers across Sri Lanka to reduce patient wait times and improve efficiency
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Benefit 1 -->
                <div class="bg-white rounded-xl p-8 shadow-md hover:shadow-xl transition-all">
                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Reduce Wait Times</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Patients spend less time waiting. Our system helps you manage queues efficiently, reducing waiting times by up to 40%.
                    </p>
                </div>

                <!-- Benefit 2 -->
                <div class="bg-white rounded-xl p-8 shadow-md hover:shadow-xl transition-all">
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Easy to Use</h3>
                    <p class="text-gray-600 leading-relaxed">
                        No technical training needed. Your staff can learn to use SmartQueue in minutes, not days. Simple and intuitive.
                    </p>
                </div>

                <!-- Benefit 3 -->
                <div class="bg-white rounded-xl p-8 shadow-md hover:shadow-xl transition-all">
                    <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Affordable Pricing</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Save money while improving service. Our plans start at just Rs. 4,900 per month - less than hiring one additional staff member.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Support Section -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-4xl mx-auto text-center">
            <div class="bg-gradient-to-br from-green-50 to-teal-50 rounded-2xl p-12 border-2 border-green-200">
                <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Get Help When You Need It</h2>
                <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                    We're here for you. All plans include WhatsApp support so you can reach us easily, anytime you have questions.
                </p>
                <div class="inline-flex items-center gap-3 px-6 py-3 bg-green-600 text-white rounded-lg font-semibold">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                    </svg>
                    <span>WhatsApp: +94 XX XXX XXXX</span>
                </div>
                <p class="text-sm text-gray-500 mt-4">Response time: Usually within 1 hour during business hours</p>
            </div>
        </div>
    </section>

    <!-- Trust Section -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-blue-50 to-teal-50">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">Try Risk-Free for 14 Days</h2>
            <div class="grid md:grid-cols-3 gap-6 mb-12">
                <div class="bg-white rounded-xl p-6 shadow-md">
                    <div class="text-4xl font-bold text-blue-600 mb-2">14</div>
                    <div class="text-gray-700 font-semibold">Days Free</div>
                    <div class="text-sm text-gray-500 mt-1">Full access, no credit card</div>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-md">
                    <div class="text-4xl font-bold text-green-600 mb-2">0</div>
                    <div class="text-gray-700 font-semibold">Setup Fees</div>
                    <div class="text-sm text-gray-500 mt-1">Get started immediately</div>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-md">
                    <div class="text-4xl font-bold text-purple-600 mb-2">✓</div>
                    <div class="text-gray-700 font-semibold">Cancel Anytime</div>
                    <div class="text-sm text-gray-500 mt-1">No long-term contracts</div>
                </div>
            </div>
            <a href="{{ route('tenant.register') }}" class="inline-block bg-gradient-to-r from-blue-600 to-teal-600 text-white px-12 py-4 rounded-lg text-lg font-semibold hover:shadow-xl hover:scale-105 transition-all">
                Start Your Free Trial Today
            </a>
            <p class="text-gray-500 mt-4 text-sm">No credit card required • Setup in 5 minutes</p>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Frequently Asked Questions</h2>
            <div class="space-y-6">
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">How does the free trial work?</h3>
                    <p class="text-gray-600">
                        You get full access to all features for 14 days. No credit card required. After 14 days, you can choose to continue with a paid plan or cancel - it's completely up to you.
                    </p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">What payment methods do you accept?</h3>
                    <p class="text-gray-600">
                        We accept bank transfers and major credit cards. Monthly billing is available for all plans. You can pay in Sri Lankan Rupees (LKR).
                    </p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Can I change plans later?</h3>
                    <p class="text-gray-600">
                        Yes, absolutely! You can upgrade or downgrade your plan anytime. Changes take effect immediately, and we'll prorate any charges.
                    </p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Is WhatsApp support really included?</h3>
                    <p class="text-gray-600">
                        Yes! All plans include WhatsApp support. You can message us anytime with questions, and we'll respond quickly. It's the easiest way to get help.
                    </p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Do I need special equipment?</h3>
                    <p class="text-gray-600">
                        No special equipment needed. SmartQueue works on any computer, tablet, or smartphone with internet connection. You can use existing devices in your hospital.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 bg-gradient-to-r from-blue-600 to-teal-600">
        <div class="max-w-4xl mx-auto text-center text-white">
            <h2 class="text-4xl font-bold mb-6">Ready to Improve Your Patient Flow?</h2>
            <p class="text-xl mb-8 opacity-90">
                Join hospitals across Sri Lanka who are already using SmartQueue to serve patients better.
            </p>
            <a href="{{ route('tenant.register') }}" class="inline-block bg-white text-blue-600 px-12 py-4 rounded-lg text-lg font-semibold hover:bg-gray-50 hover:scale-105 transition-all shadow-lg">
                Start Your 14-Day Free Trial
            </a>
            <p class="text-sm mt-4 opacity-75">No credit card required • Cancel anytime</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto text-center">
            <div class="mb-6">
                <h3 class="text-2xl font-bold mb-2">SmartQueue</h3>
                <p class="text-gray-400">Queue management made simple for Sri Lankan hospitals</p>
            </div>
            <div class="border-t border-gray-800 pt-8 mt-8">
                <p class="text-gray-400 text-sm">© {{ date('Y') }} SmartQueue. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>

