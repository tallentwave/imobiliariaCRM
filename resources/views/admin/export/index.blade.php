<x-admin-layout title="Exportar para portais">
    <h1 class="text-2xl font-bold text-slate-900">Exportar imóveis para portais</h1>
    <p class="text-slate-500 text-sm mt-1">
        Gere feeds XML com os imóveis disponíveis para venda ou aluguel, prontos para configurar
        a integração automática nos principais portais.
    </p>

    <div class="mt-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm p-4">
        ⚠️ Estes feeds seguem a estrutura e os campos comumente exigidos por integrações de
        portais imobiliários (padrão ZAP/VivaReal e um formato simples compatível com
        integradores de anúncios). Como os portais podem alterar suas especificações sem aviso
        prévio, <strong>confirme os nomes exatos de campo com o suporte técnico do portal</strong>
        antes de ativar o envio automático em produção.
    </div>

    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-3xl">
        <div class="bg-white rounded-2xl border border-slate-100 p-6">
            <h2 class="font-semibold text-slate-800">Feed ZAP / VivaReal</h2>
            <p class="text-sm text-slate-500 mt-1">Formato XML (ListingDataFeed) aceito por esses portais.</p>
            <div class="mt-4 flex items-center gap-2">
                <input type="text" readonly value="{{ route('admin.export.zap-vivareal') }}" class="w-full rounded-lg border-slate-200 text-xs" onclick="this.select()">
            </div>
            <a href="{{ route('admin.export.zap-vivareal') }}" target="_blank" class="inline-block mt-3 text-sm font-semibold text-brand-700 hover:text-brand-800">
                Abrir / baixar XML →
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 p-6">
            <h2 class="font-semibold text-slate-800">Feed OLX / integradores</h2>
            <p class="text-sm text-slate-500 mt-1">Formato XML simplificado (id, título, preço, localização, fotos).</p>
            <div class="mt-4 flex items-center gap-2">
                <input type="text" readonly value="{{ route('admin.export.olx') }}" class="w-full rounded-lg border-slate-200 text-xs" onclick="this.select()">
            </div>
            <a href="{{ route('admin.export.olx') }}" target="_blank" class="inline-block mt-3 text-sm font-semibold text-brand-700 hover:text-brand-800">
                Abrir / baixar XML →
            </a>
        </div>
    </div>

    <p class="mt-6 text-xs text-slate-400 max-w-2xl">
        Os feeds são gerados dinamicamente a partir dos imóveis com status "Disponível" ou
        "Reservado" e finalidade "Venda" ou "Aluguel". Copie a URL acima e informe-a no painel
        de integração do portal (ou compartilhe com o integrador utilizado) para manter os
        anúncios sempre atualizados automaticamente.
    </p>
</x-admin-layout>
