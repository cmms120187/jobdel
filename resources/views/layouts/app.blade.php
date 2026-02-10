<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/iconjobdel.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/iconjobdel.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow mt-16">
                    <div class="max-w-7xl mx-auto py-3 sm:py-6 px-3 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="pt-16 pb-16 sm:pb-20 min-h-screen">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>

            <!-- Fixed Footer -->
            <footer class="fixed bottom-0 left-0 right-0 z-40 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 shadow-lg border-t-4 border-purple-400">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                    <p class="text-center text-white text-sm font-medium">
                        © {{ date('Y') }} Copyright by WamayStore @ <a href="https://tpmcmms.id" target="_blank" rel="noopener noreferrer" class="underline hover:text-purple-200 transition-colors">tpmcmms.id</a>
                    </p>
                </div>
            </footer>
        </div>
    @stack('scripts')
    </body>
</html>
