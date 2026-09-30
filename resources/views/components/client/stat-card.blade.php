@props(['label', 'value', 'color' => 'brand'])

@php
    $colors = [
        'brand' => 'bg-gradient-to-br from-brand-500 to-brand-700 text-white',
        'amber' => 'bg-gradient-to-br from-amber-400 to-amber-600 text-white',
        'emerald' => 'bg-gradient-to-br from-emerald-400 to-emerald-600 text-white',
        'violet' => 'bg-gradient-to-br from-violet-400 to-violet-600 text-white',
    ];
    $iconBg = $colors[$color] ?? $colors['brand'];
@endphp

<div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow p-5 flex items-center gap-4">
    <div class="h-12 w-12 rounded-2xl {{ $iconBg }} flex items-center justify-center flex-shrink-0 shadow-sm">
        {{ $slot }}
    </div>
    <div>
        <p class="text-2xl font-bold text-slate-900 leading-tight">{{ $value }}</p>
        <p class="text-xs font-medium text-slate-500">{{ $label }}</p>
    </div>
</div>
