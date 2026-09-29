<x-site-layout :title="$property->title">
    @push('head')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    @endpush

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <nav class="text-xs text-slate-400 mb-4">
            <a href="{{ route('home') }}" class="hover:text-slate-600">Início</a> /
            <a href="{{ route('imoveis.index') }}" class="hover:text-slate-600">Imóveis</a> /
            <span class="text-slate-600">{{ $property->title }}</span>
        </nav>

        <!-- Galeria -->
        @if($property->images->count())
            <div class="grid grid-cols-1 sm:grid-cols-4 sm:grid-rows-2 gap-2 rounded-2xl overflow-hidden h-[420px]">
                @foreach($property->images->take(5) as $i => $image)
                    <div class="{{ $i === 0 ? 'sm:col-span-2 sm:row-span-2' : '' }} h-full">
                        <img src="{{ $image->url() }}" alt="{{ $property->title }}" class="h-full w-full object-cover">
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <span class="inline-flex items-center rounded-full bg-brand-50 text-brand-700 text-xs font-semibold px-3 py-1">
                            {{ $property->purposeLabel() }} · {{ $property->typeLabel() }}
                        </span>
                        <h1 class="mt-3 text-2xl font-bold text-slate-900">{{ $property->title }}</h1>
                        <p class="mt-1 text-slate-500">{{ $property->neighborhood }}, {{ $property->city }}/{{ $property->state }} · Ref. {{ $property->reference_code }}</p>
                    </div>

                    @auth
                        <form action="{{ route('favoritos.toggle', $property) }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-full border border-slate-200 p-2.5 hover:bg-slate-50">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5 {{ $isFavorited ? 'fill-red-500 stroke-red-500' : 'fill-none stroke-slate-500' }}" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c-.2 0-.39-.06-.55-.18C7.6 17.35 3 13.62 3 9.5 3 6.87 5.1 4.75 7.7 4.75c1.5 0 2.94.72 3.8 1.9.86-1.18 2.3-1.9 3.8-1.9 2.6 0 4.7 2.12 4.7 4.75 0 4.12-4.6 7.85-8.45 10.57-.16.12-.35.18-.55.18z"/>
                                </svg>
                            </button>
                        </form>
                    @endauth
                </div>

                <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                    @if($property->bedrooms)
                        <div class="rounded-xl bg-slate-50 py-4"><p class="text-lg font-bold">{{ $property->bedrooms }}</p><p class="text-xs text-slate-500">Quartos</p></div>
                    @endif
                    @if($property->suites)
                        <div class="rounded-xl bg-slate-50 py-4"><p class="text-lg font-bold">{{ $property->suites }}</p><p class="text-xs text-slate-500">Suítes</p></div>
                    @endif
                    @if($property->parking_spots)
                        <div class="rounded-xl bg-slate-50 py-4"><p class="text-lg font-bold">{{ $property->parking_spots }}</p><p class="text-xs text-slate-500">Vagas</p></div>
                    @endif
                    @if($property->area_total)
                        <div class="rounded-xl bg-slate-50 py-4"><p class="text-lg font-bold">{{ (int) $property->area_total }} m²</p><p class="text-xs text-slate-500">Área total</p></div>
                    @endif
                </div>

                <div class="mt-8">
                    <h2 class="text-lg font-bold text-slate-900">Descrição</h2>
                    <p class="mt-2 text-slate-600 whitespace-pre-line leading-relaxed">{{ $property->description }}</p>
                </div>

                @if($property->features->count())
                    <div class="mt-8">
                        <h2 class="text-lg font-bold text-slate-900">Características</h2>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($property->features as $feature)
                                <span class="rounded-full bg-slate-100 text-slate-700 text-xs font-medium px-3 py-1.5">{{ $feature->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($property->latitude && $property->longitude)
                    <div class="mt-8">
                        <h2 class="text-lg font-bold text-slate-900">Localização</h2>
                        <div id="map" class="mt-3 h-80 w-full rounded-2xl overflow-hidden border border-slate-100"></div>
                    </div>
                @endif
            </div>

            <!-- Sidebar de contato -->
            <div>
                <div class="sticky top-24 rounded-2xl border border-slate-100 shadow-sm p-6">
                    <p class="text-2xl font-bold text-slate-900">
                        @if($property->price)
                            R$ {{ number_format($property->price, 0, ',', '.') }}{{ $property->purpose === 'aluguel' ? '/mês' : '' }}
                        @else
                            Consulte o valor
                        @endif
                    </p>
                    @if($property->condo_fee)
                        <p class="text-sm text-slate-500 mt-1">Condomínio: R$ {{ number_format($property->condo_fee, 0, ',', '.') }}</p>
                    @endif
                    @if($property->iptu)
                        <p class="text-sm text-slate-500">IPTU: R$ {{ number_format($property->iptu, 0, ',', '.') }}</p>
                    @endif

                    @if($property->agent)
                        <div class="mt-4 flex items-center gap-3 border-t border-slate-100 pt-4">
                            <div class="h-10 w-10 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold">
                                {{ strtoupper(substr($property->agent->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $property->agent->name }}</p>
                                <p class="text-xs text-slate-500">{{ $property->agent->creci ?? 'Corretor(a)' }}</p>
                            </div>
                        </div>
                    @endif

                    @if($settings->whatsappLink())
                        <a href="{{ $settings->whatsappLink('Olá! Tenho interesse no imóvel '.$property->title.' (Ref. '.$property->reference_code.')') }}"
                           target="_blank"
                           class="mt-5 flex items-center justify-center gap-2 rounded-lg bg-emerald-500 text-white font-semibold py-2.5 hover:bg-emerald-600 transition">
                            Chamar no WhatsApp
                        </a>
                    @endif

                    <form action="{{ route('leads.store') }}" method="POST" class="mt-5 space-y-3">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $property->id }}">
                        <input type="hidden" name="source" value="site">
                        <div>
                            <input type="text" name="name" required placeholder="Seu nome" value="{{ old('name', auth()->user()->name ?? '') }}" class="w-full rounded-lg border-slate-200 text-sm">
                        </div>
                        <div>
                            <input type="text" name="phone" required placeholder="Telefone / WhatsApp" value="{{ old('phone') }}" class="w-full rounded-lg border-slate-200 text-sm">
                        </div>
                        <div>
                            <input type="email" name="email" placeholder="E-mail (opcional)" value="{{ old('email', auth()->user()->email ?? '') }}" class="w-full rounded-lg border-slate-200 text-sm">
                        </div>
                        <div>
                            <textarea name="message" rows="3" placeholder="Mensagem" class="w-full rounded-lg border-slate-200 text-sm">{{ old('message', 'Tenho interesse neste imóvel, podem me passar mais informações?') }}</textarea>
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-brand-700 text-white font-semibold py-2.5 hover:bg-brand-800 transition">
                            Quero mais informações
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @if($related->count())
            <div class="mt-16">
                <h2 class="text-xl font-bold text-slate-900 mb-6">Imóveis parecidos</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($related as $item)
                        <x-property-card :property="$item" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @if($property->latitude && $property->longitude)
        @push('scripts')
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var map = L.map('map').setView([{{ $property->latitude }}, {{ $property->longitude }}], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors',
                        maxZoom: 19,
                    }).addTo(map);
                    L.marker([{{ $property->latitude }}, {{ $property->longitude }}]).addTo(map);
                });
            </script>
        @endpush
    @endif
</x-site-layout>
