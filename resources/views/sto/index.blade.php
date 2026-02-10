<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl sm:text-2xl text-gray-800 leading-tight">
            Bagan STO
        </h2>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">
            @if(Auth::user()->position && Auth::user()->position->name === 'Superuser')
                Hirarki semua user
            @else
                Hanya data Anda dan bawahan Anda
            @endif
        </p>
    </x-slot>

    <div class="py-4 sm:py-8 bg-gradient-to-br from-slate-50 via-blue-50/50 to-indigo-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            @if(empty($tree))
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 text-center">
                    <p class="text-gray-600">Tidak ada data hirarki.</p>
                </div>
            @else
                <div class="bg-white/80 backdrop-blur rounded-2xl shadow-xl border border-gray-200/80 p-4 sm:p-8 overflow-x-auto">
                    <div class="bagan-sto min-w-max mx-auto py-4">
                        @foreach($tree as $node)
                            @include('sto.partials.bagan-node', ['node' => $node])
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
