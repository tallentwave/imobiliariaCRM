<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionEvent;
use App\Models\CommissionPlan;
use App\Models\CommissionSplit;
use App\Models\Deal;
use App\Models\DealChecklist;
use App\Models\DealParty;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DealController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $deals = Deal::query()
            ->where('organization_id', $user->organization_id)
            ->when(! $user->hasRole('admin') && ! $user->hasRole('financeiro'), fn ($q) => $q->where('agent_user_id', $user->id))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->with(['property', 'buyerContact', 'agent'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.deals.index', compact('deals'));
    }

    public function createFromProposal(Proposal $proposal)
    {
        abort_unless($proposal->status === 'ACCEPTED', 422, 'A proposta precisa estar aceita para virar negócio.');

        $property = $proposal->property;
        $seller = $property->owners()->first();

        $deal = Deal::create([
            'organization_id' => Auth::user()->organization_id,
            'unit_id' => Auth::user()->unit_id,
            'property_id' => $property->id,
            'opportunity_id' => $proposal->opportunity_id,
            'proposal_id' => $proposal->id,
            'buyer_contact_id' => $proposal->buyer_contact_id,
            'seller_contact_id' => $seller?->id,
            'agent_user_id' => Auth::id(),
            'captor_user_id' => $property->listingAgreements()->first()?->captor_user_id,
            'value' => $proposal->price,
            'status' => 'NEGOTIATION',
        ]);

        DealParty::create(['deal_id' => $deal->id, 'contact_id' => $proposal->buyer_contact_id, 'role' => 'BUYER']);
        if ($seller) {
            DealParty::create(['deal_id' => $deal->id, 'contact_id' => $seller->id, 'role' => 'SELLER']);
        }

        foreach (['Assinatura do contrato', 'Envio de documentação', 'Registro em cartório', 'Liberação de chaves'] as $i => $title) {
            DealChecklist::create(['deal_id' => $deal->id, 'title' => $title, 'order' => $i]);
        }

        return redirect()->route('admin.deals.show', $deal)->with('success', 'Negócio criado no Deal Room.');
    }

    public function show(Deal $deal)
    {
        $deal->load([
            'property.images', 'opportunity', 'proposal', 'buyerContact', 'sellerContact',
            'agent', 'captor', 'parties.contact', 'checklistItems.responsible', 'documents',
            'commissionEvent.splits.user',
        ]);

        return view('admin.deals.show', compact('deal'));
    }

    public function updateStatus(Request $request, Deal $deal)
    {
        $data = $request->validate([
            'status' => ['required', 'in:NEGOTIATION,CONTRACT,DOCUMENTATION,CLOSED_WON,CLOSED_LOST'],
            'lost_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $deal->update($data);

        if ($deal->status === 'CLOSED_WON') {
            $this->generateCommission($deal);
            $deal->opportunity?->update(['status' => 'WON', 'closed_at' => now()]);
            $deal->property->update(['status' => $deal->property->purpose === 'aluguel' ? 'alugado' : 'vendido']);
        }

        return back()->with('success', 'Status do negócio atualizado.');
    }

    public function toggleChecklist(DealChecklist $checklist)
    {
        $checklist->update(['status' => $checklist->status === 'DONE' ? 'PENDING' : 'DONE']);

        return back()->with('success', 'Checklist atualizado.');
    }

    private function generateCommission(Deal $deal): void
    {
        if ($deal->commissionEvent()->exists()) {
            return;
        }

        $purpose = $deal->property->purpose === 'aluguel' ? 'aluguel' : null;

        $plan = CommissionPlan::where('organization_id', $deal->organization_id)
            ->where('purpose', $purpose)
            ->where('active', true)
            ->first()
            ?? CommissionPlan::where('organization_id', $deal->organization_id)->where('is_default', true)->first();

        if (! $plan) {
            return;
        }

        $percent = (float) $plan->base_percent;
        $grossValue = (float) $deal->value;
        $total = round($grossValue * $percent / 100, 2);

        $event = CommissionEvent::create([
            'deal_id' => $deal->id,
            'commission_plan_id' => $plan->id,
            'gross_value' => $grossValue,
            'total_commission_percent' => $percent,
            'total_commission_value' => $total,
            'status' => 'PENDING',
        ]);

        foreach ($plan->rules as $rule) {
            $userId = match ($rule->dimension) {
                'CAP' => $deal->captor_user_id,
                'BUY' => $deal->agent_user_id,
                default => null,
            };

            CommissionSplit::create([
                'commission_event_id' => $event->id,
                'user_id' => $userId,
                'dimension' => $rule->dimension,
                'percentage' => $rule->percentage,
                'value' => round($total * $rule->percentage / 100, 2),
            ]);
        }
    }
}
