<x-admin-layout title="Contatos">
    <h1 class="text-2xl font-bold text-slate-900">Contatos</h1>
    <p class="text-slate-500 text-sm mt-1">Registro mestre de pessoas e empresas (clientes, proprietários, indicadores)</p>

    <form method="GET" class="mt-6 flex gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nome, e-mail, telefone ou CPF..." class="w-full max-w-md rounded-lg border-slate-200 text-sm">
        <button class="rounded-lg bg-slate-800 text-white text-sm font-semibold px-4 py-2">Buscar</button>
    </form>

    <div class="mt-6 bg-white rounded-2xl border border-slate-100 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">Nome</th>
                    <th class="px-4 py-3 text-left">Contato</th>
                    <th class="px-4 py-3 text-left">Responsável</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($contacts as $contact)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <p class="font-semibold text-slate-800">{{ $contact->displayName() }}</p>
                            <p class="text-xs text-slate-400">{{ $contact->type === 'COMPANY' ? 'Pessoa jurídica' : 'Pessoa física' }}</p>
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ $contact->mobile }}<br>
                            <span class="text-xs text-slate-400">{{ $contact->email }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $contact->owner?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold rounded-full px-2.5 py-1 {{ $contact->status === 'ACTIVE' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $contact->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.contacts.show', $contact) }}" class="text-brand-700 hover:text-brand-800 text-xs font-semibold">Ver ficha 360° →</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Nenhum contato encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $contacts->links() }}</div>
</x-admin-layout>
