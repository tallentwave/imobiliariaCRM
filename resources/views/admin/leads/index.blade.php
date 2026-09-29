<x-admin-layout title="Funil de leads">
    <h1 class="text-2xl font-bold text-slate-900">Funil de vendas</h1>
    <p class="text-slate-500 text-sm mt-1">Acompanhe seus leads em cada etapa do funil</p>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 xl:grid-cols-6 gap-4 overflow-x-auto">
        @foreach($stages as $stageKey => $stageLabel)
            <div class="bg-slate-100/70 rounded-2xl p-3 min-w-[240px]">
                <div class="flex items-center justify-between mb-3 px-1">
                    <h2 class="text-sm font-bold text-slate-700">{{ $stageLabel }}</h2>
                    <span class="text-xs font-semibold text-slate-400">{{ $leads->get($stageKey, collect())->count() }}</span>
                </div>

                <div class="space-y-3">
                    @forelse($leads->get($stageKey, collect()) as $lead)
                        <div class="bg-white rounded-xl border border-slate-100 p-3 shadow-sm">
                            <a href="{{ route('admin.leads.show', $lead) }}" class="block">
                                <p class="text-sm font-semibold text-slate-800">{{ $lead->name }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $lead->property->title ?? 'Contato geral' }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $lead->phone }}</p>
                            </a>

                            <form action="{{ route('admin.leads.update', $lead) }}" method="POST" class="mt-2 flex gap-1">
                                @csrf @method('PUT')
                                <select name="stage" onchange="this.form.submit()" class="w-full text-xs rounded-lg border-slate-200">
                                    @foreach($stages as $key => $label)
                                        <option value="{{ $key }}" @selected($lead->stage === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>

                            <p class="text-[11px] text-slate-400 mt-2">{{ $lead->agent->name ?? 'Sem corretor' }} · {{ $lead->created_at->format('d/m') }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 px-1">Nenhum lead nesta etapa.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</x-admin-layout>
