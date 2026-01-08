<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Detail Pekerjaan') }}
            </h2>
            <a href="{{ route('tasks.show', $task) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Task: <span class="font-semibold">{{ $task->title }}</span></p>
                        <p class="text-sm text-gray-600">Detail Pekerjaan: <span class="font-semibold">{{ $taskItem->title }}</span></p>
                    </div>

                    <form action="{{ route('task-items.update', [$task, $taskItem]) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-4">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Detail Pekerjaan *</label>
                                <input type="text" name="title" id="title" required 
                                    value="{{ old('title', $taskItem->title) }}" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                    placeholder="Contoh: Setup database, Design UI, dll">
                                @error('title')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                <textarea name="description" id="description" rows="3" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                    placeholder="Deskripsi detail pekerjaan (opsional)">{{ old('description', $taskItem->description) }}</textarea>
                                @error('description')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Assign To</label>
                                    <select name="assigned_to" id="assigned_to" 
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Pilih User (Opsional)</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('assigned_to', $taskItem->assigned_to) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->nik }})
                                                @if($user->position)
                                                    - {{ $user->position->name }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">User yang terdaftar dalam delegasi task ini</p>
                                    @error('assigned_to')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" 
                                        value="{{ old('start_date', $taskItem->start_date ? $taskItem->start_date->format('Y-m-d') : '') }}" 
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('start_date')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                    <input type="time" name="start_time" id="start_time" 
                                        value="{{ old('start_time', $taskItem->start_time ?? '') }}" 
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('start_time')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date *</label>
                                    <input type="date" name="due_date" id="due_date" 
                                        value="{{ old('due_date', $taskItem->due_date ? $taskItem->due_date->format('Y-m-d') : '') }}" 
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('due_date')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="due_time" class="block text-sm font-medium text-gray-700 mb-1">Due Time *</label>
                                    <input type="time" name="due_time" id="due_time" required
                                        value="{{ old('due_time', $taskItem->due_time ? $taskItem->due_time : '') }}" 
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <p class="text-xs text-gray-500 mt-1">Waktu batas akhir untuk reminder</p>
                                    @error('due_time')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select name="status" id="status" 
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="pending" {{ old('status', $taskItem->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ old('status', $taskItem->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ old('status', $taskItem->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ old('status', $taskItem->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="progress_percentage" class="block text-sm font-medium text-gray-700 mb-1">Progress (%)</label>
                                    <input type="number" name="progress_percentage" id="progress_percentage" 
                                        min="0" max="100" 
                                        value="{{ old('progress_percentage', $taskItem->progress_percentage) }}" 
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <div class="mt-2">
                                        <input type="range" id="progress_range" min="0" max="100" 
                                            value="{{ old('progress_percentage', $taskItem->progress_percentage) }}" 
                                            class="w-full" 
                                            oninput="document.getElementById('progress_percentage').value = this.value">
                                    </div>
                                    @error('progress_percentage')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="order" class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                                <input type="number" name="order" id="order" min="0" 
                                    value="{{ old('order', $taskItem->order) }}" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="text-xs text-gray-500 mt-1">Urutan tampil detail pekerjaan (0 = paling atas)</p>
                                @error('order')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan Update</label>
                                <textarea name="notes" id="notes" rows="2" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                    placeholder="Catatan untuk update ini (opsional)">{{ old('notes') }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Catatan ini akan ditambahkan ke history update jika status atau progress berubah</p>
                                @error('notes')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex gap-2 justify-end pt-4 border-t">
                                <a href="{{ route('tasks.show', $task) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                                    Batal
                                </a>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sync range input with number input
        document.getElementById('progress_percentage').addEventListener('input', function() {
            document.getElementById('progress_range').value = this.value;
        });

        document.getElementById('progress_range').addEventListener('input', function() {
            document.getElementById('progress_percentage').value = this.value;
        });
    </script>
</x-app-layout>

