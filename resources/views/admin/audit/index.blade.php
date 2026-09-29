<x-admin-layout title="Auditoria">
    <h1 class="text-2xl font-bold text-slate-900">Auditoria</h1>
    <p class="text-slate-500 text-sm mt-1">Registro de eventos sensíveis: preços, transferências, comissões, permissões e acessos</p>

    <form method="GET" class="mt-6 flex gap-3">
        <input type="text" name="event" value="{{ request('event') }}" placeholder="Filtrar por tipo de evento (ex: property.price_changed)" class="w-full max-w-md rounded-lg border-slate-200 text-sm">
        <button class="rounded-lg bg-slate-800 text-white text-sm font-semibold px-4 py-2">Filtrar</button>
    </form>

    <div class="mt-6 bg-white rounded-2xl border border-slate-100 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">Data</th>
                    <th class="px-4 py-3 text-left">Usuário</th>
                    <th class="px-4 py-3 text-left">Evento</th>
                    <th class="px-4 py-3 text-left">Campo</th>
                    <th class="px-4 py-3 text-left">De → Para</th>
                    <th class="px-4 py-3 text-left">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-500 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $log->user->name ?? 'Sistema' }}</td>
                        <td class="px-4 py-3"><code class="text-xs bg-slate-100 rounded px-1.5 py-0.5">{{ $log->event }}</code></td>
                        <td class="px-4 py-3 text-slate-500">{{ $log->field ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">
                            @if($log->old_value || $log->new_value)
                                {{ $log->old_value }} → {{ $log->new_value }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-400 text-xs">{{ $log->ip_address }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Nenhum evento registrado ainda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $logs->links() }}</div>
</x-admin-layout>
