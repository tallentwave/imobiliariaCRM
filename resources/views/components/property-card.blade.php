@props(['property'])

@php
    $cover = $property->images->firstWhere('is_cover', true) ?? $property->images->first();
@endphp

<a href="{{ route('imoveis.show', $property) }}" class="group block rounded-2xl border border-slate-100 bg-white shadow-sm hover:shadow-lg transition overflow-hidden">
    <div class="relative aspect-[4/3] bg-slate-100 overflow-hidden">
        @if($cover)
            <img src="{{ $cover->url() }}" alt="{{ $property->title }}" class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
        @else
            <div class="flex items-center justify-center h-full text-slate-400 text-sm">Sem foto</div>
        @endif

        <div class="absolute top-3 left-3 flex gap-2">
            <span class="rounded-full bg-white/95 px-3 py-1 text-xs font-semibold text-slate-700 shadow">
                {{ $property->purposeLabel() }}
            </span>
            @if($property->featured)
                <span class="rounded-full bg-accent-500 px-3 py-1 text-xs font-semibold text-white shadow">Destaque</span>
            @endif
        </div>

        @auth
            <form action="{{ route('favoritos.toggle', $property) }}" method="POST" class="absolute top-3 right-3" onclick="event.stopPropagation()">
                @csrf
                @php
                    $favorited = auth()->user()->favorites()->where('property_id', $property->id)->exists();
                @endphp
                <button type="submit" class="rounded-full bg-white/95 p-2 shadow hover:bg-white" aria-label="Favoritar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5 {{ $favorited ? 'fill-red-500 stroke-red-500' : 'fill-none stroke-slate-500' }}" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c-.2 0-.39-.06-.55-.18C7.6 17.35 3 13.62 3 9.5 3 6.87 5.1 4.75 7.7 4.75c1.5 0 2.94.72 3.8 1.9.86-1.18 2.3-1.9 3.8-1.9 2.6 0 4.7 2.12 4.7 4.75 0 4.12-4.6 7.85-8.45 10.57-.16.12-.35.18-.55.18z"/>
                    </svg>
                </button>
            </form>
        @endauth
    </div>

    <div class="p-4">
        <p class="text-lg font-bold text-slate-900">
            @if($property->price)
                R$ {{ number_format($property->price, 0, ',', '.') }}{{ $property->purpose === 'aluguel' ? '/mês' : '' }}
            @else
                Consulte o valor
            @endif
        </p>
        <h3 class="mt-1 text-sm font-semibold text-slate-700 line-clamp-2">{{ $property->title }}</h3>
        <p class="mt-1 text-sm text-slate-500">{{ $property->neighborhood }}, {{ $property->city }}</p>

        <div class="mt-3 flex items-center gap-4 text-xs text-slate-500">
            @if($property->bedrooms)
                <span>{{ $property->bedrooms }} quartos</span>
            @endif
            @if($property->parking_spots)
                <span>{{ $property->parking_spots }} vagas</span>
            @endif
            @if($property->area_total)
                <span>{{ (int) $property->area_total }} m²</span>
            @endif
        </div>
    </div>
</a>
