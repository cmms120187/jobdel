<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <div class="w-12 h-12 gradient-orange rounded-lg flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Edit Room') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Edit informasi room</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-xl overflow-hidden fade-in">
                <div class="gradient-orange p-6">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>
                        Informasi Room
                    </h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('rooms.update', $room) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Room -->
                        <div class="mb-6">
                            <x-input-label for="room" :value="__('Room')" />
                            <x-text-input id="room" class="block mt-2 w-full border-2 border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 rounded-lg transition-all" type="text" name="room" :value="old('room', $room->room)" required autofocus />
                            <x-input-error :messages="$errors->get('room')" class="mt-2" />
                        </div>

                        <!-- Plant -->
                        <div class="mb-6">
                            <x-input-label for="plant" :value="__('Plant')" />
                            <x-text-input id="plant" class="block mt-2 w-full border-2 border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 rounded-lg transition-all" type="text" name="plant" :value="old('plant', $room->plant)" required />
                            <x-input-error :messages="$errors->get('plant')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="4" class="block mt-2 w-full border-2 border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 rounded-lg shadow-sm transition-all">{{ old('description', $room->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('rooms.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors mr-4">
                                Batal
                            </a>
                            <button type="submit" class="bg-gradient-to-r from-orange-500 to-pink-600 text-white font-semibold py-2 px-6 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200 inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ __('Update Room') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

