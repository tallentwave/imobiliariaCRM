<x-admin-layout title="Editar visita">
    <h1 class="text-2xl font-bold text-slate-900 mb-6">Editar visita</h1>

    <form action="{{ route('admin.visits.update', $visit) }}" method="POST">
        @include('admin.visits._form')
    </form>
</x-admin-layout>
