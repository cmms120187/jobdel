<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center min-w-0">
                <div class="w-10 h-10 sm:w-12 sm:h-12 gradient-purple rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="font-bold text-lg sm:text-2xl text-gray-800 leading-tight break-words">
                        {{ __('Laporan Detail: ') }} {{ $targetUser->name }}
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Informasi lengkap kinerja user</p>
                </div>
            </div>
            <a href="{{ route('leader.reports.user-reports', ['start_date' => $start, 'end_date' => $end]) }}" class="touch-target min-h-[44px] inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors self-start sm:self-center">
                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8 bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <!-- Filter Section -->
            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6">
                <form method="GET" action="{{ route('leader.reports.user-detail', ['userId' => $targetUser->id]) }}" class="flex flex-col sm:flex-row gap-4 items-end">
                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="start_date" :value="__('Tanggal Mulai')" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="request('start_date', $start)" />
                        </div>
                        <div>
                            <x-input-label for="end_date" :value="__('Tanggal Akhir')" />
                            <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="request('end_date', $end)" />
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="touch-target min-h-[44px] btn-primary inline-flex items-center px-4 sm:px-6 py-2.5">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('leader.reports.user-detail', ['userId' => $targetUser->id]) }}" class="touch-target min-h-[44px] inline-flex items-center px-4 sm:px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
                <p class="text-xs text-gray-500 mt-2">
                    Periode: <strong>{{ \Carbon\Carbon::parse($start)->locale('id')->translatedFormat('d F Y') }}</strong> sampai <strong>{{ \Carbon\Carbon::parse($end)->locale('id')->translatedFormat('d F Y') }}</strong>
                </p>
            </div>

            <!-- User Info Card -->
            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-2xl sm:text-3xl flex-shrink-0">
                        {{ strtoupper(substr($targetUser->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-1 break-words">{{ $targetUser->name }}</h3>
                        <p class="text-sm sm:text-base text-gray-600 mb-2 break-all">{{ $targetUser->email }}</p>
                        <div class="flex flex-wrap gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">NIK:</span>
                                <span class="font-semibold text-gray-800 ml-2">{{ $targetUser->nik ?? 'N/A' }}</span>
                            </div>
                            @if($targetUser->position)
                                <div>
                                    <span class="text-gray-500">Posisi:</span>
                                    <span class="font-semibold text-gray-800 ml-2">{{ $targetUser->position->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Tasks Created -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <span class="text-3xl font-bold text-gray-800">{{ $tasksCreatedStats['total'] }}</span>
                    </div>
                    <div class="text-sm font-medium text-gray-600 mb-2">Tasks Dibuat</div>
                    <div class="flex gap-2 text-xs">
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded">{{ $tasksCreatedStats['pending'] }} Pending</span>
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">{{ $tasksCreatedStats['in_progress'] }} Progress</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded">{{ $tasksCreatedStats['completed'] }} Done</span>
                    </div>
                </div>

                <!-- Task Items Assigned -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span class="text-3xl font-bold text-gray-800">{{ $taskItemsStats['total'] }}</span>
                    </div>
                    <div class="text-sm font-medium text-gray-600 mb-2">Task Items</div>
                    <div class="flex gap-2 text-xs">
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded">{{ $taskItemsStats['pending'] }} Pending</span>
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">{{ $taskItemsStats['in_progress'] }} Progress</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded">{{ $taskItemsStats['completed'] }} Done</span>
                    </div>
                </div>

                <!-- Delegations -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <span class="text-3xl font-bold text-gray-800">{{ $delegationsReceived->count() }}</span>
                    </div>
                    <div class="text-sm font-medium text-gray-600 mb-2">Delegasi Diterima</div>
                    <div class="text-xs text-gray-500">Diberikan: {{ $delegationsGiven->count() }}</div>
                </div>

                <!-- Work Time -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-gray-800">
                            @php
                                $hours = floor($totalMinutes / 60);
                                $mins = $totalMinutes % 60;
                            @endphp
                            @if($hours > 0){{ $hours }}j @endif{{ $mins }}m
                        </span>
                    </div>
                    <div class="text-sm font-medium text-gray-600 mb-2">Total Waktu Kerja</div>
                    <div class="text-xs text-gray-500 mb-1">{{ number_format($totalMinutes / 60, 2) }} jam</div>
                    <div class="text-xs text-gray-500">
                        Rata-rata: <span class="font-semibold">{{ number_format($averageMinutesPerDay, 1) }} menit/hari</span>
                        <span class="text-gray-400">({{ $daysWithWork }} hari kerja dari {{ $totalDaysInRange }} hari)</span>
                    </div>
                </div>
            </div>

            <!-- Tasks Created Section -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Tasks Dibuat ({{ $tasksCreated->count() }})
                </h3>
                @if($tasksCreated->count() > 0)
                    <div class="space-y-3">
                        @foreach($tasksCreated->take(10) as $task)
                            <a href="{{ route('tasks.show', $task) }}" class="block p-4 border-2 border-gray-200 rounded-lg hover:border-blue-400 hover:shadow-md transition-all">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 mb-2">{{ $task->title }}</h4>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="badge {{ $task->status === 'completed' ? 'badge-success' : ($task->status === 'in_progress' ? 'badge-info' : 'badge-warning') }}">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                            @if($task->room)
                                                <span class="text-xs text-gray-500">{{ $task->room->name }}</span>
                                            @endif
                                            <span class="text-xs text-gray-500">{{ $task->created_at->locale('id')->translatedFormat('d F Y') }}</span>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                        @if($tasksCreated->count() > 10)
                            <p class="text-sm text-gray-500 text-center mt-4">Menampilkan 10 dari {{ $tasksCreated->count() }} tasks</p>
                        @endif
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Tidak ada tasks yang dibuat pada periode ini.</p>
                @endif
            </div>

            <!-- Task Items Assigned Section -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Task Items yang Di-assign ({{ $taskItemsAssigned->count() }})
                </h3>
                @if($taskItemsAssigned->count() > 0)
                    <div class="space-y-3">
                        @foreach($taskItemsAssigned->take(10) as $taskItem)
                            <a href="{{ route('tasks.show', $taskItem->task) }}" class="block p-4 border-2 border-gray-200 rounded-lg hover:border-purple-400 hover:shadow-md transition-all">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 mb-1">{{ $taskItem->title }}</h4>
                                        <p class="text-sm text-gray-600 mb-2">Task: {{ $taskItem->task->title }}</p>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="badge {{ $taskItem->status === 'completed' ? 'badge-success' : ($taskItem->status === 'in_progress' ? 'badge-info' : 'badge-warning') }}">
                                                {{ ucfirst(str_replace('_', ' ', $taskItem->status)) }}
                                            </span>
                                            <span class="text-xs text-gray-500">Progress: {{ $taskItem->progress_percentage }}%</span>
                                            @if($taskItem->task->room)
                                                <span class="text-xs text-gray-500">{{ $taskItem->task->room->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                        @if($taskItemsAssigned->count() > 10)
                            <p class="text-sm text-gray-500 text-center mt-4">Menampilkan 10 dari {{ $taskItemsAssigned->count() }} task items</p>
                        @endif
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Tidak ada task items yang di-assign pada periode ini.</p>
                @endif
            </div>

            <!-- Work Time Details Section -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Detail Waktu Kerja
                    </h3>
                    @if(count($workTimeByDate) > 0)
                        <div class="bg-orange-50 border border-orange-200 rounded-lg px-4 py-2">
                            <div class="text-xs text-orange-700">
                                <span class="font-semibold">Rata-rata per hari:</span> {{ number_format($averageMinutesPerDay, 1) }} menit
                                <span class="text-orange-600">({{ $daysWithWork }} hari kerja)</span>
                            </div>
                        </div>
                    @endif
                </div>
                @if(count($workTimeByDate) > 0)
                    <div class="space-y-4">
                        @foreach($workTimeByDate as $dateData)
                            <div class="border-2 border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-800">{{ $dateData['formatted_date'] }}</h4>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Rata-rata: <span class="font-semibold">{{ number_format($averageMinutesPerDay, 1) }} menit/hari</span>
                                            @if($dateData['minutes'] > $averageMinutesPerDay)
                                                <span class="text-green-600">↑ {{ number_format($dateData['minutes'] - $averageMinutesPerDay, 1) }} menit di atas rata-rata</span>
                                            @elseif($dateData['minutes'] < $averageMinutesPerDay && $dateData['minutes'] > 0)
                                                <span class="text-red-600">↓ {{ number_format($averageMinutesPerDay - $dateData['minutes'], 1) }} menit di bawah rata-rata</span>
                                            @endif
                                        </p>
                                    </div>
                                    <span class="text-lg font-bold text-orange-600">
                                        @php
                                            $hours = floor($dateData['minutes'] / 60);
                                            $mins = $dateData['minutes'] % 60;
                                        @endphp
                                        @if($hours > 0){{ $hours }}j @endif{{ $mins }}m
                                    </span>
                                </div>
                                <div class="space-y-2">
                                    @foreach($dateData['delegations'] as $delegationData)
                                        @php
                                            $delegation = $delegationData['delegation'];
                                            $duration = $delegationData['duration_minutes'];
                                        @endphp
                                        @if($duration > 0)
                                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-800">{{ $delegationData['task_title'] }}</p>
                                                    <p class="text-xs text-gray-500">
                                                        Mulai: {{ $delegationData['accepted_at']->locale('id')->translatedFormat('d M Y H:i') }}
                                                        @if($delegationData['completed_at'])
                                                            | Selesai: {{ $delegationData['completed_at']->locale('id')->translatedFormat('d M Y H:i') }}
                                                        @else
                                                            | Masih berjalan
                                                        @endif
                                                    </p>
                                                </div>
                                                <span class="text-sm font-semibold text-gray-700">
                                                    @php
                                                        $hours = floor($duration / 60);
                                                        $mins = $duration % 60;
                                                    @endphp
                                                    @if($hours > 0){{ $hours }}j @endif{{ $mins }}m
                                                </span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Tidak ada data waktu kerja pada periode ini.</p>
                @endif
            </div>

            <!-- Delegations Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Delegations Received -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        Delegasi Diterima ({{ $delegationsReceived->count() }})
                    </h3>
                    @if($delegationsReceived->count() > 0)
                        <div class="space-y-3">
                            @foreach($delegationsReceived->take(5) as $delegation)
                                <a href="{{ route('delegations.show', $delegation) }}" class="block p-3 border-2 border-gray-200 rounded-lg hover:border-green-400 hover:shadow-md transition-all">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900 mb-1">{{ $delegation->task->title }}</h4>
                                            <p class="text-xs text-gray-500 mb-2">Dari: {{ $delegation->delegatedBy->name }}</p>
                                            <span class="badge {{ $delegation->status === 'completed' ? 'badge-success' : ($delegation->status === 'accepted' || $delegation->status === 'in_progress' ? 'badge-info' : 'badge-warning') }}">
                                                {{ ucfirst(str_replace('_', ' ', $delegation->status)) }}
                                            </span>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </a>
                            @endforeach
                            @if($delegationsReceived->count() > 5)
                                <p class="text-sm text-gray-500 text-center mt-4">Menampilkan 5 dari {{ $delegationsReceived->count() }} delegasi</p>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">Tidak ada delegasi yang diterima pada periode ini.</p>
                    @endif
                </div>

                <!-- Delegations Given -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        Delegasi Diberikan ({{ $delegationsGiven->count() }})
                    </h3>
                    @if($delegationsGiven->count() > 0)
                        <div class="space-y-3">
                            @foreach($delegationsGiven->take(5) as $delegation)
                                <a href="{{ route('delegations.show', $delegation) }}" class="block p-3 border-2 border-gray-200 rounded-lg hover:border-blue-400 hover:shadow-md transition-all">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900 mb-1">{{ $delegation->task->title }}</h4>
                                            <p class="text-xs text-gray-500 mb-2">Kepada: {{ $delegation->delegatedTo->name }}</p>
                                            <span class="badge {{ $delegation->status === 'completed' ? 'badge-success' : ($delegation->status === 'accepted' || $delegation->status === 'in_progress' ? 'badge-info' : 'badge-warning') }}">
                                                {{ ucfirst(str_replace('_', ' ', $delegation->status)) }}
                                            </span>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </a>
                            @endforeach
                            @if($delegationsGiven->count() > 5)
                                <p class="text-sm text-gray-500 text-center mt-4">Menampilkan 5 dari {{ $delegationsGiven->count() }} delegasi</p>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">Tidak ada delegasi yang diberikan pada periode ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
