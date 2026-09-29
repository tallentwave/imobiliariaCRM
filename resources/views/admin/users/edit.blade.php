<x-admin-layout title="Editar usuário">
    <h1 class="text-2xl font-bold text-slate-900 mb-6">Editar usuário</h1>

    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @include('admin.users._form')
    </form>
</x-admin-layout>
