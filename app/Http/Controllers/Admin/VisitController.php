<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $visits = Visit::query()
            ->when(! $user->hasRole('admin') && ! $user->hasRole('financeiro'), fn ($q) => $q->where('agent_id', $user->id))
            ->with(['property', 'agent', 'lead'])
            ->orderBy('scheduled_at')
            ->get();

        return view('admin.visits.index', compact('visits'));
    }

    public function create()
    {
        $properties = Property::orderBy('title')->get();
        $agents = User::role('corretor')->get();

        return view('admin.visits.create', compact('properties', 'agents'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        Visit::create($data);

        return redirect()->route('admin.visits.index')->with('success', 'Visita agendada.');
    }

    public function edit(Visit $visit)
    {
        $properties = Property::orderBy('title')->get();
        $agents = User::role('corretor')->get();

        return view('admin.visits.edit', compact('visit', 'properties', 'agents'));
    }

    public function update(Request $request, Visit $visit)
    {
        $data = $this->validated($request);

        $visit->update($data);

        return back()->with('success', 'Visita atualizada.');
    }

    public function destroy(Visit $visit)
    {
        $visit->delete();

        return redirect()->route('admin.visits.index')->with('success', 'Visita removida.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
            'agent_id' => ['nullable', 'exists:users,id'],
            'lead_id' => ['nullable', 'exists:leads,id'],
            'scheduled_at' => ['required', 'date'],
            'status' => ['required', 'in:agendada,realizada,cancelada'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
