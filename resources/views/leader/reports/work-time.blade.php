<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 gradient-blue rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        {{ __('Laporan Waktu Kerja Tim') }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Tracking waktu kerja tim Anda per hari</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8 bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filter Section -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <form method="GET" action="{{ route('leader.reports.work-time') }}" class="flex flex-col sm:flex-row gap-4 items-end">
                    <div class="flex-1">
                        <x-input-label for="date" :value="__('Pilih Tanggal')" />
                        <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" :value="$date" />
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary inline-flex items-center px-6 py-2">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('leader.reports.work-time', ['date' => now()->toDateString()]) }}" class="inline-flex items-center px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            Hari Ini
                        </a>
                    </div>
                </form>
                <p class="text-xs text-gray-500 mt-2">
                    Menampilkan data untuk: <strong>{{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y') }}</strong>
                </p>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Total Waktu Tim</p>
                            <p class="text-3xl font-bold text-gray-800">{{ number_format($teamTotalHours, 1) }} jam</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $teamTotalMinutes }} menit</p>
                        </div>
                        <div class="bg-blue-100 rounded-lg p-3">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Total Task Aktif</p>
                            <p class="text-3xl font-bold text-gray-800">{{ $teamTotalTasks }}</p>
                            <p class="text-xs text-gray-400 mt-1">Task yang dikerjakan</p>
                        </div>
                        <div class="bg-green-100 rounded-lg p-3">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Anggota Tim</p>
                            <p class="text-3xl font-bold text-gray-800">{{ count($workTimePerUser) }}</p>
                            <p class="text-xs text-gray-400 mt-1">User yang aktif</p>
                        </div>
                        <div class="bg-purple-100 rounded-lg p-3">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.124-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.124-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Work Time Per User -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Waktu Kerja Per User</h3>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-2">
                        <p class="text-xs text-blue-700">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Data waktu kerja diambil dari update progress yang mengisi "Waktu dari" dan "Waktu sampai"
                        </p>
                    </div>
                </div>
                
                @if(count($workTimePerUser) > 0)
                    <div class="space-y-4">
                        @foreach($workTimePerUser as $userId => $userData)
                            <div class="border-2 border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-lg mr-4">
                                            {{ strtoupper(substr($userData['user']->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-lg text-gray-800">{{ $userData['user']->name }}</h4>
                                            <p class="text-sm text-gray-500">{{ $userData['user']->nik ?? 'N/A' }}</p>
                                            @if($userData['user']->position)
                                                <p class="text-xs text-gray-400">{{ $userData['user']->position->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-blue-600">{{ $userData['formatted_time'] }}</div>
                                        <div class="text-sm text-gray-500">{{ number_format($userData['total_hours'], 2) }} jam</div>
                                        <div class="text-xs text-gray-400 mt-1">{{ $userData['task_count'] }} task</div>
                                    </div>
                                </div>

                                @if(count($userData['task_details']) > 0)
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <h5 class="font-semibold text-gray-700 mb-3 text-sm">Detail Task:</h5>
                                        <div class="space-y-2">
                                            @foreach($userData['task_details'] as $detail)
                                                <div class="bg-gray-50 rounded-lg p-3 flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <p class="font-medium text-sm text-gray-800">{{ $detail['task_item_title'] }}</p>
                                                        <p class="text-xs text-gray-500 mt-1">{{ $detail['task_title'] }}</p>
                                                        @if($detail['notes'])
                                                            <p class="text-xs text-gray-400 mt-1 italic">{{ Str::limit($detail['notes'], 100) }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="text-right ml-4">
                                                        <p class="text-sm font-semibold text-gray-700">{{ $detail['formatted_duration'] }}</p>
                                                        <p class="text-xs text-gray-500">
                                                            {{ \Carbon\Carbon::parse($detail['time_from'])->format('H:i') }} - 
                                                            {{ \Carbon\Carbon::parse($detail['time_to'])->format('H:i') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <p class="text-sm text-gray-400 italic">Belum ada task yang dikerjakan hari ini</p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            Untuk tracking waktu kerja, user perlu melakukan update progress dengan mengisi kolom "Waktu dari" dan "Waktu sampai" saat update progress task item.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500 text-lg font-medium">Tidak ada data waktu kerja</p>
                        <p class="text-gray-400 text-sm mt-1">Belum ada update progress dengan waktu kerja pada tanggal ini</p>
                    </div>
                @endif
            </div>

            <!-- Tasks In Progress -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Task yang Sedang Berjalan</h3>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-2">
                        <p class="text-xs text-yellow-700">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Menampilkan task yang memiliki detail pekerjaan (task items) di-assign ke anggota tim
                        </p>
                    </div>
                </div>
                
                @if($tasksInProgress->count() > 0)
                    <div class="space-y-4">
                        @foreach($tasksInProgress as $task)
                            @php
                                // Get all task items assigned to team members (not just in_progress)
                                $activeTaskItems = $task->taskItems->filter(function($item) {
                                    return $item->assignedUser && in_array($item->status, ['pending', 'in_progress']);
                                });
                            @endphp
                            
                            @if($activeTaskItems->count() > 0)
                                <div class="border-2 border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <h4 class="font-bold text-lg text-gray-800">
                                                <a href="{{ route('tasks.show', $task) }}" class="hover:text-blue-600">
                                                    {{ $task->title }}
                                                </a>
                                            </h4>
                                            @if($task->project_code)
                                                <p class="text-sm text-gray-500 mt-1">Project: {{ $task->project_code }}</p>
                                            @endif
                                        </div>
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </div>

                                    <div class="space-y-2 mt-4">
                                        @foreach($activeTaskItems as $item)
                                            <div class="bg-gray-50 rounded-lg p-3">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <p class="font-medium text-sm text-gray-800">{{ $item->title }}</p>
                                                        <p class="text-xs text-gray-500 mt-1">
                                                            Assigned to: <span class="font-semibold">{{ $item->assignedUser->name }}</span>
                                                        </p>
                                                        <div class="mt-2">
                                                            <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                                                <span>Status</span>
                                                                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $item->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                                    {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                                                </span>
                                                            </div>
                                                            @if($item->progress_percentage > 0)
                                                                <div class="flex items-center justify-between text-xs text-gray-600 mb-1 mt-1">
                                                                    <span>Progress</span>
                                                                    <span>{{ $item->progress_percentage }}%</span>
                                                                </div>
                                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $item->progress_percentage }}%"></div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="text-right ml-4">
                                                        @php
                                                            $todayUpdates = $item->updates->filter(function($update) use ($date) {
                                                                return $update->update_date && $update->update_date->format('Y-m-d') == $date && $update->duration_in_minutes;
                                                            });
                                                            $todayMinutes = $todayUpdates->sum(function($update) {
                                                                return $update->duration_in_minutes ?? 0;
                                                            });
                                                        @endphp
                                                        @if($todayMinutes > 0)
                                                            <p class="text-sm font-semibold text-blue-600">
                                                                {{ floor($todayMinutes / 60) }}j {{ $todayMinutes % 60 }}m
                                                            </p>
                                                            <p class="text-xs text-gray-500">Hari ini</p>
                                                        @else
                                                            <p class="text-xs text-gray-400">Belum ada</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-gray-500 text-lg font-medium">Tidak ada task yang sedang berjalan</p>
                        <p class="text-gray-400 text-sm mt-1">Semua task tim sudah selesai atau belum dimulai</p>
                        <p class="text-gray-400 text-xs mt-2 italic">
                            Catatan: Task yang ditampilkan adalah task yang memiliki detail pekerjaan (task items) yang di-assign ke anggota tim.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Back Button -->
            <div class="mt-6">
                <a href="{{ route('leader.reports.overview') }}" class="inline-flex items-center px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Overview
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
