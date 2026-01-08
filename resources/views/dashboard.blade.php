<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 leading-tight">
                    {{ __('Dashboard') }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Selamat datang, {{ Auth::user()->name }}!</p>
            </div>
            @if(isset($isSuperuser) && $isSuperuser)
                <span class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm rounded-full gradient-purple text-purple-800 font-bold shadow-lg whitespace-nowrap">
                    ⭐ Superuser Mode
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-4 sm:py-8 bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 fade-in bg-gradient-to-r from-green-400 to-green-600 text-white px-6 py-4 rounded-xl shadow-lg flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Tasks -->
                <div class="stat-card fade-in">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="stat-card-icon gradient-blue">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <span class="text-3xl font-bold text-gray-800">{{ $stats['total_tasks'] }}</span>
                        </div>
                        <div class="text-sm font-medium text-gray-600">Total Tasks</div>
                        <div class="mt-2 text-xs text-gray-500">Semua pekerjaan</div>
                    </div>
                </div>

                <!-- Pending Tasks -->
                <div class="stat-card fade-in">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="stat-card-icon gradient-warning">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-3xl font-bold text-yellow-600">{{ $stats['pending_tasks'] }}</span>
                        </div>
                        <div class="text-sm font-medium text-gray-600">Pending Tasks</div>
                        <div class="mt-2 text-xs text-gray-500">Menunggu tindakan</div>
                    </div>
                </div>

                <!-- In Progress -->
                <div class="stat-card fade-in">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="stat-card-icon gradient-info">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <span class="text-3xl font-bold text-blue-600">{{ $stats['in_progress_tasks'] }}</span>
                        </div>
                        <div class="text-sm font-medium text-gray-600">In Progress</div>
                        <div class="mt-2 text-xs text-gray-500">Sedang dikerjakan</div>
                    </div>
                </div>

                <!-- Completed -->
                <div class="stat-card fade-in">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="stat-card-icon gradient-success">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-3xl font-bold text-green-600">{{ $stats['completed_tasks'] }}</span>
                        </div>
                        <div class="text-sm font-medium text-gray-600">Completed</div>
                        <div class="mt-2 text-xs text-gray-500">Selesai</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- My Tasks -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden fade-in">
                    <div class="gradient-blue p-4">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                @if(isset($isSuperuser) && $isSuperuser)
                                    Semua Tasks
                                @else
                                    Tasks Saya
                                @endif
                            </h3>
                            <a href="{{ route('tasks.create') }}" class="bg-white text-blue-600 font-bold py-2 px-4 rounded-lg text-sm hover:bg-blue-50 transform hover:scale-105 transition-all duration-200 shadow-md">
                                + Buat Task
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($myTasks->count() > 0)
                            <div class="space-y-3">
                                @foreach($myTasks->take(5) as $task)
                                    <a href="{{ route('tasks.show', $task) }}" class="block task-card {{ $task->status === 'completed' ? 'task-card-completed' : ($task->status === 'in_progress' ? 'task-card-progress' : 'task-card-pending') }}">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900 mb-2 hover:text-blue-600 transition-colors">
                                                    {{ $task->title }}
                                                </h4>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="badge {{ $task->status === 'completed' ? 'badge-success' : ($task->status === 'in_progress' ? 'badge-info' : 'badge-warning') }}">
                                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                    </span>
                                                    <span class="badge {{ $task->priority === 'high' ? 'badge-danger' : ($task->priority === 'medium' ? 'badge-warning' : 'badge-info') }}">
                                                        {{ ucfirst($task->priority) }} Priority
                                                    </span>
                                                    @if($task->type)
                                                        <span class="badge badge-purple">
                                                            {{ $task->type }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            <div class="mt-6 text-center">
                                <a href="{{ route('tasks.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm inline-flex items-center">
                                    Lihat semua tasks
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-gray-500 mb-4">Belum ada tasks.</p>
                                <a href="{{ route('tasks.create') }}" class="btn-primary inline-block">
                                    Buat Task Pertama
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Delegated To Me -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden fade-in">
                    <div class="gradient-green p-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            @if(isset($isSuperuser) && $isSuperuser)
                                Semua Delegasi
                            @else
                                Delegasi untuk Saya
                            @endif
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($delegatedToMe->count() > 0)
                            <div class="space-y-3">
                                @foreach($delegatedToMe->take(5) as $delegation)
                                    <a href="{{ route('delegations.show', $delegation) }}" class="block task-card task-card-progress">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900 mb-2 hover:text-green-600 transition-colors">
                                                    {{ $delegation->task->title }}
                                                </h4>
                                                <div class="flex items-center gap-2 mb-2 flex-wrap">
                                                    <span class="badge {{ $delegation->status === 'completed' ? 'badge-success' : ($delegation->status === 'accepted' || $delegation->status === 'in_progress' ? 'badge-info' : 'badge-warning') }}">
                                                        {{ ucfirst(str_replace('_', ' ', $delegation->status)) }}
                                                    </span>
                                                </div>
                                                <div class="mt-2">
                                                    <div class="flex items-center justify-between mb-1">
                                                        <span class="text-xs font-medium text-gray-600">Progress</span>
                                                        <span class="text-xs font-bold text-blue-600">{{ $delegation->progress_percentage }}%</span>
                                                    </div>
                                                    <div class="progress-bar">
                                                        <div class="progress-fill bg-gradient-to-r from-blue-500 to-purple-600" style="width: {{ $delegation->progress_percentage }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            <div class="mt-6 text-center">
                                <a href="{{ route('delegations.index') }}" class="text-green-600 hover:text-green-800 font-semibold text-sm inline-flex items-center">
                                    Lihat semua delegasi
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="text-gray-500">Tidak ada delegasi untuk Anda.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
