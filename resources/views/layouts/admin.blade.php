<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}CRM · {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    @livewireStyles
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-900 text-slate-200 transform transition-transform lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="h-16 flex items-center px-6 border-b border-slate-800">
                <a href="{{ route('admin.dashboard') }}" class="text-lg font-bold text-white">
                    {{ \App\Models\Setting::current()->site_name }} <span class="text-accent-500">CRM</span>
                </a>
            </div>
            <nav class="px-3 py-4 space-y-1 text-sm">
                @php $user = auth()->user(); @endphp

                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800/60' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.properties.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.properties.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800/60' }}">
                    Imóveis
                </a>
                <a href="{{ route('admin.leads.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.leads.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800/60' }}">
                    Leads / Funil
                </a>
                <a href="{{ route('admin.visits.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.visits.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800/60' }}">
                    Visitas
                </a>

                @if($user->hasRole('admin') || $user->hasRole('financeiro'))
                    <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.reports.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800/60' }}">
                        Relatórios
                    </a>
                @endif

                @if($user->hasRole('admin'))
                    <a href="{{ route('admin.export.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.export.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800/60' }}">
                        Exportar p/ portais
                    </a>
                    <a href="{{ route('admin.features.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.features.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800/60' }}">
                        Características
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.users.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800/60' }}">
                        Usuários
                    </a>
                    <a href="{{ route('admin.settings.edit') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.settings.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800/60' }}">
                        Configurações
                    </a>
                @endif

                <div class="pt-4 mt-4 border-t border-slate-800">
                    <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-slate-800/60">
                        Ver site público ↗
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-slate-800/60">
                        Meu perfil
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-slate-800/60">
                            Sair
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <div class="fixed inset-0 bg-black/40 z-20 lg:hidden" x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak></div>

        <!-- Content -->
        <div class="flex-1 lg:ml-64">
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-10">
                <button class="lg:hidden text-slate-600" @click="sidebarOpen = !sidebarOpen">
                    ☰
                </button>
                <div class="hidden lg:block text-sm text-slate-500">
                    {{ now()->translatedFormat('l, d \d\e F \d\e Y') }}
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-slate-600">{{ auth()->user()->name }}</span>
                    <span class="inline-flex items-center rounded-full bg-brand-50 text-brand-700 text-xs font-semibold px-2.5 py-1">
                        {{ ucfirst(auth()->user()->roles->first()?->name ?? '') }}
                    </span>
                </div>
            </header>

            <main class="p-4 sm:p-6">
                @if (session('success'))
                    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
    @livewireScripts
</body>
</html>
