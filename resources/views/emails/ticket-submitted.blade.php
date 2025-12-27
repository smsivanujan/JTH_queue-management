@component('mail::message')
# New Support Ticket Submitted

A new support ticket has been submitted by **{{ $ticket->user->name }}** from **{{ $ticket->tenant->name }}**.

**Subject:** {{ $ticket->subject }}

**Category:** {{ ucfirst(str_replace('_', ' ', $ticket->category)) }}

**Priority:** {{ ucfirst($ticket->priority) }}

**Message:**
{{ $ticket->message }}

@component('mail::button', ['url' => route('platform.support.show', $ticket)])
View Ticket
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
