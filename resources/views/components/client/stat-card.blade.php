@props(['label', 'value', 'color' => 'brand'])

@php
    $colors = [
        'brand' => 'bg-brand-50 text-brand-700',
        'amber' => 'bg-amber-50 text-amber-700',
        'emerald' => 'bg-emerald-50 text-emerald-700',
        'violet' => 'bg-violet-50 text-violet-700',
    ];
    $iconBg = $colors[$color] ?? $colors['brand'];
@endphp

<div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center gap-4">
    <div class="h-11 w-11 rounded-xl {{ $iconBg }} flex items-center justify-center flex-shrink-0">
        {{ $slot }}
    </div>
    <div>
        <p class="text-2xl font-bold text-slate-900 leading-tight">{{ $value }}</p>
        <p class="text-xs font-medium text-slate-500">{{ $label }}</p>
    </div>
</div>
