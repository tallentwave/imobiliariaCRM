@csrf
@if(isset($agreement)) @method('PUT') @endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl border border-slate-100 p-6 space-y-4">
        <h2 class="font-semibold text-slate-800">Contrato de captação</h2>

        <div>
            <label class="text-xs font-semibold text-slate-500">Imóvel</label>
            <select name="property_id" required class="mt-1 w-full rounded-lg border-slate-200 text-sm" {{ isset($agreement) ? 'disabled' : '' }}>
                @foreach($properties as $property)
                    <option value="{{ $property->id }}" @selected(old('property_id', $agreement->property_id ?? null) == $property->id)>{{ $property->title }}</option>
                @endforeach
            </select>
            @if(isset($agreement))
                <input type="hidden" name="property_id" value="{{ $agreement->property_id }}">
            @endif
        </div>

        <div>
            <label class="text-xs font-semibold text-slate-500">Captador responsável</label>
            <select name="captor_user_id" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                <option value="">—</option>
                @foreach(\App\Models\User::role('captador')->orWhereHas('roles', fn($q) => $q->where('name', 'admin'))->get() as $user)
                    <option value="{{ $user->id }}" @selected(old('captor_user_id', $agreement->captor_user_id ?? auth()->id()) == $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-semibold text-slate-500">Tipo de captação</label>
                <select name="listing_type" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                    @foreach(\App\Models\ListingAgreement::TYPES as $key => $label)
                        <option value="{{ $key }}" @selected(old('listing_type', $agreement->listing_type ?? 'OPEN') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">Comissão (%)</label>
                <input type="number" step="0.01" name="commission_percent" value="{{ old('commission_percent', $agreement->commission_percent ?? 5) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-semibold text-slate-500">Início da vigência</label>
                <input type="date" name="starts_at" value="{{ old('starts_at', isset($agreement) ? $agreement->starts_at?->format('Y-m-d') : now()->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">Fim da vigência</label>
                <input type="date" name="ends_at" value="{{ old('ends_at', isset($agreement) ? $agreement->ends_at?->format('Y-m-d') : null) }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            </div>
        </div>

        <div>
            <label class="text-xs font-semibold text-slate-500">Status</label>
            <select name="status" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                @foreach(\App\Models\ListingAgreement::STAGES as $key => $label)
                    <option value="{{ $key }}" @selected(old('status', $agreement->status ?? 'PROSPECT') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-100 p-6 space-y-4">
            <h2 class="font-semibold text-slate-800">Proprietário</h2>
            @if(isset($agreement) && $agreement->property->owners->isNotEmpty())
                @foreach($agreement->property->owners as $owner)
                    <a href="{{ route('admin.contacts.show', $owner) }}" class="block rounded-lg bg-slate-50 p-3 text-sm hover:bg-slate-100">
                        {{ $owner->displayName() }} · {{ $owner->pivot->authorization_status }}
                    </a>
                @endforeach
                <p class="text-xs text-slate-400">Para adicionar outro proprietário, preencha os campos abaixo.</p>
            @endif

            <div>
                <label class="text-xs font-semibold text-slate-500">Nome do proprietário</label>
                <input type="text" name="owner_name" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500">E-mail</label>
                    <input type="email" name="owner_email" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">Celular</label>
                    <input type="text" name="owner_mobile" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
                </div>
            </div>
            <p class="text-[11px] text-slate-400">Se um contato com este e-mail já existir, ele será reaproveitado como proprietário.</p>
        </div>

        <button type="submit" class="w-full rounded-lg bg-brand-700 text-white font-semibold py-3 hover:bg-brand-800">
            {{ isset($agreement) ? 'Salvar captação' : 'Registrar captação' }}
        </button>
    </div>
</div>
