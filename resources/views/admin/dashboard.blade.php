<x-admin-layout title="Dashboard">
    <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
    <p class="text-slate-500 text-sm mt-1">Visão geral do seu negócio</p>

    <div class="mt-6 grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl border border-slate-100 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase">Imóveis ativos</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $stats['properties_available'] }}</p>
            <p class="text-xs text-slate-400 mt-1">de {{ $stats['properties_total'] }} cadastrados</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase">Leads no mês</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $stats['leads_month'] }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $stats['leads_open'] }} em aberto</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase">SLA estourado</p>
            <p class="mt-2 text-2xl font-bold {{ $stats['sla_overdue'] > 0 ? 'text-red-600' : 'text-slate-900' }}">{{ $stats['sla_overdue'] }}</p>
            <p class="text-xs text-slate-400 mt-1">leads sem 1ª resposta a tempo</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase">Negócios fechados (mês)</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $stats['deals_won_month'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase">Comissões (mês)</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">R$ {{ number_format($stats['commission_month'], 2, ',', '.') }}</p>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-100 p-5">
            <x-admin.bar-chart title="Leads captados (últimos 6 meses)" :labels="$monthlySeries['labels']" :values="$monthlySeries['leads']" color="#2a78d6" />
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-5">
            <x-admin.bar-chart title="Negócios fechados (últimos 6 meses)" :labels="$monthlySeries['labels']" :values="$monthlySeries['deals']" color="#eb6834" />
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-5">
            <h2 class="font-semibold text-slate-800 mb-4">Funil de leads</h2>
            <div class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-11 gap-2">
                @foreach(\App\Models\Lead::STAGES as $key => $label)
                    <div class="rounded-xl bg-slate-50 p-3 text-center">
                        <p class="text-lg font-bold text-slate-800">{{ $leadsByStage[$key] ?? 0 }}</p>
                        <p class="text-[11px] text-slate-500 mt-1">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('admin.leads.index') }}" class="inline-block mt-4 text-sm font-semibold text-brand-700 hover:text-brand-800">Abrir funil completo →</a>

            <h2 class="font-semibold text-slate-800 mt-8 mb-4">Leads recentes</h2>
            <div class="divide-y divide-slate-100">
                @forelse($recentLeads as $lead)
                    <a href="{{ route('admin.leads.show', $lead) }}" class="flex items-center justify-between py-3 hover:bg-slate-50 -mx-2 px-2 rounded-lg">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $lead->name }}</p>
                            <p class="text-xs text-slate-500">{{ $lead->property->title ?? 'Contato geral' }}</p>
                        </div>
                        <span class="text-xs font-semibold text-slate-500">{{ $lead->stageLabel() }}</span>
                    </a>
                @empty
                    <p class="text-sm text-slate-500 py-4">Nenhum lead recente.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 p-5">
            <h2 class="font-semibold text-slate-800 mb-4">Próximas visitas</h2>
            <div class="space-y-3">
                @forelse($upcomingVisits as $visit)
                    <div class="rounded-xl bg-slate-50 p-3">
                        <p class="text-sm font-semibold text-slate-800">{{ $visit->property->title }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $visit->scheduled_at->translatedFormat('d/m/Y H:i') }}</p>
                        <p class="text-xs text-slate-400">{{ $visit->agent->name ?? 'Sem corretor' }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Nenhuma visita agendada.</p>
                @endforelse
            </div>
            <a href="{{ route('admin.visits.index') }}" class="inline-block mt-4 text-sm font-semibold text-brand-700 hover:text-brand-800">Ver agenda completa →</a>
        </div>
    </div>
</x-admin-layout>
