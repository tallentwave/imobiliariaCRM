<x-site-layout title="Imóveis disponíveis">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-2xl font-bold text-slate-900">Imóveis disponíveis</h1>
        <p class="text-slate-500 text-sm mt-1">{{ $properties->total() }} imóveis encontrados</p>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Filtros -->
            <aside class="lg:col-span-1">
                <form method="GET" class="bg-white border border-slate-100 rounded-2xl p-5 space-y-4 sticky top-24">
                    <div>
                        <label class="text-xs font-semibold text-slate-500">Palavra-chave</label>
                        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Bairro, título, código..." class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-500">Finalidade</label>
                        <select name="purpose" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                            <option value="">Todas</option>
                            @foreach(['venda' => 'Comprar', 'aluguel' => 'Alugar', 'temporada' => 'Temporada'] as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['purpose'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-500">Tipo</label>
                        <select name="type" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                            <option value="">Todos</option>
                            @foreach(['apartamento' => 'Apartamento', 'casa' => 'Casa', 'casa_condominio' => 'Casa em condomínio', 'cobertura' => 'Cobertura', 'terreno' => 'Terreno', 'comercial' => 'Comercial', 'sala' => 'Sala comercial', 'galpao' => 'Galpão', 'rural' => 'Rural'] as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['type'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-500">Cidade</label>
                        <select name="city" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                            <option value="">Todas</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}" @selected(($filters['city'] ?? '') === $city)>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-500">Bairro</label>
                        <input type="text" name="neighborhood" value="{{ $filters['neighborhood'] ?? '' }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-slate-500">Preço mín.</label>
                            <input type="number" name="price_min" value="{{ $filters['price_min'] ?? '' }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500">Preço máx.</label>
                            <input type="number" name="price_max" value="{{ $filters['price_max'] ?? '' }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-500">Quartos (mín.)</label>
                        <select name="bedrooms" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                            <option value="">Qualquer</option>
                            @foreach([1,2,3,4,5] as $n)
                                <option value="{{ $n }}" @selected(($filters['bedrooms'] ?? '') == $n)>{{ $n }}+</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-brand-700 text-white font-semibold text-sm py-2.5 hover:bg-brand-800 transition">
                        Filtrar
                    </button>

                    @if(count(array_filter($filters ?? [])))
                        <a href="{{ route('imoveis.index') }}" class="block text-center text-xs text-slate-400 hover:text-slate-600">Limpar filtros</a>
                    @endif
                </form>
            </aside>

            <!-- Resultados -->
            <div class="lg:col-span-3">
                @auth
                    @if(count(array_filter($filters ?? [])))
                        <form method="POST" action="{{ route('buscas-salvas.store') }}" class="mb-4 flex items-center justify-end">
                            @csrf
                            @foreach($filters as $key => $value)
                                @if($value !== null && $value !== '')
                                    <input type="hidden" name="filters[{{ $key }}]" value="{{ $value }}">
                                @endif
                            @endforeach
                            <button type="submit" class="text-sm font-semibold text-brand-700 hover:text-brand-800">
                                ★ Salvar esta busca e receber novidades
                            </button>
                        </form>
                    @endif
                @endauth

                @if($properties->isEmpty())
                    <div class="bg-white border border-slate-100 rounded-2xl p-10 text-center text-slate-500">
                        Nenhum imóvel encontrado com esses filtros.
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($properties as $property)
                            <x-property-card :property="$property" />
                        @endforeach
                    </div>

                    <div class="mt-10">
                        {{ $properties->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-site-layout>
