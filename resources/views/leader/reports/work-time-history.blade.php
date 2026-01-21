<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 gradient-blue rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        {{ __('Efektivitas Waktu Kerja Tim') }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Laporan efektivitas waktu kerja tim untuk periode tertentu</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8 bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filter Section -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <form method="GET" action="{{ route('leader.reports.work-time-history') }}" class="flex flex-col sm:flex-row gap-4 items-end">
                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="start_date" :value="__('Tanggal Mulai')" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="$startDate" />
                        </div>
                        <div>
                            <x-input-label for="end_date" :value="__('Tanggal Akhir')" />
                            <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="$endDate" />
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary inline-flex items-center px-6 py-2">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('leader.reports.work-time-history', ['start_date' => now()->startOfWeek()->toDateString(), 'end_date' => now()->endOfWeek()->toDateString()]) }}" class="inline-flex items-center px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            Minggu Ini
                        </a>
                        <a href="{{ route('leader.reports.work-time-history', ['start_date' => now()->startOfMonth()->toDateString(), 'end_date' => now()->endOfMonth()->toDateString()]) }}" class="inline-flex items-center px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            Bulan Ini
                        </a>
                    </div>
                </form>
                <p class="text-xs text-gray-500 mt-2">
                    Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('d F Y') }}</strong> sampai <strong>{{ \Carbon\Carbon::parse($endDate)->locale('id')->translatedFormat('d F Y') }}</strong>
                    ({{ $daysInRange }} hari)
                </p>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Total Waktu</p>
                            <p class="text-3xl font-bold text-gray-800">{{ number_format($overallTotalHours, 1) }} jam</p>
                            <p class="text-xs text-gray-400 mt-1">
                                @php
                                    $hours = floor($overallTotalMinutes / 60);
                                    $mins = $overallTotalMinutes % 60;
                                    echo $hours > 0 ? $hours . ' jam ' . $mins . ' menit' : $mins . ' menit';
                                @endphp
                            </p>
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
                            <p class="text-sm text-gray-500 mb-1">Rata-rata per Hari</p>
                            <p class="text-3xl font-bold text-gray-800">{{ number_format($averageMinutesPerDay / 60, 1) }} jam</p>
                            <p class="text-xs text-gray-400 mt-1">
                                @php
                                    $avgHours = floor($averageMinutesPerDay / 60);
                                    $avgMins = $averageMinutesPerDay % 60;
                                    echo $avgHours > 0 ? $avgHours . ' jam ' . $avgMins . ' menit' : $avgMins . ' menit';
                                @endphp
                            </p>
                        </div>
                        <div class="bg-green-100 rounded-lg p-3">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Anggota Tim</p>
                            <p class="text-3xl font-bold text-gray-800">{{ count($workTimeData) }}</p>
                            <p class="text-xs text-gray-400 mt-1">User aktif</p>
                        </div>
                        <div class="bg-purple-100 rounded-lg p-3">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.124-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.124-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Hari Kerja</p>
                            <p class="text-3xl font-bold text-gray-800">{{ $daysInRange }}</p>
                            <p class="text-xs text-gray-400 mt-1">Hari dalam periode</p>
                        </div>
                        <div class="bg-yellow-100 rounded-lg p-3">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Trend Waktu Kerja Harian</h3>
                <div class="relative" style="height: 400px;">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>

            <!-- Daily Breakdown -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Rincian Harian</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-blue-600 to-purple-600">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider">Total Waktu</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider">User Aktif</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($dailyTotals as $daily)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $daily['formatted_date'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm font-semibold text-blue-600">{{ $daily['formatted_time'] }}</span>
                                        <p class="text-xs text-gray-500">{{ number_format($daily['total_hours'], 2) }} jam</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                        {{ $daily['user_count'] }} user
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- User Effectiveness -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Efektivitas Per User</h3>
                
                @if(count($workTimeData) > 0)
                    <div class="space-y-4">
                        @foreach($workTimeData as $userData)
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
                                        <div class="text-2xl font-bold text-blue-600">
                                            @php
                                                $userHours = floor($userData['total_minutes'] / 60);
                                                $userMins = $userData['total_minutes'] % 60;
                                                echo $userHours > 0 ? $userHours . ' jam ' . $userMins . ' menit' : $userMins . ' menit';
                                            @endphp
                                        </div>
                                        <div class="text-sm text-gray-500">{{ number_format($userData['total_hours'], 2) }} jam total</div>
                                        <div class="text-xs text-gray-400 mt-1">Rata-rata: {{ number_format($userData['average_minutes_per_day'] / 60, 1) }} jam/hari</div>
                                        <div class="text-xs text-gray-400">{{ $userData['days_worked'] }} hari bekerja</div>
                                    </div>
                                </div>

                                <!-- Daily breakdown for this user -->
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <h5 class="font-semibold text-gray-700 mb-3 text-sm">Rincian Harian:</h5>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-7 gap-2">
                                        @foreach($userData['daily_work_time'] as $dateStr => $dayData)
                                            <div class="bg-gray-50 rounded-lg p-2 text-center {{ $dayData['minutes'] > 0 ? 'bg-blue-50 border border-blue-200' : '' }}">
                                                <p class="text-xs text-gray-500 mb-1">{{ \Carbon\Carbon::parse($dateStr)->format('d/m') }}</p>
                                                <p class="text-xs font-semibold {{ $dayData['minutes'] > 0 ? 'text-blue-600' : 'text-gray-400' }}">
                                                    @if($dayData['minutes'] > 0)
                                                        @php
                                                            $dHours = floor($dayData['minutes'] / 60);
                                                            $dMins = $dayData['minutes'] % 60;
                                                            echo $dHours > 0 ? $dHours . 'j ' . $dMins . 'm' : $dMins . 'm';
                                                        @endphp
                                                    @else
                                                        -
                                                    @endif
                                                </p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="text-gray-500 text-lg font-medium">Tidak ada data</p>
                        <p class="text-gray-400 text-sm mt-1">Belum ada tracking waktu kerja pada periode ini</p>
                    </div>
                @endif
            </div>

            <!-- Navigation Buttons -->
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('leader.reports.work-time') }}" class="inline-flex items-center px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Laporan Harian
                </a>
                <a href="{{ route('leader.reports.overview') }}" class="inline-flex items-center px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Overview
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        (function(){
            const ctx = document.getElementById('dailyChart').getContext('2d');
            const labels = {!! json_encode($chartLabels) !!};
            const data = {!! json_encode($chartData) !!};

            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(99, 102, 241, 0.8)');
            gradient.addColorStop(1, 'rgba(99, 102, 241, 0.2)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Waktu Kerja (Jam)',
                        data: data,
                        borderColor: 'rgb(99, 102, 241)',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: 'rgb(99, 102, 241)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14,
                                    weight: '500'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return 'Total: ' + context.parsed.y.toFixed(2) + ' jam';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                maxRotation: 45,
                                minRotation: 45
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    size: 12
                                },
                                callback: function(value) {
                                    return value.toFixed(1) + ' jam';
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        }
                    }
                }
            });
        })();
    </script>
    @endpush
</x-app-layout>
