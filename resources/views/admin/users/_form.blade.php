@csrf
@if(isset($user)) @method('PUT') @endif

<div class="bg-white rounded-2xl border border-slate-100 p-6 space-y-4 max-w-xl">
    <div>
        <label class="text-xs font-semibold text-slate-500">Nome</label>
        <input type="text" name="name" required value="{{ old('name', $user->name ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
    </div>
    <div>
        <label class="text-xs font-semibold text-slate-500">E-mail</label>
        <input type="email" name="email" required value="{{ old('email', $user->email ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
    </div>
    <div>
        <label class="text-xs font-semibold text-slate-500">Senha {{ isset($user) ? '(deixe em branco para manter)' : '' }}</label>
        <input type="password" name="password" {{ isset($user) ? '' : 'required' }} class="mt-1 w-full rounded-lg border-slate-200 text-sm">
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-xs font-semibold text-slate-500">Telefone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
        </div>
        <div>
            <label class="text-xs font-semibold text-slate-500">CRECI</label>
            <input type="text" name="creci" value="{{ old('creci', $user->creci ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-xs font-semibold text-slate-500">Papel</label>
            <select name="role" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" @selected(old('role', isset($user) ? $user->roles->first()?->name : '') === $role->name)>{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold text-slate-500">Comissão padrão (%)</label>
            <input type="number" step="0.01" name="default_commission_percent" value="{{ old('default_commission_percent', $user->default_commission_percent ?? 0) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
        </div>
    </div>
    <label class="flex items-center gap-2 text-sm text-slate-600">
        <input type="checkbox" name="active" value="1" @checked(old('active', $user->active ?? true)) class="rounded border-slate-300 text-brand-700">
        Usuário ativo
    </label>

    <button type="submit" class="w-full rounded-lg bg-brand-700 text-white font-semibold py-2.5 hover:bg-brand-800">
        {{ isset($user) ? 'Salvar alterações' : 'Criar usuário' }}
    </button>
</div>
