<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Aplikasi Job Delegation - {{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/iconjobdel.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/iconjobdel.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
            @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-blue-50 via-white to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="min-h-screen flex flex-col">
            <!-- Header -->
            <header class="w-full py-4 px-6">
                <nav class="max-w-7xl mx-auto flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('images/iconjobdel.png') }}" alt="Job Delegation Icon" class="w-full h-full object-contain">
                        </div>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">Job Delegation</span>
                    </div>
                    <div class="flex items-center space-x-4">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                                class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors"
                        >
                            Dashboard
                        </a>
                    @else
                            @if (Route::has('login'))
                        <a
                            href="{{ route('login') }}"
                                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all shadow-md hover:shadow-lg font-medium"
                                >
                                    Masuk
                            </a>
                        @endif
                    @endauth
                    </div>
                </nav>
        </header>

            <!-- Main Content -->
            <main class="flex-1 flex items-center justify-center px-6 py-12">
                <div class="max-w-6xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Left Content -->
                    <div class="text-center lg:text-left space-y-8">
                        <div class="space-y-4">
                            <h1 class="text-4xl lg:text-5xl xl:text-6xl font-bold text-gray-900 dark:text-white leading-tight">
                                Kelola Delegasi Pekerjaan dengan
                                <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Mudah & Efisien</span>
                            </h1>
                            <p class="text-lg lg:text-xl text-gray-600 dark:text-gray-300 max-w-2xl">
                                Sistem manajemen delegasi pekerjaan yang membantu tim Anda dalam membuat, melacak, dan memantau progress setiap tugas secara real-time.
                            </p>
                        </div>

                        <!-- Features -->
                        <div class="grid sm:grid-cols-2 gap-4 pt-4">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Manajemen Task</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Buat, edit, dan kelola tugas dengan mudah</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Delegasi Pekerjaan</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Delegasikan tugas ke anggota tim</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Tracking Progress</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Pantau progress real-time setiap tugas</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Dashboard Overview</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Lihat semua tugas dan delegasi dalam satu tempat</p>
                                </div>
                            </div>
                        </div>

                        <!-- CTA Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                            @if (Route::has('login'))
                                <a
                                    href="{{ route('login') }}"
                                    class="inline-flex items-center justify-center px-8 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl font-semibold text-lg"
                                >
                                    Masuk ke Aplikasi
                                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Right Content / Illustration -->
                    <div class="relative hidden lg:block">
                        <div class="relative z-10 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 border border-gray-200 dark:border-gray-700">
                            <div class="space-y-6">
                                <!-- Task Card -->
                                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Task Management</h3>
                                        <span class="px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded-full">High</span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Mengelola dan melacak semua tugas tim</p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-semibold">JD</div>
                                            <span class="text-sm text-gray-700 dark:text-gray-300">John Doe</span>
                                        </div>
                                        <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: 75%"></div>
                                        </div>
                                    </div>
                </div>

                                <!-- Delegation Card -->
                                <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6 border border-green-200 dark:border-green-800">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Delegation Tracking</h3>
                                        <span class="px-3 py-1 bg-green-600 text-white text-xs font-medium rounded-full">Active</span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Monitor progress delegasi secara real-time</p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white text-xs font-semibold">JS</div>
                                            <span class="text-sm text-gray-700 dark:text-gray-300">Jane Smith</span>
                                        </div>
                                        <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-green-600 h-2 rounded-full" style="width: 90%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Stats -->
                                <div class="grid grid-cols-3 gap-4 pt-4">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">50+</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">Tasks</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">30+</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">Delegations</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">85%</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">Completed</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Decorative elements -->
                        <div class="absolute -top-4 -right-4 w-24 h-24 bg-blue-200 dark:bg-blue-900 rounded-full opacity-20 blur-2xl"></div>
                        <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-indigo-200 dark:bg-indigo-900 rounded-full opacity-20 blur-2xl"></div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
