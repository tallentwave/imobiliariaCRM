@csrf
@if(isset($visit)) @method('PUT') @endif

<div class="bg-white rounded-2xl border border-slate-100 p-6 space-y-4 max-w-xl">
    <div>
        <label class="text-xs font-semibold text-slate-500">Imóvel</label>
        <select name="property_id" required class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            @foreach($properties as $property)
                <option value="{{ $property->id }}" @selected(old('property_id', $visit->property_id ?? request('property_id')) == $property->id)>{{ $property->title }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="text-xs font-semibold text-slate-500">Corretor</label>
        <select name="agent_id" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            <option value="">Sem corretor</option>
            @foreach($agents as $agent)
                <option value="{{ $agent->id }}" @selected(old('agent_id', $visit->agent_id ?? auth()->id()) == $agent->id)>{{ $agent->name }}</option>
            @endforeach
        </select>
    </div>

    <input type="hidden" name="lead_id" value="{{ old('lead_id', $visit->lead_id ?? request('lead_id')) }}">

    <div>
        <label class="text-xs font-semibold text-slate-500">Data e hora</label>
        <input type="datetime-local" name="scheduled_at" required
               value="{{ old('scheduled_at', isset($visit) ? $visit->scheduled_at->format('Y-m-d\TH:i') : '') }}"
               class="mt-1 w-full rounded-lg border-slate-200 text-sm">
    </div>

    <div>
        <label class="text-xs font-semibold text-slate-500">Status</label>
        <select name="status" class="mt-1 w-full rounded-lg border-slate-200 text-sm">
            @foreach(['agendada' => 'Agendada', 'realizada' => 'Realizada', 'cancelada' => 'Cancelada'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $visit->status ?? 'agendada') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="text-xs font-semibold text-slate-500">Observações</label>
        <textarea name="notes" rows="3" class="mt-1 w-full rounded-lg border-slate-200 text-sm">{{ old('notes', $visit->notes ?? '') }}</textarea>
    </div>

    <button type="submit" class="w-full rounded-lg bg-brand-700 text-white font-semibold py-2.5 hover:bg-brand-800">
        {{ isset($visit) ? 'Salvar alterações' : 'Agendar visita' }}
    </button>
</div>
