<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Delegasi Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Task: {{ $task->title }}</h3>
                    <p class="text-sm text-gray-600">{{ $task->description }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('delegations.store', $task) }}">
                        @csrf

                        <!-- Delegated To -->
                        <div class="mb-4">
                            <x-input-label for="delegated_to" :value="__('Delegasikan ke')" />
                            <select id="delegated_to" name="delegated_to" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-- Pilih User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('delegated_to') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} @if($user->position) - {{ $user->position->name }} @endif
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('delegated_to')" class="mt-2" />
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <x-input-label for="notes" :value="__('Catatan (Opsional)')" />
                            <textarea id="notes" name="notes" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('tasks.show', $task) }}" class="text-gray-600 hover:text-gray-800 mr-4">Batal</a>
                            <x-primary-button>
                                {{ __('Buat Delegasi') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

