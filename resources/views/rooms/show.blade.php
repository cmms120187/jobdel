<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Detail Room') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Informasi lengkap room</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('rooms.edit', $room) }}" class="bg-gradient-to-r from-orange-500 to-pink-600 text-white font-bold py-2 px-4 rounded-lg text-sm shadow-md hover:shadow-lg transform hover:scale-105 transition-all">
                    Edit
                </a>
                <a href="{{ route('rooms.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg text-sm">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 fade-in bg-gradient-to-r from-green-400 to-green-600 text-white px-6 py-4 rounded-xl shadow-lg flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Room Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 fade-in">
                        <div class="gradient-green p-6">
                            <h1 class="text-2xl font-bold text-white">{{ $room->room }}</h1>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 gap-6 mb-6">
                                <div>
                                    <span class="text-sm font-semibold text-gray-500 uppercase">Room</span>
                                    <p class="text-lg font-bold text-gray-900 mt-1">{{ $room->room }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-gray-500 uppercase">Plant</span>
                                    <p class="text-lg font-bold text-gray-900 mt-1">
                                        <span class="badge badge-info text-base">{{ $room->plant }}</span>
                                    </p>
                                </div>
                            </div>

                            @if($room->description)
                                <div class="mb-6">
                                    <span class="text-sm font-semibold text-gray-500 uppercase">Description</span>
                                    <p class="text-gray-700 mt-2 whitespace-pre-wrap">{{ $room->description }}</p>
                                </div>
                            @endif

                            <div class="pt-4 border-t border-gray-200">
                                <div class="text-sm text-gray-500">
                                    <p>Dibuat: {{ $room->created_at->format('d M Y H:i') }}</p>
                                    <p>Diupdate: {{ $room->updated_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks in this Room -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden fade-in">
                        <div class="gradient-blue p-4">
                            <h2 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Tasks di Room Ini ({{ $room->tasks->count() }})
                            </h2>
                        </div>
                        <div class="p-6">
                            @if($room->tasks->count() > 0)
                                <div class="space-y-3">
                                    @foreach($room->tasks as $task)
                                        <a href="{{ route('tasks.show', $task) }}" class="block task-card {{ $task->status === 'completed' ? 'task-card-completed' : ($task->status === 'in_progress' ? 'task-card-progress' : 'task-card-pending') }}">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-gray-900 mb-1 hover:text-blue-600 transition-colors">
                                                        {{ $task->title }}
                                                    </h4>
                                                    <div class="flex items-center gap-2">
                                                        <span class="badge {{ $task->status === 'completed' ? 'badge-success' : ($task->status === 'in_progress' ? 'badge-info' : 'badge-warning') }}">
                                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                        </span>
                                                        <span class="badge {{ $task->priority === 'high' ? 'badge-danger' : ($task->priority === 'medium' ? 'badge-warning' : 'badge-info') }}">
                                                            {{ ucfirst($task->priority) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <svg class="w-5 h-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-4">Belum ada tasks di room ini.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div>
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden fade-in">
                        <div class="p-6">
                            <h3 class="font-semibold text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-2">
                                <a href="{{ route('rooms.edit', $room) }}" class="block w-full bg-gradient-to-r from-orange-500 to-pink-600 text-white text-center font-bold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all">
                                    Edit Room
                                </a>
                                <form method="POST" action="{{ route('rooms.destroy', $room) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus room ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="block w-full bg-red-500 hover:bg-red-700 text-white text-center font-bold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all">
                                        Hapus Room
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

