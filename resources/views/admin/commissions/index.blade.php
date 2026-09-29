<x-admin-layout title="Comissões">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Comissões</h1>
            <p class="text-slate-500 text-sm mt-1">Motor de comissões — eventos, rateio (splits) e pagamentos</p>
        </div>
        <a href="{{ route('admin.commissions.export', request()->query()) }}" class="rounded-lg bg-slate-800 text-white text-sm font-semibold px-4 py-2 hover:bg-slate-900">
            Exportar CSV
        </a>
    </div>

    <form method="GET" class="mt-6 flex flex-wrap items-end gap-3">
        <div>
            <label class="text-xs font-semibold text-slate-500">Mês</label>
            <select name="month" class="mt-1 rounded-lg border-slate-200 text-sm">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" @selected($month == $m)>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold text-slate-500">Ano</label>
            <select name="year" class="mt-1 rounded-lg border-slate-200 text-sm">
                @foreach(range(now()->year, now()->year - 3) as $y)
                    <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <button class="rounded-lg bg-brand-700 text-white text-sm font-semibold px-4 py-2 hover:bg-brand-800">Filtrar</button>
    </form>

    <div class="mt-6 grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-100 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase">Total</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">R$ {{ number_format($summary['total'], 2, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase">Pago</p>
            <p class="mt-2 text-2xl font-bold text-emerald-600">R$ {{ number_format($summary['paid'], 2, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase">Pendente</p>
            <p class="mt-2 text-2xl font-bold text-amber-600">R$ {{ number_format($summary['pending'], 2, ',', '.') }}</p>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-slate-100 p-5">
            <h2 class="font-semibold text-slate-800 mb-4">Por beneficiário</h2>
            @forelse($byUser as $name => $data)
                <div class="flex items-center justify-between text-sm border-b border-slate-50 py-2 last:border-0">
                    <span class="text-slate-700">{{ $name }}</span>
                    <span class="font-semibold">R$ {{ number_format($data['total'], 2, ',', '.') }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-400">Nenhum dado no período.</p>
            @endforelse
        </div>

        <div class="lg:col-span-2 space-y-4">
            @forelse($events as $event)
                <div class="bg-white rounded-2xl border border-slate-100 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $event->deal->property->title ?? '—' }}</p>
                            <p class="text-xs text-slate-500">R$ {{ number_format($event->total_commission_value, 2, ',', '.') }} ({{ $event->total_commission_percent }}%)</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold rounded-full px-2.5 py-1 {{ match($event->status) { 'PAID' => 'bg-emerald-50 text-emerald-700', 'APPROVED' => 'bg-blue-50 text-blue-700', default => 'bg-amber-50 text-amber-700' } }}">
                                {{ $event->statusLabel() }}
                            </span>
                            @if($event->status === 'PENDING')
                                <form action="{{ route('admin.commissions.approve', $event) }}" method="POST">
                                    @csrf
                                    <button class="text-xs font-semibold text-brand-700">Aprovar</button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="mt-3 divide-y divide-slate-50">
                        @foreach($event->splits as $split)
                            <div class="flex items-center justify-between py-2 text-sm">
                                <span>{{ $split->dimensionLabel() }} — {{ $split->user->name ?? 'Empresa' }} ({{ $split->percentage }}%)</span>
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold">R$ {{ number_format($split->value, 2, ',', '.') }}</span>
                                    @if(! $split->paid)
                                        <form action="{{ route('admin.commissions.mark-paid', $split) }}" method="POST">
                                            @csrf
                                            <button class="text-xs text-brand-700 font-semibold">Marcar pago</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-emerald-600 font-semibold">Pago ✓</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-slate-100 p-8 text-center text-slate-400">
                    Nenhuma comissão neste período.
                </div>
            @endforelse
        </div>
    </div>
</x-admin-layout>
