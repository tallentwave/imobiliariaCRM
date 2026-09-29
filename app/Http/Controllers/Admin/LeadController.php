<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
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

        $lead->load(['property', 'agent', 'visits', 'documents']);
        $agents = User::role('corretor')->get();

        return view('admin.leads.show', compact('lead', 'agents'));
    }

    public function update(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $data = $request->validate([
            'stage' => ['required', 'in:'.implode(',', array_keys(Lead::STAGES))],
            'agent_id' => ['nullable', 'exists:users,id'],
            'negotiated_value' => ['nullable', 'numeric', 'min:0'],
            'commission_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'lost_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $lead->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'lead' => $lead]);
        }

        return back()->with('success', 'Lead atualizado.');
    }

    public function destroy(Lead $lead)
    {
        $this->authorize('delete', $lead);

        $lead->delete();

        return redirect()->route('admin.leads.index')->with('success', 'Lead removido.');
    }
}
