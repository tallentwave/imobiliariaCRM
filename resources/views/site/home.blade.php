<x-site-layout>
    <!-- Hero -->
    <section class="relative bg-slate-900">
        <div class="absolute inset-0">
            <img src="https://picsum.photos/seed/hero-imoveis/1600/700" class="w-full h-full object-cover opacity-40" alt="">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/70 to-slate-900/30"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-32">
            <h1 class="text-3xl sm:text-5xl font-bold text-white max-w-2xl">
                Encontre o imóvel perfeito para o seu próximo passo
            </h1>
            <p class="mt-4 text-slate-200 max-w-xl">
                {{ $settings->slogan ?? 'Compra, venda e aluguel com atendimento próximo e especializado.' }}
            </p>

            <form action="{{ route('imoveis.index') }}" method="GET" class="mt-8 bg-white rounded-2xl shadow-xl p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <select name="purpose" class="rounded-lg border-slate-200 text-sm">
                    <option value="">Finalidade</option>
                    <option value="venda">Comprar</option>
                    <option value="aluguel">Alugar</option>
                    <option value="temporada">Temporada</option>
                </select>
                <select name="type" class="rounded-lg border-slate-200 text-sm">
                    <option value="">Tipo de imóvel</option>
                    <option value="apartamento">Apartamento</option>
                    <option value="casa">Casa</option>
                    <option value="casa_condominio">Casa em condomínio</option>
                    <option value="cobertura">Cobertura</option>
                    <option value="terreno">Terreno</option>
                    <option value="comercial">Comercial</option>
                </select>
                <select name="city" class="rounded-lg border-slate-200 text-sm">
                    <option value="">Cidade</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}">{{ $city }}</option>
                    @endforeach
                </select>
                <input type="text" name="q" placeholder="Bairro, código ou palavra-chave" class="rounded-lg border-slate-200 text-sm lg:col-span-1">
                <button type="submit" class="rounded-lg bg-brand-700 text-white font-semibold text-sm hover:bg-brand-800 transition px-4 py-2">
                    Buscar imóveis
                </button>
            </form>
        </div>
    </section>

    <!-- Destaques -->
    @if($featured->count())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="flex items-end justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Imóveis em destaque</h2>
                    <p class="text-slate-500 text-sm mt-1">Seleção especial com as melhores oportunidades</p>
                </div>
                <a href="{{ route('imoveis.index') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">Ver todos →</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featured as $property)
                    <x-property-card :property="$property" />
                @endforeach
            </div>
        </section>
    @endif

    <!-- Últimos anúncios -->
    <section class="bg-slate-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Últimos imóveis anunciados</h2>
                </div>
                <a href="{{ route('imoveis.index') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">Ver todos →</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($latest as $property)
                    <x-property-card :property="$property" />
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="rounded-3xl bg-brand-900 text-white px-8 py-12 sm:px-16 sm:py-16 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div>
                <h2 class="text-2xl font-bold">Quer anunciar ou avaliar seu imóvel?</h2>
                <p class="mt-2 text-slate-200">Fale com um de nossos corretores especialistas agora mesmo.</p>
            </div>
            <a href="{{ route('contato') }}" class="inline-flex items-center rounded-full bg-white text-brand-900 font-semibold px-6 py-3 hover:bg-slate-100 transition">
                Fale conosco
            </a>
        </div>
    </section>
</x-site-layout>
