<x-admin-layout title="Lead: {{ $lead->name }}">
    <a href="{{ route('admin.leads.index') }}" class="text-sm text-slate-400 hover:text-slate-600">← Voltar ao funil</a>

    <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-slate-900">{{ $lead->name }}</h1>
                        <p class="text-sm text-slate-500 mt-1">{{ $lead->phone }} @if($lead->email) · {{ $lead->email }} @endif</p>
                        <p class="text-xs text-slate-400 mt-1">Origem: {{ ucfirst($lead->source) }} · Recebido em {{ $lead->created_at->format('d/m/Y H:i') }}</p>
                        @if($lead->contact)
                            <a href="{{ route('admin.contacts.show', $lead->contact) }}" class="text-xs font-semibold text-brand-700">Ver ficha do contato (360°) →</a>
                        @endif
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-semibold rounded-full px-3 py-1 bg-brand-50 text-brand-700">{{ $lead->stageLabel() }}</span>
                        @if($lead->isSlaOverdue())
                            <p class="text-xs font-semibold text-red-600 mt-1">SLA estourado</p>
                        @endif
                        @if($lead->escalated_at)
                            <p class="text-xs font-semibold text-amber-600 mt-1">Escalonado em {{ $lead->escalated_at->format('d/m H:i') }}</p>
                        @endif
                        @if($lead->redistributed_at)
                            <p class="text-xs font-semibold text-purple-600 mt-1">Redistribuído automaticamente</p>
                        @endif
                    </div>
                </div>

                @if($lead->opportunities->isNotEmpty())
                    <div class="mt-4 rounded-xl bg-emerald-50 p-3">
                        <p class="text-xs font-semibold text-emerald-700">Oportunidade(s) geradas</p>
                        @foreach($lead->opportunities as $opp)
                            <a href="{{ route('admin.opportunities.show', $opp) }}" class="block text-sm text-emerald-800 hover:underline">
                                {{ $opp->purposeLabel() }} · {{ $opp->statusLabel() }}
                            </a>
                        @endforeach
                    </div>
                @elseif(!in_array($lead->stage, \App\Models\Lead::LOST_STAGES))
                    <form action="{{ route('admin.leads.convert', $lead) }}" method="POST" class="mt-4">
                        @csrf
                        <button class="text-sm font-semibold text-brand-700 hover:text-brand-800">+ Converter em oportunidade →</button>
                    </form>
                @endif

                @if($lead->property)
                    <a href="{{ route('admin.properties.edit', $lead->property) }}" class="mt-4 flex items-center gap-3 rounded-xl bg-slate-50 p-3 hover:bg-slate-100">
                        <div class="text-sm">
                            <p class="font-semibold text-slate-800">{{ $lead->property->title }}</p>
                            <p class="text-xs text-slate-500">{{ $lead->property->reference_code }}</p>
                        </div>
                    </a>
                @endif

                @if($lead->message)
                    <div class="mt-4">
                        <p class="text-xs font-semibold text-slate-500">Mensagem</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $lead->message }}</p>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h2 class="font-semibold text-slate-800 mb-4">Visitas</h2>
                @forelse($lead->visits as $visit)
                    <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                        <div>
                            <p class="text-sm text-slate-700">{{ $visit->scheduled_at->format('d/m/Y H:i') }}</p>
                            <p class="text-xs text-slate-400">{{ $visit->property?->title ?? 'Imóvel removido' }}</p>
                        </div>
                        <span class="text-xs font-semibold text-slate-500">{{ ucfirst($visit->status) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Nenhuma visita registrada.</p>
                @endforelse

                <a href="{{ route('admin.visits.create', ['lead_id' => $lead->id, 'property_id' => $lead->property_id]) }}" class="inline-block mt-4 text-sm font-semibold text-brand-700">
                    + Agendar visita
                </a>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h2 class="font-semibold text-slate-800 mb-4">Documentos e contratos</h2>

                @forelse($lead->documents as $document)
                    <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                        <div>
                            <a href="{{ route('admin.documents.download', $document) }}" class="text-sm font-medium text-brand-700 hover:text-brand-800">
                                {{ $document->name }}
                            </a>
                            <p class="text-xs text-slate-400">
                                {{ $document->sizeForHumans() }} · enviado por {{ $document->uploadedBy?->name ?? '—' }}
                                em {{ $document->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                        <form action="{{ route('admin.documents.destroy', $document) }}" method="POST" onsubmit="return confirm('Remover este documento?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:text-red-700">Excluir</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Nenhum documento anexado.</p>
                @endforelse

                <form action="{{ route('admin.leads.documents.store', $lead) }}" method="POST" enctype="multipart/form-data" class="mt-4 flex items-center gap-3">
                    @csrf
                    <input type="file" name="file" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="text-xs flex-1">
                    <button type="submit" class="rounded-lg bg-slate-800 text-white text-xs font-semibold px-3 py-2 hover:bg-slate-900 whitespace-nowrap">
                        Enviar
                    </button>
                </form>
                <p class="text-[11px] text-slate-400 mt-1">PDF, Word ou imagem, até 10 MB.</p>
            </div>
        </div>

        <div class="space-y-6">
            <form action="{{ route('admin.leads.update', $lead) }}" method="POST" class="bg-white rounded-2xl border border-slate-100 p-6 space-y-4">
                @csrf @method('PUT')
                <h2 class="font-semibold text-slate-800">Gestão do lead</h2>

                <div>
                    <label class="text-xs font-semibold text-slate-500">Etapa</label>
                    <select name="stage" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                        @foreach(\App\Models\Lead::STAGES as $key => $label)
                            <option value="{{ $key }}" @selected($lead->stage === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500">Corretor responsável</label>
                    <select name="agent_id" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                        <option value="">Sem corretor</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" @selected($lead->agent_id === $agent->id)>{{ $agent->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500">Temperatura</label>
                    <select name="temperature" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                        @foreach(['COLD' => 'Fria', 'WARM' => 'Morna', 'HOT' => 'Quente'] as $key => $label)
                            <option value="{{ $key }}" @selected($lead->temperature === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500">Motivo de perda (obrigatório para etapas de saída)</label>
                    <input type="text" name="lost_reason" value="{{ $lead->lost_reason }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>

                <button type="submit" class="w-full rounded-lg bg-brand-700 text-white font-semibold py-2.5 hover:bg-brand-800">
                    Salvar alterações
                </button>
            </form>

            <form action="{{ route('admin.leads.destroy', $lead) }}" method="POST" onsubmit="return confirm('Excluir este lead definitivamente?')">
                @csrf @method('DELETE')
                <button class="w-full rounded-lg border border-red-200 text-red-600 font-semibold py-2.5 hover:bg-red-50">
                    Excluir lead
                </button>
            </form>
        </div>
    </div>
</x-admin-layout>
