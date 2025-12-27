@extends('layouts.landing')

@section('title', 'Privacy Policy - SmartQueue')

@section('content')
<div class="min-h-screen bg-white">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Privacy Policy</h1>
                <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">← Back to Home</a>
            </div>
        </div>
    </div>

    <!-- Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <div class="prose prose-lg max-w-none">
            <p class="text-gray-600 text-sm mb-8">Last updated: {{ date('F j, Y') }}</p>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Introduction</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    SmartQueue ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our queue management service ("the Service").
                </p>
                <p class="text-gray-700 leading-relaxed">
                    Please read this Privacy Policy carefully. By using the Service, you consent to the practices described in this policy.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Information We Collect</h2>
                
                <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-4">2.1 Information You Provide</h3>
                <p class="text-gray-700 leading-relaxed mb-4">
                    We collect information that you voluntarily provide to us when you:
                </p>
                <ul class="list-disc list-inside text-gray-700 space-y-2 mb-4">
                    <li>Create an account (name, email address, password)</li>
                    <li>Register your organization (organization name, contact information)</li>
                    <li>Set up clinics, services, and staff members</li>
                    <li>Contact us for support or inquiries</li>
                    <li>Subscribe to our service (billing information)</li>
                </ul>

                <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-4">2.2 Usage Information</h3>
                <p class="text-gray-700 leading-relaxed mb-4">
                    We automatically collect certain information about how you use the Service, including:
                </p>
                <ul class="list-disc list-inside text-gray-700 space-y-2 mb-4">
                    <li>Queue management activity and usage patterns</li>
                    <li>Display screen usage and access logs</li>
                    <li>Service metrics and analytics data</li>
                    <li>Device information and IP addresses</li>
                    <li>Browser type and operating system</li>
                </ul>

                <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-4">2.3 Payment Information</h3>
                <p class="text-gray-700 leading-relaxed">
                    When you make a payment, we collect payment information through our payment processors (Stripe for card payments, or bank details for manual transfers). We do not store full credit card numbers on our servers. Payment information is processed securely by third-party payment processors.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">3. How We Use Your Information</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    We use the information we collect to:
                </p>
                <ul class="list-disc list-inside text-gray-700 space-y-2 mb-4">
                    <li>Provide, maintain, and improve the Service</li>
                    <li>Process transactions and send related information</li>
                    <li>Send you technical notices, updates, and support messages</li>
                    <li>Respond to your comments, questions, and requests</li>
                    <li>Monitor and analyze usage patterns and trends</li>
                    <li>Detect, prevent, and address technical issues and security threats</li>
                    <li>Comply with legal obligations</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Data Storage and Security</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    We implement reasonable security measures to protect your information from unauthorized access, alteration, disclosure, or destruction. These measures include:
                </p>
                <ul class="list-disc list-inside text-gray-700 space-y-2 mb-4">
                    <li>Encryption of data in transit and at rest</li>
                    <li>Secure authentication and access controls</li>
                    <li>Regular security assessments and updates</li>
                    <li>Restricted access to personal information on a need-to-know basis</li>
                </ul>
                <p class="text-gray-700 leading-relaxed">
                    However, no method of transmission over the internet or electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your information, we cannot guarantee absolute security.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Data Retention</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    We retain your information for as long as your account is active or as needed to provide you the Service. We may retain certain information for longer periods as required by law or for legitimate business purposes, such as:
                </p>
                <ul class="list-disc list-inside text-gray-700 space-y-2 mb-4">
                    <li>Resolving disputes and enforcing agreements</li>
                    <li>Complying with legal obligations</li>
                    <li>Maintaining business records for accounting and tax purposes</li>
                </ul>
                <p class="text-gray-700 leading-relaxed">
                    When you cancel your account, we will delete or anonymize your data within a reasonable time period, typically within 30 days, unless we are required to retain it for legal or legitimate business purposes.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Data Sharing and Disclosure</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    We do not sell, trade, or rent your personal information to third parties. We may share your information only in the following circumstances:
                </p>
                <ul class="list-disc list-inside text-gray-700 space-y-2 mb-4">
                    <li><strong>Service Providers:</strong> We may share information with third-party service providers who perform services on our behalf, such as payment processing, hosting, and analytics. These providers are contractually obligated to protect your information.</li>
                    <li><strong>Legal Requirements:</strong> We may disclose information if required to do so by law or in response to valid requests by public authorities.</li>
                    <li><strong>Business Transfers:</strong> In the event of a merger, acquisition, or sale of assets, your information may be transferred to the acquiring entity.</li>
                    <li><strong>With Your Consent:</strong> We may share information with your explicit consent.</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Your Rights and Choices</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    You have the following rights regarding your personal information:
                </p>
                <ul class="list-disc list-inside text-gray-700 space-y-2 mb-4">
                    <li><strong>Access:</strong> You can access and review your personal information through your account settings.</li>
                    <li><strong>Correction:</strong> You can update or correct your information at any time through your account.</li>
                    <li><strong>Deletion:</strong> You can request deletion of your account and associated data by contacting us.</li>
                    <li><strong>Opt-Out:</strong> You can opt out of certain communications by updating your preferences or contacting us.</li>
                </ul>
                <p class="text-gray-700 leading-relaxed">
                    To exercise these rights, please contact us at support@smartqueue.com. We will respond to your request within a reasonable timeframe.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Cookies and Tracking Technologies</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    We use cookies and similar tracking technologies to track activity on our Service and store certain information. Cookies are files with small amounts of data that are stored on your device.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent. However, if you do not accept cookies, you may not be able to use some portions of our Service.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Third-Party Links</h2>
                <p class="text-gray-700 leading-relaxed">
                    Our Service may contain links to third-party websites or services that are not owned or controlled by SmartQueue. We are not responsible for the privacy practices of these third-party sites. We encourage you to review the privacy policies of any third-party sites you visit.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Changes to This Privacy Policy</h2>
                <p class="text-gray-700 leading-relaxed">
                    We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last updated" date. You are advised to review this Privacy Policy periodically for any changes.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">11. Contact Us</h2>
                <p class="text-gray-700 leading-relaxed">
                    If you have any questions about this Privacy Policy, please contact us at:
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

