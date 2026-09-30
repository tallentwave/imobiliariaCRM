<x-admin-layout title="Contato: {{ $contact->displayName() }}">
    <a href="{{ route('admin.contacts.index') }}" class="text-sm text-slate-400 hover:text-slate-600">← Voltar aos contatos</a>

    <div class="mt-4 flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $contact->displayName() }}</h1>
            <p class="text-sm text-slate-500 mt-1">
                {{ $contact->mobile }} @if($contact->email) · {{ $contact->email }} @endif
                @if($contact->cpf) · CPF {{ $contact->cpf }} @endif
            </p>
        </div>
        <a href="{{ route('admin.contacts.edit', $contact) }}" class="rounded-lg bg-slate-800 text-white text-sm font-semibold px-4 py-2 hover:bg-slate-900">
            Editar
        </a>
    </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h2 class="font-semibold text-slate-800 mb-4">Timeline — Leads e Oportunidades</h2>
                <div class="space-y-3">
                    @forelse($contact->leads as $lead)
                        <a href="{{ route('admin.leads.show', $lead) }}" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-slate-100">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $lead->property?->title ?? 'Contato geral' }}</p>
                                <p class="text-xs text-slate-400">Lead criado em {{ $lead->created_at->format('d/m/Y') }} · origem: {{ $lead->source }}</p>
                            </div>
                            <span class="text-xs font-semibold text-slate-500">{{ $lead->stageLabel() }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-slate-400">Nenhum lead registrado.</p>
                    @endforelse

                    @foreach($contact->opportunities as $opp)
                        <a href="{{ route('admin.opportunities.show', $opp) }}" class="flex items-center justify-between p-3 rounded-xl bg-emerald-50 hover:bg-emerald-100">
                            <div>
                                <p class="text-sm font-semibold text-emerald-900">Oportunidade · {{ $opp->purposeLabel() }}</p>
                                <p class="text-xs text-emerald-700">{{ $opp->proposals->count() }} proposta(s)</p>
                            </div>
                            <span class="text-xs font-semibold text-emerald-700">{{ $opp->statusLabel() }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            @if($contact->ownedProperties->isNotEmpty())
                <div class="bg-white rounded-2xl border border-slate-100 p-6">
                    <h2 class="font-semibold text-slate-800 mb-4">Imóveis (proprietário)</h2>
                    <div class="space-y-2">
                        @foreach($contact->ownedProperties as $property)
                            <a href="{{ route('admin.properties.edit', $property) }}" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-slate-100">
                                <p class="text-sm font-semibold text-slate-800">{{ $property->title }}</p>
                                <span class="text-xs text-slate-500">{{ $property->pivot->ownership_percentage ?? 100 }}% · {{ $property->pivot->authorization_status }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h2 class="font-semibold text-slate-800 mb-4">Relacionamentos</h2>
                @forelse($contact->relationships as $rel)
                    <p class="text-sm text-slate-600">{{ ucfirst(strtolower($rel->relationship_type)) }}: {{ $rel->relatedContact?->displayName() ?? '—' }}</p>
                @empty
                    <p class="text-sm text-slate-400">Nenhum relacionamento cadastrado.</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h2 class="font-semibold text-slate-800 mb-3">Dados</h2>
                <dl class="text-sm space-y-2">
                    <div><dt class="text-xs text-slate-400">Tipo</dt><dd class="text-slate-700">{{ $contact->type === 'COMPANY' ? 'Empresa' : 'Pessoa física' }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Responsável</dt><dd class="text-slate-700">{{ $contact->owner?->name ?? '—' }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Status</dt><dd class="text-slate-700">{{ $contact->status }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Cadastrado em</dt><dd class="text-slate-700">{{ $contact->created_at->format('d/m/Y') }}</dd></div>
                </dl>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h2 class="font-semibold text-slate-800 mb-3">Consentimentos (LGPD)</h2>
                @forelse($contact->consents as $consent)
                    <div class="flex items-center justify-between text-sm py-1">
                        <span class="text-slate-600">{{ $consent->purpose }}</span>
                        <span class="text-xs {{ $consent->isActive() ? 'text-emerald-600' : 'text-slate-400' }}">
                            {{ $consent->isActive() ? 'Ativo' : 'Revogado' }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Nenhum consentimento registrado.</p>
                @endforelse
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h2 class="font-semibold text-slate-800 mb-3">Documentos</h2>
                @forelse($contact->documents as $document)
                    <a href="{{ route('admin.documents.download', $document) }}" class="block text-sm text-brand-700 hover:text-brand-800 py-1">{{ $document->name }}</a>
                @empty
                    <p class="text-sm text-slate-400">Nenhum documento anexado.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
