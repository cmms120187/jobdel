<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 leading-tight">
                    {{ __('Tasks') }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola semua tasks Anda</p>
            </div>
            <a href="{{ route('tasks.create') }}" class="btn-primary inline-flex items-center text-sm w-full sm:w-auto justify-center">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="hidden sm:inline">Buat Task Baru</span>
                <span class="sm:hidden">Buat Task</span>
            </a>
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

            <!-- Filter Toggle Button -->
            @if(isset($filterUsers) && $filterUsers->count() > 0)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden fade-in mb-6">
                    <div class="p-4">
                        <button type="button" onclick="toggleFilter()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm inline-flex items-center">
                            <svg id="filterIcon" class="w-4 h-4 mr-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                            Filter
                        </button>
                    </div>
                </div>

                <!-- Filter Section (Hidden by default) -->
                <div id="filterSection" class="bg-white rounded-xl shadow-lg overflow-hidden fade-in mb-6 hidden">
                    <div class="p-6">
                        <form method="GET" action="{{ route('tasks.index') }}" class="flex gap-4 items-end">
                            <div class="flex-1">
                                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Filter by User</label>
                                <select name="user_id" id="user_id" onchange="this.form.submit()" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                                    @if($filterUsers->count() > 1)
                                        <option value="all" {{ (isset($filterUserId) && $filterUserId == 'all') ? 'selected' : '' }}>
                                            Semua User (Saya + Bawahan)
                                        </option>
                                    @endif
                                </select>
                            </div>
                            @if(isset($filterUserId))
                                <a href="{{ route('tasks.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm whitespace-nowrap">
                                    Reset Filter
                                </a>
                            @endif
                            <!-- Hidden input to preserve pagination -->
                            <input type="hidden" name="page" value="1">
                        </form>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-lg overflow-hidden fade-in">
                <div class="p-3 sm:p-6">
                    @if($tasks->count() > 0)
                        <!-- Desktop Table View -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full divide-y divide-gray-200" style="table-layout: fixed;">
                                <thead class="gradient-blue">
                                    <tr>
                                        <th class="px-3 py-4 text-center text-xs font-bold text-white uppercase tracking-wider" style="width: 50px;">No</th>
                                        <th class="px-3 py-4 text-left text-xs font-bold text-white uppercase tracking-wider" style="width: 200px;">Project Name</th>
                                        <th class="px-3 py-4 text-center text-xs font-bold text-white uppercase tracking-wider" style="width: 90px;">Type</th>
                                        <th class="px-3 py-4 text-left text-xs font-bold text-white uppercase tracking-wider" style="width: 140px;">Room</th>
                                        <th class="px-3 py-4 text-center text-xs font-bold text-white uppercase tracking-wider" style="width: 100px;">Project Code</th>
                                        <th class="px-3 py-4 text-center text-xs font-bold text-white uppercase tracking-wider" style="width: 85px;">Priority</th>
                                        <th class="px-3 py-4 text-center text-xs font-bold text-white uppercase tracking-wider" style="width: 100px;">Status</th>
                                        <th class="px-3 py-4 text-center text-xs font-bold text-white uppercase tracking-wider" style="width: 100px;">Due Date</th>
                                        <th class="px-3 py-4 text-center text-xs font-bold text-white uppercase tracking-wider" style="width: 70px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($tasks as $index => $task)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-3 py-3 text-center text-sm font-medium text-gray-900">
                                                {{ ($tasks->currentPage() - 1) * $tasks->perPage() + $index + 1 }}
                                            </td>
                                            <td class="px-3 py-3">
                                                <div class="flex items-start gap-2">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-sm font-semibold text-gray-900 truncate" title="{{ $task->title }}">{{ $task->title }}</div>
                                                        @if($task->description)
                                                            <div class="text-xs text-gray-500 mt-1 line-clamp-1 truncate" title="{{ $task->description }}">{{ Str::limit($task->description, 40) }}</div>
                                                        @endif
                                                        @if($task->created_by != Auth::id() && $task->creator)
                                                            <div class="text-xs text-gray-500 mt-1 truncate">
                                                                Oleh: <span class="font-semibold">{{ $task->creator->name }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @if($task->created_by != Auth::id())
                                                        <span class="badge badge-info text-xs whitespace-nowrap flex-shrink-0" title="Didelegasikan ke saya">
                                                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            Delegasi
                                                        </span>
                                                    @else
                                                        <span class="badge badge-success text-xs whitespace-nowrap flex-shrink-0" title="Task yang saya buat">
                                                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                            Saya
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-3 py-3 text-center">
                                                @if($task->type)
                                                    <span class="badge badge-purple text-xs">{{ $task->type }}</span>
                                                @else
                                                    <span class="text-sm text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3">
                                                @if($task->room)
                                                    <div class="text-xs text-gray-900 truncate" title="{{ $task->room->room }}">{{ Str::limit($task->room->room, 15) }}</div>
                                                    <div class="text-xs text-gray-500 truncate" title="{{ $task->room->plant }}">{{ Str::limit($task->room->plant, 15) }}</div>
                                                @else
                                                    <span class="text-xs text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3 text-center">
                                                <div class="text-xs text-gray-900 truncate" title="{{ $task->project_code ?? '-' }}">{{ Str::limit($task->project_code ?? '-', 10) }}</div>
                                            </td>
                                            <td class="px-3 py-3 text-center">
                                                <span class="badge text-xs {{ $task->priority === 'high' ? 'badge-danger' : ($task->priority === 'medium' ? 'badge-warning' : 'badge-info') }}">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-3 text-center">
                                                <span class="badge text-xs {{ $task->status === 'completed' ? 'badge-success' : ($task->status === 'in_progress' ? 'badge-info' : 'badge-warning') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-3 text-center">
                                                @if($task->due_date)
                                                    <div class="text-xs text-gray-900">{{ $task->due_date->format('d M Y') }}</div>
                                                @else
                                                    <span class="text-xs text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3 text-center">
                                                <div class="flex flex-col items-center justify-center gap-1.5">
                                                    <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="View">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </a>
                                                    <form method="POST" action="{{ route('tasks.duplicate', $task) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-900 transition-colors" title="Duplicate">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                    @can('update', $task)
                                                        <a href="{{ route('tasks.edit', $task) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors" title="Edit">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </a>
                                                    @endcan
                                                    @can('delete', $task)
                                                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus task ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Delete">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="md:hidden space-y-4">
                            @foreach($tasks as $index => $task)
                                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="text-xs font-semibold text-gray-500">#{{ ($tasks->currentPage() - 1) * $tasks->perPage() + $index + 1 }}</span>
                                                <h3 class="text-sm font-bold text-gray-900 truncate">{{ $task->title }}</h3>
                                            </div>
                                            @if($task->description)
                                                <p class="text-xs text-gray-600 mb-2 line-clamp-2">{{ Str::limit($task->description, 80) }}</p>
                                            @endif
                                            @if($task->created_by != Auth::id() && $task->creator)
                                                <p class="text-xs text-gray-500 mb-2">Oleh: <span class="font-semibold">{{ $task->creator->name }}</span></p>
                                            @endif
                                        </div>
                                        @if($task->created_by != Auth::id())
                                            <span class="badge badge-info text-xs flex-shrink-0 ml-2">
                                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Delegasi
                                            </span>
                                        @else
                                            <span class="badge badge-success text-xs flex-shrink-0 ml-2">
                                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Saya
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-2 mb-3 text-xs">
                                        <div>
                                            <span class="text-gray-500">Type:</span>
                                            @if($task->type)
                                                <span class="badge badge-purple ml-1">{{ $task->type }}</span>
                                            @else
                                                <span class="text-gray-400 ml-1">-</span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Room:</span>
                                            <span class="text-gray-900 ml-1">{{ $task->room ? Str::limit($task->room->room, 15) : '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Code:</span>
                                            <span class="text-gray-900 ml-1">{{ Str::limit($task->project_code ?? '-', 10) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Priority:</span>
                                            <span class="badge ml-1 {{ $task->priority === 'high' ? 'badge-danger' : ($task->priority === 'medium' ? 'badge-warning' : 'badge-info') }}">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="badge text-xs {{ $task->status === 'completed' ? 'badge-success' : ($task->status === 'in_progress' ? 'badge-info' : 'badge-warning') }}">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                            @if($task->due_date)
                                                <span class="text-xs text-gray-600">{{ $task->due_date->format('d M Y') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-center gap-2 pt-2 border-t border-gray-200">
                                        <a href="{{ route('tasks.show', $task) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 px-2 rounded text-center transition-colors">
                                            View
                                        </a>
                                        <form method="POST" action="{{ route('tasks.duplicate', $task) }}" class="flex-1">
                                            @csrf
                                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2 px-2 rounded transition-colors">
                                                Copy
                                            </button>
                                        </form>
                                        @can('update', $task)
                                            <a href="{{ route('tasks.edit', $task) }}" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2 px-2 rounded text-center transition-colors">
                                                Edit
                                            </a>
                                        @endcan
                                        @can('delete', $task)
                                            <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="flex-1" onsubmit="return confirm('Hapus task ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white text-xs font-bold py-2 px-2 rounded transition-colors">
                                                    Del
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 sm:mt-6">
                            {{ $tasks->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Belum ada tasks</h3>
                            <p class="text-gray-500 mb-6">Mulai dengan membuat task pertama Anda</p>
                            <a href="{{ route('tasks.create') }}" class="btn-primary inline-block">
                                Buat Task Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleFilter() {
            const filterSection = document.getElementById('filterSection');
            const filterIcon = document.getElementById('filterIcon');
            
            if (filterSection) {
                if (filterSection.classList.contains('hidden')) {
                    filterSection.classList.remove('hidden');
                    if (filterIcon) filterIcon.style.transform = 'rotate(180deg)';
                } else {
                    filterSection.classList.add('hidden');
                    if (filterIcon) filterIcon.style.transform = 'rotate(0deg)';
                }
            }
        }
    </script>
</x-app-layout>
