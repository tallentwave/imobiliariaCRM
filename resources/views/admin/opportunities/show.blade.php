<x-admin-layout title="Oportunidade">
    <a href="{{ route('admin.opportunities.index') }}" class="text-sm text-slate-400 hover:text-slate-600">← Voltar às oportunidades</a>

    <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-slate-900">{{ $opportunity->contact->displayName() }}</h1>
                        <p class="text-sm text-slate-500 mt-1">{{ $opportunity->purposeLabel() }} · Responsável: {{ $opportunity->assignedUser->name ?? '—' }}</p>
                        <a href="{{ route('admin.contacts.show', $opportunity->contact) }}" class="text-xs font-semibold text-brand-700">Ver ficha do contato →</a>
                    </div>
                    <span class="text-xs font-semibold rounded-full px-3 py-1 bg-brand-50 text-brand-700">{{ $opportunity->statusLabel() }}</span>
                </div>

                <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                    <div class="rounded-xl bg-slate-50 py-3"><p class="text-sm font-bold">R$ {{ number_format($opportunity->budget_min ?? 0, 0, ',', '.') }}</p><p class="text-[11px] text-slate-500">Orçamento mín.</p></div>
                    <div class="rounded-xl bg-slate-50 py-3"><p class="text-sm font-bold">R$ {{ number_format($opportunity->budget_max ?? 0, 0, ',', '.') }}</p><p class="text-[11px] text-slate-500">Orçamento máx.</p></div>
                    <div class="rounded-xl bg-slate-50 py-3"><p class="text-sm font-bold">{{ $opportunity->financing_required ? 'Sim' : 'Não' }}</p><p class="text-[11px] text-slate-500">Financiamento</p></div>
                    <div class="rounded-xl bg-slate-50 py-3"><p class="text-sm font-bold">{{ $opportunity->fgts ? 'Sim' : 'Não' }}</p><p class="text-[11px] text-slate-500">Usa FGTS</p></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h2 class="font-semibold text-slate-800 mb-4">Critérios de busca (para o Match)</h2>

                <div class="flex flex-wrap gap-2">
                    @forelse($opportunity->requirements as $requirement)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 text-slate-700 text-xs px-3 py-1.5">
                            {{ ['city' => 'Cidade', 'neighborhood' => 'Bairro', 'type' => 'Tipo', 'bedrooms' => 'Dormitórios', 'area_min' => 'Área mín.', 'parking_spots' => 'Vagas', 'feature' => 'Característica'][$requirement->feature_key] ?? $requirement->feature_key }}:
                            <strong>{{ $requirement->feature_value }}</strong>
                            <form action="{{ route('admin.opportunities.requirements.destroy', [$opportunity, $requirement]) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-slate-400 hover:text-red-500">✕</button>
                            </form>
                        </span>
                    @empty
                        <p class="text-sm text-slate-400">Nenhum critério definido ainda — o Match usará apenas orçamento e finalidade.</p>
                    @endforelse
                </div>

                <details class="mt-4">
                    <summary class="text-sm font-semibold text-brand-700 cursor-pointer">+ Adicionar critério</summary>
                    <form action="{{ route('admin.opportunities.requirements.store', $opportunity) }}" method="POST" class="mt-3 flex flex-wrap items-end gap-2 bg-slate-50 rounded-xl p-4">
                        @csrf
                        <div>
                            <label class="text-xs font-semibold text-slate-500">Critério</label>
                            <select name="feature_key" class="mt-1 rounded-lg border-slate-200 text-sm">
                                <option value="city">Cidade</option>
                                <option value="neighborhood">Bairro</option>
                                <option value="type">Tipo de imóvel</option>
                                <option value="bedrooms">Dormitórios (mín.)</option>
                                <option value="area_min">Área mín. (m²)</option>
                                <option value="parking_spots">Vagas (mín.)</option>
                                <option value="feature">Característica</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500">Valor</label>
                            <input type="text" name="feature_value" required class="mt-1 rounded-lg border-slate-200 text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500">Prioridade</label>
                            <select name="priority" class="mt-1 rounded-lg border-slate-200 text-sm">
                                <option value="OBRIGATORIO">Obrigatório</option>
                                <option value="DESEJAVEL" selected>Desejável</option>
                                <option value="INDIFERENTE">Indiferente</option>
                                <option value="EXCLUDENTE">Excludente</option>
                            </select>
                        </div>
                        <button class="rounded-lg bg-brand-700 text-white text-sm font-semibold px-4 py-2 hover:bg-brand-800">Adicionar</button>
                    </form>
                </details>
            </div>

            @if($matches->isNotEmpty())
                <div class="bg-white rounded-2xl border border-slate-100 p-6">
                    <h2 class="font-semibold text-slate-800 mb-1">ALTIUS Match — imóveis sugeridos</h2>
                    <p class="text-xs text-slate-400 mb-4">Ranqueado por preço, localização, tipologia, dormitórios, área, vagas e características.</p>
                    <div class="space-y-4">
                        @foreach($matches as $item)
                            <div class="flex items-center gap-4 rounded-xl border border-slate-100 p-3">
                                <a href="{{ route('imoveis.show', $item['property']) }}" target="_blank" class="shrink-0 h-14 w-14 rounded-lg bg-slate-100 overflow-hidden">
                                    @if($cover = $item['property']->images->first())
                                        <img src="{{ $cover->url() }}" class="h-full w-full object-cover" alt="">
                                    @endif
                                </a>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $item['property']->title }}</p>
                                    <p class="text-xs text-slate-400">{{ $item['property']->neighborhood }}, {{ $item['property']->city }} · R$ {{ number_format($item['property']->price ?? 0, 0, ',', '.') }}</p>
                                </div>
                                <div class="w-40 shrink-0">
                                    <x-admin.match-score :score="$item['score']" :breakdown="$item['breakdown']" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h2 class="font-semibold text-slate-800 mb-4">Propostas</h2>

                @forelse($opportunity->proposals->whereNull('parent_proposal_id') as $proposal)
                    @include('admin.opportunities._proposal-thread', ['proposal' => $proposal])
                @empty
                    <p class="text-sm text-slate-400">Nenhuma proposta registrada ainda.</p>
                @endforelse

                <details class="mt-4">
                    <summary class="text-sm font-semibold text-brand-700 cursor-pointer">+ Nova proposta</summary>
                    <form action="{{ route('admin.proposals.store', $opportunity) }}" method="POST" class="mt-3 space-y-3 bg-slate-50 rounded-xl p-4">
                        @csrf
                        <div>
                            <label class="text-xs font-semibold text-slate-500">Imóvel</label>
                            <select name="property_id" required class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                                @foreach(\App\Models\Property::orderBy('title')->get() as $property)
                                    <option value="{{ $property->id }}">{{ $property->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-slate-500">Preço proposto</label>
                                <input type="number" step="0.01" name="price" required class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-500">Entrada</label>
                                <input type="number" step="0.01" name="down_payment" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500">Condições</label>
                            <textarea name="conditions" rows="2" class="mt-1 w-full rounded-lg border-slate-200 text-sm"></textarea>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500">Válida até</label>
                            <input type="datetime-local" name="valid_until" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                        </div>
                        <button class="rounded-lg bg-brand-700 text-white text-sm font-semibold px-4 py-2 hover:bg-brand-800">Registrar proposta</button>
                    </form>
                </details>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h2 class="font-semibold text-slate-800 mb-4">Visitas</h2>
                @forelse($opportunity->visits as $visit)
                    <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                        <p class="text-sm text-slate-700">{{ $visit->property->title }} · {{ $visit->scheduled_at->format('d/m/Y H:i') }}</p>
                        <span class="text-xs font-semibold text-slate-500">{{ ucfirst($visit->status) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Nenhuma visita registrada.</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <form action="{{ route('admin.opportunities.update', $opportunity) }}" method="POST" class="bg-white rounded-2xl border border-slate-100 p-6 space-y-4">
                @csrf @method('PUT')
                <h2 class="font-semibold text-slate-800">Gestão</h2>
                <div>
                    <label class="text-xs font-semibold text-slate-500">Status</label>
                    <select name="status" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                        <option value="OPEN" @selected($opportunity->status === 'OPEN')>Aberta</option>
                        <option value="WON" @selected($opportunity->status === 'WON')>Ganha</option>
                        <option value="LOST" @selected($opportunity->status === 'LOST')>Perdida</option>
                    </select>
                </div>
                <button class="w-full rounded-lg bg-brand-700 text-white font-semibold py-2.5 hover:bg-brand-800">Salvar</button>
            </form>

            @if($opportunity->deals->isNotEmpty())
                <div class="bg-white rounded-2xl border border-slate-100 p-6">
                    <h2 class="font-semibold text-slate-800 mb-3">Negócios (Deal Room)</h2>
                    @foreach($opportunity->deals as $deal)
                        <a href="{{ route('admin.deals.show', $deal) }}" class="block text-sm text-brand-700 hover:text-brand-800 py-1">
                            {{ $deal->property->title }} — {{ $deal->statusLabel() }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
