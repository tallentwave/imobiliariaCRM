@props(['label', 'value', 'sub' => null, 'color' => 'brand'])

@php
    $colors = [
        'brand' => 'bg-brand-50 text-brand-700',
        'amber' => 'bg-amber-50 text-amber-700',
        'red' => 'bg-red-50 text-red-600',
        'emerald' => 'bg-emerald-50 text-emerald-700',
        'violet' => 'bg-violet-50 text-violet-700',
    ];
    $iconBg = $colors[$color] ?? $colors['brand'];
@endphp

<div class="bg-white rounded-2xl border border-slate-100 p-5">
    <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl {{ $iconBg }} flex items-center justify-center flex-shrink-0">
            {{ $slot }}
        </div>
        <p class="text-xs font-semibold text-slate-500 uppercase leading-tight">{{ $label }}</p>
    </div>
    <p class="mt-3 text-2xl font-bold text-slate-900">{{ $value }}</p>
    @if($sub)
        <p class="text-xs text-slate-400 mt-1">{{ $sub }}</p>
    @endif
</div>
