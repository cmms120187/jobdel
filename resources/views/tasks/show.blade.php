@php
    use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight break-words">
                {{ __('Detail Task') }}
            </h2>
            <div class="flex flex-wrap gap-2">
                <form method="POST" action="{{ route('tasks.duplicate', $task) }}" class="inline">
                    @csrf
                    <button type="submit" class="touch-target min-h-[44px] bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-3 sm:px-4 rounded text-xs sm:text-sm inline-flex items-center">
                        <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Duplikat
                    </button>
                </form>
                @can('update', $task)
                <a href="{{ route('tasks.edit', $task) }}" class="touch-target min-h-[44px] bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-3 sm:px-4 rounded text-xs sm:text-sm inline-flex items-center">
                    Edit
                </a>
                @endcan
                <a href="{{ route('tasks.index', ['page' => session('tasks_page', 1)]) }}" class="touch-target min-h-[44px] bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-3 sm:px-4 rounded text-xs sm:text-sm inline-flex items-center">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-12">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Task Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-4 sm:p-6">
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-3 sm:mb-4 break-words">{{ $task->title }}</h1>
                            
                            <div class="mb-4 flex flex-wrap gap-2">
                                <span class="px-2 sm:px-3 py-1 text-xs sm:text-sm rounded {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($task->status) }}
                                </span>
                                <span class="px-2 sm:px-3 py-1 text-xs sm:text-sm rounded {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                                @if($task->type)
                                    <span class="px-2 sm:px-3 py-1 text-xs sm:text-sm rounded bg-blue-100 text-blue-800">
                                        {{ $task->type }}
                                    </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 text-xs sm:text-sm mb-4">
                                @if($task->room)
                                    <div>
                                        <span class="font-semibold text-gray-700 block mb-1">Room:</span>
                                        <p class="text-gray-600">
                                            <a href="{{ route('rooms.show', $task->room) }}" class="text-blue-600 hover:text-blue-800 font-semibold break-words">
                                                {{ $task->room->room }}
                                            </a>
                                            <span class="text-xs text-gray-500">({{ $task->room->plant }})</span>
                                        </p>
                                    </div>
                                @endif
                                @if($task->project_code)
                                    <div>
                                        <span class="font-semibold text-gray-700 block mb-1">Kode Project:</span>
                                        <p class="text-gray-600 break-words">{{ $task->project_code }}</p>
                                    </div>
                                @endif
                            </div>

                            @if($task->description)
                                <div class="mb-4">
                                    <h3 class="font-semibold text-gray-700 mb-2 text-sm sm:text-base">Deskripsi:</h3>
                                    <p class="text-gray-600 whitespace-pre-wrap text-xs sm:text-sm break-words">{{ $task->description }}</p>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 text-xs sm:text-sm">
                                <div>
                                    <span class="font-semibold text-gray-700 block mb-1">User Create:</span>
                                    <p class="text-gray-600 break-words">
                                        {{ $task->creator->nik }} - {{ $task->creator->name }}
                                        @if($task->creator->position)
                                            <span class="text-xs text-gray-500">({{ $task->creator->position->name }})</span>
                                        @endif
                                    </p>
                                </div>
                                @if($task->requester)
                                    <div>
                                        <span class="font-semibold text-gray-700 block mb-1">User Request:</span>
                                        <p class="text-gray-600 break-words">
                                            {{ $task->requester->nik }} - {{ $task->requester->name }}
                                            @if($task->requester->position)
                                                <span class="text-xs text-gray-500">({{ $task->requester->position->name }})</span>
                                            @endif
                                        </p>
                                    </div>
                                @endif
                                @if($task->add_request)
                                    <div class="sm:col-span-2">
                                        <span class="font-semibold text-gray-700 block mb-1">Add Request:</span>
                                        <p class="text-gray-600 break-words">{{ $task->add_request }}</p>
                                    </div>
                                @endif
                                @if($task->start_date)
                                    <div>
                                        <span class="font-semibold text-gray-700 block mb-1">Start Date:</span>
                                        <p class="text-gray-600">{{ $task->start_date->format('d M Y') }}</p>
                                    </div>
                                @endif
                                <div>
                                    <span class="font-semibold text-gray-700 block mb-1">Deadline:</span>
                                    <p class="text-gray-600">{{ $task->due_date ? $task->due_date->format('d M Y') : '-' }}</p>
                                </div>
                                @if($task->approve_level !== null)
                                    <div>
                                        <span class="font-semibold text-gray-700 block mb-1">Approve Level:</span>
                                        <p class="text-gray-600">{{ $task->approve_level }}</p>
                                    </div>
                                @endif
                                <div>
                                    <span class="font-semibold text-gray-700 block mb-1">Dibuat pada:</span>
                                    <p class="text-gray-600">{{ $task->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-700 block mb-1">Diupdate pada:</span>
                                    <p class="text-gray-600">{{ $task->updated_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>

                            <!-- Attachments Section -->
                            <div class="mt-4 pt-4 border-t" x-data="{ showUploadForm: false }">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="font-semibold text-gray-700 text-sm sm:text-base">Dokumen & Lampiran:</h3>
                                    @php
                                        $canUpload = ($task->created_by === Auth::id()) || 
                                                    $task->delegations()->where('delegated_to', Auth::id())->exists() ||
                                                    ($task->requested_by === Auth::id()) ||
                                                    (Auth::user()->position && Auth::user()->position->name === 'Superuser');
                                    @endphp
                                    @if($canUpload)
                                        <button @click="showUploadForm = !showUploadForm" type="button" class="text-xs sm:text-sm bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg inline-flex items-center transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            <span x-show="!showUploadForm">Tambah File</span>
                                            <span x-show="showUploadForm">Tutup</span>
                                        </button>
                                    @endif
                                </div>

                                <!-- Upload Form -->
                                @if($canUpload)
                                    <div x-show="showUploadForm" x-transition class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                        <form action="{{ route('tasks.upload-attachment', $task) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                            @csrf
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih File (Maks. 10MB per file)</label>
                                                <input type="file" name="files[]" multiple accept="*/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                                @error('files.*')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan (Opsional)</label>
                                                <textarea name="description" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Tambahkan keterangan tentang file ini..."></textarea>
                                            </div>
                                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                                                Upload File
                                            </button>
                                        </form>
                                    </div>
                                @endif

                                <!-- Attachments List -->
                                @if($task->attachments && $task->attachments->count() > 0)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                                        @foreach($task->attachments as $attachment)
                                            @php
                                                $canDelete = ($attachment->uploaded_by === Auth::id()) || 
                                                            ($task->created_by === Auth::id()) ||
                                                            ($task->requested_by === Auth::id()) ||
                                                            (Auth::user()->position && Auth::user()->position->name === 'Superuser');
                                            @endphp
                                            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                                <!-- File Icon/Preview -->
                                                <div class="mb-3">
                                                    @if($attachment->is_image)
                                                        <a href="{{ route('tasks.preview-file', ['task' => $task->id, 'fileKey' => $attachment->id]) }}" target="_blank" class="block">
                                                            <img src="{{ route('tasks.preview-file', ['task' => $task->id, 'fileKey' => $attachment->id]) }}" alt="{{ $attachment->original_name }}" class="w-full h-32 object-cover rounded border border-gray-200 hover:border-blue-500 transition-colors">
                                                        </a>
                                                    @elseif(str_contains($attachment->file_type, 'pdf'))
                                                        <a href="{{ route('tasks.preview-file', ['task' => $task->id, 'fileKey' => $attachment->id]) }}" target="_blank" class="block bg-red-50 border-2 border-red-200 rounded p-8 text-center hover:border-red-400 transition-colors">
                                                            <svg class="w-16 h-16 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                            </svg>
                                                            <p class="text-xs text-red-600 mt-2 font-semibold">PDF</p>
                                                        </a>
                                                    @else
                                                        <div class="bg-gray-50 border-2 border-gray-200 rounded p-8 text-center">
                                                            <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                            <p class="text-xs text-gray-500 mt-2 font-semibold uppercase">{{ $attachment->extension }}</p>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- File Info -->
                                                <div class="mb-3">
                                                    <h4 class="text-sm font-semibold text-gray-800 truncate" title="{{ $attachment->original_name }}">
                                                        {{ $attachment->original_name }}
                                                    </h4>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        {{ $attachment->formatted_size }}
                                                    </p>
                                                    @if($attachment->description)
                                                        <p class="text-xs text-gray-600 mt-1 italic">{{ $attachment->description }}</p>
                                                    @endif
                                                    <p class="text-xs text-gray-400 mt-1">
                                                        Diupload oleh: {{ $attachment->uploader->name }}<br>
                                                        {{ $attachment->created_at->format('d M Y, H:i') }}
                                                    </p>
                                                </div>

                                                <!-- Actions -->
                                                <div class="flex gap-2">
                                                    <a href="{{ route('tasks.download-file', ['task' => $task->id, 'fileKey' => $attachment->id]) }}" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold py-2 px-3 rounded text-center transition-colors">
                                                        Download
                                                    </a>
                                                    @if($attachment->can_preview)
                                                        <a href="{{ route('tasks.preview-file', ['task' => $task->id, 'fileKey' => $attachment->id]) }}" target="_blank" class="bg-green-500 hover:bg-green-600 text-white text-xs font-semibold py-2 px-3 rounded transition-colors" title="Preview">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                    @if($canDelete)
                                                        <form action="{{ route('tasks.delete-attachment', ['task' => $task->id, 'attachment' => $attachment->id]) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus file ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs font-semibold py-2 px-3 rounded transition-colors" title="Hapus">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Legacy File Support (for backward compatibility) -->
                            @if($task->file_support_1 || $task->file_support_2)
                                    <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg mb-4">
                                        <p class="text-xs text-yellow-700 font-semibold mb-2">Dokumen Lama (Legacy):</p>
                                    <div class="space-y-2">
                                        @if($task->file_support_1)
                                                <div class="flex items-center justify-between text-sm bg-white p-2 rounded">
                                                    <span class="text-gray-700">File Support 1</span>
                                                    <a href="{{ route('tasks.download-file', ['task' => $task->id, 'fileKey' => 'file_support_1']) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-semibold text-xs">
                                                        Download
                                                    </a>
                                            </div>
                                        @endif
                                        @if($task->file_support_2)
                                                <div class="flex items-center justify-between text-sm bg-white p-2 rounded">
                                                    <span class="text-gray-700">File Support 2</span>
                                                    <a href="{{ route('tasks.download-file', ['task' => $task->id, 'fileKey' => 'file_support_2']) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-semibold text-xs">
                                                        Download
                                                    </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                @if((!$task->attachments || $task->attachments->count() === 0) && !$task->file_support_1 && !$task->file_support_2)
                                    <div class="text-center py-8 text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-sm">Belum ada file lampiran</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Task Items (Detail Pekerjaan) -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-4 sm:p-6">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 sm:gap-0 mb-4">
                                <div class="flex-1 min-w-0">
                                    <h2 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 sm:mb-0">Detail Pekerjaan</h2>
                                    @if($task->taskItems->count() > 0)
                                        @php
                                            $overallProgress = $task->overall_progress;
                                        @endphp
                                        <div class="mt-2 sm:mt-3">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="flex-1 bg-gray-200 rounded-full h-3 sm:h-2.5">
                                                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 h-3 sm:h-2.5 rounded-full transition-all" style="width: {{ $overallProgress }}%"></div>
                                                </div>
                                                <span class="text-sm sm:text-base font-semibold text-gray-700 whitespace-nowrap">{{ $overallProgress }}%</span>
                                            </div>
                                            <p class="text-xs text-gray-500 mb-1">Progress keseluruhan berdasarkan detail pekerjaan</p>
                                            <p class="text-xs text-orange-600 font-medium leading-relaxed">
                                                ⚠️ Progress delegation akan terupdate otomatis berdasarkan detail pekerjaan yang di-assign ke masing-masing user
                                            </p>
                                        </div>
                                    @endif
                                </div>
                                <button onclick="document.getElementById('add-task-item-form').classList.toggle('hidden')" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2.5 px-4 rounded text-sm w-full sm:w-auto flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <span>Tambah Detail</span>
                                </button>
                            </div>

                            <!-- Add Task Item Form -->
                            <div id="add-task-item-form" class="hidden mb-6 p-4 sm:p-6 bg-gray-50 rounded-lg border border-gray-200">
                                <h3 class="font-semibold text-gray-900 mb-3 text-sm sm:text-base">Tambah Detail Pekerjaan</h3>
                                <form action="{{ route('task-items.store', $task) }}" method="POST">
                                    @csrf
                                    <div class="space-y-4">
                                        <div>
                                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Detail Pekerjaan *</label>
                                            <input type="text" name="title" id="title" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base" placeholder="Contoh: Setup database, Design UI, dll">
                                            @error('title')
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                            <textarea name="description" id="description" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base" placeholder="Deskripsi detail pekerjaan (opsional)"></textarea>
                                        </div>
                                        <div>
                                            <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Assign To</label>
                                            <select name="assigned_to" id="assigned_to" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
                                                <option value="">Pilih User (Opsional)</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" {{ $user->id == $currentUser->id ? 'selected' : '' }}>
                                                        {{ $user->name }} ({{ $user->nik }})
                                                        @if($user->position)
                                                            - {{ $user->position->name }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <p class="text-xs text-gray-500 mt-1">User yang terdaftar dalam delegasi task ini</p>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                                <input type="date" name="start_date" id="start_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
                                            </div>
                                            <div>
                                                <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                                <input type="time" name="start_time" id="start_time" value="{{ \Carbon\Carbon::now()->format('H:i') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date *</label>
                                                <input type="date" name="due_date" id="due_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
                                            </div>
                                            <div>
                                                <label for="due_time" class="block text-sm font-medium text-gray-700 mb-1">Due Time *</label>
                                                <input type="time" name="due_time" id="due_time" value="{{ \Carbon\Carbon::now()->format('H:i') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base" required>
                                                <p class="text-xs text-gray-500 mt-1">Waktu batas akhir untuk reminder</p>
                                            </div>
                                        </div>
                                        <div class="flex flex-col sm:flex-row gap-2 pt-2">
                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded text-sm w-full sm:w-auto">
                                                Simpan
                                            </button>
                                            <button type="button" onclick="document.getElementById('add-task-item-form').classList.add('hidden')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2.5 px-4 rounded text-sm w-full sm:w-auto">
                                                Batal
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Task Items List -->
                            @if($task->taskItems->count() > 0)
                                <div class="space-y-3 sm:space-y-4">
                                    @foreach($task->taskItems as $item)
                                        <div class="border rounded-lg p-3 sm:p-4 hover:shadow-md transition-shadow bg-white">
                                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 mb-3">
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                                        <span class="text-xs font-semibold text-gray-500">#{{ $loop->iteration }}</span>
                                                        <h3 class="font-semibold text-sm sm:text-base text-gray-900 break-words">{{ $item->title }}</h3>
                                                        <span class="px-2 py-1 text-xs rounded whitespace-nowrap {{ $item->status === 'completed' ? 'bg-green-100 text-green-800' : ($item->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                                        </span>
                                                    </div>
                                                    @if($item->description)
                                                        <p class="text-xs sm:text-sm text-gray-600 mb-2 break-words">{{ $item->description }}</p>
                                                    @endif
                                                    <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-3 text-xs text-gray-500">
                                                        @if($item->assignedUser)
                                                            <span class="break-words">Assigned: <span class="font-semibold">{{ $item->assignedUser->name }}</span></span>
                                                        @endif
                                                        @if($item->start_date)
                                                            <span class="break-words">Start: {{ $item->start_date->format('d M Y') }}{{ $item->start_time ? ' ' . $item->start_time : '' }}</span>
                                                        @endif
                                                        @if($item->due_date)
                                                            <span class="{{ $item->isOverdue() ? 'text-red-600 font-bold' : '' }} break-words">
                                                                Due: {{ $item->due_date->format('d M Y') }}{{ $item->due_time ? ' ' . $item->due_time : '' }}
                                                                @if($item->isOverdue())
                                                                    <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 whitespace-nowrap">
                                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                                        </svg>
                                                                        TERLAMBAT
                                                                    </span>
                                                                @endif
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex flex-wrap gap-2 sm:flex-nowrap sm:flex-col">
                                                    @php
                                                        $isSuperuser = Auth::user()->position && Auth::user()->position->name === 'Superuser';
                                                        $isAssignedUser = $item->assigned_to == Auth::id();
                                                        $canUpdateProgress = $isSuperuser || $isAssignedUser;
                                                    @endphp
                                                    @if($isSuperuser)
                                                        <a href="{{ route('task-items.edit', [$task, $item]) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white text-xs font-bold py-2 px-3 rounded inline-flex items-center justify-center flex-1 sm:flex-none">
                                                            <svg class="w-3 h-3 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                            <span class="hidden sm:inline">Edit</span>
                                                        </a>
                                                    @endif
                                                    @if($canUpdateProgress)
                                                        @if($item->progress_percentage < 100)
                                                            <button onclick="openProgressModal({{ $item->id }}, {{ $item->progress_percentage }}, '{{ addslashes($item->title) }}', {{ $item->start_time ? "'" . $item->start_time . "'" : 'null' }}, {{ $item->due_time ? "'" . $item->due_time . "'" : 'null' }})" class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-2 px-3 rounded flex-1 sm:flex-none">
                                                                <span class="hidden sm:inline">Update Progress</span>
                                                                <span class="sm:hidden">Update</span>
                                                            </button>
                                                        @else
                                                            <button onclick="openProgressModal({{ $item->id }}, {{ $item->progress_percentage }}, '{{ addslashes($item->title) }}', {{ $item->start_time ? "'" . $item->start_time . "'" : 'null' }}, {{ $item->due_time ? "'" . $item->due_time . "'" : 'null' }})" class="bg-green-500 hover:bg-green-700 text-white text-xs font-bold py-2 px-3 rounded flex-1 sm:flex-none" title="Progress sudah 100%. Bisa tambah catatan/foto">
                                                                <span class="hidden sm:inline">Tambah Catatan/Foto</span>
                                                                <span class="sm:hidden">Catatan</span>
                                                            </button>
                                                        @endif
                                                    @endif
                                                    <form action="{{ route('task-items.destroy', [$task, $item]) }}" method="POST" onsubmit="return confirm('Hapus detail pekerjaan ini?');" class="inline flex-1 sm:flex-none">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white text-xs font-bold py-2 px-3 rounded w-full">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <!-- Progress Bar -->
                                            <div class="mb-3">
                                                <div class="flex items-center justify-between mb-1.5">
                                                    <span class="text-xs font-medium text-gray-600">Progress</span>
                                                    <span class="text-xs sm:text-sm font-bold text-gray-700">{{ $item->progress_percentage }}%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-3 sm:h-2.5">
                                                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 h-3 sm:h-2.5 rounded-full transition-all flex items-center justify-end pr-2" style="width: {{ $item->progress_percentage }}%">
                                                        @if($item->progress_percentage > 20)
                                                            <span class="text-xs font-bold text-white">{{ $item->progress_percentage }}%</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Recent Updates -->
                                            @if($item->updates->count() > 0)
                                                <div class="mt-3 pt-3 border-t border-gray-200">
                                                    <h4 class="text-xs font-semibold text-gray-700 mb-2">Update Terbaru ({{ $item->updates->count() }} update):</h4>
                                                    <div class="space-y-2 max-h-60 overflow-y-auto">
                                                        @foreach($item->updates->take(5) as $update)
                                                            <div class="bg-gray-50 rounded p-2.5 sm:p-3 border-l-2 border-blue-500">
                                                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-1.5">
                                                                    <div class="flex-1 min-w-0">
                                                                        <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 text-xs font-bold text-blue-600 mb-1.5">
                                                                            @if($update->new_progress_percentage !== null)
                                                                                <span class="break-words">Progress: {{ $update->old_progress_percentage ?? 0 }}% → {{ $update->new_progress_percentage }}%</span>
                                                                            @endif
                                                                            @if($update->new_status)
                                                                                <span class="hidden sm:inline">|</span>
                                                                                <span class="break-words">Status: {{ ucfirst($update->old_status ?? 'pending') }} → {{ ucfirst($update->new_status) }}</span>
                                                                            @endif
                                                                        </div>
                                                                        <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-2 text-xs text-gray-500">
                                                                            <span class="flex items-center gap-1">
                                                                                <span>📅</span>
                                                                                <span>{{ $update->update_date ? $update->update_date->format('d M Y') : $update->created_at->format('d M Y') }}</span>
                                                                            </span>
                                                                            @if($update->time_from || $update->time_to)
                                                                                <span class="hidden sm:inline text-gray-400">|</span>
                                                                                <span class="flex items-center gap-1">
                                                                                    <span>🕐</span>
                                                                                    <span>
                                                                                        @if($update->time_from && $update->time_to)
                                                                                            {{ \Carbon\Carbon::parse($update->time_from)->format('H:i') }} - {{ \Carbon\Carbon::parse($update->time_to)->format('H:i') }}
                                                                                        @elseif($update->time_from)
                                                                                            Mulai: {{ \Carbon\Carbon::parse($update->time_from)->format('H:i') }}
                                                                                        @elseif($update->time_to)
                                                                                            Selesai: {{ \Carbon\Carbon::parse($update->time_to)->format('H:i') }}
                                                                                        @endif
                                                                                    </span>
                                                                                </span>
                                                                            @else
                                                                                <span class="hidden sm:inline text-gray-400">|</span>
                                                                                <span class="flex items-center gap-1">
                                                                                    <span>🕐</span>
                                                                                    <span>{{ $update->created_at->format('H:i') }}</span>
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    @if(Auth::user()->position && Auth::user()->position->name === 'Superuser')
                                                                        <a href="{{ route('task-items.update.edit', [$task, $item, $update]) }}" class="text-xs text-indigo-600 hover:text-indigo-800 flex-shrink-0 self-start sm:self-center" title="Edit Update">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                            </svg>
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                                @if($update->notes)
                                                                    <p class="text-xs text-gray-700 mt-1.5 break-words">{{ $update->notes }}</p>
                                                                @endif
                                                                
                                                                <!-- Photo Attachments -->
                                                                @if($update->attachments && count($update->attachments) > 0)
                                                                    <div class="mt-2 flex flex-wrap gap-2">
                                                                        @foreach($update->attachments as $i => $attachment)
                                                                            <a href="{{ route('progress.download-file', ['progressUpdate' => $update->id, 'index' => $i]) }}" target="_blank" class="block">
                                                                                <img src="{{ route('progress.download-file', ['progressUpdate' => $update->id, 'index' => $i]) }}" alt="Bukti" class="w-16 h-16 object-cover rounded border border-gray-200 hover:border-blue-500 transition-colors cursor-pointer">
                                                                            </a>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                                
                                                                <p class="text-xs text-gray-500 mt-2 break-words">
                                                                    <span class="font-semibold">Oleh:</span> {{ $update->updater->name }}
                                                                    @if($update->updater->position)
                                                                        <span class="text-gray-400">({{ $update->updater->position->name }})</span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        @endforeach
                                                        @if($item->updates->count() > 5)
                                                            <p class="text-xs text-gray-500 text-center">... dan {{ $item->updates->count() - 5 }} update lainnya</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-4">Belum ada detail pekerjaan. Klik tombol "Tambah Detail" untuk menambahkan.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Delegations -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-semibold text-gray-900">Delegasi</h2>
                                <a href="{{ route('delegations.create', $task) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                    Tambah Delegasi
                                </a>
                            </div>

                            @if($task->delegations->count() > 0)
                                <div class="space-y-4">
                                    @foreach($task->delegations as $delegation)
                                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="flex-1">
                                                    <a href="{{ route('delegations.show', $delegation) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                                        Delegasi ke {{ $delegation->delegatedTo->name }}
                                                        @if($delegation->delegatedTo->position)
                                                            <span class="text-xs text-gray-500">({{ $delegation->delegatedTo->position->name }})</span>
                                                        @endif
                                                    </a>
                                                    <div class="mt-1">
                                                        <span class="px-2 py-0.5 text-xs rounded {{ $delegation->status === 'completed' ? 'bg-green-100 text-green-800' : ($delegation->status === 'accepted' || $delegation->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : ($delegation->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                                            Status: {{ ucfirst($delegation->status) }}
                                                        </span>
                                                    </div>
                                                    <p class="text-sm text-gray-500">
                                                        Oleh {{ $delegation->delegatedBy->name }}
                                                        @if($delegation->delegatedBy->position)
                                                            <span class="text-xs">({{ $delegation->delegatedBy->position->name }})</span>
                                                        @endif
                                                    </p>
                                                </div>
                                                <span class="px-2 py-1 text-xs rounded {{ $delegation->status === 'completed' ? 'bg-green-100 text-green-800' : ($delegation->status === 'accepted' || $delegation->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst($delegation->status) }}
                                                </span>
                                            </div>
                                            
                                            <!-- Waktu Mulai dan Selesai -->
                                            <div class="mb-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs sm:text-sm">
                                                @if($delegation->accepted_at)
                                                    <div class="bg-green-50 border border-green-200 rounded-lg p-2">
                                                        <span class="font-semibold text-green-700 block mb-1">Mulai Kerja:</span>
                                                        <p class="text-green-800">
                                                            {{ \Carbon\Carbon::parse($delegation->accepted_at)->setTimezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y, H:i') }} WIB
                                                        </p>
                                                    </div>
                                                @else
                                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-2">
                                                        <span class="font-semibold text-gray-700 block mb-1">Mulai Kerja:</span>
                                                        <p class="text-gray-500">Belum diterima</p>
                                                    </div>
                                                @endif
                                                
                                                @if($delegation->completed_at)
                                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-2">
                                                        <span class="font-semibold text-blue-700 block mb-1">Selesai Kerja:</span>
                                                        <p class="text-blue-800">
                                                            {{ \Carbon\Carbon::parse($delegation->completed_at)->setTimezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y, H:i') }} WIB
                                                        </p>
                                                    </div>
                                                @elseif($delegation->accepted_at)
                                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-2">
                                                        <span class="font-semibold text-yellow-700 block mb-1">Selesai Kerja:</span>
                                                        <p class="text-yellow-600">Masih berjalan</p>
                                                    </div>
                                                @else
                                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-2">
                                                        <span class="font-semibold text-gray-700 block mb-1">Selesai Kerja:</span>
                                                        <p class="text-gray-500">-</p>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="mb-3">
                                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                    <div class="bg-blue-600 h-2.5 rounded-full transition-all" style="width: {{ $delegation->progress_percentage }}%"></div>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">Progress: {{ $delegation->progress_percentage }}%</p>
                                            </div>
                                            @if($delegation->notes)
                                                <p class="text-sm text-gray-600 mb-3">{{ $delegation->notes }}</p>
                                            @endif

                                            <!-- Progress Updates -->
                                            @if($delegation->progressUpdates->count() > 0)
                                                <div class="mt-4 pt-4 border-t border-gray-200">
                                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Update Progress:</h4>
                                                    <div class="space-y-3">
                                                        @foreach($delegation->progressUpdates->sortByDesc('created_at')->take(3) as $update)
                                                            <div class="bg-gray-50 rounded-lg p-3 border-l-4 border-blue-500">
                                                                <div class="flex items-center justify-between mb-1">
                                                                    <span class="text-sm font-bold text-blue-600">{{ $update->progress_percentage }}%</span>
                                                                    <span class="text-xs text-gray-500">{{ $update->created_at->format('d M Y, H:i') }}</span>
                                                                </div>
                                                                @if($update->notes)
                                                                    <p class="text-xs text-gray-700 mt-1">{{ Str::limit($update->notes, 100) }}</p>
                                                                @endif
                                                                @if($update->attachments && count($update->attachments) > 0)
                                                                    <div class="mt-2 flex gap-2">
                                                                        @php $maxThumb = min(3, count($update->attachments)); @endphp
                                                                        @for($i = 0; $i < $maxThumb; $i++)
                                                                            @php $attachment = $update->attachments[$i]; @endphp
                                                                            <a href="{{ route('progress.download-file', ['progressUpdate' => $update->id, 'index' => $i]) }}" target="_blank" class="block">
                                                                                <img src="{{ route('progress.download-file', ['progressUpdate' => $update->id, 'index' => $i]) }}" alt="Bukti" class="w-12 h-12 object-cover rounded border border-gray-200 hover:border-blue-500 transition-colors">
                                                                            </a>
                                                                        @endfor
                                                                        @if(count($update->attachments) > 3)
                                                                            <div class="w-12 h-12 bg-gray-200 rounded border border-gray-300 flex items-center justify-center">
                                                                                <span class="text-xs text-gray-600">+{{ count($update->attachments) - 3 }}</span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                                <p class="text-xs text-gray-500 mt-1">
                                                                    Oleh: {{ $update->updater->name }}
                                                                    @if($update->updater->position)
                                                                        <span class="text-gray-400">({{ $update->updater->position->name }})</span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        @endforeach
                                                        @if($delegation->progressUpdates->count() > 3)
                                                            <a href="{{ route('delegations.show', $delegation) }}" class="text-xs text-blue-600 hover:text-blue-800 font-semibold">
                                                                Lihat semua update ({{ $delegation->progressUpdates->count() }})
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <p class="text-xs text-gray-400 mt-2">Belum ada update progress</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">Belum ada delegasi untuk task ini.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Task History -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Perubahan Task</h2>
                            
                            @if($task->histories->count() > 0)
                                <div class="space-y-4">
                                    @foreach($task->histories as $history)
                                        <div class="border-l-4 border-blue-500 pl-4 py-2 bg-gray-50 rounded-r-lg">
                                            <div class="flex items-start justify-between mb-2">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="badge {{ $history->action === 'created' ? 'badge-success' : ($history->action === 'deleted' ? 'badge-danger' : 'badge-info') }}">
                                                            {{ ucfirst($history->action) }}
                                                        </span>
                                                        <span class="text-sm text-gray-500">
                                                            {{ $history->created_at->format('d M Y, H:i') }}
                                                        </span>
                                                    </div>
                                                    <p class="text-sm text-gray-700">
                                                        Oleh: <span class="font-semibold">{{ $history->updater->name }}</span>
                                                        @if($history->updater->position)
                                                            <span class="text-xs text-gray-500">({{ $history->updater->position->name }})</span>
                                                        @endif
                                                    </p>
                                                    @if($history->notes)
                                                        <p class="text-xs text-gray-600 mt-1">{{ $history->notes }}</p>
                                                    @endif
                                                    
                                                    @if($history->action === 'deleted' && $history->old_values)
                                                        <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded">
                                                            <p class="text-xs text-red-700 font-semibold">Task telah dihapus</p>
                                                            <p class="text-xs text-red-600 mt-1">Data task sebelum dihapus telah disimpan dalam history</p>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($history->action === 'updated' && $history->old_values && $history->new_values)
                                                        <div class="mt-2 space-y-1">
                                                            @php
                                                                $fieldLabels = [
                                                                    'room_id' => 'Room',
                                                                    'project_code' => 'Project Code',
                                                                    'title' => 'Title',
                                                                    'description' => 'Description',
                                                                    'priority' => 'Priority',
                                                                    'type' => 'Type',
                                                                    'status' => 'Status',
                                                                    'due_date' => 'Due Date',
                                                                    'start_date' => 'Start Date',
                                                                    'requested_by' => 'User Request',
                                                                    'add_request' => 'Add Request',
                                                                    'file_support_1' => 'File Support 1',
                                                                    'file_support_2' => 'File Support 2',
                                                                    'approve_level' => 'Approve Level',
                                                                ];
                                                                $oldValuesArray = is_array($history->old_values) ? $history->old_values : [];
                                                                $newValuesArray = is_array($history->new_values) ? $history->new_values : [];
                                                            @endphp
                                                            @foreach($newValuesArray as $field => $newValue)
                                                                @if(isset($oldValuesArray[$field]) && $oldValuesArray[$field] != $newValue)
                                                                    @php
                                                                        // Handle special fields
                                                                        $oldDisplay = $oldValuesArray[$field];
                                                                        $newDisplay = $newValue;
                                                                        
                                                                        // Format dates
                                                                        if (in_array($field, ['due_date', 'start_date']) && $oldDisplay) {
                                                                            try {
                                                                                $oldDisplay = \Carbon\Carbon::parse($oldDisplay)->format('d M Y');
                                                                            } catch (\Exception $e) {
                                                                                // Keep original if parsing fails
                                                                            }
                                                                        }
                                                                        if (in_array($field, ['due_date', 'start_date']) && $newDisplay) {
                                                                            try {
                                                                                $newDisplay = \Carbon\Carbon::parse($newDisplay)->format('d M Y');
                                                                            } catch (\Exception $e) {
                                                                                // Keep original if parsing fails
                                                                            }
                                                                        }
                                                                        
                                                                        // Handle room_id
                                                                        if ($field === 'room_id' && $oldDisplay) {
                                                                            $oldRoom = \App\Models\Room::find($oldDisplay);
                                                                            $oldDisplay = $oldRoom ? $oldRoom->room : $oldDisplay;
                                                                        }
                                                                        if ($field === 'room_id' && $newDisplay) {
                                                                            $newRoom = \App\Models\Room::find($newDisplay);
                                                                            $newDisplay = $newRoom ? $newRoom->room : $newDisplay;
                                                                        }
                                                                        
                                                                        // Handle requested_by
                                                                        if ($field === 'requested_by' && $oldDisplay) {
                                                                            $oldUser = \App\Models\User::find($oldDisplay);
                                                                            $oldDisplay = $oldUser ? $oldUser->name : $oldDisplay;
                                                                        }
                                                                        if ($field === 'requested_by' && $newDisplay) {
                                                                            $newUser = \App\Models\User::find($newDisplay);
                                                                            $newDisplay = $newUser ? $newUser->name : $newDisplay;
                                                                        }
                                                                    @endphp
                                                                    <div class="text-xs bg-white p-2 rounded border">
                                                                        <span class="font-semibold text-gray-700">{{ $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field)) }}:</span>
                                                                        <div class="mt-1">
                                                                            <span class="text-red-600 line-through">{{ $oldDisplay ?? '-' }}</span>
                                                                            <span class="text-gray-400 mx-2">→</span>
                                                                            <span class="text-green-600 font-semibold">{{ $newDisplay ?? '-' }}</span>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-4">Belum ada riwayat perubahan.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="font-semibold text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-2">
                                <form method="POST" action="{{ route('tasks.duplicate', $task) }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full bg-green-500 hover:bg-green-700 text-white text-center font-bold py-2 px-4 rounded inline-flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        Duplikat Task
                                    </button>
                                </form>
                                @can('create', [App\Models\Delegation::class, $task])
                                <a href="{{ route('delegations.create', $task) }}" class="block w-full bg-blue-500 hover:bg-blue-700 text-white text-center font-bold py-2 px-4 rounded">
                                    Delegasikan Task
                                </a>
                                @endcan
                                @can('update', $task)
                                <a href="{{ route('tasks.edit', $task) }}" class="block w-full bg-indigo-500 hover:bg-indigo-700 text-white text-center font-bold py-2 px-4 rounded">
                                    Edit Task
                                </a>
                                @endcan
                                @can('delete', $task)
                                    <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus task ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            Hapus Task
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Update Modal -->
    <div id="progressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full my-4 sm:my-8 max-h-[90vh] overflow-y-auto">
            <div class="p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Update Progress</h3>
                <p class="text-xs sm:text-sm text-gray-600 mb-4 break-words" id="modal-item-title"></p>
                
                <form id="progressForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="progress_percentage" class="block text-sm font-medium text-gray-700 mb-2">Progress (0-100%)</label>
                            <input type="number" name="progress_percentage" id="progress_percentage" min="0" max="100" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
                            <div class="mt-2">
                                <input type="range" id="progress_range" min="0" max="100" class="w-full" oninput="document.getElementById('progress_percentage').value = this.value">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Catatan: Progress bisa diupdate berkali-kali sampai mencapai 100%</p>
                        </div>
                        <div>
                            <label for="update_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Update *</label>
                            <input type="date" name="update_date" id="update_date" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
                            <p class="text-xs text-gray-500 mt-1">Pilih tanggal update progress (default: hari ini)</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="time_from" class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai</label>
                                <input type="time" name="time_from" id="time_from" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
                                <p class="text-xs text-gray-500 mt-1">Jam mulai bekerja (opsional)</p>
                            </div>
                            <div>
                                <label for="time_to" class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai</label>
                                <input type="time" name="time_to" id="time_to" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
                                <p class="text-xs text-gray-500 mt-1">Jam selesai bekerja (opsional)</p>
                            </div>
                        </div>
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                            <textarea name="notes" id="notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base" placeholder="Tambahkan catatan update (opsional)"></textarea>
                        </div>
                        
                        <!-- Photo Upload Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Bukti (Opsional)</label>
                            <div class="space-y-2">
                                <!-- File Input (Hidden) -->
                                <input type="file" id="photo-input" name="photos[]" accept="image/*" multiple class="hidden" onchange="handlePhotoSelect(event)">
                                
                                <!-- Upload Button -->
                                <button type="button" onclick="document.getElementById('photo-input').click()" class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm mb-2">
                                    📷 Upload Foto
                                </button>
                                
                                <!-- Camera Button (Mobile) -->
                                <button type="button" onclick="openCamera()" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                    📸 Ambil Foto dari Kamera
                                </button>
                                
                                <!-- Camera Input (Hidden) -->
                                <input type="file" id="camera-input" name="photos[]" accept="image/*" capture="environment" multiple class="hidden" onchange="handlePhotoSelect(event)">
                                
                                <!-- Preview Images -->
                                <div id="photo-preview" class="grid grid-cols-2 gap-2 mt-3"></div>
                                <p class="text-xs text-gray-500 mt-1">Maksimal 5MB per foto. Bisa upload multiple foto.</p>
                            </div>
                        </div>
                        
                        <div class="flex flex-col-reverse sm:flex-row gap-2 sm:justify-end pt-4 border-t">
                            <button type="button" onclick="closeProgressModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2.5 px-4 rounded text-sm w-full sm:w-auto">
                                Batal
                            </button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded text-sm w-full sm:w-auto">
                                Simpan Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let selectedPhotos = [];

        function openProgressModal(itemId, currentProgress, itemTitle, startTime, dueTime) {
            const modal = document.getElementById('progressModal');
            const form = document.getElementById('progressForm');
            const progressInput = document.getElementById('progress_percentage');
            const progressRange = document.getElementById('progress_range');
            const titleElement = document.getElementById('modal-item-title');
            const photoPreview = document.getElementById('photo-preview');
            
            // Reset form
            titleElement.textContent = itemTitle;
            progressInput.value = currentProgress;
            progressRange.value = currentProgress;
            document.getElementById('notes').value = '';
            // Set default tanggal hari ini
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('update_date').value = today;
            // Set default waktu mulai dari start_time task item, jika ada
            if (startTime) {
                document.getElementById('time_from').value = startTime;
            } else {
                document.getElementById('time_from').value = '';
            }
            // Set default waktu selesai dari due_time task item, jika ada
            if (dueTime) {
                document.getElementById('time_to').value = dueTime;
            } else {
                document.getElementById('time_to').value = '';
            }
            document.getElementById('photo-input').value = '';
            document.getElementById('camera-input').value = '';
            photoPreview.innerHTML = '';
            selectedPhotos = [];
            
            form.action = '{{ route("task-items.update-progress", [$task, ":itemId"]) }}'.replace(':itemId', itemId);
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeProgressModal() {
            const modal = document.getElementById('progressModal');
            const photoPreview = document.getElementById('photo-preview');
            
            // Reset
            photoPreview.innerHTML = '';
            selectedPhotos = [];
            document.getElementById('photo-input').value = '';
            document.getElementById('camera-input').value = '';
            
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openCamera() {
            // Try to open camera directly
            document.getElementById('camera-input').click();
        }

        function handlePhotoSelect(event) {
            const files = Array.from(event.target.files);
            const photoPreview = document.getElementById('photo-preview');
            
            files.forEach(file => {
                if (file.type.startsWith('image/')) {
                    selectedPhotos.push(file);
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative';
                        div.innerHTML = `
                            <img src="${e.target.result}" alt="Preview" class="w-full h-24 object-cover rounded border border-gray-300">
                            <button type="button" onclick="removePhoto(this)" class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">×</button>
                        `;
                        photoPreview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        function removePhoto(button) {
            const div = button.parentElement;
            const index = Array.from(div.parentElement.children).indexOf(div);
            selectedPhotos.splice(index, 1);
            div.remove();
        }

        // Close modal when clicking outside
        document.getElementById('progressModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeProgressModal();
            }
        });

        // Sync range input with number input
        document.getElementById('progress_percentage').addEventListener('input', function() {
            document.getElementById('progress_range').value = this.value;
        });

        document.getElementById('progress_range').addEventListener('input', function() {
            document.getElementById('progress_percentage').value = this.value;
        });

        // Handle form submission with multiple file inputs
        document.getElementById('progressForm').addEventListener('submit', function(e) {
            // Merge files from both inputs if needed
            const photoInput = document.getElementById('photo-input');
            const cameraInput = document.getElementById('camera-input');
            
            if (photoInput.files.length > 0 && cameraInput.files.length > 0) {
                // Create a new FileList or DataTransfer to combine files
                const dt = new DataTransfer();
                Array.from(photoInput.files).forEach(file => dt.items.add(file));
                Array.from(cameraInput.files).forEach(file => dt.items.add(file));
                photoInput.files = dt.files;
            }
        });
    </script>
</x-app-layout>

