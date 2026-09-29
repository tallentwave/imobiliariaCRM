<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opportunity;
use App\Services\MatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpportunityController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $opportunities = Opportunity::query()
            ->where('organization_id', $user->organization_id)
            ->when(! $user->hasRole('admin') && ! $user->hasRole('financeiro'), fn ($q) => $q->where('assigned_user_id', $user->id))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->with(['contact', 'assignedUser'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.opportunities.index', compact('opportunities'));
    }

    public function show(Opportunity $opportunity, MatchService $matchService)
    {
        $opportunity->load(['contact', 'lead.property', 'assignedUser', 'requirements', 'proposals.property', 'deals', 'visits.property']);

        $matches = $opportunity->status === 'OPEN'
            ? $matchService->topMatchesForOpportunity($opportunity, 6)
            : collect();

        return view('admin.opportunities.show', compact('opportunity', 'matches'));
    }

    public function update(Request $request, Opportunity $opportunity)
    {
        $data = $request->validate([
            'status' => ['required', 'in:OPEN,WON,LOST'],
            'budget_min' => ['nullable', 'numeric', 'min:0'],
            'budget_max' => ['nullable', 'numeric', 'min:0'],
        ]);

        $opportunity->update($data);

        return back()->with('success', 'Oportunidade atualizada.');
    }

    public function storeRequirement(Request $request, Opportunity $opportunity)
    {
        $data = $request->validate([
            'feature_key' => ['required', 'in:city,neighborhood,type,bedrooms,area_min,parking_spots,feature'],
            'feature_value' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'in:OBRIGATORIO,DESEJAVEL,INDIFERENTE,EXCLUDENTE'],
        ]);

        $opportunity->requirements()->create($data);

        return back()->with('success', 'Critério adicionado.');
    }

    public function destroyRequirement(Opportunity $opportunity, \App\Models\OpportunityRequirement $requirement)
    {
        abort_unless($requirement->opportunity_id === $opportunity->id, 404);

        $requirement->delete();

        return back()->with('success', 'Critério removido.');
    }
}
