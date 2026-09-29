<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opportunity;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposalController extends Controller
{
    public function store(Request $request, Opportunity $opportunity)
    {
        $data = $this->validated($request);

        $proposal = Proposal::create([
            ...$data,
            'opportunity_id' => $opportunity->id,
            'buyer_contact_id' => $opportunity->contact_id,
            'created_by' => Auth::id(),
            'version' => 1,
            'status' => 'PRESENTED',
        ]);

        return redirect()->route('admin.opportunities.show', $opportunity)->with('success', 'Proposta registrada.');
    }

    public function counter(Request $request, Proposal $proposal)
    {
        $data = $this->validated($request);

        $proposal->createCounter($data);

        return back()->with('success', 'Contraproposta registrada (nova versão criada).');
    }

    public function accept(Proposal $proposal)
    {
        $proposal->update(['status' => 'ACCEPTED']);

        return back()->with('success', 'Proposta aceita. Você já pode criar o negócio (Deal Room).');
    }

    public function reject(Request $request, Proposal $proposal)
    {
        $proposal->update(['status' => 'REJECTED']);

        return back()->with('success', 'Proposta rejeitada.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'down_payment' => ['nullable', 'numeric', 'min:0'],
            'financing_amount' => ['nullable', 'numeric', 'min:0'],
            'fgts_amount' => ['nullable', 'numeric', 'min:0'],
            'conditions' => ['nullable', 'string'],
            'valid_until' => ['nullable', 'date'],
        ]);
    }
}
