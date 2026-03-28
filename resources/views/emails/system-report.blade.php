<x-mail::message>
# {{ __('reports.email.greeting', ['name' => $userName]) }}

{{ __('reports.email.intro', ['start' => $startDate, 'end' => $endDate]) }}

{{ __('reports.email.body') }}

{{ __('reports.email.support') }}

{{ __('Regards') }},<br>
{{ config('app.name') }}
</x-mail::message>
