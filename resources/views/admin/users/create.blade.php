<x-admin-layout title="Novo usuário">
    <h1 class="text-2xl font-bold text-slate-900 mb-6">Novo usuário</h1>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @include('admin.users._form')
    </form>
</x-admin-layout>
