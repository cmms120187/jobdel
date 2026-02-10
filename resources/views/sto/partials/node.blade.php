@php
    $user = $node['user'];
    $children = $node['children'] ?? [];
@endphp
<li class="sto-node {{ $level > 0 ? 'mt-2' : '' }}">
    <div class="flex items-center gap-3 py-2 px-3 rounded-lg {{ $level === 0 ? 'bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-100' : 'bg-gray-50 border border-gray-100' }}">
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-400 to-pink-500 flex items-center justify-center text-white font-bold flex-shrink-0">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div class="min-w-0 flex-1">
            <div class="font-semibold text-gray-800">{{ $user->name }}</div>
            <div class="text-sm text-gray-500">
                {{ optional($user->position)->name ?? '—' }}
                @if($user->nik)
                    <span class="ml-2">NIK: {{ $user->nik }}</span>
                @endif
            </div>
        </div>
    </div>
    @if(count($children) > 0)
        <ul class="space-y-0 mt-1 sto-tree border-l-2 border-purple-200 ml-4 pl-2">
            @foreach($children as $childNode)
                @include('sto.partials.node', ['node' => $childNode, 'level' => $level + 1])
            @endforeach
        </ul>
    @endif
</li>
