<x-admin-layout title="Editar contato">
    <h1 class="text-2xl font-bold text-slate-900 mb-6">Editar contato</h1>

    <form action="{{ route('admin.contacts.update', $contact) }}" method="POST" class="bg-white rounded-2xl border border-slate-100 p-6 space-y-4 max-w-xl">
        @csrf @method('PUT')

        <div>
            <label class="text-xs font-semibold text-slate-500">Tipo</label>
            <select name="type" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                <option value="PERSON" @selected($contact->type === 'PERSON')>Pessoa física</option>
                <option value="COMPANY" @selected($contact->type === 'COMPANY')>Empresa</option>
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold text-slate-500">Nome completo</label>
            <input type="text" name="full_name" required value="{{ old('full_name', $contact->full_name) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
        </div>
        <div>
            <label class="text-xs font-semibold text-slate-500">Razão social (se empresa)</label>
            <input type="text" name="legal_name" value="{{ old('legal_name', $contact->legal_name) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-semibold text-slate-500">CPF</label>
                <input type="text" name="cpf" value="{{ old('cpf', $contact->cpf) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">CNPJ</label>
                <input type="text" name="cnpj" value="{{ old('cnpj', $contact->cnpj) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-semibold text-slate-500">E-mail</label>
                <input type="email" name="email" value="{{ old('email', $contact->email) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">Celular</label>
                <input type="text" name="mobile" value="{{ old('mobile', $contact->mobile) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-semibold text-slate-500">WhatsApp</label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp', $contact->whatsapp) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">Data de nascimento</label>
                <input type="date" name="birth_date" value="{{ old('birth_date', $contact->birth_date?->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            </div>
        </div>
        <div>
            <label class="text-xs font-semibold text-slate-500">Status</label>
            <select name="status" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                @foreach(['ACTIVE' => 'Ativo', 'INACTIVE' => 'Inativo', 'ARCHIVED' => 'Arquivado'] as $key => $label)
                    <option value="{{ $key }}" @selected($contact->status === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="w-full rounded-lg bg-brand-700 text-white font-semibold py-2.5 hover:bg-brand-800">
            Salvar alterações
        </button>
    </form>
</x-admin-layout>
