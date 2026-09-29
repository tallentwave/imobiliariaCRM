<x-admin-layout title="Match — {{ $property->title }}">
    <a href="{{ route('admin.properties.edit', $property) }}" class="text-sm text-slate-400 hover:text-slate-600">← Voltar ao imóvel</a>

    <h1 class="text-2xl font-bold text-slate-900 mt-2">{{ $property->title }}</h1>
    <p class="text-slate-500 text-sm mt-1">ALTIUS Match — compradores/oportunidades compatíveis com este imóvel</p>

    <div class="mt-6 bg-white rounded-2xl border border-slate-100 p-6">
        @forelse($matches as $item)
            <div class="flex items-center gap-4 py-3 border-b border-slate-50 last:border-0">
                <div class="h-10 w-10 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold shrink-0">
                    {{ strtoupper(substr($item['opportunity']->contact->displayName(), 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('admin.opportunities.show', $item['opportunity']) }}" class="text-sm font-semibold text-slate-800 hover:text-brand-700">
                        {{ $item['opportunity']->contact->displayName() }}
                    </a>
                    <p class="text-xs text-slate-400">
                        {{ $item['opportunity']->purposeLabel() }}
                        @if($item['opportunity']->budget_min || $item['opportunity']->budget_max)
                            · R$ {{ number_format($item['opportunity']->budget_min ?? 0, 0, ',', '.') }} – R$ {{ number_format($item['opportunity']->budget_max ?? 0, 0, ',', '.') }}
                        @endif
                    </p>
                </div>
                <div class="w-40 shrink-0">
                    <x-admin.match-score :score="$item['score']" :breakdown="$item['breakdown']" />
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-400">Nenhuma oportunidade aberta no momento para comparar.</p>
        @endforelse
    </div>
</x-admin-layout>
