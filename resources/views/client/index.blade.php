<x-client-layout title="Minha conta">
    <h1 class="text-2xl font-bold text-slate-900">Olá, {{ auth()->user()->name }}!</h1>
    <p class="text-slate-500 text-sm mt-1">Acompanhe seus favoritos, buscas salvas e visitas agendadas.</p>

    <div class="mt-10 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-10">
            <section>
                <h2 class="text-lg font-bold text-slate-900 mb-4">Meus favoritos</h2>
                @if($favorites->isEmpty())
                    <p class="text-sm text-slate-500 bg-white rounded-xl border border-slate-100 p-6">
                        Você ainda não favoritou nenhum imóvel. <a href="{{ route('imoveis.index') }}" class="text-brand-700 font-semibold">Explore os imóveis disponíveis</a>.
                    </p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        @foreach($favorites as $property)
                            <x-property-card :property="$property" />
                        @endforeach
                    </div>
                @endif
            </section>

            <section>
                <h2 class="text-lg font-bold text-slate-900 mb-4">Minhas visitas</h2>
                @if($visits->isEmpty())
                    <p class="text-sm text-slate-500 bg-white rounded-xl border border-slate-100 p-6">Nenhuma visita agendada.</p>
                @else
                    <div class="bg-white rounded-xl border border-slate-100 divide-y divide-slate-100">
                        @foreach($visits as $visit)
                            <div class="p-4 flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-slate-800 text-sm">{{ $visit->property->title }}</p>
                                    <p class="text-xs text-slate-500">{{ $visit->scheduled_at->translatedFormat('d/m/Y \à\s H:i') }} · com {{ $visit->agent->name ?? 'a definir' }}</p>
                                </div>
                                <span class="text-xs font-semibold rounded-full px-3 py-1
                                    {{ $visit->status === 'agendada' ? 'bg-amber-50 text-amber-700' : ($visit->status === 'realizada' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500') }}">
                                    {{ ucfirst($visit->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <section>
                <h2 class="text-lg font-bold text-slate-900 mb-4">Meus contatos enviados</h2>
                @if($leads->isEmpty())
                    <p class="text-sm text-slate-500 bg-white rounded-xl border border-slate-100 p-6">Você ainda não enviou nenhum contato.</p>
                @else
                    <div class="bg-white rounded-xl border border-slate-100 divide-y divide-slate-100">
                        @foreach($leads as $lead)
                            <div class="p-4">
                                <p class="font-semibold text-slate-800 text-sm">{{ $lead->property->title ?? 'Contato geral' }}</p>
                                <p class="text-xs text-slate-500 mt-1">{{ $lead->stageLabel() }} · enviado em {{ $lead->created_at->format('d/m/Y') }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-4">Buscas salvas</h2>
            @if($savedSearches->isEmpty())
                <p class="text-sm text-slate-500 bg-white rounded-xl border border-slate-100 p-6">
                    Salve uma busca na página de imóveis para receber novidades por e-mail.
                </p>
            @else
                <div class="space-y-3">
                    @foreach($savedSearches as $search)
                        <div class="bg-white rounded-xl border border-slate-100 p-4">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-sm text-slate-800">{{ $search->name }}</p>
                                <form action="{{ route('buscas-salvas.destroy', $search) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-500 hover:text-red-700">Remover</button>
                                </form>
                            </div>
                            <div class="mt-2 flex flex-wrap gap-1">
                                @foreach($search->filters as $key => $value)
                                    <span class="text-xs bg-slate-100 rounded-full px-2 py-0.5 text-slate-600">{{ $key }}: {{ $value }}</span>
                                @endforeach
                            </div>
                            <a href="{{ route('imoveis.index', $search->filters) }}" class="mt-2 inline-block text-xs font-semibold text-brand-700">Ver resultados →</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-client-layout>
