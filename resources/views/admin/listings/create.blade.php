<x-admin-layout title="Nova captação">
    <h1 class="text-2xl font-bold text-slate-900 mb-6">Nova captação</h1>

    <form action="{{ route('admin.listings.store') }}" method="POST">
        @include('admin.listings._form')
    </form>
</x-admin-layout>
