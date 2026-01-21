@php
    use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Progress Update') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Task: {{ $task->title }}</h3>
                    <p class="text-sm text-gray-600 mb-2">Detail Pekerjaan: {{ $taskItem->title }}</p>
                    <p class="text-xs text-orange-600 font-medium">
                        ⚠️ Hanya Administrator yang dapat mengedit progress update
                    </p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('task-items.update.update', [$task, $taskItem, $update]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="space-y-6">
                            <!-- Update Date -->
                            <div>
                                <x-input-label for="update_date" :value="__('Tanggal Update')" />
                                <x-text-input id="update_date" class="block mt-1 w-full" type="date" name="update_date" :value="old('update_date', $update->update_date ? $update->update_date->format('Y-m-d') : $update->created_at->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('update_date')" class="mt-2" />
                            </div>

                            <!-- Time From - Time To -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="time_from" :value="__('Waktu Mulai')" />
                                    <x-text-input id="time_from" class="block mt-1 w-full" type="time" name="time_from" :value="old('time_from', $update->time_from ? \Carbon\Carbon::parse($update->time_from)->format('H:i') : '')" />
                                    <x-input-error :messages="$errors->get('time_from')" class="mt-2" />
                                    <p class="text-xs text-gray-500 mt-1">Jam mulai bekerja (opsional)</p>
                                </div>
                                <div>
                                    <x-input-label for="time_to" :value="__('Waktu Selesai')" />
                                    <x-text-input id="time_to" class="block mt-1 w-full" type="time" name="time_to" :value="old('time_to', $update->time_to ? \Carbon\Carbon::parse($update->time_to)->format('H:i') : '')" />
                                    <x-input-error :messages="$errors->get('time_to')" class="mt-2" />
                                    <p class="text-xs text-gray-500 mt-1">Jam selesai bekerja (opsional)</p>
                                </div>
                            </div>

                            <!-- Old Progress -->
                            <div>
                                <x-input-label for="old_progress_percentage" :value="__('Progress Lama')" />
                                <x-text-input id="old_progress_percentage" class="block mt-1 w-full" type="number" name="old_progress_percentage" :value="old('old_progress_percentage', $update->old_progress_percentage ?? 0)" min="0" max="100" required />
                                <x-input-error :messages="$errors->get('old_progress_percentage')" class="mt-2" />
                            </div>

                            <!-- New Progress -->
                            <div>
                                <x-input-label for="new_progress_percentage" :value="__('Progress Baru')" />
                                <x-text-input id="new_progress_percentage" class="block mt-1 w-full" type="number" name="new_progress_percentage" :value="old('new_progress_percentage', $update->new_progress_percentage ?? 0)" min="0" max="100" required />
                                <x-input-error :messages="$errors->get('new_progress_percentage')" class="mt-2" />
                            </div>

                            <!-- Notes -->
                            <div>
                                <x-input-label for="notes" :value="__('Catatan')" />
                                <textarea id="notes" name="notes" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $update->notes) }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>

                            <!-- Existing Photos -->
                            @if($update->attachments && count($update->attachments) > 0)
                                <div>
                                    <x-input-label :value="__('Foto yang Ada')" />
                                    <div class="mt-2 grid grid-cols-4 gap-2">
                                        @foreach($update->attachments as $index => $attachment)
                                            <div class="relative">
                                                <a href="{{ route('progress.download-file', ['progressUpdate' => $update->id, 'index' => $index]) }}" target="_blank">
                                                    <img src="{{ route('progress.download-file', ['progressUpdate' => $update->id, 'index' => $index]) }}" alt="Foto {{ $index + 1 }}" class="w-full h-24 object-cover rounded border border-gray-300">
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Foto baru akan ditambahkan ke foto yang sudah ada</p>
                                </div>
                            @endif

                            <!-- New Photos -->
                            <div>
                                <x-input-label for="photos" :value="__('Tambah Foto Baru (Opsional)')" />
                                <x-text-input id="photos" class="block mt-1 w-full" type="file" name="photos[]" accept="image/*" multiple />
                                <x-input-error :messages="$errors->get('photos')" class="mt-2" />
                                <p class="text-xs text-gray-500 mt-1">Maksimal 5MB per foto. Format: JPEG, PNG, JPG, GIF, WEBP.</p>
                            </div>

                            <div class="flex items-center justify-end gap-4 pt-4 border-t">
                                <a href="{{ route('tasks.show', $task) }}" class="text-gray-600 hover:text-gray-800">
                                    Batal
                                </a>
                                <x-primary-button>
                                    {{ __('Simpan Perubahan') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
