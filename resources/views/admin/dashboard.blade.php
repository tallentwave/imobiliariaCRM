<x-admin-layout title="Dashboard">
    <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
    <p class="text-slate-500 text-sm mt-1">Visão geral do seu negócio</p>

    <div class="mt-6 grid grid-cols-2 lg:grid-cols-5 gap-4">
        <x-admin.stat-card label="Imóveis ativos" :value="$stats['properties_available']" :sub="'de '.$stats['properties_total'].' cadastrados'" color="brand">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/></svg>
        </x-admin.stat-card>
        <x-admin.stat-card label="Leads no mês" :value="$stats['leads_month']" :sub="$stats['leads_open'].' em aberto'" color="violet">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z"/></svg>
        </x-admin.stat-card>
        <x-admin.stat-card label="SLA estourado" :value="$stats['sla_overdue']" sub="leads sem 1ª resposta a tempo" :color="$stats['sla_overdue'] > 0 ? 'red' : 'brand'">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12V16.5Zm9.75-4.5a9.75 9.75 0 1 1-19.5 0 9.75 9.75 0 0 1 19.5 0Z"/></svg>
        </x-admin.stat-card>
        <x-admin.stat-card label="Negócios fechados (mês)" :value="$stats['deals_won_month']" color="emerald">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
        </x-admin.stat-card>
        <x-admin.stat-card label="Comissões (mês)" :value="'R$ '.number_format($stats['commission_month'], 2, ',', '.')" color="amber">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182.553-.44 1.278-.659 2.003-.659.725 0 1.45.22 2.003.659l.415.33"/></svg>
        </x-admin.stat-card>
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
                    @php
                        $isOpen = in_array($key, \App\Models\Lead::OPEN_STAGES);
                        $isLost = in_array($key, \App\Models\Lead::LOST_STAGES);
                    @endphp
                    <div @class([
                        'rounded-xl p-3 text-center border',
                        'bg-brand-50/60 border-brand-100' => $isOpen,
                        'bg-slate-50 border-slate-100' => $isLost,
                        'bg-emerald-50/60 border-emerald-100' => ! $isOpen && ! $isLost,
                    ])>
                        <p @class([
                            'text-lg font-bold',
                            'text-brand-800' => $isOpen,
                            'text-slate-500' => $isLost,
                            'text-emerald-800' => ! $isOpen && ! $isLost,
                        ])>{{ $leadsByStage[$key] ?? 0 }}</p>
                        <p class="text-[11px] text-slate-500 mt-1 leading-tight">{{ $label }}</p>
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
                            <p class="text-xs text-slate-500">{{ $lead->property?->title ?? 'Contato geral' }}</p>
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
                        <p class="text-sm font-semibold text-slate-800">{{ $visit->property?->title ?? 'Imóvel removido' }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $visit->scheduled_at->translatedFormat('d/m/Y H:i') }}</p>
                        <p class="text-xs text-slate-400">{{ $visit->agent?->name ?? 'Sem corretor' }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Nenhuma visita agendada.</p>
                @endforelse
            </div>
            <a href="{{ route('admin.visits.index') }}" class="inline-block mt-4 text-sm font-semibold text-brand-700 hover:text-brand-800">Ver agenda completa →</a>
        </div>
    </div>
</x-admin-layout>
