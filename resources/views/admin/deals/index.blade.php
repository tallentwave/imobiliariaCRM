<x-admin-layout title="Negócios">
    <h1 class="text-2xl font-bold text-slate-900">Negócios</h1>
    <p class="text-slate-500 text-sm mt-1">Deal Room de cada negócio em andamento ou fechado</p>

    <div class="mt-6 bg-white rounded-2xl border border-slate-100 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">Imóvel</th>
                    <th class="px-4 py-3 text-left">Comprador</th>
                    <th class="px-4 py-3 text-left">Valor</th>
                    <th class="px-4 py-3 text-left">Corretor</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($deals as $deal)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $deal->property->title }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $deal->buyerContact?->displayName() ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">R$ {{ number_format($deal->value, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $deal->agent->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold rounded-full px-2.5 py-1
                                {{ match($deal->status) { 'CLOSED_WON' => 'bg-emerald-50 text-emerald-700', 'CLOSED_LOST' => 'bg-red-50 text-red-600', default => 'bg-amber-50 text-amber-700' } }}">
                                {{ $deal->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.deals.show', $deal) }}" class="text-brand-700 hover:text-brand-800 text-xs font-semibold">Abrir Deal Room →</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Nenhum negócio criado ainda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $deals->links() }}</div>
</x-admin-layout>
