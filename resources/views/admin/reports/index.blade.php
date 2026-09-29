<x-admin-layout title="Relatórios financeiros">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Relatórios financeiros</h1>
            <p class="text-slate-500 text-sm mt-1">Comissões de negócios fechados por período</p>
        </div>
        <a href="{{ route('admin.reports.export', request()->query()) }}" class="rounded-lg bg-slate-800 text-white text-sm font-semibold px-4 py-2 hover:bg-slate-900">
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
        <div>
            <label class="text-xs font-semibold text-slate-500">Corretor</label>
            <select name="agent_id" class="mt-1 rounded-lg border-slate-200 text-sm">
                <option value="">Todos</option>
                @foreach($agents as $agent)
                    <option value="{{ $agent->id }}" @selected($agentId == $agent->id)>{{ $agent->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="rounded-lg bg-brand-700 text-white text-sm font-semibold px-4 py-2 hover:bg-brand-800">Filtrar</button>
    </form>

    <div class="mt-6 grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-100 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase">Negócios fechados</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $summary['deals_count'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase">Valor negociado</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">R$ {{ number_format($summary['total_negotiated'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase">Comissão total</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">R$ {{ number_format($summary['total_commission'], 2, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase">Paga / Pendente</p>
            <p class="mt-2 text-sm font-bold text-emerald-600">R$ {{ number_format($summary['commission_paid'], 2, ',', '.') }} paga</p>
            <p class="text-sm font-bold text-amber-600">R$ {{ number_format($summary['commission_pending'], 2, ',', '.') }} pendente</p>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-slate-100 p-5">
            <h2 class="font-semibold text-slate-800 mb-4">Por corretor</h2>
            <div class="space-y-3">
                @forelse($byAgent as $agentName => $data)
                    <div class="flex items-center justify-between text-sm border-b border-slate-50 pb-2 last:border-0">
                        <div>
                            <p class="font-semibold text-slate-700">{{ $agentName }}</p>
                            <p class="text-xs text-slate-400">{{ $data['deals_count'] }} negócio(s)</p>
                        </div>
                        <p class="font-semibold text-slate-800">R$ {{ number_format($data['total_commission'], 2, ',', '.') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Nenhum dado no período.</p>
                @endforelse
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Fechamento</th>
                        <th class="px-4 py-3 text-left">Corretor</th>
                        <th class="px-4 py-3 text-left">Imóvel</th>
                        <th class="px-4 py-3 text-left">Comissão</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($deals as $deal)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-600">{{ optional($deal->closed_at)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $deal->agent->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $deal->property->title ?? '—' }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">R$ {{ number_format($deal->commission_value, 2, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-semibold rounded-full px-2.5 py-1 {{ $deal->commission_paid ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ $deal->commission_paid ? 'Paga' : 'Pendente' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form action="{{ route('admin.reports.mark-paid', $deal) }}" method="POST">
                                    @csrf
                                    <button class="text-xs font-semibold text-brand-700 hover:text-brand-800">
                                        Marcar como {{ $deal->commission_paid ? 'pendente' : 'paga' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Nenhum negócio fechado neste período.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
