@component('mail::message')
# @if($daysRemaining === 0)
Your trial period has ended
@elseif($daysRemaining === 3)
Your trial ends in 3 days
@else
Your trial ends in 7 days
@endif

Hello {{ $tenant->name }},

@if($daysRemaining === 0)
Your free trial period ended today. To continue using SmartQueue, please subscribe to one of our plans.
@elseif($daysRemaining === 3)
Your free trial period ends in just 3 days! Don't miss out on uninterrupted queue management for your organization.
@else
Your free trial period ends in 7 days. We hope you're enjoying SmartQueue and ready to continue your journey with us.
@endif

**What happens next?**

- Your account will continue to work during a grace period
- Subscribe to a plan to ensure uninterrupted service
- You can upgrade at any time from your dashboard

@component('mail::button', ['url' => route('app.plans.index')])
View Plans & Subscribe
@endcomponent

**Need help?**

If you have any questions or need assistance choosing the right plan, please don't hesitate to reach out to our support team.

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
