<x-admin-layout title="Captação">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Captação de imóveis</h1>
            <p class="text-slate-500 text-sm mt-1">Contratos de captação e pipeline de proprietários</p>
        </div>
        <a href="{{ route('admin.listings.create') }}" class="rounded-lg bg-brand-700 text-white text-sm font-semibold px-4 py-2 hover:bg-brand-800">
            + Nova captação
        </a>
    </div>

    <div class="mt-6 grid grid-cols-2 sm:grid-cols-5 lg:grid-cols-10 gap-2">
        @foreach(\App\Models\ListingAgreement::STAGES as $key => $label)
            <a href="{{ route('admin.listings.index', ['status' => $key]) }}" class="rounded-xl {{ request('status') === $key ? 'bg-brand-700 text-white' : 'bg-slate-100 text-slate-600' }} p-2 text-center">
                <p class="text-xs font-semibold">{{ $agreements->where('status', $key)->count() ?? 0 }}</p>
                <p class="text-[10px] mt-0.5">{{ $label }}</p>
            </a>
        @endforeach
    </div>

    <div class="mt-6 bg-white rounded-2xl border border-slate-100 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">Imóvel</th>
                    <th class="px-4 py-3 text-left">Tipo</th>
                    <th class="px-4 py-3 text-left">Captador</th>
                    <th class="px-4 py-3 text-left">Vigência</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($agreements as $agreement)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $agreement->property->title }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $agreement->typeLabel() }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $agreement->captor->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ $agreement->starts_at?->format('d/m/y') }} – {{ $agreement->ends_at?->format('d/m/y') }}
                            @if($agreement->isExpired())<span class="text-red-500 text-xs font-semibold"> (expirada)</span>@endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold rounded-full px-2.5 py-1 {{ $agreement->status === 'ACTIVE' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $agreement->stageLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.listings.edit', $agreement) }}" class="text-brand-700 hover:text-brand-800 text-xs font-semibold">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Nenhuma captação registrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $agreements->links() }}</div>
</x-admin-layout>
