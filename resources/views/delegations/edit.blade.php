<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Edit Delegasi') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Edit informasi delegasi</p>
            </div>
            <a href="{{ route('delegations.index', ['page' => session('delegations_page', 1)]) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden fade-in">
                <div class="gradient-blue p-6">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Informasi Delegasi
                    </h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('delegations.update', $delegation) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Task Info (Read Only) -->
                        <div class="mb-6">
                            <x-input-label for="task" :value="__('Task')" />
                            <div class="mt-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-sm font-semibold text-gray-900">{{ $delegation->task->title }}</p>
                                @if($delegation->task->project_code)
                                    <p class="text-xs text-gray-500 mt-1">{{ $delegation->task->project_code }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Delegated To (Read Only) -->
                        <div class="mb-6">
                            <x-input-label for="delegated_to" :value="__('Didelegasikan ke')" />
                            <div class="mt-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-sm font-semibold text-gray-900">{{ $delegation->delegatedTo->name }}</p>
                                @if($delegation->delegatedTo->position)
                                    <p class="text-xs text-gray-500 mt-1">{{ $delegation->delegatedTo->position->name }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Delegated By (Read Only) -->
                        <div class="mb-6">
                            <x-input-label for="delegated_by" :value="__('Didelegasikan oleh')" />
                            <div class="mt-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-sm font-semibold text-gray-900">{{ $delegation->delegatedBy->name }}</p>
                                @if($delegation->delegatedBy->position)
                                    <p class="text-xs text-gray-500 mt-1">{{ $delegation->delegatedBy->position->name }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-6">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="block mt-2 w-full border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-lg shadow-sm transition-all" required>
                                <option value="pending" {{ old('status', $delegation->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="accepted" {{ old('status', $delegation->status) == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                <option value="rejected" {{ old('status', $delegation->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="in_progress" {{ old('status', $delegation->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status', $delegation->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea id="notes" name="notes" rows="4" class="block mt-2 w-full border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-lg shadow-sm transition-all">{{ old('notes', $delegation->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('delegations.index', ['page' => session('delegations_page', 1)]) }}" class="px-6 py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors mr-4">
                                Batal
                            </a>
                            <button type="submit" class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold py-2 px-6 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200 inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ __('Update Delegasi') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
