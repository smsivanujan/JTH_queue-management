@extends('layouts.landing')

@section('title', 'Refund & Cancellation Policy - SmartQueue')

@section('content')
<div class="min-h-screen bg-white">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Refund & Cancellation Policy</h1>
                <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">← Back to Home</a>
            </div>
        </div>
    </div>

    <!-- Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <div class="prose prose-lg max-w-none">
            <p class="text-gray-600 text-sm mb-8">Last updated: {{ date('F j, Y') }}</p>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Trial Period</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    We offer a free trial period for new users to evaluate the Service. The trial period allows you to access all features of the Service without charge.
                </p>
                <p class="text-gray-700 leading-relaxed mb-4">
                    <strong>Trial Duration:</strong> The trial period is typically 14 days, but may vary based on promotional offers.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    <strong>No Payment Required:</strong> No payment information is required to start a trial. If you do not wish to continue using the Service after the trial period, you may cancel your account at any time before the trial ends, and you will not be charged.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Subscription Cancellation</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    You may cancel your subscription at any time through your account settings or by contacting us at support@smartqueue.com.
                </p>
                <p class="text-gray-700 leading-relaxed mb-4">
                    <strong>Cancellation Timing:</strong> Your cancellation will take effect at the end of your current billing period. You will continue to have access to the Service until the end of the billing period for which you have already paid.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    <strong>No Partial Refunds:</strong> We do not provide partial refunds for unused portions of your billing period. Once a payment has been processed, it covers the entire billing period (monthly or annual).
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Refund Policy</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    All subscription fees are generally non-refundable. However, we may consider refunds on a case-by-case basis in the following circumstances:
                </p>
                <ul class="list-disc list-inside text-gray-700 space-y-2 mb-4">
                    <li><strong>Service Failure:</strong> If the Service is unavailable or non-functional for a significant portion of your billing period due to our error, we may provide a partial or full refund.</li>
                    <li><strong>Billing Error:</strong> If you were charged incorrectly due to our error, we will refund the incorrect amount.</li>
                    <li><strong>Duplicate Charges:</strong> If you were charged multiple times for the same subscription period, we will refund the duplicate charges.</li>
                    <li><strong>Special Circumstances:</strong> We may consider refunds for other circumstances at our sole discretion.</li>
                </ul>
                <p class="text-gray-700 leading-relaxed">
                    To request a refund, please contact us at support@smartqueue.com within 30 days of the charge. Include your account information and a detailed explanation of why you are requesting a refund.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Payment Methods</h2>
                
                <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-4">4.1 Credit Card Payments (Stripe)</h3>
                <p class="text-gray-700 leading-relaxed mb-4">
                    When you pay by credit card through Stripe, payments are processed immediately. Refunds, if approved, will be issued to the original payment method and may take 5-10 business days to appear in your account, depending on your financial institution.
                </p>

                <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-4">4.2 Manual Bank Transfer</h3>
                <p class="text-gray-700 leading-relaxed mb-4">
                    For manual bank transfer payments, you will receive payment instructions after selecting a subscription plan. Once payment is received and verified, your subscription will be activated.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    Refunds for manual payments, if approved, will be processed via bank transfer and may take 7-14 business days to be completed.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Subscription Renewal</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Unless you cancel your subscription before the end of the billing period, your subscription will automatically renew for the same billing period (monthly or annual) and you will be charged the then-current subscription fee.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    We will notify you in advance of any price changes before they take effect. If you do not agree to the new pricing, you may cancel your subscription before the renewal date.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Downgrades and Plan Changes</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    You may upgrade or downgrade your subscription plan at any time through your account settings. Plan changes will take effect immediately.
                </p>
                <p class="text-gray-700 leading-relaxed mb-4">
                    <strong>Upgrades:</strong> When upgrading, you will be charged the difference for the remainder of your billing period, prorated to the upgrade date. Your next billing cycle will reflect the new plan price.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    <strong>Downgrades:</strong> When downgrading, the new plan will take effect immediately, but no refund will be provided for the unused portion of your current plan. Your next billing cycle will reflect the lower plan price.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Termination by Us</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    We reserve the right to suspend or terminate your account at any time for violations of our Terms of Service or for any other reason we deem necessary.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    In the event of termination by us for reasons other than your violation of our Terms of Service, we may provide a prorated refund for the unused portion of your subscription at our sole discretion.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Disputes and Chargebacks</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    If you have a dispute regarding a charge, we encourage you to contact us first at support@smartqueue.com before initiating a chargeback with your payment provider.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    Initiating a chargeback without first contacting us may result in immediate suspension or termination of your account. We will work with you to resolve any billing disputes in good faith.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Contact Information</h2>
                <p class="text-gray-700 leading-relaxed">
                    For questions about refunds, cancellations, or billing, please contact us at:
                </p>
                <p class="text-gray-700 leading-relaxed mt-2">
                    <strong>Email:</strong> support@smartqueue.com<br>
                    <strong>Website:</strong> <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">www.smartqueue.com</a>
                </p>
                <p class="text-gray-700 leading-relaxed mt-4">
                    We aim to respond to all refund and cancellation requests within 2-3 business days.
                </p>
            </section>
        </div>
    </main>
</div>
@endsection

