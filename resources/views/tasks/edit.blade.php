@php
    use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Task') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('tasks.update', $task) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <!-- Room -->
                        <div class="mb-6">
                            <x-input-label for="room_id" :value="__('Room')" />
                            <select id="room_id" name="room_id" class="block mt-2 w-full border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 rounded-lg shadow-sm transition-all">
                                <option value="">-- Pilih Room --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ old('room_id', $task->room_id) == $room->id ? 'selected' : '' }}>
                                        {{ $room->room }} - {{ $room->plant }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('room_id')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">Tidak ada room? <a href="{{ route('rooms.create') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">Tambah Room</a></p>
                        </div>

                        <!-- Project Code -->
                        <div class="mb-4">
                            <x-input-label for="project_code" :value="__('Kode Project')" />
                            <x-text-input id="project_code" class="block mt-1 w-full" type="text" name="project_code" :value="old('project_code', $task->project_code)" />
                            <x-input-error :messages="$errors->get('project_code')" class="mt-2" />
                        </div>

                        <!-- Title -->
                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Project Name / Judul Task')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $task->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Description / Remark -->
                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Remark / Deskripsi')" />
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $task->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Priority -->
                        <div class="mb-4">
                            <x-input-label for="priority" :value="__('Prioritas')" />
                            <select id="priority" name="priority" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
                            </select>
                            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                        </div>

                        <!-- Type -->
                        <div class="mb-4">
                            <x-input-label for="type" :value="__('Type')" />
                            <select id="type" name="type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="TASK" {{ old('type', $task->type) == 'TASK' ? 'selected' : '' }}>TASK</option>
                                <option value="JOB DESCRIPTION" {{ old('type', $task->type) == 'JOB DESCRIPTION' ? 'selected' : '' }}>JOB DESCRIPTION</option>
                                <option value="PROJECT" {{ old('type', $task->type) == 'PROJECT' ? 'selected' : '' }}>PROJECT</option>
                                <option value="OTHER" {{ old('type', $task->type) == 'OTHER' ? 'selected' : '' }}>OTHER</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Status (Read-only, dihitung otomatis dari detail pekerjaan) -->
                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <input type="text" id="status" value="{{ ucfirst(str_replace('_', ' ', $task->status)) }}" class="block mt-1 w-full border-gray-300 bg-gray-100 rounded-md shadow-sm cursor-not-allowed" readonly disabled>
                            <p class="mt-1 text-xs text-gray-500">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Status dihitung otomatis berdasarkan progress detail pekerjaan (task items)
                            </p>
                        </div>

                        <!-- User Request -->
                        <div class="mb-6">
                            <x-input-label for="requested_by" :value="__('User Request')" />
                            <select id="requested_by" name="requested_by" class="block mt-2 w-full border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 rounded-lg shadow-sm transition-all">
                                <option value="">-- Pilih User Request --</option>
                                @foreach($superiors as $superior)
                                    <option value="{{ $superior->id }}" {{ old('requested_by', $task->requested_by) == $superior->id ? 'selected' : '' }}>
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
                            <x-text-input id="add_request" class="block mt-2 w-full border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 rounded-lg transition-all" type="text" name="add_request" :value="old('add_request', $task->add_request)" placeholder="Input manual request tambahan" />
                            <x-input-error :messages="$errors->get('add_request')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">Input manual untuk request tambahan</p>
                        </div>

                        <!-- Start Date -->
                        <div class="mb-4">
                            <x-input-label for="start_date" :value="__('Start Date')" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date', $task->start_date ? $task->start_date->format('Y-m-d') : '')" />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                        </div>

                        <!-- Due Date / Deadline -->
                        <div class="mb-4">
                            <x-input-label for="due_date" :value="__('Deadline / Tanggal Jatuh Tempo')" />
                            <x-text-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" :value="old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '')" />
                            <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                        </div>

                        <!-- Attachments -->
                        <div class="mb-4">
                            <x-input-label for="attachments" :value="__('Dokumen Pendukung (Bisa upload multiple files)')" />
                            
                            <!-- Existing Attachments -->
                            @if($task->attachments && $task->attachments->count() > 0)
                                <div class="mb-3 p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm font-semibold text-gray-700 mb-2">Dokumen yang sudah diupload:</p>
                                    <ul class="space-y-2">
                                        @foreach($task->attachments as $attachment)
                                            <li class="flex items-center justify-between text-sm">
                                                <span class="text-gray-600">
                                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    {{ $attachment->original_name }}
                                                    <span class="text-xs text-gray-500">({{ $attachment->formatted_size }})</span>
                                                </span>
                                                <a href="{{ route('tasks.download-file', ['task' => $task->id, 'fileKey' => $attachment->id]) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-semibold">
                                                    Download
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <!-- Legacy file_support_1 and file_support_2 (for backward compatibility) -->
                            @if($task->file_support_1 || $task->file_support_2)
                                <div class="mb-3 p-3 bg-yellow-50 rounded-lg">
                                    <p class="text-sm font-semibold text-yellow-700 mb-2">Dokumen lama (legacy):</p>
                                    <ul class="space-y-2">
                                        @if($task->file_support_1)
                                            <li class="flex items-center justify-between text-sm">
                                                <span class="text-gray-600">File Support 1</span>
                                                <a href="{{ route('tasks.download-file', ['task' => $task->id, 'fileKey' => 'file_support_1']) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-semibold">
                                                    Download
                                                </a>
                                            </li>
                                        @endif
                                        @if($task->file_support_2)
                                            <li class="flex items-center justify-between text-sm">
                                                <span class="text-gray-600">File Support 2</span>
                                                <a href="{{ route('tasks.download-file', ['task' => $task->id, 'fileKey' => 'file_support_2']) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-semibold">
                                                    Download
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            @endif
                            
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
                            <x-text-input id="approve_level" class="block mt-1 w-full" type="number" name="approve_level" :value="old('approve_level', $task->approve_level)" min="0" />
                            <x-input-error :messages="$errors->get('approve_level')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Level persetujuan yang diperlukan untuk task ini (opsional). Digunakan untuk workflow approval jika diperlukan.
                            </p>
                        </div>

                        <!-- Delegasikan ke (untuk task baru yang diduplikasi) -->
                        @if($task->delegations->isEmpty())
                            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <x-input-label :value="__('Delegasikan ke (Wajib - Pilih minimal 1 user)')" />
                                <p class="text-xs text-blue-700 mb-3 mt-1">
                                    Task ini belum memiliki delegasi. Silakan pilih user yang akan didelegasikan.
                                </p>
                                <div class="mt-2 border-2 border-blue-200 rounded-lg p-4 max-h-64 overflow-y-auto bg-white">
                                    @if($users->count() > 0)
                                        <div class="space-y-2">
                                            @foreach($users as $userItem)
                                                <label class="flex items-center p-2 rounded hover:bg-blue-50 cursor-pointer transition-colors {{ $userItem->id == Auth::id() ? 'bg-blue-100' : '' }}">
                                                    <input type="checkbox" name="delegated_to[]" value="{{ $userItem->id }}" {{ in_array($userItem->id, old('delegated_to', [])) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
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
                                    <p class="mt-2 text-xs text-blue-700">
                                        <span class="font-semibold">Pilih minimal 1 user</span> untuk didelegasikan. Atau bisa juga delegasikan melalui tombol "Delegasikan Task" di halaman detail task.
                                    </p>
                                @endif
                            </div>

                            <!-- Delegation Notes -->
                            <div class="mb-4">
                                <x-input-label for="delegation_notes" :value="__('Catatan Delegasi (Opsional)')" />
                                <textarea id="delegation_notes" name="delegation_notes" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('delegation_notes') }}</textarea>
                                <x-input-error :messages="$errors->get('delegation_notes')" class="mt-2" />
                            </div>
                        @else
                            <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                <x-input-label :value="__('Informasi Delegasi')" />
                                <p class="text-xs text-gray-600 mt-1">
                                    Task ini sudah memiliki delegasi. Untuk menambah/mengubah delegasi, gunakan tombol "Delegasikan Task" di halaman detail task setelah update.
                                </p>
                            </div>
                        @endif

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('tasks.index', ['page' => session('tasks_page', 1)]) }}" class="text-gray-600 hover:text-gray-800 mr-4">Batal</a>
                            <x-primary-button>
                                {{ __('Update Task') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
