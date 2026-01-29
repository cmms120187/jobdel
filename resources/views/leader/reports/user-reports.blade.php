<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center min-w-0">
                <div class="w-10 h-10 sm:w-12 sm:h-12 gradient-purple rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="font-bold text-lg sm:text-2xl text-gray-800 leading-tight break-words">
                        {{ __('Laporan Detail Per User') }}
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Ringkasan kinerja tim Anda</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8 bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <!-- Filter Section -->
            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6">
                <form method="GET" action="{{ route('leader.reports.user-reports') }}" class="flex flex-col sm:flex-row gap-4 items-end">
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
                        <a href="{{ route('leader.reports.user-reports') }}" class="touch-target min-h-[44px] inline-flex items-center px-4 sm:px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
                <p class="text-xs text-gray-500 mt-2">
                    Periode: <strong>{{ \Carbon\Carbon::parse($start)->locale('id')->translatedFormat('d F Y') }}</strong> sampai <strong>{{ \Carbon\Carbon::parse($end)->locale('id')->translatedFormat('d F Y') }}</strong>
                </p>
            </div>

            <!-- User Reports List -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($userReports as $report)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                        <div class="gradient-blue p-4 sm:p-6">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="flex items-center flex-1 min-w-0">
                                    <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-white font-bold text-xl sm:text-2xl mr-3 sm:mr-4 flex-shrink-0">
                                        {{ strtoupper(substr($report['user']->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-base sm:text-lg font-bold text-white mb-1 truncate">{{ $report['user']->name }}</h3>
                                        <p class="text-xs sm:text-sm text-white/80">{{ $report['user']->nik ?? 'N/A' }}</p>
                                        @if($report['user']->position)
                                            <p class="text-xs text-white/70 mt-1">{{ $report['user']->position->name }}</p>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('leader.reports.user-detail', ['userId' => $report['user']->id, 'start_date' => $start, 'end_date' => $end]) }}" class="touch-target min-h-[44px] bg-white text-blue-600 font-bold py-2 px-4 rounded-lg text-sm hover:bg-blue-50 transform hover:scale-105 transition-all duration-200 shadow-md inline-flex items-center justify-center self-start sm:self-center">
                                    Detail
                                </a>
                            </div>
                        </div>
                        
                        <div class="p-4 sm:p-6">
                            <!-- Statistics Grid -->
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <!-- Tasks Created -->
                                <div class="bg-blue-50 rounded-lg p-4 border-2 border-blue-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-medium text-blue-700">Tasks Dibuat</span>
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </div>
                                    <div class="text-2xl font-bold text-blue-800 mb-1">{{ $report['tasks_created'] }}</div>
                                    <div class="flex gap-1 text-xs">
                                        <span class="text-yellow-600">{{ $report['tasks_created_stats']['pending'] }} P</span>
                                        <span class="text-blue-600">{{ $report['tasks_created_stats']['in_progress'] }} IP</span>
                                        <span class="text-green-600">{{ $report['tasks_created_stats']['completed'] }} C</span>
                                    </div>
                                </div>

                                <!-- Task Items Assigned -->
                                <div class="bg-purple-50 rounded-lg p-4 border-2 border-purple-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-medium text-purple-700">Task Items</span>
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="text-2xl font-bold text-purple-800 mb-1">{{ $report['task_items_assigned'] }}</div>
                                    <div class="flex gap-1 text-xs">
                                        <span class="text-yellow-600">{{ $report['task_items_stats']['pending'] }} P</span>
                                        <span class="text-blue-600">{{ $report['task_items_stats']['in_progress'] }} IP</span>
                                        <span class="text-green-600">{{ $report['task_items_stats']['completed'] }} C</span>
                                    </div>
                                </div>

                                <!-- Delegations -->
                                <div class="bg-green-50 rounded-lg p-4 border-2 border-green-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-medium text-green-700">Delegasi Diterima</span>
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                    <div class="text-2xl font-bold text-green-800">{{ $report['delegations_received'] }}</div>
                                </div>

                                <!-- Work Time -->
                                <div class="bg-orange-50 rounded-lg p-4 border-2 border-orange-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-medium text-orange-700">Waktu Kerja</span>
                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="text-lg font-bold text-orange-800">
                                        @php
                                            $hours = floor($report['total_work_minutes'] / 60);
                                            $mins = $report['total_work_minutes'] % 60;
                                        @endphp
                                        @if($hours > 0)
                                            {{ $hours }} jam
                                            @if($mins > 0) {{ $mins }} menit @endif
                                        @else
                                            {{ $mins }} menit
                                        @endif
                                    </div>
                                    <div class="text-xs text-orange-600 mt-1">{{ number_format($report['total_work_minutes'], 0) }} menit</div>
                                    @if($report['days_with_work'] > 0)
                                        <div class="text-xs text-orange-500 mt-1">
                                            Rata-rata: {{ number_format($report['average_minutes_per_day'], 1) }} menit/hari
                                            <span class="text-orange-400">({{ $report['days_with_work'] }} hari kerja)</span>
                                        </div>
                                    @else
                                        <div class="text-xs text-orange-500 mt-1">Rata-rata: 0 menit/hari</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex items-center justify-between text-sm mb-3">
                                    <span class="text-gray-600">Delegasi Diberikan:</span>
                                    <span class="font-semibold text-gray-800">{{ $report['delegations_given'] }}</span>
                                </div>
                                
                                <!-- Detail Waktu Kerja Per Hari -->
                                @if(count($report['work_time_by_date']) > 0)
                                    <div x-data="{ showDetail: false }" class="border-t border-gray-200 pt-3">
                                        <button 
                                            @click="showDetail = !showDetail" 
                                            class="w-full flex items-center justify-between text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors"
                                        >
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Detail Waktu Kerja Per Hari
                                            </span>
                                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': showDetail }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        
                                        <div x-show="showDetail" x-transition class="mt-3 space-y-2 max-h-64 overflow-y-auto">
                                            @foreach($report['work_time_by_date'] as $dateData)
                                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg border border-gray-200">
                                                    <div class="flex-1">
                                                        <div class="text-xs font-medium text-gray-700">{{ $dateData['formatted_date'] }}</div>
                                                        <div class="text-xs text-gray-500">
                                                            @php
                                                                $hours = floor($dateData['minutes'] / 60);
                                                                $mins = $dateData['minutes'] % 60;
                                                            @endphp
                                                            @if($hours > 0){{ $hours }} jam @endif{{ $mins }} menit
                                                        </div>
                                                    </div>
                                                    <div class="text-sm font-bold text-orange-600">
                                                        {{ number_format($dateData['minutes'], 0) }}m
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if(count($userReports) === 0)
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg">Tidak ada data untuk periode yang dipilih.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
