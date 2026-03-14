<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        <wireui:scripts />
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="bg-gray-50 font-sans antialiased">
        <x-notifications />

        @auth
            <div class="flex flex-col lg:flex-row min-h-screen">
                <!-- Sidebar -->
                <livewire:layout.sidebar />

                <!-- Main Content -->
                <main class="flex-1 overflow-y-auto">
                    <div class="p-4 lg:p-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        @else
            {{ $slot }}
        @endauth

        @livewireScripts
    </body>
</html>
