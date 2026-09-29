<x-admin-layout title="Imóveis">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Imóveis</h1>
        <a href="{{ route('admin.properties.create') }}" class="rounded-lg bg-brand-700 text-white text-sm font-semibold px-4 py-2 hover:bg-brand-800">
            + Novo imóvel
        </a>
    </div>

    <form method="GET" class="mt-6 flex flex-wrap gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por título..." class="rounded-lg border-slate-200 text-sm">
        <select name="status" class="rounded-lg border-slate-200 text-sm">
            <option value="">Todos os status</option>
            @foreach(['disponivel' => 'Disponível', 'reservado' => 'Reservado', 'vendido' => 'Vendido', 'alugado' => 'Alugado', 'inativo' => 'Inativo'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-slate-800 text-white text-sm font-semibold px-4 py-2">Filtrar</button>
    </form>

    <div class="mt-6 bg-white rounded-2xl border border-slate-100 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">Imóvel</th>
                    <th class="px-4 py-3 text-left">Corretor</th>
                    <th class="px-4 py-3 text-left">Preço</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Visualizações</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($properties as $property)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <p class="font-semibold text-slate-800">{{ $property->title }}</p>
                            <p class="text-xs text-slate-500">{{ $property->neighborhood }}, {{ $property->city }} · {{ $property->reference_code }}</p>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $property->agent->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">
                            @if($property->price)
                                R$ {{ number_format($property->price, 0, ',', '.') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold rounded-full px-2.5 py-1
                                {{ match($property->status) {
                                    'disponivel' => 'bg-emerald-50 text-emerald-700',
                                    'reservado' => 'bg-amber-50 text-amber-700',
                                    'vendido', 'alugado' => 'bg-brand-50 text-brand-700',
                                    default => 'bg-slate-100 text-slate-500',
                                } }}">
                                {{ $property->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $property->views_count }}</td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('imoveis.show', $property) }}" target="_blank" class="text-slate-400 hover:text-slate-700 text-xs">Ver</a>
                            <a href="{{ route('admin.properties.edit', $property) }}" class="text-brand-700 hover:text-brand-800 text-xs font-semibold">Editar</a>
                            <form action="{{ route('admin.properties.destroy', $property) }}" method="POST" class="inline" onsubmit="return confirm('Remover este imóvel?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700 text-xs font-semibold">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Nenhum imóvel cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $properties->links() }}</div>
</x-admin-layout>
