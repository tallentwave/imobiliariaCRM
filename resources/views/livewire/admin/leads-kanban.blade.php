<div
    x-data="{
        notify(message) {
            this.toast = message;
            clearTimeout(this.toastTimeout);
            this.toastTimeout = setTimeout(() => this.toast = null, 3000);
        },
        toast: null,
        toastTimeout: null,
    }"
    x-on:notify.window="notify($event.detail.message)"
    wire:key="leads-kanban"
>
    <div
        x-show="toast"
        x-transition
        x-cloak
        class="fixed top-4 right-4 z-50 rounded-lg bg-slate-900 text-white text-sm px-4 py-2.5 shadow-lg"
        x-text="toast"
    ></div>

    <div class="flex gap-4 overflow-x-auto pb-4" wire:loading.class="opacity-60">
        @foreach($stages as $stageKey => $stageLabel)
            <div class="bg-slate-100/70 rounded-2xl p-3 w-[240px] flex-shrink-0">
                <div class="flex items-center justify-between mb-3 px-1">
                    <h2 class="text-sm font-bold text-slate-700">{{ $stageLabel }}</h2>
                    <span class="text-xs font-semibold text-slate-400">{{ $leads->get($stageKey, collect())->count() }}</span>
                </div>

                <div
                    class="space-y-3 min-h-[60px]"
                    data-stage="{{ $stageKey }}"
                    x-data
                    x-init="
                        Sortable.create($el, {
                            group: 'leads',
                            animation: 150,
                            ghostClass: 'opacity-40',
                            onEnd: (evt) => {
                                const leadId = evt.item.dataset.leadId;
                                const newStage = evt.to.dataset.stage;
                                $wire.moveLead(parseInt(leadId), newStage);
                            },
                        })
                    "
                >
                    @forelse($leads->get($stageKey, collect()) as $lead)
                        <div
                            wire:key="lead-{{ $lead->id }}"
                            data-lead-id="{{ $lead->id }}"
                            class="bg-white rounded-xl border border-slate-100 p-3 shadow-sm cursor-move hover:shadow-md transition"
                        >
                            <a href="{{ route('admin.leads.show', $lead) }}" class="block" onclick="event.stopPropagation()">
                                <div class="flex items-center justify-between gap-1">
                                    <p class="text-sm font-semibold text-slate-800">{{ $lead->name }}</p>
                                    @if($lead->isSlaOverdue())
                                        <span class="shrink-0 h-2 w-2 rounded-full bg-red-500" title="SLA estourado"></span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $lead->property->title ?? 'Contato geral' }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $lead->phone }}</p>
                            </a>
                            <p class="text-[11px] text-slate-400 mt-2">{{ $lead->agent->name ?? 'Sem corretor' }} · {{ $lead->created_at->format('d/m') }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 px-1 py-2">Arraste um lead para cá.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
