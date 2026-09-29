<x-admin-layout title="Características">
    <h1 class="text-2xl font-bold text-slate-900 mb-6">Características / Comodidades</h1>

    <form action="{{ route('admin.features.store') }}" method="POST" class="flex gap-3 max-w-md mb-6">
        @csrf
        <input type="text" name="name" required placeholder="Nova característica" class="w-full rounded-lg border-slate-200 text-sm">
        <button class="rounded-lg bg-brand-700 text-white text-sm font-semibold px-4 py-2 hover:bg-brand-800 whitespace-nowrap">Adicionar</button>
    </form>

    <div class="bg-white rounded-2xl border border-slate-100 divide-y divide-slate-100 max-w-md">
        @foreach($features as $feature)
            <div class="flex items-center justify-between px-4 py-3">
                <span class="text-sm text-slate-700">{{ $feature->name }}</span>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-400">{{ $feature->properties_count }} imóveis</span>
                    <form action="{{ route('admin.features.destroy', $feature) }}" method="POST" onsubmit="return confirm('Remover esta característica?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-500 hover:text-red-700">Excluir</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</x-admin-layout>
