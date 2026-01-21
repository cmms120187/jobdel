@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-semibold mb-4">Bawahan Saya</h1>

    <p class="text-sm text-gray-600 mb-6">Daftar bawahan termasuk bawahan bawahan (rekursif). Klik nama untuk melihat profil.</p>

    @if($subordinates->isEmpty())
        <div class="bg-white p-4 rounded shadow">Anda tidak memiliki bawahan.</div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($subordinates as $sub)
                <a href="{{ route('leader.subordinates.show', $sub->id) }}" class="block bg-white hover:shadow-lg transition-shadow p-4 rounded">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-yellow-400 to-pink-500 text-white font-bold flex items-center justify-center me-4">{{ strtoupper(substr($sub->name,0,1)) }}</div>
                        <div>
                            <div class="font-semibold">{{ $sub->name }}</div>
                            <div class="text-sm text-gray-500">{{ optional($sub->position)->name ?? '—' }}</div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
