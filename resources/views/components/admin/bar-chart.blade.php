@props(['title', 'labels' => [], 'values' => [], 'color' => '#2a78d6'])

@php
    $max = max(1, max($values ?: [0]));
    $chartHeight = 140;
    $barWidth = 22;
    $gap = 14;
    $n = count($values);
    $svgWidth = max(1, $n) * ($barWidth + $gap);
    $niceMax = $max <= 5 ? $max : (int) ceil($max / 5) * 5;
@endphp

<div x-data="{ tooltip: null }" class="relative">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold text-slate-700">{{ $title }}</h3>
    </div>

    <div
        x-show="tooltip"
        x-cloak
        x-text="tooltip"
        class="absolute -top-1 left-1/2 -translate-x-1/2 rounded-md bg-slate-900 text-white text-xs px-2 py-1 pointer-events-none z-10 whitespace-nowrap"
    ></div>

    <svg viewBox="0 0 {{ $svgWidth }} {{ $chartHeight + 24 }}" class="w-full" style="max-height: 180px" role="img" aria-label="{{ $title }}">
        {{-- Linhas de grade (hairline) --}}
        @foreach([0, 0.5, 1] as $fraction)
            @php $y = $chartHeight - ($chartHeight * $fraction) + 4; @endphp
            <line x1="0" y1="{{ $y }}" x2="{{ $svgWidth }}" y2="{{ $y }}" stroke="#e1e0d9" stroke-width="1" />
        @endforeach

        @foreach($values as $i => $value)
            @php
                $barHeight = $niceMax > 0 ? ($value / $niceMax) * $chartHeight : 0;
                $x = $i * ($barWidth + $gap) + ($gap / 2);
                $y = $chartHeight - $barHeight + 4;
                $isMax = $value === $max && $max > 0;
            @endphp

            <g
                @mouseenter="tooltip = '{{ $labels[$i] ?? '' }}: {{ $value }}'"
                @mouseleave="tooltip = null"
                class="cursor-default"
            >
                <rect
                    x="{{ $x }}"
                    y="{{ $y }}"
                    width="{{ $barWidth }}"
                    height="{{ max(1, $barHeight) }}"
                    rx="4"
                    fill="{{ $color }}"
                />
                @if($isMax)
                    <text x="{{ $x + $barWidth / 2 }}" y="{{ $y - 6 }}" text-anchor="middle" font-size="10" fill="#52514e" font-weight="600">{{ $value }}</text>
                @endif
                <text x="{{ $x + $barWidth / 2 }}" y="{{ $chartHeight + 18 }}" text-anchor="middle" font-size="9" fill="#898781">{{ $labels[$i] ?? '' }}</text>
            </g>
        @endforeach
    </svg>

    <table class="sr-only">
        <caption>{{ $title }}</caption>
        <thead><tr><th>Mês</th><th>Valor</th></tr></thead>
        <tbody>
            @foreach($values as $i => $value)
                <tr><td>{{ $labels[$i] ?? '' }}</td><td>{{ $value }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
