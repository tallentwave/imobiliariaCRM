<x-admin-layout title="Funil de leads">
    @push('head')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js" integrity="sha512-ozq8xQKq6urvuU6jNgkfqAmT7jKN2XumbrX1JiB3TnF7tI48DPI4Gy1GXKD/V3EExgAs1V+pRO7vwtS1LHg0Gw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @endpush

    <h1 class="text-2xl font-bold text-slate-900">Funil de vendas</h1>
    <p class="text-slate-500 text-sm mt-1">Arraste os cartões entre as colunas para atualizar a etapa de cada lead</p>

    <div class="mt-6">
        @livewire('admin.leads-kanban')
    </div>
</x-admin-layout>
