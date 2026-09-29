<x-site-layout title="Sobre nós">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h1 class="text-3xl font-bold text-slate-900">Sobre {{ $settings->site_name }}</h1>
        @if($settings->creci_company)
            <p class="mt-1 text-sm text-slate-500">{{ $settings->creci_company }}</p>
        @endif

        <div class="mt-8 prose prose-slate max-w-none">
            @if($settings->about_text)
                <p class="whitespace-pre-line text-slate-600 leading-relaxed">{{ $settings->about_text }}</p>
            @else
                <p class="text-slate-600 leading-relaxed">
                    Somos uma imobiliária comprometida em ajudar você a encontrar o imóvel ideal, seja para comprar,
                    vender ou alugar. Nossa equipe de corretores especializados oferece atendimento personalizado
                    em cada etapa do processo, com transparência e agilidade.
                </p>
            @endif
        </div>

        <div class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
            <div class="rounded-2xl bg-slate-50 p-6">
                <p class="text-3xl font-bold text-brand-700">{{ \App\Models\Property::count() }}</p>
                <p class="text-sm text-slate-500 mt-1">Imóveis cadastrados</p>
            </div>
            <div class="rounded-2xl bg-slate-50 p-6">
                <p class="text-3xl font-bold text-brand-700">{{ \App\Models\User::role('corretor')->count() }}</p>
                <p class="text-sm text-slate-500 mt-1">Corretores especializados</p>
            </div>
            <div class="rounded-2xl bg-slate-50 p-6">
                <p class="text-3xl font-bold text-brand-700">{{ \App\Models\Lead::where('stage', 'fechado_ganho')->count() }}</p>
                <p class="text-sm text-slate-500 mt-1">Negócios fechados</p>
            </div>
        </div>
    </div>
</x-site-layout>
