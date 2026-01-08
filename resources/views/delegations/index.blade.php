<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Delegasi untuk Saya') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola semua delegasi yang diberikan kepada Anda</p>
            </div>
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
                    @if($delegations->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 table-fixed">
                                <thead class="gradient-blue">
                                    <tr>
                                        <th class="w-16 px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">No</th>
                                        <th class="w-64 px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Task</th>
                                        <th class="w-48 px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Dari</th>
                                        <th class="w-32 px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Status</th>
                                        <th class="w-40 px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Progress</th>
                                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Notes</th>
                                        <th class="w-32 px-4 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($delegations as $index => $delegation)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ ($delegations->currentPage() - 1) * $delegations->perPage() + $index + 1 }}
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="text-sm font-semibold text-gray-900 truncate" title="{{ $delegation->task->title }}">
                                                    {{ $delegation->task->title }}
                                                </div>
                                                @if($delegation->task->project_code)
                                                    <div class="text-xs text-gray-500 mt-1 truncate">{{ $delegation->task->project_code }}</div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="text-sm text-gray-900 truncate" title="{{ $delegation->delegatedBy->name }}">
                                                    {{ $delegation->delegatedBy->name }}
                                                </div>
                                                @if($delegation->delegatedBy->position)
                                                    <div class="text-xs text-gray-500 truncate">{{ $delegation->delegatedBy->position->name }}</div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <span class="badge {{ $delegation->status === 'completed' ? 'badge-success' : ($delegation->status === 'accepted' || $delegation->status === 'in_progress' ? 'badge-info' : 'badge-warning') }}">
                                                    {{ ucfirst($delegation->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1 bg-gray-200 rounded-full h-2.5">
                                                        <div class="bg-blue-600 h-2.5 rounded-full transition-all" style="width: {{ $delegation->progress_percentage }}%"></div>
                                                    </div>
                                                    <span class="text-xs font-semibold text-gray-700 w-12 text-right">{{ $delegation->progress_percentage }}%</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4">
                                                @if($delegation->notes)
                                                    <div class="text-sm text-gray-600 truncate" title="{{ $delegation->notes }}">
                                                        {{ Str::limit($delegation->notes, 50) }}
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center justify-center gap-3">
                                                    <a href="{{ route('delegations.show', $delegation) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="View">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </a>
                                                    @can('update', $delegation)
                                                        <a href="{{ route('delegations.edit', $delegation) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors" title="Edit">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </a>
                                                    @endcan
                                                    @can('delete', $delegation)
                                                        <form method="POST" action="{{ route('delegations.destroy', $delegation) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus delegasi ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Delete">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <div class="mt-6">
                            {{ $delegations->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak ada delegasi</h3>
                            <p class="text-gray-500">Belum ada delegasi yang diberikan kepada Anda</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
