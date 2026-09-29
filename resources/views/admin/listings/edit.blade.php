<x-admin-layout title="Editar captação">
    <h1 class="text-2xl font-bold text-slate-900 mb-6">Captação — {{ $agreement->property->title }}</h1>

    <form action="{{ route('admin.listings.update', $agreement) }}" method="POST">
        @include('admin.listings._form')
    </form>
</x-admin-layout>
