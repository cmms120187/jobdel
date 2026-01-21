@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4">
    <div class="bg-white p-6 rounded shadow">
        <div class="flex items-center mb-4">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-yellow-400 to-pink-500 flex items-center justify-center text-white font-bold text-xl me-4">{{ strtoupper(substr($user->name,0,1)) }}</div>
            <div>
                <h2 class="text-xl font-semibold">{{ $user->name }}</h2>
                <div class="text-sm text-gray-600">{{ $user->email }}</div>
                <div class="text-sm text-gray-600">{{ optional($user->position)->name ?? '—' }}</div>
            </div>
        </div>

        <div class="mt-4">
            <h3 class="font-semibold">Informasi Lain</h3>
            <dl class="mt-2">
                <dt class="text-sm text-gray-500">NIK</dt>
                <dd class="mb-2">{{ $user->nik ?? '—' }}</dd>
                <dt class="text-sm text-gray-500">Atasan</dt>
                <dd class="mb-2">{{ optional($user->leader)->name ?? '—' }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
