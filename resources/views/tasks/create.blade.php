<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <div class="w-12 h-12 gradient-blue rounded-lg flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Buat Task Baru') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Isi form di bawah untuk membuat task baru</p>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8 bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-xl overflow-hidden fade-in">
                <div class="gradient-blue p-6">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Informasi Task
                    </h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('tasks.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Room -->
                        <div class="mb-6">
                            <x-input-label for="room_id" :value="__('Room')" />
                            <select id="room_id" name="room_id" class="block mt-2 w-full border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-lg shadow-sm transition-all">
                                <option value="">-- Pilih Room --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                        {{ $room->room }} - {{ $room->plant }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('room_id')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">Tidak ada room? <a href="{{ route('rooms.create') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Tambah Room</a></p>
                        </div>

                        <!-- Project Code -->
                        <div class="mb-6">
                            <x-input-label for="project_code" :value="__('Kode Project')" />
                            <x-text-input id="project_code" class="block mt-2 w-full border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-lg transition-all" type="text" name="project_code" :value="old('project_code')" />
                            <x-input-error :messages="$errors->get('project_code')" class="mt-2" />
                        </div>

                        <!-- Title -->
                        <div class="mb-6">
                            <x-input-label for="title" :value="__('Project Name / Judul Task')" />
                            <x-text-input id="title" class="block mt-2 w-full border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-lg transition-all" type="text" name="title" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Description / Remark -->
                        <div class="mb-6">
                            <x-input-label for="description" :value="__('Remark / Deskripsi')" />
                            <textarea id="description" name="description" rows="4" class="block mt-2 w-full border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-lg shadow-sm transition-all">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Priority -->
                        <div class="mb-4">
                            <x-input-label for="priority" :value="__('Prioritas')" />
                            <select id="priority" name="priority" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                            </select>
                            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                        </div>

                        <!-- Type -->
                        <div class="mb-4">
                            <x-input-label for="type" :value="__('Type')" />
                            <select id="type" name="type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="TASK" {{ old('type') == 'TASK' ? 'selected' : '' }}>TASK</option>
                                <option value="JOB DESCRIPTION" {{ old('type') == 'JOB DESCRIPTION' ? 'selected' : '' }}>JOB DESCRIPTION</option>
                                <option value="PROJECT" {{ old('type') == 'PROJECT' ? 'selected' : '' }}>PROJECT</option>
                                <option value="OTHER" {{ old('type') == 'OTHER' ? 'selected' : '' }}>OTHER</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- User Request -->
                        <div class="mb-6">
                            <x-input-label for="requested_by" :value="__('User Request')" />
                            <select id="requested_by" name="requested_by" class="block mt-2 w-full border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-lg shadow-sm transition-all">
                                <option value="">-- Pilih User Request --</option>
                                @foreach($superiors as $superior)
                                    <option value="{{ $superior->id }}" {{ old('requested_by') == $superior->id ? 'selected' : '' }}>
                                        {{ $superior->nik }} - {{ $superior->name }} @if($superior->position) ({{ $superior->position->name }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('requested_by')" class="mt-2" />
                            @if($superiors->count() == 0)
                                <p class="mt-1 text-xs text-gray-500">Tidak ada user dengan level di atas Anda</p>
                            @endif
                        </div>

                        <!-- Add Request -->
                        <div class="mb-6">
                            <x-input-label for="add_request" :value="__('Add Request')" />
                            <x-text-input id="add_request" class="block mt-2 w-full border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-lg transition-all" type="text" name="add_request" :value="old('add_request')" placeholder="Input manual request tambahan" />
                            <x-input-error :messages="$errors->get('add_request')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">Input manual untuk request tambahan</p>
                        </div>

                        <!-- Start Date -->
                        <div class="mb-4">
                            <x-input-label for="start_date" :value="__('Start Date')" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date', \Carbon\Carbon::now()->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">Default: tanggal hari ini</p>
                        </div>

                        <!-- Due Date / Deadline -->
                        <div class="mb-4">
                            <x-input-label for="due_date" :value="__('Deadline / Tanggal Jatuh Tempo')" />
                            <x-text-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" :value="old('due_date', \Carbon\Carbon::now()->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">Default: tanggal hari ini</p>
                        </div>

                        <!-- Attachments -->
                        <div class="mb-4">
                            <x-input-label for="attachments" :value="__('Dokumen Pendukung (Bisa upload multiple files)')" />
                            <x-text-input id="attachments" class="block mt-1 w-full" type="file" name="attachments[]" multiple />
                            <x-input-error :messages="$errors->get('attachments')" class="mt-2" />
                            <x-input-error :messages="$errors->get('attachments.*')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">
                                Anda dapat mengupload berbagai jenis dokumen (PDF, Word, Excel, Image, dll). Maksimal 50MB per file.
                            </p>
                        </div>

                        <!-- Approve Level -->
                        <div class="mb-4">
                            <x-input-label for="approve_level" :value="__('Approve Level')" />
                            <x-text-input id="approve_level" class="block mt-1 w-full" type="number" name="approve_level" :value="old('approve_level', 0)" min="0" />
                            <x-input-error :messages="$errors->get('approve_level')" class="mt-2" />
                        </div>

                        <!-- Delegated To (Checkbox) -->
                        <div class="mb-6">
                            <x-input-label :value="__('Delegasikan ke (Wajib - Pilih minimal 1 user)')" />
                            <div class="mt-2 border-2 border-gray-200 rounded-lg p-4 max-h-64 overflow-y-auto bg-gray-50">
                                @if($users->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($users as $userItem)
                                            <label class="flex items-center p-2 rounded hover:bg-blue-50 cursor-pointer transition-colors {{ $userItem->id == Auth::id() ? 'bg-blue-100' : '' }}">
                                                <input type="checkbox" name="delegated_to[]" value="{{ $userItem->id }}" {{ in_array($userItem->id, old('delegated_to', [Auth::id()])) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                <span class="ml-3 text-sm text-gray-700">
                                                    <span class="font-semibold">{{ $userItem->nik }}</span> - {{ $userItem->name }}
                                                    @if($userItem->position)
                                                        <span class="text-gray-500">({{ $userItem->position->name }})</span>
                                                    @endif
                                                    @if($userItem->id == Auth::id())
                                                        <span class="text-xs text-blue-600 font-semibold ml-2">(Saya)</span>
                                                    @endif
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 text-center py-4">Tidak ada user yang bisa didelegasikan</p>
                                @endif
                            </div>
                            <x-input-error :messages="$errors->get('delegated_to')" class="mt-2" />
                            @if($users->count() > 0)
                                <p class="mt-1 text-xs text-gray-500">
                                    <span class="font-semibold">Pilih minimal 1 user</span> untuk didelegasikan. User sendiri secara default tercentang.
                                </p>
                            @endif
                        </div>

                        <!-- Delegation Notes -->
                        <div class="mb-4">
                            <x-input-label for="delegation_notes" :value="__('Catatan Delegasi (Opsional)')" />
                            <textarea id="delegation_notes" name="delegation_notes" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('delegation_notes') }}</textarea>
                            <x-input-error :messages="$errors->get('delegation_notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('tasks.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors mr-4">
                                Batal
                            </a>
                            <button type="submit" class="btn-primary inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ __('Buat Task') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

