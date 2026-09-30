<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
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

        // Leads antigos (ou criados sem formulário) podem não ter um contato
        // vinculado, mas opportunities.contact_id é obrigatório — cria/associa
        // um contato a partir dos próprios dados do lead antes de prosseguir.
        if (! $lead->contact_id) {
            $contact = $this->findOrCreateContactForLead($lead);
            $lead->update(['contact_id' => $contact->id]);
        }

        $opportunity = Opportunity::create([
            'organization_id' => $lead->organization_id,
            'unit_id' => $lead->unit_id,
            'contact_id' => $lead->contact_id,
            'lead_id' => $lead->id,
            'assigned_user_id' => $lead->agent_id,
            'purpose' => 'MORAR',
            'status' => 'OPEN',
        ]);

        // Pré-preenche os critérios de match a partir do imóvel que originou o lead,
        // para o ALTIUS Match já ter dados úteis desde a criação da oportunidade.
        if ($property = $lead->property) {
            $requirements = array_filter([
                $property->city ? ['feature_key' => 'city', 'feature_value' => $property->city, 'priority' => 'DESEJAVEL'] : null,
                $property->neighborhood ? ['feature_key' => 'neighborhood', 'feature_value' => $property->neighborhood, 'priority' => 'DESEJAVEL'] : null,
                ['feature_key' => 'type', 'feature_value' => $property->type, 'priority' => 'DESEJAVEL'],
                $property->bedrooms ? ['feature_key' => 'bedrooms', 'feature_value' => (string) $property->bedrooms, 'priority' => 'DESEJAVEL'] : null,
                $property->parking_spots ? ['feature_key' => 'parking_spots', 'feature_value' => (string) $property->parking_spots, 'priority' => 'DESEJAVEL'] : null,
            ]);

            $opportunity->requirements()->createMany($requirements);
        }

        $lead->update(['stage' => 'OPPORTUNITY', 'last_activity_at' => now()]);

        return redirect()->route('admin.opportunities.show', $opportunity)->with('success', 'Oportunidade criada a partir do lead, com critérios de match pré-preenchidos.');
    }

    public function destroy(Lead $lead)
    {
        $this->authorize('delete', $lead);

        $lead->delete();

        return redirect()->route('admin.leads.index')->with('success', 'Lead removido.');
    }

    private function findOrCreateContactForLead(Lead $lead): Contact
    {
        $query = Contact::where('organization_id', $lead->organization_id);

        if ($lead->email) {
            $query->where('email', $lead->email);
        } elseif ($lead->phone) {
            $query->where('mobile', $lead->phone);
        } else {
            return Contact::create([
                'organization_id' => $lead->organization_id,
                'unit_id' => $lead->unit_id,
                'type' => 'PERSON',
                'full_name' => $lead->name,
                'owner_user_id' => $lead->agent_id,
                'status' => 'ACTIVE',
            ]);
        }

        return $query->first() ?? Contact::create([
            'organization_id' => $lead->organization_id,
            'unit_id' => $lead->unit_id,
            'type' => 'PERSON',
            'full_name' => $lead->name,
            'email' => $lead->email,
            'mobile' => $lead->phone,
            'owner_user_id' => $lead->agent_id,
            'status' => 'ACTIVE',
        ]);
    }
}
