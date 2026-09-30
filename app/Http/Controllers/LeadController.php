<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\Property;
use App\Models\User;
use App\Notifications\NewLeadReceived;
use App\Services\LeadAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class LeadController extends Controller
{
    public function __construct(private LeadAssignmentService $assignmentService) {}

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'message' => ['nullable', 'string', 'max:2000'],
            'property_id' => ['nullable', 'exists:properties,id'],
            'source' => ['nullable', 'string', 'max:50'],
        ]);

        $property = isset($data['property_id']) ? Property::find($data['property_id']) : null;

        $agentId = $this->assignmentService->pickAgent($property)?->id;

        $organization = Organization::first();

        $contact = $this->findOrCreateContact($organization?->id, $data);

        $lead = Lead::create([
            'organization_id' => $organization?->id,
            'contact_id' => $contact?->id,
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'message' => $data['message'] ?? null,
            'source' => $data['source'] ?? 'site',
            'property_id' => $property?->id,
            'agent_id' => $agentId,
            'user_id' => auth()->id(),
            'stage' => 'NEW',
            'temperature' => 'WARM',
        ]);

        $recipients = User::role('admin')->get();

        if ($agentId && $agent = User::find($agentId)) {
            $recipients->push($agent);
        }

        // O aviso por e-mail para a equipe é um efeito colateral, não o objetivo do
        // envio do formulário — uma falha aqui (SMTP fora do ar, mal configurado)
        // nunca pode impedir o visitante de ver a confirmação do contato enviado.
        try {
            Notification::send($recipients->unique('id'), new NewLeadReceived($lead));
        } catch (\Throwable $e) {
            Log::warning('Falha ao notificar equipe sobre novo lead #'.$lead->id.': '.$e->getMessage());
        }

        return back()->with('success', 'Recebemos seu contato! Em breve um de nossos corretores irá falar com você.');
    }

    private function findOrCreateContact(?int $organizationId, array $data): ?Contact
    {
        if (! $organizationId) {
            return null;
        }

        $query = Contact::where('organization_id', $organizationId);

        if (! empty($data['email'])) {
            $query->where('email', $data['email']);
        } else {
            $query->where('mobile', $data['phone']);
        }

        $contact = $query->first();

        if ($contact) {
            return $contact;
        }

        return Contact::create([
            'organization_id' => $organizationId,
            'type' => 'PERSON',
            'full_name' => $data['name'],
            'email' => $data['email'] ?? null,
            'mobile' => $data['phone'],
            'user_id' => auth()->id(),
            'status' => 'ACTIVE',
        ]);
    }
}
