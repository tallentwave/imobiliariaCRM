<?php

namespace App\Livewire\Admin;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LeadsKanban extends Component
{
    public array $stages = [];

    public function mount(): void
    {
        $this->stages = Lead::STAGES;
    }

    public function getLeadsProperty()
    {
        $user = Auth::user();

        return Lead::query()
            ->when(! $user->hasRole('admin') && ! $user->hasRole('financeiro'), fn ($q) => $q->where('agent_id', $user->id))
            ->with(['property', 'agent'])
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('stage');
    }

    public function getAgentsProperty()
    {
        return User::role('corretor')->orderBy('name')->get();
    }

    public function moveLead(int $leadId, string $stage): void
    {
        if (! array_key_exists($stage, Lead::STAGES)) {
            return;
        }

        $lead = Lead::findOrFail($leadId);

        $this->authorize('update', $lead);

        $lead->update(['stage' => $stage]);

        $this->dispatch('notify', message: 'Lead movido para "'.Lead::STAGES[$stage].'".');
    }

    public function render()
    {
        return view('livewire.admin.leads-kanban', [
            'leads' => $this->leads,
            'agents' => $this->agents,
        ]);
    }
}
