<x-admin-layout title="Agendar visita">
    <h1 class="text-2xl font-bold text-slate-900 mb-6">Agendar visita</h1>

    <form action="{{ route('admin.visits.store') }}" method="POST">
        @include('admin.visits._form')
    </form>
</x-admin-layout>
