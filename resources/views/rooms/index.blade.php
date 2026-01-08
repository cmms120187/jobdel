<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Room Management') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola data Room</p>
            </div>
            <a href="{{ route('rooms.create') }}" class="btn-primary inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Room
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 fade-in bg-gradient-to-r from-green-400 to-green-600 text-white px-6 py-4 rounded-xl shadow-lg flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-lg overflow-hidden fade-in">
                <div class="p-6">
                    @if($rooms->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="gradient-blue">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">No</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Room</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Plant</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($rooms as $index => $room)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ ($rooms->currentPage() - 1) * $rooms->perPage() + $index + 1 }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-gray-900">{{ $room->room }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="badge badge-info">{{ $room->plant }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-600">{{ $room->description ?? '-' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('rooms.show', $room) }}" class="text-blue-600 hover:text-blue-900 font-semibold">View</a>
                                                    <span class="text-gray-300">|</span>
                                                    <a href="{{ route('rooms.edit', $room) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">Edit</a>
                                                    <span class="text-gray-300">|</span>
                                                    <form method="POST" action="{{ route('rooms.destroy', $room) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus room ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-6">
                            {{ $rooms->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                            </svg>
                            <p class="text-gray-500 mb-4">Belum ada room.</p>
                            <a href="{{ route('rooms.create') }}" class="btn-primary inline-block">
                                Tambah Room Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

