<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <div class="w-12 h-12 gradient-green rounded-lg flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Tambah User Baru') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Isi form di bawah untuk menambah user baru</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-xl overflow-hidden fade-in">
                <div class="gradient-green p-6">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Informasi User
                    </h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf

                        <!-- NIK -->
                        <div class="mb-6">
                            <x-input-label for="nik" :value="__('NIK')" />
                            <x-text-input id="nik" class="block mt-2 w-full border-2 border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-lg transition-all" type="text" name="nik" :value="old('nik', '12345')" required autofocus />
                            <x-input-error :messages="$errors->get('nik')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">Minimal 5 karakter</p>
                        </div>

                        <!-- Name -->
                        <div class="mb-6">
                            <x-input-label for="name" :value="__('Nama')" />
                            <x-text-input id="name" class="block mt-2 w-full border-2 border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-lg transition-all" type="text" name="name" :value="old('name')" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="mb-6">
                            <x-input-label for="email" :value="__('Email (Opsional - akan di-generate dari nama)')" />
                            <x-text-input id="email" class="block mt-2 w-full border-2 border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-lg transition-all" type="email" name="email" :value="old('email')" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">Jika kosong, email akan di-generate otomatis dari nama</p>
                        </div>

                        <!-- Password -->
                        <div class="mb-6">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-2 w-full border-2 border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-lg transition-all" type="password" name="password" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">Minimal 5 karakter</p>
                        </div>

                        <!-- Position -->
                        <div class="mb-6">
                            <x-input-label for="position_id" :value="__('Position')" />
                            <select id="position_id" name="position_id" class="block mt-2 w-full border-2 border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-lg shadow-sm transition-all" required>
                                <option value="">-- Pilih Position --</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                        {{ $position->name }} (Level {{ $position->level }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('position_id')" class="mt-2" />
                        </div>

                        <!-- Leader -->
                        <div class="mb-6">
                            <x-input-label for="leader_id" :value="__('Leader')" />
                            <select id="leader_id" name="leader_id" class="block mt-2 w-full border-2 border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-lg shadow-sm transition-all">
                                <option value="">-- Pilih Leader (Opsional) --</option>
                                @foreach($leaders as $leader)
                                    <option value="{{ $leader->id }}" {{ old('leader_id') == $leader->id ? 'selected' : '' }}>
                                        {{ $leader->nik }} - {{ $leader->name }} @if($leader->position) ({{ $leader->position->name }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('leader_id')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">Pilih leader untuk user ini (harus level di atas position user)</p>
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.users.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors mr-4">
                                Batal
                            </a>
                            <button type="submit" class="btn-success inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ __('Simpan User') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

