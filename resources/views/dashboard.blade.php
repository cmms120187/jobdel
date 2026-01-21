<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 leading-tight">
                    {{ __('Dashboard') }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Selamat datang, {{ Auth::user()->name }}!</p>
            </div>
            @if(isset($isSuperuser) && $isSuperuser)
                <span class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm rounded-full gradient-purple text-purple-800 font-bold shadow-lg whitespace-nowrap">
                    ⭐ Superuser Mode
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-4 sm:py-8 bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 fade-in bg-gradient-to-r from-green-400 to-green-600 text-white px-6 py-4 rounded-xl shadow-lg flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Tasks -->
                <div class="stat-card fade-in">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="stat-card-icon gradient-blue">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <span class="text-3xl font-bold text-gray-800">{{ $stats['total_tasks'] }}</span>
                        </div>
                        <div class="text-sm font-medium text-gray-600">Total Tasks</div>
                        <div class="mt-2 text-xs text-gray-500">Semua pekerjaan</div>
                    </div>
                </div>

                <!-- Pending Tasks -->
                <div class="stat-card fade-in">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="stat-card-icon gradient-warning">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-3xl font-bold text-yellow-600">{{ $stats['pending_tasks'] }}</span>
                        </div>
                        <div class="text-sm font-medium text-gray-600">Pending Tasks</div>
                        <div class="mt-2 text-xs text-gray-500">Menunggu tindakan</div>
                    </div>
                </div>

                <!-- In Progress -->
                <div class="stat-card fade-in">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="stat-card-icon gradient-info">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <span class="text-3xl font-bold text-blue-600">{{ $stats['in_progress_tasks'] }}</span>
                        </div>
                        <div class="text-sm font-medium text-gray-600">In Progress</div>
                        <div class="mt-2 text-xs text-gray-500">Sedang dikerjakan</div>
                    </div>
                </div>

                <!-- Completed -->
                <div class="stat-card fade-in">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="stat-card-icon gradient-success">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-3xl font-bold text-green-600">{{ $stats['completed_tasks'] }}</span>
                        </div>
                        <div class="text-sm font-medium text-gray-600">Completed</div>
                        <div class="mt-2 text-xs text-gray-500">Selesai</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- My Tasks -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden fade-in">
                    <div class="gradient-blue p-4">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                @if(isset($isSuperuser) && $isSuperuser)
                                    Semua Tasks
                                @else
                                    Tasks Saya
                                @endif
                            </h3>
                            <a href="{{ route('tasks.create') }}" class="bg-white text-blue-600 font-bold py-2 px-4 rounded-lg text-sm hover:bg-blue-50 transform hover:scale-105 transition-all duration-200 shadow-md">
                                + Buat Task
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($myTasks->count() > 0)
                            <div class="space-y-3">
                                @foreach($myTasks->take(5) as $task)
                                    <a href="{{ route('tasks.show', $task) }}" class="block task-card {{ $task->status === 'completed' ? 'task-card-completed' : ($task->status === 'in_progress' ? 'task-card-progress' : 'task-card-pending') }}">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900 mb-2 hover:text-blue-600 transition-colors">
                                                    {{ $task->title }}
                                                </h4>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="badge {{ $task->status === 'completed' ? 'badge-success' : ($task->status === 'in_progress' ? 'badge-info' : 'badge-warning') }}">
                                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                    </span>
                                                    <span class="badge {{ $task->priority === 'high' ? 'badge-danger' : ($task->priority === 'medium' ? 'badge-warning' : 'badge-info') }}">
                                                        {{ ucfirst($task->priority) }} Priority
                                                    </span>
                                                    @if($task->type)
                                                        <span class="badge badge-purple">
                                                            {{ $task->type }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            <div class="mt-6 text-center">
                                <a href="{{ route('tasks.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm inline-flex items-center">
                                    Lihat semua tasks
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-gray-500 mb-4">Belum ada tasks.</p>
                                <a href="{{ route('tasks.create') }}" class="btn-primary inline-block">
                                    Buat Task Pertama
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Delegated To Me -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden fade-in">
                    <div class="gradient-green p-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            @if(isset($isSuperuser) && $isSuperuser)
                                Semua Delegasi
                            @else
                                Delegasi untuk Saya
                            @endif
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($delegatedToMe->count() > 0)
                            <div class="space-y-3">
                                @foreach($delegatedToMe->take(5) as $delegation)
                                    <a href="{{ route('delegations.show', $delegation) }}" class="block task-card task-card-progress">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900 mb-2 hover:text-green-600 transition-colors">
                                                    {{ $delegation->task->title }}
                                                </h4>
                                                <div class="flex items-center gap-2 mb-2 flex-wrap">
                                                    <span class="badge {{ $delegation->status === 'completed' ? 'badge-success' : ($delegation->status === 'accepted' || $delegation->status === 'in_progress' ? 'badge-info' : 'badge-warning') }}">
                                                        {{ ucfirst(str_replace('_', ' ', $delegation->status)) }}
                                                    </span>
                                                </div>
                                                <div class="mt-2">
                                                    <div class="flex items-center justify-between mb-1">
                                                        <span class="text-xs font-medium text-gray-600">Progress</span>
                                                        <span class="text-xs font-bold text-blue-600">{{ $delegation->progress_percentage }}%</span>
                                                    </div>
                                                    <div class="progress-bar">
                                                        <div class="progress-fill bg-gradient-to-r from-blue-500 to-purple-600" style="width: {{ $delegation->progress_percentage }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            <div class="mt-6 text-center">
                                <a href="{{ route('delegations.index') }}" class="text-green-600 hover:text-green-800 font-semibold text-sm inline-flex items-center">
                                    Lihat semua delegasi
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="text-gray-500">Tidak ada delegasi untuk Anda.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tutorial Section -->
            <div class="mt-12 mb-8">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden fade-in">
                    <div class="gradient-purple p-4 sm:p-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                Panduan Penggunaan Aplikasi
                            </h2>
                        </div>
                        <p class="text-white/90 text-sm sm:text-base mt-2">Pelajari cara menggunakan Job Delegation dengan mudah</p>
                    </div>

                    <div class="p-4 sm:p-6">
                        <!-- Tutorial Tabs -->
                        <div class="mb-6">
                            <div class="flex flex-wrap gap-2 border-b border-gray-200">
                                <button onclick="showTutorial('create-task')" id="tab-create-task" class="tutorial-tab active px-4 py-2 text-sm font-medium text-gray-700 border-b-2 border-purple-600">
                                    📝 Membuat Task
                                </button>
                                <button onclick="showTutorial('detail-pekerjaan')" id="tab-detail-pekerjaan" class="tutorial-tab px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700">
                                    📋 Detail Pekerjaan
                                </button>
                                <button onclick="showTutorial('update-progress')" id="tab-update-progress" class="tutorial-tab px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700">
                                    📊 Update Progress
                                </button>
                                <button onclick="showTutorial('delegasi')" id="tab-delegasi" class="tutorial-tab px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700">
                                    👥 Delegasi
                                </button>
                            </div>
                        </div>

                        <!-- Tutorial Content: Membuat Task -->
                        <div id="tutorial-create-task" class="tutorial-content">
                            <div class="space-y-6">
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                                    <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Apa itu Task?
                                    </h3>
                                    <p class="text-sm text-blue-800">Task adalah pekerjaan atau proyek yang perlu dikerjakan. Setiap task dapat memiliki detail pekerjaan yang lebih spesifik dan dapat didelegasikan ke user lain.</p>
                                </div>

                                <div class="space-y-4">
                                    <h4 class="font-semibold text-gray-900 text-lg">Langkah-langkah Membuat Task:</h4>
                                    
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold text-sm">1</div>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-semibold text-gray-800 mb-1">Klik tombol "Buat Task" atau menu "Tasks" → "Create Task"</h5>
                                            <p class="text-sm text-gray-600 mb-2">Anda akan diarahkan ke halaman form pembuatan task baru.</p>
                                            <div class="bg-gray-100 p-3 rounded-lg text-xs text-gray-700">
                                                <strong>Tips:</strong> Tombol "Buat Task" tersedia di dashboard atau di halaman Tasks.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold text-sm">2</div>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-semibold text-gray-800 mb-1">Isi informasi Task</h5>
                                            <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside ml-2">
                                                <li><strong>Title:</strong> Judul task (wajib)</li>
                                                <li><strong>Description:</strong> Deskripsi detail task (opsional)</li>
                                                <li><strong>Room:</strong> Pilih ruangan/lokasi (wajib)</li>
                                                <li><strong>Type:</strong> TASK atau PROJECT</li>
                                                <li><strong>Priority:</strong> High, Medium, atau Low</li>
                                                <li><strong>Due Date:</strong> Tanggal deadline</li>
                                                <li><strong>Project Code:</strong> Kode proyek (opsional)</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold text-sm">3</div>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-semibold text-gray-800 mb-1">Klik "Create Task"</h5>
                                            <p class="text-sm text-gray-600">Task akan dibuat dan Anda dapat menambahkan detail pekerjaan setelahnya.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-6">
                                    <h4 class="font-semibold text-green-900 mb-2 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Contoh Task:
                                    </h4>
                                    <div class="text-sm text-green-800 space-y-2">
                                        <p><strong>Title:</strong> Laporan Benchmark ke JX</p>
                                        <p><strong>Room:</strong> Plant A</p>
                                        <p><strong>Type:</strong> TASK</p>
                                        <p><strong>Priority:</strong> High</p>
                                        <p><strong>Due Date:</strong> 15 Januari 2026</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tutorial Content: Detail Pekerjaan -->
                        <div id="tutorial-detail-pekerjaan" class="tutorial-content hidden">
                            <div class="space-y-6">
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                                    <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Apa itu Detail Pekerjaan?
                                    </h3>
                                    <p class="text-sm text-blue-800">Detail Pekerjaan adalah breakdown dari task utama menjadi pekerjaan-pekerjaan yang lebih spesifik. Setiap detail pekerjaan dapat di-assign ke user tertentu dan memiliki progress sendiri.</p>
                                </div>

                                <div class="space-y-4">
                                    <h4 class="font-semibold text-gray-900 text-lg">Langkah-langkah Menambahkan Detail Pekerjaan:</h4>
                                    
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold text-sm">1</div>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-semibold text-gray-800 mb-1">Buka halaman detail Task</h5>
                                            <p class="text-sm text-gray-600 mb-2">Klik pada task yang ingin ditambahkan detail pekerjaannya.</p>
                                        </div>
                                    </div>

                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold text-sm">2</div>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-semibold text-gray-800 mb-1">Klik tombol "+ Tambah Detail"</h5>
                                            <p class="text-sm text-gray-600 mb-2">Tombol ini berada di section "Detail Pekerjaan" pada halaman task.</p>
                                        </div>
                                    </div>

                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold text-sm">3</div>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-semibold text-gray-800 mb-1">Isi form Detail Pekerjaan</h5>
                                            <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside ml-2">
                                                <li><strong>Judul Detail Pekerjaan:</strong> Nama detail pekerjaan (wajib)</li>
                                                <li><strong>Deskripsi:</strong> Penjelasan detail (opsional)</li>
                                                <li><strong>Assign To:</strong> User yang akan mengerjakan (opsional, bisa diisi nanti)</li>
                                                <li><strong>Start Date & Time:</strong> Tanggal dan jam mulai (opsional)</li>
                                                <li><strong>Due Date & Time:</strong> Tanggal dan jam deadline (wajib)</li>
                                            </ul>
                                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded-r-lg mt-2">
                                                <p class="text-xs text-yellow-800"><strong>⚠️ Penting:</strong> Jika detail pekerjaan melewati due date, akan muncul indikator "TERLAMBAT" berwarna merah.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold text-sm">4</div>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-semibold text-gray-800 mb-1">Klik "Simpan"</h5>
                                            <p class="text-sm text-gray-600">Detail pekerjaan akan ditambahkan dan progress task akan terupdate otomatis.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-6">
                                    <h4 class="font-semibold text-green-900 mb-2 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Contoh Detail Pekerjaan:
                                    </h4>
                                    <div class="text-sm text-green-800 space-y-2">
                                        <p><strong>Judul:</strong> Investment Plan</p>
                                        <p><strong>Deskripsi:</strong> File Investment Plan Request FIT January 2026.xls untuk data detail Investment</p>
                                        <p><strong>Assigned To:</strong> WAHID NURCIPTO</p>
                                        <p><strong>Start:</strong> 08 Jan 2026 07:00</p>
                                        <p><strong>Due:</strong> 08 Jan 2026 08:00</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tutorial Content: Update Progress -->
                        <div id="tutorial-update-progress" class="tutorial-content hidden">
                            <div class="space-y-6">
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                                    <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Update Progress
                                    </h3>
                                    <p class="text-sm text-blue-800">Progress dapat diupdate oleh user yang di-assign atau administrator. Setiap update dapat disertai dengan catatan dan foto bukti.</p>
                                </div>

                                <div class="space-y-4">
                                    <h4 class="font-semibold text-gray-900 text-lg">Langkah-langkah Update Progress:</h4>
                                    
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold text-sm">1</div>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-semibold text-gray-800 mb-1">Buka halaman detail Task</h5>
                                            <p class="text-sm text-gray-600 mb-2">Pilih task yang ingin diupdate progress detail pekerjaannya.</p>
                                        </div>
                                    </div>

                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold text-sm">2</div>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-semibold text-gray-800 mb-1">Klik tombol "Update Progress" atau "Tambah Catatan/Foto"</h5>
                                            <p class="text-sm text-gray-600 mb-2">Tombol ini hanya muncul untuk user yang di-assign atau administrator.</p>
                                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded-r-lg mt-2">
                                                <p class="text-xs text-yellow-800"><strong>⚠️ Catatan:</strong> Jika progress sudah 100%, tombol akan berubah menjadi "Tambah Catatan/Foto" untuk menambahkan dokumentasi.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold text-sm">3</div>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-semibold text-gray-800 mb-1">Isi form Update Progress</h5>
                                            <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside ml-2">
                                                <li><strong>Progress:</strong> Persentase progress (0-100%)</li>
                                                <li><strong>Tanggal Update:</strong> Tanggal update (default: hari ini)</li>
                                                <li><strong>Waktu Mulai & Selesai:</strong> Jam kerja (opsional)</li>
                                                <li><strong>Catatan:</strong> Keterangan update (opsional)</li>
                                                <li><strong>Foto Bukti:</strong> Upload foto dokumentasi (opsional, maks 5MB per foto)</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold text-sm">4</div>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-semibold text-gray-800 mb-1">Klik "Simpan Update"</h5>
                                            <p class="text-sm text-gray-600">Progress akan terupdate dan tercatat dalam history update.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-6">
                                    <h4 class="font-semibold text-green-900 mb-2 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Tips Update Progress:
                                    </h4>
                                    <ul class="text-sm text-green-800 space-y-1 list-disc list-inside">
                                        <li>Update progress secara berkala untuk tracking yang akurat</li>
                                        <li>Gunakan foto bukti untuk dokumentasi pekerjaan</li>
                                        <li>Tambahkan catatan yang jelas untuk setiap update</li>
                                        <li>Progress dapat diupdate berkali-kali sampai mencapai 100%</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Tutorial Content: Delegasi -->
                        <div id="tutorial-delegasi" class="tutorial-content hidden">
                            <div class="space-y-6">
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                                    <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Delegasi Pekerjaan
                                    </h3>
                                    <p class="text-sm text-blue-800">Delegasi memungkinkan Anda untuk memberikan tugas kepada user lain. User yang menerima delegasi dapat mengupdate progress dan status pekerjaan.</p>
                                </div>

                                <div class="space-y-4">
                                    <h4 class="font-semibold text-gray-900 text-lg">Cara Mendelegasikan Pekerjaan:</h4>
                                    
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold text-sm">1</div>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-semibold text-gray-800 mb-1">Buka halaman detail Task</h5>
                                            <p class="text-sm text-gray-600 mb-2">Pilih task yang ingin didelegasikan.</p>
                                        </div>
                                    </div>

                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold text-sm">2</div>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-semibold text-gray-800 mb-1">Klik tombol "Delegasi" di section "Delegations"</h5>
                                            <p class="text-sm text-gray-600 mb-2">Anda akan melihat form untuk membuat delegasi baru.</p>
                                        </div>
                                    </div>

                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold text-sm">3</div>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-semibold text-gray-800 mb-1">Pilih user dan isi informasi delegasi</h5>
                                            <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside ml-2">
                                                <li><strong>User:</strong> Pilih user yang akan menerima delegasi</li>
                                                <li><strong>Notes:</strong> Catatan atau instruksi (opsional)</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold text-sm">4</div>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-semibold text-gray-800 mb-1">Klik "Create Delegation"</h5>
                                            <p class="text-sm text-gray-600">User yang menerima delegasi akan melihatnya di dashboard dan dapat mengupdate progress.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-6">
                                    <h4 class="font-semibold text-green-900 mb-2 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Status Delegasi:
                                    </h4>
                                    <div class="text-sm text-green-800 space-y-2">
                                        <p><strong>Pending:</strong> Delegasi baru dibuat, menunggu user menerima</p>
                                        <p><strong>Accepted:</strong> User telah menerima delegasi</p>
                                        <p><strong>In Progress:</strong> User sedang mengerjakan</p>
                                        <p><strong>Completed:</strong> Delegasi selesai dikerjakan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .tutorial-tab {
            transition: all 0.3s ease;
        }
        .tutorial-tab.active {
            color: #7c3aed;
        }
        .tutorial-content {
            animation: fadeIn 0.3s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <script>
        function showTutorial(tutorialId) {
            // Hide all tutorial contents
            document.querySelectorAll('.tutorial-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tutorial-tab').forEach(tab => {
                tab.classList.remove('active', 'text-purple-600', 'border-purple-600');
                tab.classList.add('text-gray-500', 'border-transparent');
            });
            
            // Show selected tutorial content
            document.getElementById('tutorial-' + tutorialId).classList.remove('hidden');
            
            // Add active class to selected tab
            const activeTab = document.getElementById('tab-' + tutorialId);
            activeTab.classList.add('active', 'text-purple-600', 'border-purple-600');
            activeTab.classList.remove('text-gray-500', 'border-transparent');
        }
    </script>
</x-app-layout>
