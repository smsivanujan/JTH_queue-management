@component('mail::message')
# Get started with SmartQueue

Hello {{ $tenant->name }},

@if($isNewTenant)
We noticed you haven't created your first queue yet. Let's get you started with SmartQueue!
@else
We noticed you haven't been active recently. Here's a friendly reminder to make the most of your SmartQueue subscription.
@endif

**Quick Start Guide:**

1. **Create a Clinic** - Set up your first location
2. **Add Services** - Define the services you offer
3. **Open a Queue** - Start managing your customer flow

It only takes a few minutes to get started, and we're here to help every step of the way.

@component('mail::button', ['url' => route('app.dashboard')])
Go to Dashboard
@endcomponent

**Need help?**

If you're facing any challenges or have questions, please reach out to our support team - we're happy to help!

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
