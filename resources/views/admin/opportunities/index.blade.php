<x-admin-layout title="Oportunidades">
    <h1 class="text-2xl font-bold text-slate-900">Oportunidades</h1>
    <p class="text-slate-500 text-sm mt-1">Necessidades comerciais qualificadas, com buyer profile</p>

    <div class="mt-6 bg-white rounded-2xl border border-slate-100 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">Contato</th>
                    <th class="px-4 py-3 text-left">Finalidade</th>
                    <th class="px-4 py-3 text-left">Orçamento</th>
                    <th class="px-4 py-3 text-left">Responsável</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($opportunities as $opportunity)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $opportunity->contact->displayName() }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $opportunity->purposeLabel() }}</td>
                        <td class="px-4 py-3 text-slate-600">
                            @if($opportunity->budget_min || $opportunity->budget_max)
                                R$ {{ number_format($opportunity->budget_min ?? 0, 0, ',', '.') }} – R$ {{ number_format($opportunity->budget_max ?? 0, 0, ',', '.') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $opportunity->assignedUser->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold rounded-full px-2.5 py-1 {{ match($opportunity->status) { 'WON' => 'bg-emerald-50 text-emerald-700', 'LOST' => 'bg-red-50 text-red-600', default => 'bg-amber-50 text-amber-700' } }}">
                                {{ $opportunity->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.opportunities.show', $opportunity) }}" class="text-brand-700 hover:text-brand-800 text-xs font-semibold">Abrir →</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Nenhuma oportunidade ainda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $opportunities->links() }}</div>
</x-admin-layout>
