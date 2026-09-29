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
                    </div>
                    <span class="text-xs font-semibold rounded-full px-3 py-1 bg-brand-50 text-brand-700">{{ $lead->stageLabel() }}</span>
                </div>

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
                            <p class="text-xs text-slate-400">{{ $visit->property->title }}</p>
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

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-slate-500">Valor negociado</label>
                        <input type="number" step="0.01" name="negotiated_value" value="{{ $lead->negotiated_value }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-500">Comissão (%)</label>
                        <input type="number" step="0.01" name="commission_percent" value="{{ $lead->commission_percent }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500">Motivo de perda (se aplicável)</label>
                    <input type="text" name="lost_reason" value="{{ $lead->lost_reason }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>

                @if($lead->commission_value)
                    <p class="text-sm text-slate-600">Comissão calculada: <strong>R$ {{ number_format($lead->commission_value, 2, ',', '.') }}</strong></p>
                @endif

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
