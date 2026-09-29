<x-admin-layout title="Novo imóvel">
    <h1 class="text-2xl font-bold text-slate-900 mb-6">Novo imóvel</h1>

    <form action="{{ route('admin.properties.store') }}" method="POST" enctype="multipart/form-data">
        @include('admin.properties._form')
    </form>
</x-admin-layout>
