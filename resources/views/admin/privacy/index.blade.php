<x-admin-layout title="Privacidade (LGPD)">
    <h1 class="text-2xl font-bold text-slate-900">Central de Privacidade — LGPD</h1>
    <p class="text-slate-500 text-sm mt-1">Solicitações de titulares de dados (acesso, exclusão, correção, portabilidade)</p>

    <div class="mt-6 bg-white rounded-2xl border border-slate-100 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">Solicitante</th>
                    <th class="px-4 py-3 text-left">Tipo</th>
                    <th class="px-4 py-3 text-left">Recebida em</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Responsável</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($requests as $request)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <p class="font-semibold text-slate-800">{{ $request->requester_name ?? $request->contact?->displayName() }}</p>
                            <p class="text-xs text-slate-400">{{ $request->requester_email ?? $request->contact?->email }}</p>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $request->typeLabel() }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $request->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold rounded-full px-2.5 py-1 {{ $request->status === 'CLOSED' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $request->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $request->handledBy->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <form action="{{ route('admin.privacy.update', $request) }}" method="POST" class="flex items-center gap-2 justify-end">
                                @csrf @method('PATCH')
                                <select name="status" class="rounded-lg border-slate-200 text-xs">
                                    @foreach(\App\Models\PrivacyRequest::STATUSES as $key => $label)
                                        <option value="{{ $key }}" @selected($request->status === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button class="text-xs font-semibold text-brand-700">Salvar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Nenhuma solicitação registrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $requests->links() }}</div>
</x-admin-layout>
