<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{
        darkMode: localStorage.getItem('darkMode') === 'true' ||
                 (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
      }"
      x-init="
          $watch('darkMode', val => {
              localStorage.setItem('darkMode', val);
              if (val) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          })
      "
      :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        @php
            $favicon = \App\Models\Banner::logoUrl();
        @endphp
        @if ($favicon)
            <link rel="icon" href="{{ $favicon }}" type="image/png">
            <link rel="apple-touch-icon" href="{{ $favicon }}">
        @else
            <link rel="icon" href="/favicon.ico" sizes="any">
        @endif

        <script>
            function applyDarkMode() {
                if (localStorage.getItem('darkMode') === 'true' ||
                    (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
            applyDarkMode();
            document.addEventListener('livewire:navigated', applyDarkMode);
        </script>

        <wireui:scripts />
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased transition-colors duration-300" x-cloak>
        <x-notifications />

        @auth
            <div class="flex flex-col lg:flex-row min-h-screen relative">
                <!-- Sidebar -->
                <livewire:layout.sidebar />

                <!-- Main Content -->
                <main class="flex-1 min-w-0 lg:ml-64 pt-16 lg:pt-0 min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col overflow-x-clip">
                    <div class="flex-1 p-4 lg:p-8 flex flex-col w-full">
                        {{ $slot }}
                    </div>
                </main>

                <!-- Changelog Modal -->
                <livewire:changelog.modal />
            </div>
        @else
            {{ $slot }}
        @endauth

        @livewireScripts
    </body>
</html>
