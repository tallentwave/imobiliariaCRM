<div class="rounded-xl border border-slate-100 p-4 mb-3 {{ $proposal->status === 'ACCEPTED' ? 'bg-emerald-50 border-emerald-200' : 'bg-white' }}">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-semibold text-slate-800">
                Versão {{ $proposal->version }} · {{ $proposal->property?->title ?? 'Imóvel removido' }}
            </p>
            <p class="text-lg font-bold text-slate-900 mt-1">R$ {{ number_format($proposal->price, 0, ',', '.') }}</p>
            @if($proposal->down_payment)
                <p class="text-xs text-slate-500">Entrada: R$ {{ number_format($proposal->down_payment, 0, ',', '.') }}</p>
            @endif
            @if($proposal->conditions)
                <p class="text-xs text-slate-500 mt-1">{{ $proposal->conditions }}</p>
            @endif
            @if($proposal->valid_until)
                <p class="text-xs text-slate-400 mt-1">Válida até {{ $proposal->valid_until->format('d/m/Y H:i') }} {{ $proposal->isExpired() ? '(expirada)' : '' }}</p>
            @endif
        </div>
        <span class="text-xs font-semibold rounded-full px-2.5 py-1
            {{ match($proposal->status) { 'ACCEPTED' => 'bg-emerald-100 text-emerald-700', 'REJECTED' => 'bg-red-50 text-red-600', 'COUNTERED' => 'bg-amber-50 text-amber-700', default => 'bg-slate-100 text-slate-600' } }}">
            {{ $proposal->statusLabel() }}
        </span>
    </div>

    @if(in_array($proposal->status, ['PRESENTED']))
        <div class="mt-3 flex flex-wrap items-center gap-2">
            <form action="{{ route('admin.proposals.accept', $proposal) }}" method="POST">
                @csrf
                <button class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">✓ Aceitar</button>
            </form>
            <form action="{{ route('admin.proposals.reject', $proposal) }}" method="POST">
                @csrf
                <button class="text-xs font-semibold text-red-600 hover:text-red-700">✕ Rejeitar</button>
            </form>
            <details class="inline-block">
                <summary class="text-xs font-semibold text-amber-700 cursor-pointer">↺ Contraproposta</summary>
                <form action="{{ route('admin.proposals.counter', $proposal) }}" method="POST" class="mt-2 space-y-2 bg-slate-50 p-3 rounded-lg w-64">
                    @csrf
                    <input type="hidden" name="property_id" value="{{ $proposal->property_id }}">
                    <input type="number" step="0.01" name="price" placeholder="Novo valor" required class="w-full rounded-lg border-slate-200 text-xs">
                    <textarea name="conditions" placeholder="Condições" rows="2" class="w-full rounded-lg border-slate-200 text-xs"></textarea>
                    <button class="w-full rounded-lg bg-amber-600 text-white text-xs font-semibold py-1.5">Enviar contraproposta</button>
                </form>
            </details>
        </div>
    @endif

    @if($proposal->status === 'ACCEPTED')
        <form action="{{ route('admin.proposals.create-deal', $proposal) }}" method="POST" class="mt-3">
            @csrf
            <button class="text-xs font-semibold text-brand-700 hover:text-brand-800">→ Criar negócio (Deal Room)</button>
        </form>
    @endif

    @foreach($proposal->counters as $counter)
        <div class="ml-6 mt-3 border-l-2 border-slate-100 pl-4">
            @include('admin.opportunities._proposal-thread', ['proposal' => $counter])
        </div>
    @endforeach
</div>
