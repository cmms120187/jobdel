@php
    use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Delegasi') }}
            </h2>
            <a href="{{ route('delegations.index', ['page' => session('delegations_page', 1)]) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Task Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">
                                <a href="{{ route('tasks.show', $delegation->task) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $delegation->task->title }}
                                </a>
                            </h1>
                            <p class="text-gray-600 mb-4">{{ $delegation->task->description }}</p>
                            <div class="flex gap-2">
                                <span class="px-3 py-1 text-sm rounded {{ $delegation->status === 'completed' ? 'bg-green-100 text-green-800' : ($delegation->status === 'accepted' || $delegation->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($delegation->status) }}
                                </span>
                                <span class="px-3 py-1 text-sm rounded {{ $delegation->task->priority === 'high' ? 'bg-red-100 text-red-800' : ($delegation->task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($delegation->task->priority) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Delegation Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Delegasi</h2>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-semibold text-gray-700">Delegasikan ke:</span>
                                    <p class="text-gray-600">
                                        {{ $delegation->delegatedTo->name }}
                                        @if($delegation->delegatedTo->position)
                                            <span class="text-xs text-gray-500">({{ $delegation->delegatedTo->position->name }})</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-700">Delegasikan oleh:</span>
                                    <p class="text-gray-600">
                                        {{ $delegation->delegatedBy->name }}
                                        @if($delegation->delegatedBy->position)
                                            <span class="text-xs text-gray-500">({{ $delegation->delegatedBy->position->name }})</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-700">Progress:</span>
                                    <p class="text-gray-600">{{ $delegation->progress_percentage }}%</p>
                                </div>
                                @if($delegation->accepted_at)
                                    <div>
                                        <span class="font-semibold text-gray-700">Diterima pada:</span>
                                        <p class="text-gray-600">{{ $delegation->accepted_at->format('d M Y H:i') }}</p>
                                    </div>
                                @endif
                                @if($delegation->completed_at)
                                    <div>
                                        <span class="font-semibold text-gray-700">Selesai pada:</span>
                                        <p class="text-gray-600">{{ $delegation->completed_at->format('d M Y H:i') }}</p>
                                    </div>
                                @endif
                            </div>
                            @if($delegation->notes)
                                <div class="mt-4">
                                    <span class="font-semibold text-gray-700">Catatan:</span>
                                    <p class="text-gray-600 mt-1">{{ $delegation->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Task Items Progress -->
                    @if($delegation->task->taskItems->count() > 0)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-lg font-semibold text-gray-900">Detail Pekerjaan Task</h2>
                                    <a href="{{ route('tasks.show', $delegation->task) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Lihat Detail Task →
                                    </a>
                                </div>
                                @php
                                    // Get task items assigned to this delegation user only
                                    $userTaskItems = $delegation->task->taskItems->where('assigned_to', $delegation->delegated_to);
                                    $userProgress = 0;
                                    if ($userTaskItems->isNotEmpty()) {
                                        $totalProgress = $userTaskItems->sum('progress_percentage');
                                        $userProgress = round($totalProgress / $userTaskItems->count());
                                    }
                                @endphp
                                <div class="mb-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-sm font-semibold text-gray-700">Progress Anda:</span>
                                        <span class="text-lg font-bold text-blue-600">{{ $userProgress }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 h-3 rounded-full transition-all" style="width: {{ $userProgress }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Progress berdasarkan detail pekerjaan yang di-assign ke Anda</p>
                                    @if($userTaskItems->isEmpty())
                                        <p class="text-xs text-orange-600 mt-2 font-medium">
                                            ⚠️ Belum ada detail pekerjaan yang di-assign ke Anda. Delegasi tetap pending sampai Anda mulai bekerja.
                                        </p>
                                    @endif
                                </div>
                                <div class="space-y-3 max-h-96 overflow-y-auto">
                                    @if($userTaskItems->isEmpty())
                                        <div class="text-center py-4 text-gray-500">
                                            <p class="text-sm">Belum ada detail pekerjaan yang di-assign ke Anda.</p>
                                            <p class="text-xs mt-1">Klik "Lihat Detail Task" di atas untuk melihat semua detail pekerjaan.</p>
                                        </div>
                                    @endif
                                    @foreach($userTaskItems as $item)
                                        <div class="border rounded-lg p-3 bg-gray-50">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-sm text-gray-900">{{ $item->title }}</h4>
                                                    @if($item->description)
                                                        <p class="text-xs text-gray-600 mt-1">{{ Str::limit($item->description, 100) }}</p>
                                                    @endif
                                                </div>
                                                <span class="px-2 py-1 text-xs rounded ml-2 {{ $item->status === 'completed' ? 'bg-green-100 text-green-800' : ($item->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                                </span>
                                            </div>
                                            <div class="mt-2">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                        <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ $item->progress_percentage }}%"></div>
                                                    </div>
                                                    <span class="text-xs font-semibold text-gray-700 w-12 text-right">{{ $item->progress_percentage }}%</span>
                                                </div>
                                                @if($item->assignedUser && $item->assignedUser->id == $delegation->delegated_to)
                                                    <p class="text-xs text-gray-500 mt-1">Assigned to: {{ $item->assignedUser->name }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-4 pt-4 border-t">
                                    <p class="text-xs text-gray-500">
                                        <strong>Catatan:</strong> Progress delegation ini terupdate otomatis berdasarkan progress detail pekerjaan yang di-assign ke Anda. 
                                        Delegasi tetap <strong>pending</strong> sampai Anda mulai update progress pada detail pekerjaan yang di-assign ke Anda.
                                        Untuk update detail pekerjaan, klik "Lihat Detail Task" di atas.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Progress Updates -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Update Progress</h2>
                            
                            @if($delegation->delegated_to === auth()->id() && $delegation->status !== 'completed')
                                <form method="POST" action="{{ route('progress.store', $delegation) }}" enctype="multipart/form-data" class="mb-6">
                                    @csrf
                                    <div class="mb-4">
                                        <x-input-label for="progress_percentage" :value="__('Progress (%)')" />
                                        <x-text-input id="progress_percentage" class="block mt-1 w-full" type="number" name="progress_percentage" min="0" max="100" :value="old('progress_percentage', $delegation->progress_percentage)" required />
                                        <x-input-error :messages="$errors->get('progress_percentage')" class="mt-2" />
                                        <p class="mt-1 text-xs text-gray-500">Masukkan persentase progress (0-100%)</p>
                                    </div>
                                    <div class="mb-4">
                                        <x-input-label for="notes" :value="__('Catatan / Laporan')" />
                                        <textarea id="notes" name="notes" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Jelaskan perkembangan task...">{{ old('notes') }}</textarea>
                                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                                    </div>
                                    <div class="mb-4">
                                        <x-input-label for="photos" :value="__('Upload Photo Bukti (Opsional - bisa pilih multiple)')" />
                                        <input id="photos" name="photos[]" type="file" multiple accept="image/*" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                        <x-input-error :messages="$errors->get('photos')" class="mt-2" />
                                        <x-input-error :messages="$errors->get('photos.*')" class="mt-2" />
                                        <p class="mt-1 text-xs text-gray-500">Maksimal 5MB per foto. Format: JPG, PNG, GIF</p>
                                    </div>
                                    <x-primary-button class="w-full">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        {{ __('Update Progress') }}
                                    </x-primary-button>
                                </form>
                            @endif

                            @if($delegation->progressUpdates->count() > 0)
                                <div class="space-y-6">
                                    @foreach($delegation->progressUpdates->sortByDesc('created_at') as $update)
                                        <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-blue-500">
                                            <div class="flex justify-between items-start mb-3">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-3 mb-2">
                                                        <span class="text-2xl font-bold text-blue-600">{{ $update->progress_percentage }}%</span>
                                                        <div class="flex-1 bg-gray-200 rounded-full h-3">
                                                            <div class="bg-blue-600 h-3 rounded-full transition-all" style="width: {{ $update->progress_percentage }}%"></div>
                                                        </div>
                                                    </div>
                                                    <p class="text-sm text-gray-500 mb-2">
                                                        <span class="font-semibold">{{ $update->updater->name }}</span>
                                                        @if($update->updater->position)
                                                            <span class="text-xs">({{ $update->updater->position->name }})</span>
                                                        @endif
                                                        <span class="text-gray-400">•</span>
                                                        <span>{{ $update->created_at->format('d M Y, H:i') }}</span>
                                                    </p>
                                                    @if($update->notes)
                                                        <p class="text-gray-700 mt-2 whitespace-pre-wrap bg-white p-3 rounded border">{{ $update->notes }}</p>
                                                    @endif
                                                    
                                                    @if($update->attachments && count($update->attachments) > 0)
                                                        <div class="mt-3">
                                                            <p class="text-sm font-semibold text-gray-700 mb-2">Photo Bukti:</p>
                                                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                                                @foreach($update->attachments as $i => $attachment)
                                                                    <div class="relative group">
                                                                        <a href="{{ route('progress.download-file', ['progressUpdate' => $update->id, 'index' => $i]) }}" target="_blank" class="block">
                                                                            <img src="{{ route('progress.download-file', ['progressUpdate' => $update->id, 'index' => $i]) }}" alt="Bukti progress" class="w-full h-32 object-cover rounded-lg border-2 border-gray-200 hover:border-blue-500 transition-all cursor-pointer">
                                                                        </a>
                                                                        <a href="{{ route('progress.download-file', ['progressUpdate' => $update->id, 'index' => $i]) }}" target="_blank" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all rounded-lg">
                                                                            <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                                                            </svg>
                                                                        </a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 bg-gray-50 rounded-lg">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <p class="text-gray-500">Belum ada update progress.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="font-semibold text-gray-900 mb-4">Actions</h3>
                            @if($delegation->delegated_to === auth()->id())
                                @if($delegation->status === 'pending')
                                    <form method="POST" action="{{ route('delegations.update', $delegation) }}" class="mb-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="action" value="accept">
                                        <button type="submit" class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm mb-2">
                                            Terima Delegasi
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('delegations.update', $delegation) }}" class="mb-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm mb-2">
                                            Tolak Delegasi
                                        </button>
                                    </form>
                                @endif
                                @if($delegation->status === 'accepted' || $delegation->status === 'in_progress')
                                    <form method="POST" action="{{ route('delegations.update', $delegation) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="action" value="complete">
                                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                            Tandai Selesai
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

