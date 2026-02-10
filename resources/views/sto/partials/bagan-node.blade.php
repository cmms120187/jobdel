@php
    $user = $node['user'];
    $children = $node['children'] ?? [];
    $isRoot = !isset($level) || $level === 0;
@endphp
<div class="bagan-node flex flex-col items-center {{ $isRoot ? '' : 'flex-1 min-w-0' }}">
    {{-- Kartu pegawai --}}
    <div class="bagan-card rounded-xl border-2 shadow-lg overflow-hidden transition-all hover:shadow-xl hover:scale-[1.02] {{ $isRoot ? 'bg-gradient-to-br from-indigo-500 to-purple-600 border-indigo-400 ring-4 ring-indigo-200/50' : 'bg-white border-purple-200 hover:border-purple-400' }} min-w-[180px] max-w-[220px]">
        <div class="p-4 {{ $isRoot ? 'text-white' : 'text-gray-800' }}">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg flex-shrink-0 {{ $isRoot ? 'bg-white/25 text-white' : 'bg-gradient-to-br from-amber-400 to-pink-500 text-white' }}">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="font-bold text-sm leading-tight truncate" title="{{ $user->name }}">{{ $user->name }}</div>
                    <div class="text-xs mt-0.5 {{ $isRoot ? 'text-white/90' : 'text-gray-500' }}">
                        {{ optional($user->position)->name ?? '—' }}
                    </div>
                    @if($user->nik)
                        <div class="text-xs mt-0.5 {{ $isRoot ? 'text-white/80' : 'text-gray-400' }}">NIK: {{ $user->nik }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(count($children) > 0)
        {{-- Garis vertikal ke bawah --}}
        <div class="w-0.5 h-7 bg-gradient-to-b from-indigo-500 to-purple-500 mx-auto rounded-full"></div>
        {{-- Garis horizontal (menghubungkan ke semua anak) --}}
        <div class="h-0.5 bg-gradient-to-r from-indigo-400 via-purple-500 to-indigo-400 mx-auto rounded-full" style="width: {{ max(80, count($children) * 100) }}px;"></div>
        {{-- Baris anak --}}
        <div class="flex flex-row items-start justify-center gap-2 sm:gap-6 pt-0">
            @foreach($children as $childNode)
                <div class="flex flex-col items-center flex-1 min-w-0" style="min-width: 160px;">
                    <div class="w-0.5 h-6 bg-gradient-to-b from-purple-500 to-purple-400 mx-auto rounded-full"></div>
                    @include('sto.partials.bagan-node', ['node' => $childNode, 'level' => ($level ?? 0) + 1])
                </div>
            @endforeach
        </div>
    @endif
</div>
