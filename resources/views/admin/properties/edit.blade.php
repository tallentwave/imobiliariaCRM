<x-admin-layout title="Editar imóvel">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Editar imóvel</h1>
        <a href="{{ route('imoveis.show', $property) }}" target="_blank" class="text-sm text-brand-700 font-semibold">Ver no site ↗</a>
    </div>

    <form action="{{ route('admin.properties.update', $property) }}" method="POST" enctype="multipart/form-data">
        @include('admin.properties._form')
    </form>
</x-admin-layout>
