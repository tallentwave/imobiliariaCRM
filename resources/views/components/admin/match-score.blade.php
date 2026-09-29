@props(['score', 'breakdown' => []])

@php
    $color = match(true) {
        $score >= 70 => ['bar' => 'bg-emerald-500', 'text' => 'text-emerald-700', 'bg' => 'bg-emerald-50'],
        $score >= 40 => ['bar' => 'bg-amber-500', 'text' => 'text-amber-700', 'bg' => 'bg-amber-50'],
        default => ['bar' => 'bg-red-400', 'text' => 'text-red-600', 'bg' => 'bg-red-50'],
    };
@endphp

<div x-data="{ open: false }" class="relative">
    <button type="button" @click="open = !open" class="w-full text-left">
        <div class="flex items-center justify-between mb-1">
            <span class="text-xs font-semibold {{ $color['text'] }}">{{ $score }}% de match</span>
            <span class="text-[10px] text-slate-400">detalhes ▾</span>
        </div>
        <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
            <div class="h-full {{ $color['bar'] }}" style="width: {{ $score }}%"></div>
        </div>
    </button>

    <div x-show="open" x-cloak @click.outside="open = false" class="absolute z-20 mt-2 w-64 rounded-xl border border-slate-100 bg-white shadow-lg p-3 space-y-1.5">
        @foreach($breakdown as $key => $item)
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-500">{{ ['price' => 'Preço', 'location' => 'Localização', 'type' => 'Tipologia', 'bedrooms' => 'Dormitórios', 'area' => 'Área', 'parking' => 'Vagas', 'features' => 'Características', 'preferences' => 'Preferências'][$key] ?? $key }}</span>
                <span class="text-slate-700 font-medium">{{ round($item['earned']) }}/{{ $item['weight'] }} · {{ $item['reason'] }}</span>
            </div>
        @endforeach
    </div>
</div>
