<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Property::class);

        $user = Auth::user();

        $properties = Property::query()
            ->when(! $user->hasRole('admin') && ! $user->hasRole('financeiro') && ! $user->hasRole('captador'), fn ($q) => $q->where('agent_id', $user->id))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->q, fn ($q, $v) => $q->where('title', 'like', "%{$v}%"))
            ->with(['agent', 'images'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.properties.index', compact('properties'));
    }

    public function create()
    {
        $this->authorize('create', Property::class);

        $agents = User::role('corretor')->orWhereHas('roles', fn ($q) => $q->where('name', 'admin'))->get();
        $features = Feature::orderBy('name')->get();

        return view('admin.properties.create', compact('agents', 'features'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Property::class);

        $data = $this->validated($request);

        $data['created_by'] = Auth::id();
        $data['agent_id'] = $data['agent_id'] ?? Auth::id();
        $data['organization_id'] = Auth::user()->organization_id;
        $data['unit_id'] = Auth::user()->unit_id;

        $property = Property::create($data);
        $property->features()->sync($request->input('features', []));

        $this->storeImages($request, $property);

        return redirect()->route('admin.properties.edit', $property)->with('success', 'Imóvel criado com sucesso.');
    }

    public function edit(Property $property)
    {
        $this->authorize('update', $property);

        $agents = User::role('corretor')->orWhereHas('roles', fn ($q) => $q->where('name', 'admin'))->get();
        $features = Feature::orderBy('name')->get();
        $property->load(['images', 'features']);

        return view('admin.properties.edit', compact('property', 'agents', 'features'));
    }

    public function update(Request $request, Property $property)
    {
        $this->authorize('update', $property);

        $data = $this->validated($request, $property);

        $property->update($data);
        $property->features()->sync($request->input('features', []));

        $this->storeImages($request, $property);

        return back()->with('success', 'Imóvel atualizado com sucesso.');
    }

    public function destroy(Property $property)
    {
        $this->authorize('delete', $property);

        $property->delete();

        return redirect()->route('admin.properties.index')->with('success', 'Imóvel removido.');
    }

    private function validated(Request $request, ?Property $property = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'purpose' => ['required', 'in:venda,aluguel,temporada'],
            'type' => ['required', 'in:apartamento,casa,casa_condominio,cobertura,terreno,comercial,sala,galpao,rural,outro'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'condo_fee' => ['nullable', 'numeric', 'min:0'],
            'iptu' => ['nullable', 'numeric', 'min:0'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'suites' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'integer', 'min:0'],
            'parking_spots' => ['nullable', 'integer', 'min:0'],
            'area_total' => ['nullable', 'numeric', 'min:0'],
            'area_built' => ['nullable', 'numeric', 'min:0'],
            'zipcode' => ['nullable', 'string', 'max:9'],
            'address' => ['nullable', 'string', 'max:255'],
            'number' => ['nullable', 'string', 'max:20'],
            'complement' => ['nullable', 'string', 'max:255'],
            'neighborhood' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:2'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'show_exact_address' => ['nullable', 'boolean'],
            'status' => ['required', 'in:disponivel,reservado,vendido,alugado,inativo'],
            'featured' => ['nullable', 'boolean'],
            'agent_id' => ['nullable', 'exists:users,id'],
        ]);
    }

    private function storeImages(Request $request, Property $property): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $order = $property->images()->max('order') ?? 0;
        $hasCover = $property->images()->where('is_cover', true)->exists();

        foreach ($request->file('images') as $file) {
            $order++;
            $path = $file->store('properties', 'public');

            $property->images()->create([
                'path' => $path,
                'order' => $order,
                'is_cover' => ! $hasCover && $order === 1,
            ]);

            $hasCover = true;
        }
    }
}
