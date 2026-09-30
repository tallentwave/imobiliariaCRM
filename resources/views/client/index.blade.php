<x-client-layout title="Minha conta">
    <div class="rounded-3xl bg-gradient-to-br from-brand-800 via-brand-700 to-brand-600 relative overflow-hidden px-6 py-8 sm:px-10 sm:py-10">
        <div class="absolute -right-10 -top-10 h-48 w-48 rounded-full bg-white/5"></div>
        <div class="absolute right-20 -bottom-8 h-32 w-32 rounded-full bg-white/5"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Olá, {{ explode(' ', auth()->user()->name)[0] }}!</h1>
                <p class="text-brand-100 text-sm mt-1.5">Aqui está um resumo da sua jornada com a gente.</p>
            </div>
            <a href="{{ route('imoveis.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white hover:bg-brand-50 text-brand-800 text-sm font-semibold px-5 py-2.5 transition shadow-lg shadow-brand-900/20">
                Explorar imóveis
            </a>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-client.stat-card label="Favoritos" :value="$stats['favorites']" color="brand">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor"><path d="M12 20.25c-.2 0-.39-.06-.55-.18C7.6 17.35 3 13.62 3 9.5 3 6.87 5.1 4.75 7.7 4.75c1.5 0 2.94.72 3.8 1.9.86-1.18 2.3-1.9 3.8-1.9 2.6 0 4.7 2.12 4.7 4.75 0 4.12-4.6 7.85-8.45 10.57-.16.12-.35.18-.55.18z"/></svg>
        </x-client.stat-card>
        <x-client.stat-card label="Visitas agendadas" :value="$stats['visits_upcoming']" color="amber">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M5.25 5.25h13.5a1.5 1.5 0 0 1 1.5 1.5v12a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-12a1.5 1.5 0 0 1 1.5-1.5Z"/></svg>
        </x-client.stat-card>
        <x-client.stat-card label="Propostas em andamento" :value="$stats['proposals_active']" color="violet">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 3.75h6M9 8.25h1.5M5.25 21h13.5a1.5 1.5 0 0 0 1.5-1.5V6.31a1.5 1.5 0 0 0-.44-1.06l-3.31-3.31a1.5 1.5 0 0 0-1.06-.44H5.25a1.5 1.5 0 0 0-1.5 1.5v16.5a1.5 1.5 0 0 0 1.5 1.5Z"/></svg>
        </x-client.stat-card>
        <x-client.stat-card label="Negócios em andamento" :value="$stats['deals_active']" color="emerald">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.625c.621 0 1.125.504 1.125 1.125V6h-.75a.75.75 0 0 1-.75-.75v-.75m-15 0v10.5m15-10.5v10.5"/></svg>
        </x-client.stat-card>
    </div>

    <div class="mt-10 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-10">

            @if($proposals->isNotEmpty() || $deals->isNotEmpty())
                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-violet-500"></span>Propostas e negócios</h2>
                    <div class="space-y-4">
                        @foreach($deals as $deal)
                            @php $progress = $deal->checklistProgress(); @endphp
                            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $deal->property?->title ?? 'Imóvel' }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">Negócio · R$ {{ number_format($deal->value, 0, ',', '.') }}</p>
                                    </div>
                                    <span @class([
                                        'text-xs font-semibold rounded-full px-3 py-1 whitespace-nowrap',
                                        'bg-emerald-50 text-emerald-700' => $deal->status === 'CLOSED_WON',
                                        'bg-red-50 text-red-600' => $deal->status === 'CLOSED_LOST',
                                        'bg-amber-50 text-amber-700' => ! in_array($deal->status, ['CLOSED_WON', 'CLOSED_LOST']),
                                    ])>{{ $deal->statusLabel() }}</span>
                                </div>

                                @if($progress['total'] > 0)
                                    <div class="mt-4">
                                        <div class="flex items-center justify-between text-xs text-slate-500 mb-1.5">
                                            <span>Etapas concluídas</span>
                                            <span>{{ $progress['done'] }}/{{ $progress['total'] }}</span>
                                        </div>
                                        <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $progress['total'] > 0 ? round($progress['done'] / $progress['total'] * 100) : 0 }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        @foreach($proposals as $proposal)
                            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow p-5 flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $proposal->property?->title ?? 'Imóvel' }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Proposta de R$ {{ number_format($proposal->price, 0, ',', '.') }}
                                        @if($proposal->version > 1) · versão {{ $proposal->version }} @endif
                                    </p>
                                </div>
                                <span @class([
                                    'text-xs font-semibold rounded-full px-3 py-1 whitespace-nowrap',
                                    'bg-emerald-50 text-emerald-700' => $proposal->status === 'ACCEPTED',
                                    'bg-red-50 text-red-600' => in_array($proposal->status, ['REJECTED', 'EXPIRED']),
                                    'bg-violet-50 text-violet-700' => in_array($proposal->status, ['PRESENTED', 'COUNTERED', 'DRAFT']),
                                ])>{{ $proposal->statusLabel() }}</span>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <section>
                <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-brand-600"></span>Meus favoritos</h2>
                @if($favorites->isEmpty())
                    <x-client.empty-state
                        title="Nenhum favorito ainda"
                        message="Explore os imóveis disponíveis e salve os que mais gostar."
                        action-label="Explorar imóveis"
                        :action-href="route('imoveis.index')"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c-.2 0-.39-.06-.55-.18C7.6 17.35 3 13.62 3 9.5 3 6.87 5.1 4.75 7.7 4.75c1.5 0 2.94.72 3.8 1.9.86-1.18 2.3-1.9 3.8-1.9 2.6 0 4.7 2.12 4.7 4.75 0 4.12-4.6 7.85-8.45 10.57-.16.12-.35.18-.55.18z"/></svg>
                    </x-client.empty-state>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        @foreach($favorites as $property)
                            <x-property-card :property="$property" />
                        @endforeach
                    </div>
                @endif
            </section>

            <section>
                <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-amber-500"></span>Minhas visitas</h2>
                @if($visits->isEmpty())
                    <x-client.empty-state title="Nenhuma visita agendada" message="Quando você agendar uma visita a um imóvel, ela aparece aqui.">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M5.25 5.25h13.5a1.5 1.5 0 0 1 1.5 1.5v12a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-12a1.5 1.5 0 0 1 1.5-1.5Z"/></svg>
                    </x-client.empty-state>
                @else
                    <div class="bg-white rounded-2xl shadow-sm divide-y divide-slate-100">
                        @foreach($visits as $visit)
                            <div class="p-4 flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-800 text-sm">{{ $visit->property?->title ?? 'Imóvel' }}</p>
                                    <p class="text-xs text-slate-500">{{ $visit->scheduled_at->translatedFormat('d/m/Y \à\s H:i') }} · com {{ $visit->agent?->name ?? 'a definir' }}</p>
                                </div>
                                <span class="text-xs font-semibold rounded-full px-3 py-1 whitespace-nowrap
                                    {{ $visit->status === 'agendada' ? 'bg-amber-50 text-amber-700' : ($visit->status === 'realizada' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500') }}">
                                    {{ ucfirst($visit->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <section>
                <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-slate-400"></span>Meus contatos enviados</h2>
                @if($leads->isEmpty())
                    <x-client.empty-state title="Nenhum contato enviado" message="Envie uma mensagem em algum imóvel para começar sua jornada.">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 10.5h7.5m-7.5 3h4.5m3.874-9.75H5.376c-1.037 0-1.876.84-1.876 1.875v11.25C3.5 17.91 4.34 18.75 5.375 18.75h5.24l3.17 3.03a.375.375 0 0 0 .631-.27v-2.76h2.958c1.036 0 1.876-.84 1.876-1.875V6.375c0-1.036-.84-1.875-1.876-1.875Z"/></svg>
                    </x-client.empty-state>
                @else
                    <div class="bg-white rounded-2xl shadow-sm divide-y divide-slate-100">
                        @foreach($leads as $lead)
                            <div class="p-4">
                                <p class="font-semibold text-slate-800 text-sm">{{ $lead->property?->title ?? 'Contato geral' }}</p>
                                <p class="text-xs text-slate-500 mt-1">{{ $lead->stageLabel() }} · enviado em {{ $lead->created_at->format('d/m/Y') }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Buscas salvas</h2>
            @if($savedSearches->isEmpty())
                <x-client.empty-state title="Nenhuma busca salva" message="Salve uma busca na página de imóveis para receber novidades por e-mail.">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                </x-client.empty-state>
            @else
                <div class="space-y-3">
                    @foreach($savedSearches as $search)
                        <div class="bg-white rounded-2xl shadow-sm p-4">
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
