<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Detail User') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Informasi lengkap user</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="bg-gradient-to-r from-orange-500 to-pink-600 text-white font-bold py-2 px-4 rounded-lg text-sm shadow-md hover:shadow-lg transform hover:scale-105 transition-all">
                    Edit
                </a>
                <a href="{{ route('admin.users.index', ['page' => session('admin_users_page', 1)]) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg text-sm">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 fade-in bg-gradient-to-r from-green-400 to-green-600 text-white px-6 py-4 rounded-xl shadow-lg flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- User Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 fade-in">
                        <div class="gradient-blue p-6">
                            <h1 class="text-2xl font-bold text-white">{{ $user->name }}</h1>
                            <p class="text-blue-100 mt-1">{{ $user->nik }}</p>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 gap-6 mb-6">
                                <div>
                                    <span class="text-sm font-semibold text-gray-500 uppercase">NIK</span>
                                    <p class="text-lg font-bold text-gray-900 mt-1">{{ $user->nik }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-gray-500 uppercase">Email</span>
                                    <p class="text-lg font-bold text-gray-900 mt-1">{{ $user->email }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-gray-500 uppercase">Position</span>
                                    <p class="text-lg font-bold text-gray-900 mt-1">
                                        @if($user->position)
                                            <span class="badge badge-info text-base">{{ $user->position->name }}</span>
                                            <span class="text-xs text-gray-500">(Level {{ $user->position->level }})</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-gray-500 uppercase">Leader</span>
                                    <p class="text-lg font-bold text-gray-900 mt-1">
                                        @if($user->leader)
                                            {{ $user->leader->name }}
                                            @if($user->leader->position)
                                                <span class="text-xs text-gray-500">({{ $user->leader->position->name }})</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-200">
                                <div class="text-sm text-gray-500">
                                    <p>Dibuat: {{ $user->created_at->format('d M Y H:i') }}</p>
                                    <p>Diupdate: {{ $user->updated_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subordinates -->
                    @if($user->subordinates->count() > 0)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden fade-in">
                            <div class="gradient-green p-4">
                                <h2 class="text-lg font-bold text-white flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Subordinates ({{ $user->subordinates->count() }})
                                </h2>
                            </div>
                            <div class="p-6">
                                <div class="space-y-3">
                                    @foreach($user->subordinates as $subordinate)
                                        <a href="{{ route('admin.users.show', $subordinate) }}" class="block task-card task-card-pending">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-gray-900 mb-1 hover:text-blue-600 transition-colors">
                                                        {{ $subordinate->nik }} - {{ $subordinate->name }}
                                                    </h4>
                                                    @if($subordinate->position)
                                                        <span class="badge badge-info">
                                                            {{ $subordinate->position->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <svg class="w-5 h-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div>
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden fade-in">
                        <div class="p-6">
                            <h3 class="font-semibold text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="block w-full bg-gradient-to-r from-orange-500 to-pink-600 text-white text-center font-bold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all">
                                    Edit User
                                </a>
                                @if($user->position && $user->position->name !== 'Superuser')
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="block w-full bg-red-500 hover:bg-red-700 text-white text-center font-bold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all">
                                            Hapus User
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

