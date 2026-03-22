<x-mail::message>
# {{ __('login.otp_subject') }}

{{ __('login.welcome_back') }},

{{ __('login.otp_line1', ['otp' => '']) }}

<x-mail::panel>
<div style="text-align: center; font-size: 32px; font-weight: 800; letter-spacing: 0.25em; color: #0ea5e9; font-family: 'Courier New', Courier, monospace;">
{{ $otp }}
</div>
</x-mail::panel>

{{ __('login.otp_line2') }}

{{ __('login.otp_line3') }}

{{ __('Regards') }},<br>
{{ config('app.name') }}
</x-mail::message>
