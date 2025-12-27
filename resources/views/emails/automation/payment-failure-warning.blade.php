@component('mail::message')
# Payment issue - Action required

Hello {{ $tenant->name }},

We encountered an issue processing your payment for your SmartQueue subscription.

**Subscription Details:**

- **Plan:** {{ $subscription->plan->name ?? 'N/A' }}
- **Status:** {{ ucfirst($subscription->status) }}
@if($subscription->ends_at)
- **Expires:** {{ $subscription->ends_at->format('F j, Y') }}
@endif

**What this means:**

- Your account remains active during a grace period
- Please update your payment method or contact support
- We'll retry the payment automatically

**What to do next:**

1. Review your payment method in your subscription settings
2. Ensure your payment card has sufficient funds
3. Contact support if you need assistance

@component('mail::button', ['url' => route('app.subscription.index')])
Update Payment Method
@endcomponent

@component('mail::button', ['url' => route('app.support.index'), 'color' => 'green'])
Contact Support
@endcomponent

**Need help?**

Our support team is here to assist you. Please don't hesitate to reach out if you have any questions or concerns.

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
