<x-site-layout title="Contato">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h1 class="text-3xl font-bold text-slate-900">Fale conosco</h1>
        <p class="mt-2 text-slate-500">Preencha o formulário abaixo e retornaremos o mais breve possível.</p>

        <div class="mt-10 grid grid-cols-1 lg:grid-cols-2 gap-10">
            <form action="{{ route('leads.store') }}" method="POST" class="space-y-4 bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
                @csrf
                <input type="hidden" name="source" value="contato">
                <div>
                    <label class="text-xs font-semibold text-slate-500">Nome</label>
                    <input type="text" name="name" required value="{{ old('name', auth()->user()->name ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">Telefone / WhatsApp</label>
                    <input type="text" name="phone" required value="{{ old('phone') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">E-mail</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">Mensagem</label>
                    <textarea name="message" rows="4" class="mt-1 w-full rounded-lg border-slate-200 text-sm">{{ old('message') }}</textarea>
                </div>
                <button type="submit" class="w-full rounded-lg bg-brand-700 text-white font-semibold py-2.5 hover:bg-brand-800 transition">
                    Enviar mensagem
                </button>
            </form>

            <div class="space-y-6">
                <div class="rounded-2xl bg-slate-50 p-6">
                    <h2 class="font-semibold text-slate-800">Informações de contato</h2>
                    <ul class="mt-3 space-y-2 text-sm text-slate-600">
                        @if($settings->phone)<li>📞 {{ $settings->phone }}</li>@endif
                        @if($settings->email)<li>✉️ {{ $settings->email }}</li>@endif
                        @if($settings->address)<li>📍 {{ $settings->address }}, {{ $settings->city }}/{{ $settings->state }}</li>@endif
                    </ul>
                </div>

                @if($settings->whatsappLink())
                    <a href="{{ $settings->whatsappLink('Olá! Gostaria de falar com um corretor.') }}" target="_blank"
                       class="flex items-center justify-center gap-2 rounded-lg bg-emerald-500 text-white font-semibold py-3 hover:bg-emerald-600 transition">
                        Falar agora pelo WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-site-layout>
