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

        <title>{{ __('Access Denied') }} - {{ config('app.name') }}</title>

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
        </script>

        <wireui:scripts />
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased transition-colors duration-300 min-h-screen flex items-center justify-center p-6" x-cloak>
        <div class="max-w-md w-full text-center">
            <!-- Icon/Illustration -->
            <div class="mb-6 relative flex justify-center">
                <div class="absolute inset-0 bg-primary-500/20 blur-3xl rounded-full scale-125 animate-pulse"></div>
                <div class="relative bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
                    <svg class="w-20 h-20 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>

            <h1 class="text-7xl font-black text-gray-900 dark:text-white tracking-tighter">403</h1>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2 tracking-tight">
                {{ __('Restricted Access') }}
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-8 leading-relaxed">
                {{ __('Oops! It seems you do not have the necessary permissions to access this page.') }}
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <x-button
                    lg
                    outline
                    secondary
                    icon="arrow-left"
                    :label="__('Go Back')"
                    onclick="window.history.back()"
                />

                <x-button
                    lg
                    primary
                    icon="home"
                    :label="__('Back to Home')"
                    href="{{ url('/') }}"
                />
            </div>

            <div class="mt-12 text-sm text-gray-400 dark:text-gray-500">
                &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
            </div>
        </div>

        @livewireScripts
    </body>
</html>
