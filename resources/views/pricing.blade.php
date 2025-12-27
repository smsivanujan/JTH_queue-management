@extends('layouts.landing')

@section('title', 'Pricing - SmartQueue')
@section('description', 'Simple, transparent pricing for SmartQueue queue management system. Choose the plan that fits your business needs.')

@push('styles')
<style>
    .pricing-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .pricing-card:hover {
        transform: translateY(-8px);
    }
    .popular-badge {
        position: absolute;
        top: -12px;
        right: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<!-- Pricing Header -->
<section class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-16 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 mb-6">
            Simple, Transparent Pricing
        </h1>
        <p class="text-xl sm:text-2xl text-gray-600 max-w-3xl mx-auto mb-4">
            Choose the plan that fits your business needs. All plans include a 14-day free trial.
        </p>
        <p class="text-sm text-gray-500">
            No credit card required • Cancel anytime • Upgrade or downgrade at any time
        </p>
    </div>
</section>

<!-- Pricing Plans -->
<section class="py-16 sm:py-20 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($plans->isEmpty())
            <div class="text-center py-12">
                <p class="text-gray-600 text-lg">No pricing plans available at the moment. Please contact us for more information.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10">
                @foreach($plans as $plan)
                    @php
                        $isPopular = strtolower($plan->name) === 'professional' || strtolower($plan->name) === 'pro';
                        $price = $plan->price ?? 0;
                        $priceFormatted = $price > 0 ? number_format($price, 2) : '0';
                    @endphp
                    
                    <div class="relative bg-white rounded-2xl shadow-lg border-2 {{ $isPopular ? 'border-blue-500 scale-105' : 'border-gray-200' }} pricing-card overflow-hidden">
                        @if($isPopular)
                            <div class="popular-badge">Most Popular</div>
                        @endif
                        
                        <div class="p-6 sm:p-8">
                            <!-- Plan Name -->
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                                <div class="flex items-baseline">
                                    <span class="text-4xl sm:text-5xl font-bold text-gray-900">${{ $priceFormatted }}</span>
                                    @if($price > 0)
                                        <span class="text-gray-600 text-lg ml-2">/month</span>
                                    @endif
                                </div>
                                @if($plan->description)
                                    <p class="text-gray-600 mt-2 text-sm">{{ $plan->description }}</p>
                                @endif
                            </div>
                            
                            <!-- Features -->
                            <ul class="space-y-4 mb-8">
                                @if($plan->max_clinics !== null)
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-gray-700">
                                            <strong>{{ $plan->max_clinics == -1 ? 'Unlimited' : $plan->max_clinics }}</strong> Locations
                                        </span>
                                    </li>
                                @endif
                                
                                @if($plan->max_users !== null)
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-gray-700">
                                            <strong>{{ $plan->max_users == -1 ? 'Unlimited' : $plan->max_users }}</strong> Staff Members
                                        </span>
                                    </li>
                                @endif
                                
                                @if($plan->max_screens !== null)
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-gray-700">
                                            <strong>{{ $plan->max_screens == -1 ? 'Unlimited' : $plan->max_screens }}</strong> Display Screens
                                        </span>
                                    </li>
                                @endif
                                
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-700">Unlimited Services</span>
                                </li>
                                
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-700">Real-time Queue Updates</span>
                                </li>
                                
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-700">Usage Statistics</span>
                                </li>
                                
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-700">14-Day Free Trial</span>
                                </li>
                            </ul>
                            
                            <!-- CTA Button -->
                            <a href="{{ route('tenant.register') }}" class="block w-full text-center px-6 py-3 rounded-lg font-semibold transition-all {{ $isPopular ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:shadow-xl hover:scale-105' : 'bg-gray-100 text-gray-900 hover:bg-gray-200' }}">
                                Get Started
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        
        <!-- Additional Info -->
        <div class="mt-16 text-center">
            <p class="text-gray-600 mb-4">
                All plans include a 14-day free trial. No credit card required.
            </p>
            <p class="text-gray-600 mb-6">
                Need a custom plan for your enterprise? <a href="mailto:support@smartqueue.com" class="text-blue-600 hover:text-blue-700 font-semibold">Contact us</a> for a quote.
            </p>
            <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                ← Back to Home
            </a>
        </div>
    </div>
</section>
@endsection
