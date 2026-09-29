<x-admin-layout title="Visitas">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Agenda de visitas</h1>
        <a href="{{ route('admin.visits.create') }}" class="rounded-lg bg-brand-700 text-white text-sm font-semibold px-4 py-2 hover:bg-brand-800">
            + Agendar visita
        </a>
    </div>

    <div class="mt-6 bg-white rounded-2xl border border-slate-100 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">Data/Hora</th>
                    <th class="px-4 py-3 text-left">Imóvel</th>
                    <th class="px-4 py-3 text-left">Corretor</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($visits as $visit)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-700">{{ $visit->scheduled_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $visit->property->title }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $visit->agent->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold rounded-full px-2.5 py-1
                                {{ match($visit->status) { 'agendada' => 'bg-amber-50 text-amber-700', 'realizada' => 'bg-emerald-50 text-emerald-700', default => 'bg-slate-100 text-slate-500' } }}">
                                {{ ucfirst($visit->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('admin.visits.edit', $visit) }}" class="text-brand-700 hover:text-brand-800 text-xs font-semibold">Editar</a>
                            <form action="{{ route('admin.visits.destroy', $visit) }}" method="POST" class="inline" onsubmit="return confirm('Remover esta visita?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700 text-xs font-semibold">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Nenhuma visita agendada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
