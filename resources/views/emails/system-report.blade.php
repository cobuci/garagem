<x-mail::message>
# Hello, {{ $userName }}!

Your system report for the period from **{{ $startDate }}** to **{{ $endDate }}** has been generated successfully.

You can find the detailed report attached to this email in PDF format.

If you have any questions, please contact support.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
