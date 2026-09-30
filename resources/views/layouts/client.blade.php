<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Minha conta' }} · {{ $settings->site_name ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#1e3a8a">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="icon" href="/icons/icon-192.png">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800">

    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="text-xl font-bold text-brand-800">{{ $settings->site_name ?? config('app.name') }}</a>

                <nav class="hidden md:flex items-center gap-1 text-sm font-medium text-slate-600">
                    <a href="{{ route('imoveis.index') }}" class="px-3 py-2 rounded-lg hover:bg-slate-50 hover:text-brand-600">Ver imóveis</a>
                    <a href="{{ route('minha-conta') }}" class="px-3 py-2 rounded-lg hover:bg-slate-50 hover:text-brand-600 {{ request()->routeIs('minha-conta') ? 'text-brand-700 bg-brand-50' : '' }}">Minha conta</a>
                    <a href="{{ route('profile.edit') }}" class="px-3 py-2 rounded-lg hover:bg-slate-50 hover:text-brand-600 {{ request()->routeIs('profile.edit') ? 'text-brand-700 bg-brand-50' : '' }}">Meus dados</a>
                </nav>

                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex items-center gap-2.5">
                        <div class="h-8 w-8 rounded-full bg-brand-700 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="text-sm font-medium text-slate-700 max-w-[9rem] truncate">{{ auth()->user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-slate-500 hover:text-red-600 px-2 py-2">Sair</button>
                    </form>
                </div>
            </div>

            <nav class="md:hidden flex items-center gap-1 text-sm font-medium text-slate-600 pb-3 -mt-1 overflow-x-auto">
                <a href="{{ route('imoveis.index') }}" class="px-3 py-1.5 rounded-lg hover:bg-slate-50 whitespace-nowrap">Ver imóveis</a>
                <a href="{{ route('minha-conta') }}" class="px-3 py-1.5 rounded-lg whitespace-nowrap {{ request()->routeIs('minha-conta') ? 'text-brand-700 bg-brand-50' : 'hover:bg-slate-50' }}">Minha conta</a>
                <a href="{{ route('profile.edit') }}" class="px-3 py-1.5 rounded-lg whitespace-nowrap {{ request()->routeIs('profile.edit') ? 'text-brand-700 bg-brand-50' : 'hover:bg-slate-50' }}">Meus dados</a>
            </nav>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @if (session('success'))
            <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{ $slot }}
    </main>
</body>
</html>
