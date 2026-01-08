<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Job Delegation') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/iconjobdel.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/iconjobdel.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-blue-50 via-white to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-24 sm:pt-16 px-4">
            <!-- Logo and Back to Home -->
            <div class="w-full max-w-md mb-6">
                <a href="/" class="flex items-center space-x-3 justify-center sm:justify-start">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('images/iconjobdel.png') }}" alt="Job Delegation Icon" class="w-full h-full object-contain">
                    </div>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">Job Delegation</span>
                </a>
            </div>

            <!-- Form Card -->
            <div class="w-full sm:max-w-md">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-8 sm:p-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
