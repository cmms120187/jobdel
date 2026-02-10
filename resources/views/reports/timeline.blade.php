<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-base sm:text-xl text-gray-800 leading-tight break-words">
                {{ __('Laporan Project Management - Timeline') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-4 sm:py-12">
        <div class="max-w-full mx-auto px-3 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Filter Toggle Button -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-3 sm:p-4">
                    <button type="button" onclick="toggleFilter()" class="touch-target min-h-[44px] bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded text-sm inline-flex items-center">
                        <svg id="filterIcon" class="w-4 h-4 mr-2 transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        Filter
                    </button>
                </div>
            </div>

            <!-- Filter Section (Hidden by default) -->
            <div id="filterSection" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 hidden">
                <div class="p-4 sm:p-6">
                    <form method="GET" action="{{ route('reports.timeline') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                            @if(isset($filterUsers) && $filterUsers->count() > 0)
                                <div>
                                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">User</label>
                                    <select name="user_id" id="user_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Semua User (Saya + Bawahan)</option>
                                        @foreach($filterUsers as $filterUser)
                                            <option value="{{ $filterUser->id }}" {{ (isset($filterUserId) && $filterUserId == $filterUser->id) ? 'selected' : '' }}>
                                                {{ $filterUser->name }} ({{ $filterUser->nik }})
                                                @if($filterUser->position)
                                                    - {{ $filterUser->position->name }}
                                                @endif
                                                @if($filterUser->id == Auth::id())
                                                    (Saya)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div>
                                <label for="room_id" class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                                <select name="room_id" id="room_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Semua Room</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}" {{ $roomId == $room->id ? 'selected' : '' }}>
                                            {{ $room->room }} ({{ $room->plant }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Semua Status</option>
                                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>

                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                                <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                                <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="group_by" class="block text-sm font-medium text-gray-700 mb-1">Tampilan Timeline</label>
                                <select name="group_by" id="group_by" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="daily" {{ ($groupBy ?? 'daily') === 'daily' ? 'selected' : '' }}>Daily (pertanggal)</option>
                                    <option value="weekly" {{ ($groupBy ?? '') === 'weekly' ? 'selected' : '' }}>Weekly (per minggu, W1 Jan, W2 Jan, dst)</option>
                                    <option value="monthly" {{ ($groupBy ?? '') === 'monthly' ? 'selected' : '' }}>Monthly (per bulan, Jan, Feb, dst)</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Pilih tampilan agar timeline rapi untuk periode panjang</p>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Filter
                            </button>
                            <a href="{{ route('reports.timeline') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Timeline Report Table -->
            @if(count($reportData) > 0)
                @foreach($reportData as $taskGroup)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <!-- Task/Project Header -->
                        <div class="p-4 sm:p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-start gap-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 break-words">{{ $taskGroup['task_title'] }}</h3>
                                    <div class="space-y-1 text-xs sm:text-sm text-gray-600">
                                        @if($taskGroup['task_code'])
                                            <p><strong>Project Code:</strong> {{ $taskGroup['task_code'] }}</p>
                                        @endif
                                        @if($taskGroup['task_room'])
                                            <p><strong>Room:</strong> {{ $taskGroup['task_room'] }}</p>
                                        @endif
                                        <p><strong>PIC Project:</strong> {{ $taskGroup['task_pic'] }}</p>
                                        @if($taskGroup['task_start_date'] || $taskGroup['task_due_date'])
                                            <p>
                                                <strong>Periode:</strong> 
                                                @if($taskGroup['task_start_date'])
                                                    {{ \Carbon\Carbon::parse($taskGroup['task_start_date'])->format('d M Y') }}
                                                @else
                                                    -
                                                @endif
                                                s/d 
                                                @if($taskGroup['task_due_date'])
                                                    {{ \Carbon\Carbon::parse($taskGroup['task_due_date'])->format('d M Y') }}
                                                @else
                                                    -
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                    @if($taskGroup['task_description'])
                                        <p class="text-xs sm:text-sm text-gray-700 mt-2 break-words">{{ $taskGroup['task_description'] }}</p>
                                    @endif
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('reports.print-task', $taskGroup['task_id']) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-3 sm:px-4 rounded text-xs sm:text-sm inline-flex items-center w-full sm:w-auto justify-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                        </svg>
                                        Print
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Table for Task Items -->
                        <div class="overflow-x-auto -mx-4 sm:mx-0" style="max-height: 70vh; overflow-y: auto;">
                            <div class="inline-block min-w-full align-middle px-4 sm:px-0">
                                <table class="w-full border-collapse border border-gray-300" style="table-layout: fixed; min-width: 1000px;">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border border-gray-300 px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase sticky left-0 bg-gray-100 z-20" style="width: 50px; min-width: 50px;">No</th>
                                        <th class="border border-gray-300 px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase sticky left-[50px] bg-gray-100 z-20" style="width: 220px; min-width: 220px;">Detail Pekerjaan</th>
                                        <th class="border border-gray-300 px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase sticky left-[270px] bg-gray-100 z-20" style="width: 280px; min-width: 280px;">Deskripsi Pekerjaan</th>
                                        <th class="border border-gray-300 px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase" style="width: 150px; min-width: 150px;">PIC</th>
                                        <th class="border border-gray-300 px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase" style="width: 110px; min-width: 110px;">Status</th>
                                        <th class="border border-gray-300 px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase" style="width: 120px; min-width: 120px;">Progress</th>
                                        <th class="border border-gray-300 px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase" style="width: 120px; min-width: 120px;">Dokumentasi</th>
                                        
                                        <!-- Period Columns (Daily / Weekly / Monthly) -->
                                        @foreach($dateRange as $period)
                                            @php
                                                $periodStart = $period['start'] instanceof \Carbon\Carbon ? $period['start'] : \Carbon\Carbon::parse($period['start']);
                                                $periodEnd = $period['end'] instanceof \Carbon\Carbon ? $period['end'] : \Carbon\Carbon::parse($period['end']);
                                                $isToday = \Carbon\Carbon::today()->between($periodStart, $periodEnd);
                                                $isWeekend = ($periodStart->isWeekend() || $periodEnd->isWeekend()) && isset($period['key']) && strlen($period['key']) === 10;
                                            @endphp
                                            <th class="border border-gray-300 px-1 py-3 text-center text-xs font-semibold text-gray-700 {{ $isToday ? 'bg-yellow-200' : ($isWeekend ? 'bg-gray-50' : 'bg-white') }}" style="width: {{ ($groupBy ?? 'daily') === 'daily' ? '45' : (($groupBy ?? '') === 'monthly' ? '70' : '55') }}px; min-width: {{ ($groupBy ?? 'daily') === 'daily' ? '45' : (($groupBy ?? '') === 'monthly' ? '70' : '55') }}px;">
                                                <div class="font-semibold">{{ $period['label'] }}</div>
                                                <div class="text-[10px] text-gray-500 mt-0.5">{{ $period['sublabel'] }}</div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($taskGroup['items'] as $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-3 py-3 text-center text-sm sticky left-0 bg-white z-10" style="width: 50px; min-width: 50px;">{{ $loop->iteration }}</td>
                                            <td class="border border-gray-300 px-3 py-3 text-left text-sm sticky left-[50px] bg-white z-10" style="width: 220px; min-width: 220px;">
                                                <div class="font-semibold text-gray-900">{{ $item['item_title'] }}</div>
                                            </td>
                                            <td class="border border-gray-300 px-3 py-3 text-left text-sm sticky left-[270px] bg-white z-10" style="width: 280px; min-width: 280px;">
                                                <div class="text-xs text-gray-600 leading-relaxed">{{ $item['item_description'] ?: '-' }}</div>
                                            </td>
                                            <td class="border border-gray-300 px-3 py-3 text-left text-sm" style="width: 150px; min-width: 150px;">
                                                <div class="text-xs text-gray-700">{{ $item['pic'] }}</div>
                                            </td>
                                            <td class="border border-gray-300 px-3 py-3 text-center text-sm" style="width: 110px; min-width: 110px;">
                                                <span class="inline-block px-2 py-1 text-xs font-medium rounded {{ $item['status'] === 'completed' ? 'bg-green-100 text-green-800' : ($item['status'] === 'in_progress' ? 'bg-blue-100 text-blue-800' : ($item['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $item['status'])) }}
                                                </span>
                                            </td>
                                            <td class="border border-gray-300 px-3 py-3 text-center text-sm" style="width: 120px; min-width: 120px;">
                                                <div class="flex items-center justify-center gap-2">
                                                    <div class="w-16 bg-gray-200 rounded-full h-2.5">
                                                        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 h-2.5 rounded-full transition-all" style="width: {{ $item['progress'] }}%"></div>
                                                    </div>
                                                    <span class="text-xs font-semibold text-gray-700 whitespace-nowrap">{{ $item['progress'] }}%</span>
                                                </div>
                                            </td>
                                            <td class="border border-gray-300 px-3 py-3 text-center text-sm" style="width: 120px; min-width: 120px;">
                                                @php
                                                    $taskItem = \App\Models\TaskItem::find($item['id']);
                                                    $allPhotos = collect();
                                                    if ($taskItem && $taskItem->updates) {
                                                        foreach ($taskItem->updates as $update) {
                                                            if ($update->attachments && is_array($update->attachments)) {
                                                                foreach ($update->attachments as $idx => $attachment) {
                                                                    $allPhotos->push(route('progress.download-file', ['progressUpdate' => $update->id, 'index' => $idx]));
                                                                }
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                @if($allPhotos->count() > 0)
                                                    <button onclick="openPhotoModal({{ json_encode($allPhotos->toArray()) }}, '{{ $item['item_title'] }}')" class="bg-green-500 hover:bg-green-700 text-white text-xs font-bold py-1.5 px-3 rounded inline-flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        {{ $allPhotos->count() }} Foto
                                                    </button>
                                                @else
                                                    <span class="text-xs text-gray-400">-</span>
                                                @endif
                                            </td>
                                            
                                            <!-- Timeline cells (by period: daily / weekly / monthly) -->
                                            @foreach($dateRange as $period)
                                                @php
                                                    $periodStart = $period['start'] instanceof \Carbon\Carbon ? $period['start']->copy() : \Carbon\Carbon::parse($period['start'])->copy();
                                                    $periodEnd = $period['end'] instanceof \Carbon\Carbon ? $period['end']->copy() : \Carbon\Carbon::parse($period['end'])->copy();
                                                    $itemStart = $item['start_date'] instanceof \Carbon\Carbon 
                                                        ? $item['start_date']->copy()->startOfDay() 
                                                        : \Carbon\Carbon::parse($item['start_date'])->startOfDay();
                                                    $itemEnd = $item['end_date'] instanceof \Carbon\Carbon 
                                                        ? $item['end_date']->copy()->endOfDay() 
                                                        : \Carbon\Carbon::parse($item['end_date'])->endOfDay();
                                                    // Overlap: item overlaps period if itemStart <= periodEnd AND itemEnd >= periodStart
                                                    $isInRange = $itemStart->lessThanOrEqualTo($periodEnd) && $itemEnd->greaterThanOrEqualTo($periodStart);
                                                    $isToday = \Carbon\Carbon::today()->between($periodStart, $periodEnd);
                                                    $isWeekend = ($periodStart->isWeekend() || $periodEnd->isWeekend()) && isset($period['key']) && strlen($period['key']) === 10;
                                                    $bgColor = '';
                                                    if ($isInRange) {
                                                        if ($item['status'] === 'completed') {
                                                            $bgColor = 'bg-green-400';
                                                        } elseif ($item['status'] === 'in_progress') {
                                                            $bgColor = 'bg-blue-400';
                                                        } elseif ($item['status'] === 'cancelled') {
                                                            $bgColor = 'bg-red-400';
                                                        } else {
                                                            $bgColor = 'bg-yellow-400';
                                                        }
                                                    } else {
                                                        $bgColor = $isToday ? 'bg-yellow-100' : ($isWeekend ? 'bg-gray-50' : 'bg-white');
                                                    }
                                                    $cellWidth = ($groupBy ?? 'daily') === 'daily' ? 45 : (($groupBy ?? '') === 'monthly' ? 70 : 55);
                                                @endphp
                                                <td class="border border-gray-300 px-1 py-2 text-center {{ $bgColor }}" 
                                                    style="width: {{ $cellWidth }}px; min-width: {{ $cellWidth }}px; height: 40px;"
                                                    title="{{ $isInRange ? $item['item_title'] . ' - ' . $itemStart->format('d M Y') . ' s/d ' . $itemEnd->format('d M Y') : '' }}">
                                                    @if($isInRange)
                                                        <div class="w-full h-full flex items-center justify-center">
                                                            <span class="text-gray-700 text-xs">●</span>
                                                        </div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 text-center mt-2 px-4 sm:hidden">Geser ke kanan/kiri untuk melihat timeline lengkap</p>
                    </div>
                @endforeach

                <!-- Legend -->
                <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
                    <div class="flex flex-wrap gap-4 items-center">
                        <span class="text-sm font-semibold text-gray-700">Legenda:</span>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-4 bg-yellow-400 rounded"></div>
                            <span class="text-xs text-gray-600">Pending</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-4 bg-blue-400 rounded"></div>
                            <span class="text-xs text-gray-600">In Progress</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-4 bg-green-400 rounded"></div>
                            <span class="text-xs text-gray-600">Completed</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-4 bg-red-400 rounded"></div>
                            <span class="text-xs text-gray-600">Cancelled</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-4 bg-yellow-200 rounded"></div>
                            <span class="text-xs text-gray-600">Hari Ini</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-center py-8 text-gray-500">
                            <p class="text-lg">Tidak ada data untuk ditampilkan.</p>
                            <p class="text-sm mt-2">Coba ubah filter atau periode tanggal.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Photo Modal -->
    <div id="photoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 my-8">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900" id="photoModalTitle"></h3>
                    <button onclick="closePhotoModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="photoModalContent" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"></div>
            </div>
        </div>
    </div>

    <script>
        function toggleFilter() {
            const filterSection = document.getElementById('filterSection');
            const filterIcon = document.getElementById('filterIcon');
            
            if (filterSection.classList.contains('hidden')) {
                filterSection.classList.remove('hidden');
                filterIcon.style.transform = 'rotate(180deg)';
            } else {
                filterSection.classList.add('hidden');
                filterIcon.style.transform = 'rotate(0deg)';
            }
        }

        function openPhotoModal(photos, title) {
            const modal = document.getElementById('photoModal');
            const titleElement = document.getElementById('photoModalTitle');
            const contentElement = document.getElementById('photoModalContent');
            
            titleElement.textContent = 'Dokumentasi: ' + title;
            contentElement.innerHTML = '';
            
            // photos already contains full secure URLs to the attachment download route
            photos.forEach(function(photoUrl) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <a href="` + photoUrl + `" target="_blank" class="block">
                        <img src="` + photoUrl + `" alt="Foto" class="w-full h-32 object-cover rounded border border-gray-300 hover:border-blue-500 transition-colors cursor-pointer">
                    </a>
                `;
                contentElement.appendChild(div);
            });
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closePhotoModal() {
            const modal = document.getElementById('photoModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('photoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePhotoModal();
            }
        });
    </script>
</x-app-layout>
