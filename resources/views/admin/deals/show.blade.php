<x-admin-layout title="Deal Room">
    <a href="{{ route('admin.deals.index') }}" class="text-sm text-slate-400 hover:text-slate-600">← Voltar aos negócios</a>

    <div class="mt-4 flex items-start justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $deal->property->title }}</h1>
            <p class="text-sm text-slate-500 mt-1">R$ {{ number_format($deal->value, 0, ',', '.') }} · Criado em {{ $deal->created_at->format('d/m/Y') }}</p>
        </div>

        <form action="{{ route('admin.deals.update-status', $deal) }}" method="POST" class="flex items-center gap-2" x-data="{ status: '{{ $deal->status }}' }">
            @csrf @method('PATCH')
            <select name="status" x-model="status" class="rounded-lg border-slate-200 text-sm">
                @foreach(\App\Models\Deal::STATUSES as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <input x-show="status === 'CLOSED_LOST'" type="text" name="lost_reason" placeholder="Motivo de perda" class="rounded-lg border-slate-200 text-sm">
            <button class="rounded-lg bg-brand-700 text-white text-sm font-semibold px-4 py-2 hover:bg-brand-800">Salvar status</button>
        </form>
    </div>

    <div x-data="{ tab: 'resumo' }" class="mt-6">
        <div class="flex flex-wrap gap-1 border-b border-slate-200">
            @foreach(['resumo' => 'Resumo', 'partes' => 'Partes', 'imovel' => 'Imóvel', 'documentos' => 'Documentos', 'checklist' => 'Checklist', 'comissoes' => 'Comissões'] as $key => $label)
                <button @click="tab = '{{ $key }}'" :class="tab === '{{ $key }}' ? 'border-brand-700 text-brand-700' : 'border-transparent text-slate-500'" class="px-4 py-2 text-sm font-semibold border-b-2 -mb-px">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div x-show="tab === 'resumo'" class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-slate-100 p-5"><p class="text-xs text-slate-400 uppercase font-semibold">Status</p><p class="mt-1 font-bold text-slate-800">{{ $deal->statusLabel() }}</p></div>
            <div class="bg-white rounded-2xl border border-slate-100 p-5"><p class="text-xs text-slate-400 uppercase font-semibold">Corretor</p><p class="mt-1 font-bold text-slate-800">{{ $deal->agent->name ?? '—' }}</p></div>
            <div class="bg-white rounded-2xl border border-slate-100 p-5"><p class="text-xs text-slate-400 uppercase font-semibold">Captador</p><p class="mt-1 font-bold text-slate-800">{{ $deal->captor->name ?? '—' }}</p></div>
            @if($deal->proposal)
                <div class="bg-white rounded-2xl border border-slate-100 p-5 lg:col-span-3">
                    <p class="text-xs text-slate-400 uppercase font-semibold">Proposta aceita</p>
                    <p class="mt-1 text-slate-700">R$ {{ number_format($deal->proposal->price, 0, ',', '.') }} · versão {{ $deal->proposal->version }}</p>
                </div>
            @endif
        </div>

        <div x-show="tab === 'partes'" x-cloak class="mt-6 bg-white rounded-2xl border border-slate-100 p-5 space-y-2">
            @foreach($deal->parties as $party)
                <a href="{{ route('admin.contacts.show', $party->contact) }}" class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50">
                    <span class="text-sm text-slate-700">{{ $party->contact->displayName() }}</span>
                    <span class="text-xs font-semibold text-slate-500">{{ $party->roleLabel() }}</span>
                </a>
            @endforeach
        </div>

        <div x-show="tab === 'imovel'" x-cloak class="mt-6 bg-white rounded-2xl border border-slate-100 p-5">
            <p class="font-semibold text-slate-800">{{ $deal->property->title }}</p>
            <p class="text-sm text-slate-500 mt-1">{{ $deal->property->neighborhood }}, {{ $deal->property->city }} — {{ $deal->property->reference_code }}</p>
            <a href="{{ route('admin.properties.edit', $deal->property) }}" class="text-xs font-semibold text-brand-700 mt-2 inline-block">Editar imóvel →</a>
        </div>

        <div x-show="tab === 'documentos'" x-cloak class="mt-6 bg-white rounded-2xl border border-slate-100 p-5">
            @forelse($deal->documents as $document)
                <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                    <a href="{{ route('admin.documents.download', $document) }}" class="text-sm text-brand-700 hover:text-brand-800">{{ $document->name }}</a>
                    <form action="{{ route('admin.documents.destroy', $document) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-500 hover:text-red-700">Excluir</button>
                    </form>
                </div>
            @empty
                <p class="text-sm text-slate-400">Nenhum documento anexado.</p>
            @endforelse
        </div>

        <div x-show="tab === 'checklist'" x-cloak class="mt-6 bg-white rounded-2xl border border-slate-100 p-5 space-y-2">
            @foreach($deal->checklistItems as $item)
                <form action="{{ route('admin.deal-checklists.toggle', $item) }}" method="POST" class="flex items-center gap-3 py-1">
                    @csrf
                    <button type="submit" class="h-5 w-5 rounded border {{ $item->status === 'DONE' ? 'bg-emerald-500 border-emerald-500' : 'border-slate-300' }} flex items-center justify-center text-white text-xs">
                        @if($item->status === 'DONE') ✓ @endif
                    </button>
                    <span class="text-sm {{ $item->status === 'DONE' ? 'line-through text-slate-400' : 'text-slate-700' }}">{{ $item->title }}</span>
                </form>
            @endforeach
        </div>

        <div x-show="tab === 'comissoes'" x-cloak class="mt-6 bg-white rounded-2xl border border-slate-100 p-5">
            @forelse($deal->commissionEvent as $event)
                <div class="mb-4">
                    <p class="text-sm text-slate-600">Comissão total: <strong>R$ {{ number_format($event->total_commission_value, 2, ',', '.') }}</strong> ({{ $event->total_commission_percent }}%) — {{ $event->statusLabel() }}</p>
                    <div class="mt-2 space-y-1">
                        @foreach($event->splits as $split)
                            <div class="flex items-center justify-between text-sm py-1 border-b border-slate-50 last:border-0">
                                <span>{{ $split->dimensionLabel() }} — {{ $split->user->name ?? 'Empresa' }}</span>
                                <span class="font-semibold">R$ {{ number_format($split->value, 2, ',', '.') }} {{ $split->paid ? '✓' : '' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400">Comissão será gerada automaticamente quando o negócio for fechado como ganho.</p>
            @endforelse
            <a href="{{ route('admin.commissions.index') }}" class="text-xs font-semibold text-brand-700">Ver todas as comissões →</a>
        </div>
    </div>
</x-admin-layout>
