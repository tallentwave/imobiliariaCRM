<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Proposal;
use App\Models\Visit;
use Illuminate\Http\Request;

class ClientAreaController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $contact = Contact::where('user_id', $user->id)->first();

        $favorites = $user->favorites()->with('images')->latest()->get();
        $savedSearches = $user->savedSearches()->latest()->get();

        // Sem contato vinculado ainda (usuário novo que nunca enviou uma mensagem) —
        // não existe nada para buscar, e where('contact_id', null) viraria "IS NULL",
        // o que exporia registros de outras pessoas sem contato definido.
        if ($contact) {
            $visits = Visit::where('contact_id', $contact->id)
                ->with(['property', 'agent'])
                ->orderByDesc('scheduled_at')
                ->get();

            $leads = Lead::where('contact_id', $contact->id)
                ->with(['property', 'agent'])
                ->latest()
                ->get();

            // Só a versão mais recente de cada proposta (sem contrapropostas ainda pendentes)
            $proposals = Proposal::where('buyer_contact_id', $contact->id)
                ->whereDoesntHave('counters')
                ->with('property')
                ->latest()
                ->get();

            $deals = Deal::where('buyer_contact_id', $contact->id)
                ->with(['property', 'checklistItems'])
                ->latest()
                ->get();
        } else {
            $visits = collect();
            $leads = collect();
            $proposals = collect();
            $deals = collect();
        }

        $stats = [
            'favorites' => $favorites->count(),
            'visits_upcoming' => $visits->where('status', 'agendada')->count(),
            'proposals_active' => $proposals->whereIn('status', ['PRESENTED', 'COUNTERED'])->count(),
            'deals_active' => $deals->whereNotIn('status', ['CLOSED_WON', 'CLOSED_LOST'])->count(),
        ];

        return view('client.index', compact('favorites', 'savedSearches', 'visits', 'leads', 'proposals', 'deals', 'stats'));
    }
}
