<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 gradient-blue rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        {{ __('Laporan Ringkas Tim') }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Ringkasan tugas tim Anda</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8 bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filter Section -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <form method="GET" action="{{ route('leader.reports.overview') }}" class="flex flex-col sm:flex-row gap-4 items-end">
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
                        <button type="submit" class="btn-primary inline-flex items-center px-6 py-2">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('leader.reports.overview') }}" class="inline-flex items-center px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
                <p class="text-xs text-gray-500 mt-2">
                    Periode: <strong>{{ \Carbon\Carbon::parse($start)->locale('id')->translatedFormat('d F Y') }}</strong> sampai <strong>{{ \Carbon\Carbon::parse($end)->locale('id')->translatedFormat('d F Y') }}</strong>
                </p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Team Card -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="gradient-blue p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-1">Tim (Termasuk Saya)</h3>
                                <p class="text-sm text-white/80">Total tugas dibuat oleh tim</p>
                            </div>
                            <div class="bg-white/20 rounded-lg p-3">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.124-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.124-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="text-4xl font-bold text-gray-800 mb-2">{{ $teamTotal }}</div>
                        <div class="text-sm text-gray-500 mb-6">Total tugas</div>
                        
                        <div class="space-y-3">
                            <h4 class="font-semibold text-gray-700 text-sm mb-3">Status Tugas:</h4>
                            @foreach(['pending','in_progress','completed','cancelled'] as $s)
                                @php
                                    $count = $teamCounts->get($s, 0);
                                    $colors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'in_progress' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'completed' => 'bg-green-100 text-green-800 border-green-200',
                                        'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                                    ];
                                    $color = $colors[$s] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                @endphp
                                <a href="{{ route('leader.reports.tasks', ['status' => $s, 'start_date' => $start, 'end_date' => $end]) }}" class="flex items-center justify-between p-3 rounded-lg border-2 hover:shadow-md transition-all {{ $color }}">
                                    <span class="capitalize font-medium">{{ str_replace('_', ' ', $s) }}</span>
                                    <span class="font-bold text-lg">{{ $count }}</span>
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-6 flex flex-wrap gap-2">
                            <a href="{{ route('leader.subordinates.index') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.124-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.124-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Lihat Bawahan
                            </a>
                            <a href="{{ route('leader.reports.tasks', ['start_date'=>$start, 'end_date'=>$end]) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium border border-gray-300">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Lihat Tugas
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Self Card -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-1">Saya</h3>
                                <p class="text-sm text-white/80">Total tugas yang saya buat</p>
                            </div>
                            <div class="bg-white/20 rounded-lg p-3">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="text-4xl font-bold text-gray-800 mb-2">{{ $selfTotal }}</div>
                        <div class="text-sm text-gray-500 mb-6">Total tugas</div>
                        
                        <div class="space-y-3">
                            <h4 class="font-semibold text-gray-700 text-sm mb-3">Status Tugas:</h4>
                            @foreach(['pending','in_progress','completed','cancelled'] as $s)
                                @php
                                    $count = $selfCounts->get($s, 0);
                                    $colors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'in_progress' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'completed' => 'bg-green-100 text-green-800 border-green-200',
                                        'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                                    ];
                                    $color = $colors[$s] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                @endphp
                                <a href="{{ route('leader.reports.tasks', ['status' => $s, 'q' => null, 'start_date' => $start, 'end_date' => $end]) }}" class="flex items-center justify-between p-3 rounded-lg border-2 hover:shadow-md transition-all {{ $color }}">
                                    <span class="capitalize font-medium">{{ str_replace('_', ' ', $s) }}</span>
                                    <span class="font-bold text-lg">{{ $count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-1">Visualisasi Status Tugas</h3>
                        <p class="text-sm text-gray-500">Perbandingan distribusi status antara Tim dan Saya</p>
                    </div>
                    <button id="downloadChartBtn" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download Chart PNG
                    </button>
                </div>
                <div class="relative" style="height: 400px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        (function(){
            const ctx = document.getElementById('statusChart').getContext('2d');
            const labels = {!! json_encode($statusOrder) !!}.map(s => s.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()));

            // Create gradient for team
            const teamGrad = ctx.createLinearGradient(0, 0, 0, 400);
            teamGrad.addColorStop(0, 'rgba(99, 102, 241, 0.95)');
            teamGrad.addColorStop(1, 'rgba(99, 102, 241, 0.6)');

            const selfGrad = ctx.createLinearGradient(0, 0, 0, 400);
            selfGrad.addColorStop(0, 'rgba(16, 185, 129, 0.95)');
            selfGrad.addColorStop(1, 'rgba(16, 185, 129, 0.6)');

            const data = {
                labels,
                datasets: [
                    {
                        label: 'Tim',
                        data: {!! json_encode($teamData) !!},
                        backgroundColor: teamGrad,
                        borderRadius: 8,
                        barThickness: 40,
                        maxBarThickness: 50
                    },
                    {
                        label: 'Saya',
                        data: {!! json_encode($selfData) !!},
                        backgroundColor: selfGrad,
                        borderRadius: 8,
                        barThickness: 40,
                        maxBarThickness: 50
                    }
                ]
            };

            const statusChart = new Chart(ctx, {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            position: 'top',
                            labels: {
                                padding: 15,
                                font: {
                                    size: 14,
                                    weight: '500'
                                }
                            }
                        },
                        title: { 
                            display: false
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
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        x: { 
                            stacked: false,
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }
                        },
                        y: { 
                            beginAtZero: true, 
                            ticks: { 
                                precision: 0,
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        }
                    },
                    interaction: { 
                        mode: 'index', 
                        intersect: false 
                    }
                }
            });

            // Download button
            const dlBtn = document.getElementById('downloadChartBtn');
            const filename = {!! json_encode("status_chart_{$start}_to_{$end}.png") !!};
            dlBtn.addEventListener('click', function(){
                const canvas = document.getElementById('statusChart');
                if (canvas.toBlob) {
                    canvas.toBlob(function(blob){
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        URL.revokeObjectURL(url);
                    }, 'image/png');
                } else {
                    const url = canvas.toDataURL('image/png');
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                }
            });
        })();
    </script>
    @endpush
</x-app-layout>
