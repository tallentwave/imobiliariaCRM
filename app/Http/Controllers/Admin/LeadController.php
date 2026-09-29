<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Lead::class);

        return view('admin.leads.index');
    }

    public function show(Lead $lead)
    {
        $this->authorize('view', $lead);

        $lead->load(['contact', 'property', 'agent', 'assignedTeam', 'visits', 'documents', 'opportunities']);
        $agents = User::role('corretor')->get();

        return view('admin.leads.show', compact('lead', 'agents'));
    }

    public function update(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $data = $request->validate([
            'stage' => ['required', 'in:'.implode(',', array_keys(Lead::STAGES))],
            'agent_id' => ['nullable', 'exists:users,id'],
            'temperature' => ['nullable', 'in:COLD,WARM,HOT'],
            'lost_reason' => ['required_if:stage,LOST,SPAM,DUPLICATE,INVALID', 'nullable', 'string', 'max:255'],
        ]);

        if (! $lead->first_response_at) {
            $data['first_response_at'] = now();
        }

        $data['last_activity_at'] = now();

        $lead->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'lead' => $lead]);
        }

        return back()->with('success', 'Lead atualizado.');
    }

    public function convertToOpportunity(Lead $lead)
    {
        $this->authorize('update', $lead);

        $opportunity = Opportunity::create([
            'organization_id' => $lead->organization_id,
            'unit_id' => $lead->unit_id,
            'contact_id' => $lead->contact_id,
            'lead_id' => $lead->id,
            'assigned_user_id' => $lead->agent_id,
            'purpose' => 'MORAR',
            'status' => 'OPEN',
        ]);

        $lead->update(['stage' => 'OPPORTUNITY', 'last_activity_at' => now()]);

        return redirect()->route('admin.opportunities.show', $opportunity)->with('success', 'Oportunidade criada a partir do lead.');
    }

    public function destroy(Lead $lead)
    {
        $this->authorize('delete', $lead);

        $lead->delete();

        return redirect()->route('admin.leads.index')->with('success', 'Lead removido.');
    }
}
