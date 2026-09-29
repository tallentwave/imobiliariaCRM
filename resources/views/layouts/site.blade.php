<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? $settings->site_name ?? config('app.name') }}</title>
    @if(isset($settings) && $settings->slogan)
        <meta name="description" content="{{ $settings->slogan }}">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#1e3a8a">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="icon" href="/icons/icon-192.png">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Nova Imóveis">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="font-sans antialiased bg-white text-slate-800">

    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    @if(isset($settings) && $settings->logo_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($settings->logo_path) }}" alt="{{ $settings->site_name }}" class="h-9 w-auto">
                    @else
                        <span class="text-xl font-bold text-brand-800">{{ $settings->site_name ?? config('app.name') }}</span>
                    @endif
                </a>

                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                    <a href="{{ route('home') }}" class="hover:text-brand-600 {{ request()->routeIs('home') ? 'text-brand-700' : '' }}">Início</a>
                    <a href="{{ route('imoveis.index') }}" class="hover:text-brand-600 {{ request()->routeIs('imoveis.*') ? 'text-brand-700' : '' }}">Imóveis</a>
                    <a href="{{ route('sobre') }}" class="hover:text-brand-600 {{ request()->routeIs('sobre') ? 'text-brand-700' : '' }}">Sobre</a>
                    <a href="{{ route('contato') }}" class="hover:text-brand-600 {{ request()->routeIs('contato') ? 'text-brand-700' : '' }}">Contato</a>
                </nav>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-700 hover:text-brand-600">
                            Minha conta
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-brand-600">Entrar</a>
                        <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center rounded-full bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800 transition">
                            Criar conta
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if ($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <main>
        {{ $slot }}
    </main>

    <footer class="bg-slate-900 text-slate-300 mt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <div>
                <span class="text-xl font-bold text-white">{{ $settings->site_name ?? config('app.name') }}</span>
                <p class="mt-3 text-sm text-slate-400">{{ $settings->slogan ?? 'Encontre o imóvel ideal para você e sua família.' }}</p>
                @if(isset($settings) && $settings->creci_company)
                    <p class="mt-2 text-xs text-slate-500">{{ $settings->creci_company }}</p>
                @endif
            </div>
            <div>
                <h3 class="text-white font-semibold mb-3">Navegação</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('imoveis.index') }}" class="hover:text-white">Imóveis</a></li>
                    <li><a href="{{ route('sobre') }}" class="hover:text-white">Sobre nós</a></li>
                    <li><a href="{{ route('contato') }}" class="hover:text-white">Contato</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-white font-semibold mb-3">Contato</h3>
                <ul class="space-y-2 text-sm text-slate-400">
                    @if(isset($settings) && $settings->phone)<li>{{ $settings->phone }}</li>@endif
                    @if(isset($settings) && $settings->email)<li>{{ $settings->email }}</li>@endif
                    @if(isset($settings) && $settings->address)<li>{{ $settings->address }}, {{ $settings->city }}/{{ $settings->state }}</li>@endif
                </ul>
            </div>
            <div>
                <h3 class="text-white font-semibold mb-3">Redes sociais</h3>
                <ul class="space-y-2 text-sm">
                    @if(isset($settings) && $settings->instagram_url)<li><a href="{{ $settings->instagram_url }}" target="_blank" class="hover:text-white">Instagram</a></li>@endif
                    @if(isset($settings) && $settings->facebook_url)<li><a href="{{ $settings->facebook_url }}" target="_blank" class="hover:text-white">Facebook</a></li>@endif
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-800 py-5 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} {{ $settings->site_name ?? config('app.name') }}. Todos os direitos reservados.
        </div>
    </footer>

    @if(isset($settings) && $settings->whatsappLink())
        <a href="{{ $settings->whatsappLink('Olá! Vim pelo site e gostaria de mais informações.') }}"
           target="_blank"
           class="fixed bottom-5 right-5 z-50 inline-flex items-center gap-2 rounded-full bg-emerald-500 text-white px-5 py-3 shadow-lg hover:bg-emerald-600 transition">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="w-6 h-6 fill-white">
                <path d="M16.001 3C9.373 3 4 8.373 4 15c0 2.34.657 4.523 1.795 6.385L4 29l7.815-2.05A11.94 11.94 0 0 0 16.001 27C22.628 27 28 21.627 28 15S22.628 3 16.001 3zm0 21.75c-1.95 0-3.769-.55-5.316-1.5l-.381-.226-4.637 1.216 1.238-4.52-.248-.396A9.71 9.71 0 0 1 5.25 15c0-5.93 4.82-10.75 10.751-10.75S26.75 9.07 26.75 15 21.932 24.75 16.001 24.75zm5.54-8.06c-.303-.152-1.793-.885-2.07-.986-.278-.101-.48-.152-.682.152-.202.303-.783.985-.96 1.188-.177.202-.354.227-.657.076-.303-.152-1.278-.471-2.435-1.503-.9-.803-1.508-1.795-1.685-2.098-.177-.303-.019-.467.133-.618.137-.136.303-.354.454-.53.152-.177.202-.303.303-.505.101-.202.05-.379-.025-.53-.076-.152-.682-1.644-.935-2.253-.246-.591-.497-.51-.682-.52l-.581-.01c-.202 0-.53.076-.808.379-.278.303-1.06 1.036-1.06 2.528 0 1.492 1.085 2.933 1.236 3.135.152.202 2.135 3.26 5.174 4.57.723.312 1.286.498 1.727.638.726.231 1.387.198 1.91.12.583-.087 1.793-.733 2.046-1.441.253-.708.253-1.315.177-1.441-.076-.126-.278-.202-.581-.354z"/>
            </svg>
            <span class="hidden sm:inline text-sm font-semibold">Fale no WhatsApp</span>
        </a>
    @endif

    @stack('scripts')
</body>
</html>
