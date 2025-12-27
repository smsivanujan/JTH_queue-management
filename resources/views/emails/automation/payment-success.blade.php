@component('mail::message')
# Payment received - Thank you!

Hello {{ $tenant->name }},

We've successfully received your payment for your SmartQueue subscription.

**Subscription Details:**

- **Plan:** {{ $subscription->plan->name ?? 'N/A' }}
- **Amount:** ${{ number_format($subscription->plan->price ?? 0, 2) }}
- **Status:** Active
@if($subscription->ends_at)
- **Next renewal:** {{ $subscription->ends_at->format('F j, Y') }}
@endif

Your subscription is now active, and you have full access to all features in your plan.

@component('mail::button', ['url' => route('app.subscription.index')])
View Subscription Details
@endcomponent

**Invoice Available:**

You can view and download your invoice from your subscription page.

Thanks for choosing SmartQueue!

Best regards,<br>
{{ config('app.name') }} Team
@endcomponent
