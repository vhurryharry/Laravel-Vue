@component('mail::message')
# Registration Successful

Welcome aboard {{ $user->firstname }}!

Thanks,<br>
{{ config('app.name') }}
@endcomponent
