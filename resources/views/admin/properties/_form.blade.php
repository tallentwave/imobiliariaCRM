@csrf
@if(isset($property)) @method('PUT') @endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl border border-slate-100 p-5 space-y-4">
            <h2 class="font-semibold text-slate-800">Dados principais</h2>

            <div>
                <label class="text-xs font-semibold text-slate-500">Título</label>
                <input type="text" name="title" required value="{{ old('title', $property->title ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500">Finalidade</label>
                    <select name="purpose" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                        @foreach(['venda' => 'Venda', 'aluguel' => 'Aluguel', 'temporada' => 'Temporada'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('purpose', $property->purpose ?? 'venda') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">Tipo</label>
                    <select name="type" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                        @foreach(['apartamento' => 'Apartamento', 'casa' => 'Casa', 'casa_condominio' => 'Casa em condomínio', 'cobertura' => 'Cobertura', 'terreno' => 'Terreno', 'comercial' => 'Comercial', 'sala' => 'Sala comercial', 'galpao' => 'Galpão', 'rural' => 'Rural', 'outro' => 'Outro'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('type', $property->type ?? 'apartamento') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-500">Descrição</label>
                <textarea name="description" rows="5" class="mt-1 w-full rounded-lg border-slate-200 text-sm">{{ old('description', $property->description ?? '') }}</textarea>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 p-5 space-y-4">
            <h2 class="font-semibold text-slate-800">Valores</h2>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500">Preço (R$)</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $property->price ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">Condomínio (R$)</label>
                    <input type="number" step="0.01" name="condo_fee" value="{{ old('condo_fee', $property->condo_fee ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">IPTU (R$)</label>
                    <input type="number" step="0.01" name="iptu" value="{{ old('iptu', $property->iptu ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 p-5 space-y-4">
            <h2 class="font-semibold text-slate-800">Características do imóvel</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500">Quartos</label>
                    <input type="number" name="bedrooms" value="{{ old('bedrooms', $property->bedrooms ?? 0) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">Suítes</label>
                    <input type="number" name="suites" value="{{ old('suites', $property->suites ?? 0) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">Banheiros</label>
                    <input type="number" name="bathrooms" value="{{ old('bathrooms', $property->bathrooms ?? 0) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">Vagas</label>
                    <input type="number" name="parking_spots" value="{{ old('parking_spots', $property->parking_spots ?? 0) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">Área total (m²)</label>
                    <input type="number" step="0.01" name="area_total" value="{{ old('area_total', $property->area_total ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">Área construída (m²)</label>
                    <input type="number" step="0.01" name="area_built" value="{{ old('area_built', $property->area_built ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-500">Comodidades</label>
                <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @php $selectedFeatures = old('features', isset($property) ? $property->features->pluck('id')->toArray() : []); @endphp
                    @foreach($features as $feature)
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="features[]" value="{{ $feature->id }}" @checked(in_array($feature->id, $selectedFeatures)) class="rounded border-slate-300 text-brand-700">
                            {{ $feature->name }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 p-5 space-y-4">
            <h2 class="font-semibold text-slate-800">Endereço</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500">CEP</label>
                    <input type="text" name="zipcode" value="{{ old('zipcode', $property->zipcode ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">Cidade</label>
                    <input type="text" name="city" value="{{ old('city', $property->city ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500">Endereço</label>
                    <input type="text" name="address" value="{{ old('address', $property->address ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">Número</label>
                    <input type="text" name="number" value="{{ old('number', $property->number ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500">Bairro</label>
                    <input type="text" name="neighborhood" value="{{ old('neighborhood', $property->neighborhood ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">Estado (UF)</label>
                    <input type="text" name="state" maxlength="2" value="{{ old('state', $property->state ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500">Latitude</label>
                    <input type="text" name="latitude" value="{{ old('latitude', $property->latitude ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">Longitude</label>
                    <input type="text" name="longitude" value="{{ old('longitude', $property->longitude ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
            </div>
            <p class="text-xs text-slate-400">Dica: copie a latitude/longitude do Google Maps ou OpenStreetMap ao clicar com o botão direito no local do imóvel.</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 p-5 space-y-4">
            <h2 class="font-semibold text-slate-800">Fotos</h2>

            @if(isset($property) && $property->images->count())
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                    @foreach($property->images as $image)
                        <div class="relative group">
                            <img src="{{ $image->url() }}" class="h-24 w-full object-cover rounded-lg {{ $image->is_cover ? 'ring-2 ring-brand-600' : '' }}">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-1 rounded-lg">
                                @if(!$image->is_cover)
                                    <form action="{{ route('admin.property-images.cover', $image) }}" method="POST">
                                        @csrf
                                        <button class="text-[10px] bg-white rounded px-1.5 py-0.5">Capa</button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.property-images.destroy', $image) }}" method="POST" onsubmit="return confirm('Remover esta foto?')">
                                    @csrf @method('DELETE')
                                    <button class="text-[10px] bg-white text-red-600 rounded px-1.5 py-0.5">Excluir</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div>
                <label class="text-xs font-semibold text-slate-500">Adicionar novas fotos</label>
                <input type="file" name="images[]" multiple accept="image/*" class="mt-1 w-full text-sm">
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-100 p-5 space-y-4">
            <h2 class="font-semibold text-slate-800">Publicação</h2>
            <div>
                <label class="text-xs font-semibold text-slate-500">Status</label>
                <select name="status" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                    @foreach(['disponivel' => 'Disponível', 'reservado' => 'Reservado', 'vendido' => 'Vendido', 'alugado' => 'Alugado', 'inativo' => 'Inativo'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $property->status ?? 'disponivel') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">Corretor responsável</label>
                <select name="agent_id" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}" @selected(old('agent_id', $property->agent_id ?? auth()->id()) == $agent->id)>{{ $agent->name }}</option>
                    @endforeach
                </select>
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="featured" value="1" @checked(old('featured', $property->featured ?? false)) class="rounded border-slate-300 text-brand-700">
                Destacar na página inicial
            </label>
        </div>

        <button type="submit" class="w-full rounded-lg bg-brand-700 text-white font-semibold py-3 hover:bg-brand-800 transition">
            {{ isset($property) ? 'Salvar alterações' : 'Cadastrar imóvel' }}
        </button>
    </div>
</div>
