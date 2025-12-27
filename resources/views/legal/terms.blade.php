@extends('layouts.landing')

@section('title', 'Terms of Service - SmartQueue')

@section('content')
<div class="min-h-screen bg-white">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Terms of Service</h1>
                <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">← Back to Home</a>
            </div>
        </div>
    </div>

    <!-- Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <div class="prose prose-lg max-w-none">
            <p class="text-gray-600 text-sm mb-8">Last updated: {{ date('F j, Y') }}</p>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Acceptance of Terms</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    By accessing and using SmartQueue ("the Service"), you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to these Terms of Service, please do not use the Service.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    These Terms of Service apply to all users of the Service, including tenants, administrators, and end users.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Description of Service</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    SmartQueue is a queue management system that allows organizations to manage customer queues, display queue information, and track service metrics. The Service is provided on a subscription basis.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    We reserve the right to modify, suspend, or discontinue the Service at any time with or without notice.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Account Registration</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    To use the Service, you must create an account and provide accurate, complete, and current information. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    You agree to immediately notify us of any unauthorized use of your account or any other breach of security.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Subscription and Payment Terms</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    The Service is provided on a subscription basis. Subscription fees are charged in advance on a monthly or annual basis, depending on your selected plan.
                </p>
                <p class="text-gray-700 leading-relaxed mb-4">
                    <strong>Payment Methods:</strong> We accept payments via credit card (processed through Stripe) or manual bank transfer. All fees are non-refundable except as required by law or as otherwise stated in our Refund Policy.
                </p>
                <p class="text-gray-700 leading-relaxed mb-4">
                    <strong>Trial Period:</strong> We may offer a free trial period. At the end of the trial period, your subscription will automatically convert to a paid plan unless cancelled before the trial ends.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    <strong>Price Changes:</strong> We reserve the right to change our subscription fees at any time. Price changes will be communicated to you in advance and will apply to subsequent billing periods.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Use of Service</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    You agree to use the Service only for lawful purposes and in accordance with these Terms of Service. You agree not to:
                </p>
                <ul class="list-disc list-inside text-gray-700 space-y-2 mb-4">
                    <li>Use the Service in any way that violates any applicable law or regulation</li>
                    <li>Transmit any harmful code, viruses, or malicious software</li>
                    <li>Attempt to gain unauthorized access to the Service or related systems</li>
                    <li>Interfere with or disrupt the Service or servers connected to the Service</li>
                    <li>Use the Service to transmit any content that is illegal, harmful, or offensive</li>
                    <li>Reverse engineer, decompile, or disassemble the Service</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Intellectual Property</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    The Service, including its original content, features, and functionality, is owned by SmartQueue and is protected by copyright, trademark, and other laws. You may not copy, modify, distribute, sell, or lease any part of the Service.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    You retain ownership of any data you submit to the Service. By using the Service, you grant us a license to use, store, and process your data solely for the purpose of providing the Service.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Limitation of Liability</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    TO THE MAXIMUM EXTENT PERMITTED BY LAW, SMARTQUEUE SHALL NOT BE LIABLE FOR ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR PUNITIVE DAMAGES, OR ANY LOSS OF PROFITS OR REVENUES, WHETHER INCURRED DIRECTLY OR INDIRECTLY, OR ANY LOSS OF DATA, USE, GOODWILL, OR OTHER INTANGIBLE LOSSES.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    Our total liability for any claims arising out of or relating to these Terms of Service or the Service shall not exceed the amount you paid to us in the twelve (12) months preceding the claim.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Termination</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Either party may terminate this agreement at any time. You may cancel your subscription at any time through your account settings or by contacting us.
                </p>
                <p class="text-gray-700 leading-relaxed mb-4">
                    We reserve the right to suspend or terminate your account immediately, without prior notice, for any violation of these Terms of Service or for any other reason we deem necessary.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    Upon termination, your right to use the Service will immediately cease. We may delete your account and data after a reasonable retention period, as outlined in our Privacy Policy.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Changes to Terms</h2>
                <p class="text-gray-700 leading-relaxed">
                    We reserve the right to modify these Terms of Service at any time. We will notify users of any material changes by posting the new Terms of Service on this page and updating the "Last updated" date. Your continued use of the Service after such changes constitutes your acceptance of the new Terms of Service.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Contact Information</h2>
                <p class="text-gray-700 leading-relaxed">
                    If you have any questions about these Terms of Service, please contact us at:
                </p>
                <p class="text-gray-700 leading-relaxed mt-2">
                    <strong>Email:</strong> support@smartqueue.com<br>
                    <strong>Website:</strong> <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">www.smartqueue.com</a>
                </p>
            </section>
        </div>
    </main>
</div>
@endsection

