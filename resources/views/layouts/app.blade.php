<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{
        darkMode: localStorage.getItem('darkMode') === 'true'
      }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
      :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        <wireui:scripts />
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased transition-colors duration-300" x-cloak>
        <x-notifications />

        @auth
            <div class="flex flex-col lg:flex-row min-h-screen">
                <!-- Sidebar -->
                <livewire:layout.sidebar />

                <!-- Main Content -->
                <main class="flex-1 overflow-y-auto pt-16 lg:pt-0">
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
